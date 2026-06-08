<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Merma;
use App\Models\Produccion;
use App\Helpers\AuditHelper;
use App\Events\StockBajoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProduccionController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::all();
        
        // Obtener lotes de producción para el selector
        $lotesProduccion = Produccion::with('producto')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();
        
        $totalMerma = Merma::where('tipo_merma', 'produccion')->sum('cantidad');
        $produccionEstimada = 30000;
        $porcentajePerdida = $produccionEstimada > 0 ? round(($totalMerma / $produccionEstimada) * 100, 2) : 0;
        $incidenciasActivas = Merma::where('tipo_merma', 'produccion')
            ->where('fecha', '>=', now()->subDays(7))
            ->count();
        $costoMerma = Merma::where('tipo_merma', 'produccion')
            ->join('productos', 'mermas.producto_id', '=', 'productos.id')
            ->selectRaw('SUM(mermas.cantidad * COALESCE(productos.precio_unitario, 0)) as total')
            ->first()->total ?? 0;
        $mermaPorProducto = Merma::where('tipo_merma', 'produccion')
            ->where('fecha', '>=', now()->subDays(7))
            ->select('producto_id', DB::raw('SUM(cantidad) as total_merma'))
            ->groupBy('producto_id')
            ->orderByDesc('total_merma')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $producto = Producto::withTrashed()->find($item->producto_id);
                return (object) [
                    'nombre' => $producto ? $producto->nombre : 'Desconocido',
                    'total' => $item->total_merma,
                ];
            });
        $mermasRecientes = Merma::where('tipo_merma', 'produccion')
            ->with('producto', 'usuario')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Verificar productos con stock bajo
        $this->verificarStockBajoGeneral();

        return view('produccion.index', compact(
            'productos',
            'totalMerma',
            'porcentajePerdida',
            'incidenciasActivas',
            'costoMerma',
            'mermaPorProducto',
            'mermasRecientes',
            'lotesProduccion'
        ));
    }
    
    public function rapida(Producto $producto)
    {
        return view('produccion.rapida', compact('producto'));
    }

    public function storeMerma(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'causa' => 'required|string',
            'lote' => 'nullable|string',
            'produccion_id' => 'nullable|exists:producciones,id',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $producto = Producto::findOrFail($request->producto_id);
            $producto = $producto->fresh();

            if (is_null($producto->stock_actual)) {
                DB::rollBack();
                if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'El producto no tiene stock definido.'], 400);
                }
                return back()->withErrors(['producto_id' => 'El producto no tiene stock definido.']);
            }

            if ($request->cantidad > $producto->stock_actual) {
                DB::rollBack();
                if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => "Stock insuficiente. Stock actual: {$producto->stock_actual}"], 400);
                }
                return back()->withErrors(['cantidad' => 'La cantidad de merma no puede superar el stock disponible (' . $producto->stock_actual . ').']);
            }

            $merma = Merma::create([
                'producto_id' => $producto->id,
                'produccion_id' => $request->produccion_id,
                'cantidad' => $request->cantidad,
                'unidad' => $producto->unidad ?? 'kg',
                'causa' => $request->causa,
                'tipo_merma' => 'produccion',
                'lote' => $request->lote,
                'fecha' => now()->toDateString(),
                'usuario_id' => Auth::id(),
                'observaciones' => $request->observaciones,
            ]);

            $producto->decrement('stock_actual', $request->cantidad);
            $nuevoStock = $producto->fresh()->stock_actual;

            // Verificar stock bajo después de la merma (usando EVENTO)
            $this->verificarStockBajo($producto);

            AuditHelper::log('merma_registrada', $producto, null, null, [
                'tipo_merma' => 'produccion',
                'cantidad' => $request->cantidad,
                'causa' => $request->causa,
                'lote' => $request->lote,
                'produccion_id' => $request->produccion_id,
                'stock_restante' => $nuevoStock,
                'merma_id' => $merma->id,
            ]);

            DB::commit();

            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'message' => "Merma registrada. Stock actualizado a {$nuevoStock}"]);
            }

            return redirect()->route('produccion')->with('success', 'Merma registrada correctamente. Stock actualizado a ' . $nuevoStock);
            
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al registrar merma: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE NOTIFICACIONES CON EVENTOS
    // =========================================================================

    /**
     * Verificar stock bajo de un producto y disparar evento
     */
    private function verificarStockBajo(Producto $producto): void
    {
        if ($producto->stock_actual <= $producto->stock_minimo && $producto->stock_minimo > 0) {
            event(new StockBajoEvent(
                $producto,
                'producto en producción',
                $producto->nombre,
                $producto->stock_actual,
                $producto->stock_minimo
            ));
        }
    }
    
    /**
     * Verificar todos los productos con stock bajo
     */
    private function verificarStockBajoGeneral(): void
    {
        $productosBajos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->get();
            
        foreach ($productosBajos as $producto) {
            $this->verificarStockBajo($producto);
        }
    }
}