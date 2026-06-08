<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Produccion;
use App\Models\Merma;
use App\Models\MateriaPrima;
use App\Helpers\AuditHelper;
use App\Helpers\NotificacionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProduccionRealController extends Controller
{
    // El middleware se aplica directamente en las rutas, no en el constructor

    public function create(Request $request)
    {
        $productos = Producto::with('recetas.materiaPrima')->orderBy('nombre')->get();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('produccion_real._content', compact('productos'));
        }

        return view('produccion_real.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_producida' => 'required|numeric|min:0',
            'producto_desechado' => 'nullable|numeric|min:0',
            'materia_prima_desechada' => 'nullable|numeric|min:0',
            'mp_consumida_real' => 'nullable|numeric|min:0',
            'fecha_produccion' => 'required|date',
            'lote' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'calidad_observaciones' => 'nullable|string',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $recetas = $producto->recetas;
        
        $elaborado = $request->cantidad_producida;
        $desechado = $request->producto_desechado ?? 0;
        $totalProducido = $elaborado + $desechado;
        $eficiencia = $totalProducido > 0 ? round(($elaborado / $totalProducido) * 100, 2) : 0;

        if ($recetas->isEmpty()) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'El producto no tiene una receta definida.'], 400);
            }
            return back()->withErrors(['producto_id' => 'El producto no tiene una receta definida. Primero defina los ingredientes en Recetas.']);
        }

        DB::beginTransaction();
        try {
            // Calcular consumo de MP
            $mpConsumidaTeorica = 0;
            foreach ($recetas as $receta) {
                $mpConsumidaTeorica += $receta->cantidad_necesaria * $elaborado;
            }
            
            $mpConsumidaReal = $request->mp_consumida_real ?? $mpConsumidaTeorica;
            $mpDesechada = $request->materia_prima_desechada ?? 0;
            $mpTotalUsada = $mpConsumidaReal + $mpDesechada;

            // Verificar stock de MP
            foreach ($recetas as $receta) {
                $materiaPrima = $receta->materiaPrima;
                $cantidadNecesaria = ($receta->cantidad_necesaria * $elaborado) + ($receta->cantidad_necesaria * ($mpDesechada / max($elaborado, 1)));
                if ($materiaPrima->stock_actual < $cantidadNecesaria) {
                    throw new \Exception("Stock insuficiente de {$materiaPrima->nombre}. Disponible: {$materiaPrima->stock_actual} {$materiaPrima->unidad}");
                }
            }

            // Crear registro de producción
            $produccion = Produccion::create([
                'lote' => $request->lote,
                'producto_id' => $producto->id,
                'cantidad_producida' => $elaborado,
                'materia_prima_desechada' => $mpDesechada,
                'producto_desechado' => $desechado,
                'fecha_produccion' => $request->fecha_produccion,
                'observaciones' => $request->observaciones,
                'calidad_observaciones' => $request->calidad_observaciones,
                'eficiencia' => $eficiencia,
                'usuario_id' => Auth::id(),
            ]);

            // Registrar consumo de MP
            foreach ($recetas as $receta) {
                $materiaPrima = $receta->materiaPrima;
                $cantidadConsumida = ($receta->cantidad_necesaria * $elaborado) + ($receta->cantidad_necesaria * ($mpDesechada / max($elaborado, 1)));
                $materiaPrima->registrarSalida($cantidadConsumida, 'consumo_produccion', "Consumo para producción del lote {$request->lote} (ID {$produccion->id})");
                
                // Verificar stock bajo de materia prima después del consumo
                $this->verificarStockBajoMP($materiaPrima);
            }

            // Aumentar stock del producto terminado (solo el elaborado)
            $producto->stock_actual += $elaborado;
            $producto->save();

            // Verificar stock bajo del producto terminado
            $this->verificarStockBajoProducto($producto);

            // Registrar merma de PT si hay desechado
            if ($desechado > 0) {
                Merma::create([
                    'producto_id' => $producto->id,
                    'cantidad' => $desechado,
                    'unidad' => $producto->unidad ?? 'kg',
                    'causa' => 'Merma en producción - lote ' . ($request->lote ?? $produccion->id),
                    'tipo_merma' => 'produccion',
                    'lote' => $request->lote,
                    'fecha' => now()->toDateString(),
                    'usuario_id' => Auth::id(),
                    'observaciones' => $request->calidad_observaciones ?? 'Producto no conforme en línea de producción',
                ]);
            }

            // Auditoría
            AuditHelper::log('produccion_registrada', $producto, null, [
                'lote' => $request->lote,
                'elaborado' => $elaborado,
                'desechado' => $desechado,
                'mp_desechada' => $mpDesechada,
                'eficiencia' => $eficiencia,
            ]);

            DB::commit();

            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => true, 'message' => "Producción registrada. Elaborado: {$elaborado}, Desechado: {$desechado}, Eficiencia: {$eficiencia}%"]);
            }

            return redirect()->route('produccion_real.historial')->with('success', "Producción registrada. Elaborado: {$elaborado}, Desechado: {$desechado}, Eficiencia: {$eficiencia}%");
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function historial(Request $request)
    {
        $producciones = Produccion::with('producto', 'usuario')->orderByDesc('created_at')->paginate(20);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('produccion_real._historial', compact('producciones'));
        }

        return view('produccion_real.historial', compact('producciones'));
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE NOTIFICACIONES
    // =========================================================================

    /**
     * Verificar stock bajo de materia prima
     */
    private function verificarStockBajoMP(MateriaPrima $materiaPrima): void
    {
        if ($materiaPrima->stock_actual <= $materiaPrima->stock_minimo && $materiaPrima->stock_minimo > 0) {
            NotificacionHelper::stockBajo(
                $materiaPrima,
                'materia prima',
                $materiaPrima->nombre,
                $materiaPrima->stock_actual,
                $materiaPrima->stock_minimo
            );
        }
    }

    /**
     * Verificar stock bajo de producto terminado
     */
    private function verificarStockBajoProducto(Producto $producto): void
    {
        if ($producto->stock_actual <= $producto->stock_minimo && $producto->stock_minimo > 0) {
            NotificacionHelper::stockBajo(
                $producto,
                'producto terminado',
                $producto->nombre,
                $producto->stock_actual,
                $producto->stock_minimo
            );
        }
    }
}