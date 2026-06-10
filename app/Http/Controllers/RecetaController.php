<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MateriaPrima;
use App\Models\Receta;
use App\Helpers\NotificacionHelper;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    // =========================================================================
    // RECETAS POR PRODUCTO (vista específica)
    // =========================================================================

    public function index(Request $request, Producto $producto)
    {
        $recetas = $producto->recetas()->with('materiaPrima')->get();
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get();

        // Verificar materias primas con stock bajo en las recetas
        $this->verificarStockBajoEnRecetas($recetas);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('recetas._content', compact('producto', 'recetas', 'materiasPrimas'));
        }

        return view('recetas.index', compact('producto', 'recetas', 'materiasPrimas'));
    }

    public function store(Request $request, Producto $producto)
    {
        $request->validate([
            'materia_prima_id' => 'required|exists:materia_prima,id',
            'cantidad_necesaria' => 'required|numeric|min:0.001',
        ]);

        $materiaPrima = MateriaPrima::find($request->materia_prima_id);
        
        $producto->recetas()->updateOrCreate(
            ['materia_prima_id' => $request->materia_prima_id],
            ['cantidad_necesaria' => $request->cantidad_necesaria]
        );

        // Verificar stock bajo de la materia prima agregada
        $this->verificarStockBajoMP($materiaPrima);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Receta actualizada.']);
        }

        return redirect()->route('recetas.index', $producto)->with('success', 'Receta actualizada.');
    }

    public function destroy(Producto $producto, $materia_prima_id, Request $request)
    {
        $producto->recetas()->where('materia_prima_id', $materia_prima_id)->delete();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Ingrediente eliminado.']);
        }

        return redirect()->route('recetas.index', $producto)->with('success', 'Ingrediente eliminado de la receta.');
    }

    // =========================================================================
    // LISTADO GENERAL DE RECETAS (todas las recetas del sistema)
    // =========================================================================

    /**
     * Mostrar todas las recetas del sistema
     */
    public function todas(Request $request)
    {
        $recetas = Receta::with(['producto', 'materiaPrima'])->get();
        $productos = Producto::orderBy('nombre')->get();
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get();

        // Verificar stock bajo en todas las recetas
        $this->verificarStockBajoEnRecetas($recetas);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('recetas._content_todas', compact('recetas', 'productos', 'materiasPrimas'));
        }

        return view('recetas.todas', compact('recetas', 'productos', 'materiasPrimas'));
    }

    /**
     * Crear una receta desde el listado general
     */
    public function storeGeneral(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'materia_prima_id' => 'required|exists:materia_prima,id',
            'cantidad_necesaria' => 'required|numeric|min:0.001',
        ]);

        $materiaPrima = MateriaPrima::find($request->materia_prima_id);
        
        Receta::updateOrCreate(
            [
                'producto_id' => $request->producto_id,
                'materia_prima_id' => $request->materia_prima_id
            ],
            ['cantidad_necesaria' => $request->cantidad_necesaria]
        );

        // Verificar stock bajo de la materia prima agregada
        $this->verificarStockBajoMP($materiaPrima);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Receta agregada correctamente.']);
        }

        return redirect()->route('recetas.todas')->with('success', 'Receta agregada correctamente.');
    }

    /**
     * Eliminar una receta desde el listado general
     */
    public function destroyGeneral($producto_id, $materia_prima_id, Request $request)
    {
        Receta::where('producto_id', $producto_id)
            ->where('materia_prima_id', $materia_prima_id)
            ->delete();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Receta eliminada.']);
        }

        return redirect()->route('recetas.todas')->with('success', 'Receta eliminada correctamente.');
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE NOTIFICACIONES
    // =========================================================================

    /**
     * Verificar stock bajo en todas las materias primas de las recetas
     */
    private function verificarStockBajoEnRecetas($recetas): void
    {
        foreach ($recetas as $receta) {
            $materiaPrima = $receta->materiaPrima;
            if ($materiaPrima && $materiaPrima->stock_actual <= $materiaPrima->stock_minimo && $materiaPrima->stock_minimo > 0) {
                NotificacionHelper::stockBajo(
                    $materiaPrima,
                    'materia prima (usada en receta)',
                    $materiaPrima->nombre,
                    $materiaPrima->stock_actual,
                    $materiaPrima->stock_minimo
                );
            }
        }
    }

    /**
     * Verificar stock bajo de una materia prima específica
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
}