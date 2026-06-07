<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use App\Models\MovimientoMateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MateriaPrimaController extends Controller
{
    /**
     * Muestra el listado de Materia Prima.
     * Soporta carga completa o parcial (AJAX).
     */
    public function index(Request $request)
    {
        $materias = MateriaPrima::orderBy('nombre')->paginate(20);
        $stockTotal = MateriaPrima::sum('stock_actual');
        $valorTotal = MateriaPrima::sum(DB::raw('stock_actual * costo_unitario'));
        
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('materia_prima._content', compact('materias', 'stockTotal', 'valorTotal'));
        }
        
        return view('materia_prima.index', compact('materias', 'stockTotal', 'valorTotal'));
    }

    public function create()
    {
        return view('materia_prima.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|unique:materia_prima,sku,NULL,id,deleted_at,NULL',
            'unidad' => 'required|string',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
            'costo_unitario' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
            'lote_compra' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $mp = MateriaPrima::create($request->all());

        if ($request->stock_actual > 0) {
            $observaciones = 'Stock inicial al crear el registro';
            if ($request->lote_compra) {
                $observaciones .= ' - Lote: ' . $request->lote_compra;
            }
            $mp->registrarEntrada($request->stock_actual, 'compra_inicial', $observaciones);
        }

        return redirect()->route('materia-prima.index')->with('success', 'Materia prima creada correctamente.');
    }

    public function edit(MateriaPrima $materia_prima)
    {
        return view('materia_prima.edit', compact('materia_prima'));
    }

    public function update(Request $request, MateriaPrima $materia_prima)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|unique:materia_prima,sku,' . $materia_prima->id . ',id,deleted_at,NULL',
            'unidad' => 'required|string',
            'stock_minimo' => 'nullable|numeric|min:0',
            'costo_unitario' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
            'lote_compra' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $materia_prima->update($request->only([
            'nombre', 'sku', 'unidad', 'stock_minimo', 'costo_unitario', 
            'proveedor', 'lote_compra', 'fecha_vencimiento'
        ]));

        return redirect()->route('materia-prima.index')->with('success', 'Materia prima actualizada.');
    }

    public function destroy(MateriaPrima $materia_prima)
    {
        $materia_prima->delete();
        return redirect()->route('materia-prima.index')->with('success', 'Materia prima eliminada.');
    }

    public function movimientos(MateriaPrima $materia_prima)
    {
        $movimientos = $materia_prima->movimientos()->with('usuario')->orderByDesc('created_at')->paginate(30);
        return view('materia_prima.movimientos', compact('materia_prima', 'movimientos'));
    }

    public function registrarEntrada(Request $request, MateriaPrima $materia_prima)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.01',
            'costo_unitario' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'lote_compra' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $costo = $request->costo_unitario ?? $materia_prima->costo_unitario;
        
        if ($request->filled('costo_unitario')) {
            $materia_prima->costo_unitario = $request->costo_unitario;
            $materia_prima->save();
        }

        // Actualizar lote y fecha de vencimiento si se proporcionan
        if ($request->filled('lote_compra')) {
            $materia_prima->lote_compra = $request->lote_compra;
            $materia_prima->save();
        }
        
        if ($request->filled('fecha_vencimiento')) {
            $materia_prima->fecha_vencimiento = $request->fecha_vencimiento;
            $materia_prima->save();
        }

        $observaciones = $request->observaciones;
        if ($request->lote_compra) {
            $observaciones = ($observaciones ? $observaciones . ' - ' : '') . 'Lote: ' . $request->lote_compra;
        }

        $materia_prima->registrarEntrada($request->cantidad, 'compra', $observaciones, $costo);

        return redirect()->route('materia-prima.movimientos', $materia_prima)->with('success', 'Compra registrada correctamente.');
    }

    public function registrarSalida(Request $request, MateriaPrima $materia_prima)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|in:ajuste,merma_produccion,consumo_produccion',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $materia_prima->registrarSalida($request->cantidad, $request->motivo, $request->observaciones);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Stock insuficiente. Stock actual: ' . $materia_prima->stock_actual]);
        }

        return redirect()->route('materia-prima.movimientos', $materia_prima)->with('success', 'Salida registrada correctamente.');
    }

    /**
     * Verificar materias primas próximas a vencer (opcional)
     */
    public function porVencer()
    {
        $proximasAVencer = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->where('fecha_vencimiento', '>=', Carbon::now())
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('materia_prima.por_vencer', compact('proximasAVencer'));
    }
}