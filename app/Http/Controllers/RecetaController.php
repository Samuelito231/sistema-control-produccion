<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MateriaPrima;
use App\Models\Receta;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    public function index(Request $request, Producto $producto)
    {
        $recetas = $producto->recetas()->with('materiaPrima')->get();
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get();

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

        $producto->recetas()->updateOrCreate(
            ['materia_prima_id' => $request->materia_prima_id],
            ['cantidad_necesaria' => $request->cantidad_necesaria]
        );

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
}