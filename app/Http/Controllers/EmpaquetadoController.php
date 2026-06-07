<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Merma;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpaquetadoController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::all();
        $totalMerma = Merma::where('tipo_merma', 'empaquetado')->sum('cantidad');
        $produccionEstimada = 20000;
        $porcentajePerdida = $produccionEstimada > 0 ? round(($totalMerma / $produccionEstimada) * 100, 2) : 0;
        $incidenciasActivas = Merma::where('tipo_merma', 'empaquetado')
            ->where('fecha', '>=', now()->subDays(7))
            ->count();
        $costoMerma = Merma::where('tipo_merma', 'empaquetado')
            ->join('products', 'mermas.producto_id', '=', 'products.id')
            ->selectRaw('SUM(mermas.cantidad * COALESCE(products.precio_unitario, 0)) as total')
            ->first()->total ?? 0;
        $mermaPorProducto = Merma::where('tipo_merma', 'empaquetado')
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
        $mermasRecientes = Merma::where('tipo_merma', 'empaquetado')
            ->with('producto', 'usuario')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('empaquetado._content', compact(
                'productos',
                'totalMerma',
                'porcentajePerdida',
                'incidenciasActivas',
                'costoMerma',
                'mermaPorProducto',
                'mermasRecientes'
            ));
        }

        return view('empaquetado.index', compact(
            'productos',
            'totalMerma',
            'porcentajePerdida',
            'incidenciasActivas',
            'costoMerma',
            'mermaPorProducto',
            'mermasRecientes'
        ));
    }

    public function rapida(Producto $producto)
    {
        return view('empaquetado.rapida', compact('producto'));
    }

    public function storeMerma(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:products,id',
            'cantidad' => 'required|numeric|min:0.01',
            'causa' => 'required|string',
            'lote' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $producto = $producto->fresh();

        if (is_null($producto->stock_actual)) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'El producto no tiene stock definido.'], 400);
            }
            return back()->withErrors(['producto_id' => 'El producto no tiene stock definido.']);
        }

        if ($request->cantidad > $producto->stock_actual) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => "Stock insuficiente. Stock actual: {$producto->stock_actual}"], 400);
            }
            return back()->withErrors(['cantidad' => 'La cantidad de merma no puede superar el stock disponible (' . $producto->stock_actual . ').']);
        }

        $merma = Merma::create([
            'producto_id' => $producto->id,
            'cantidad' => $request->cantidad,
            'unidad' => $producto->unidad ?? 'kg',
            'causa' => $request->causa,
            'tipo_merma' => 'empaquetado',
            'lote' => $request->lote,
            'fecha' => now()->toDateString(),
            'usuario_id' => Auth::id(),
            'observaciones' => $request->observaciones,
        ]);

        $producto->decrement('stock_actual', $request->cantidad);
        $nuevoStock = $producto->fresh()->stock_actual;

        AuditHelper::log('merma_registrada', $producto, null, null, [
            'tipo_merma' => 'empaquetado',
            'cantidad' => $request->cantidad,
            'causa' => $request->causa,
            'lote' => $request->lote,
            'observaciones' => $request->observaciones,
            'stock_restante' => $nuevoStock,
            'merma_id' => $merma->id,
        ]);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => "Merma registrada. Stock actualizado a {$nuevoStock}"]);
        }

        return redirect()->route('empaquetado')->with('success', 'Merma registrada correctamente. Stock actualizado a ' . $nuevoStock);
    }
}