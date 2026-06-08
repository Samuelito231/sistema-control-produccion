<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use App\Models\MovimientoMateriaPrima;
use App\Helpers\AuditHelper;
use App\Events\StockBajoEvent;
use App\Events\ProductoVencidoEvent;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MateriaPrimaController extends Controller
{
    // =========================================================================
    // CONSTANTES DE DOMINIO
    // =========================================================================

    /**
     * Motivos válidos para una salida manual.
     * "consumo_produccion" NO está aquí: ese movimiento lo genera
     * ProduccionRealController automáticamente, nunca el operario a mano.
     */
    private const MOTIVOS_SALIDA = [
        'ajuste'           => 'Ajuste de inventario',
        'merma_produccion' => 'Merma en producción',
        'merma_empaquetado'=> 'Merma en empaquetado',
        'vencimiento'      => 'Producto vencido / caducado',
        'devolucion'       => 'Devolución a proveedor',
    ];

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request): View
    {
        $query = MateriaPrima::query();

        // Búsqueda por nombre o SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('sku',    'ILIKE', "%{$search}%");
            });
        }

        // Filtro: solo los que están bajo stock mínimo
        if ($request->boolean('bajo_minimo')) {
            $query->whereColumn('stock_actual', '<', 'stock_minimo');
        }

        $materias    = $query->orderBy('nombre')->paginate(20)->withQueryString();
        $stockTotal  = MateriaPrima::sum('stock_actual');
        $valorTotal  = MateriaPrima::whereNotNull('costo_unitario')
                           ->sum(DB::raw('stock_actual * costo_unitario'));
        $bajosMinimo = MateriaPrima::whereColumn('stock_actual', '<', 'stock_minimo')->count();

        // Verificar productos vencidos para notificar
        $this->verificarProductosVencidos();

        if ($request->ajax()) {
            return view('materia_prima._content',
                compact('materias', 'stockTotal', 'valorTotal', 'bajosMinimo'));
        }

        return view('materia_prima.index',
            compact('materias', 'stockTotal', 'valorTotal', 'bajosMinimo'));
    }

    // =========================================================================
    // CREATE / STORE
    // =========================================================================

    public function create(): View
    {
        return view('materia_prima.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:255',
            'sku'              => [
                'required', 'string', 'max:100',
                Rule::unique('materia_prima', 'sku')->whereNull('deleted_at'),
            ],
            'unidad'           => 'required|string|max:20',
            'stock_actual'     => 'required|numeric|min:0',
            'stock_minimo'     => 'nullable|numeric|min:0',
            'costo_unitario'   => 'nullable|numeric|min:0',
            'proveedor'        => 'nullable|string|max:255',
            'lote_compra'      => 'nullable|string|max:100',
            'fecha_vencimiento'=> 'nullable|date|after_or_equal:today',
        ]);

        DB::transaction(function () use ($validated) {
            $stockInicial = (float) $validated['stock_actual'];

            $mp = MateriaPrima::create([
                'nombre'           => $validated['nombre'],
                'sku'              => strtoupper(trim($validated['sku'])),
                'unidad'           => $validated['unidad'],
                'stock_actual'     => 0,
                'stock_minimo'     => $validated['stock_minimo'] ?? 0,
                'costo_unitario'   => $validated['costo_unitario'] ?? null,
                'proveedor'        => $validated['proveedor'] ?? null,
                'lote_compra'      => $validated['lote_compra'] ?? null,
                'fecha_vencimiento'=> $validated['fecha_vencimiento'] ?? null,
            ]);

            if ($stockInicial > 0) {
                $obs = 'Stock inicial al crear el registro';
                if (!empty($validated['lote_compra'])) {
                    $obs .= ' — Lote: ' . $validated['lote_compra'];
                }

                $mp->registrarEntrada(
                    $stockInicial,
                    'compra_inicial',
                    $obs,
                    $validated['costo_unitario'] ?? null
                );
            }

            AuditHelper::log('create_materia_prima', $mp, null, $mp->toArray());
        });

        return redirect()
            ->route('materia-prima.index')
            ->with('success', 'Materia prima creada correctamente.');
    }

    // =========================================================================
    // EDIT / UPDATE
    // =========================================================================

    public function edit(MateriaPrima $materia_prima): View
    {
        return view('materia_prima.edit', compact('materia_prima'));
    }

    public function update(Request $request, MateriaPrima $materia_prima): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:255',
            'sku'              => [
                'required', 'string', 'max:100',
                Rule::unique('materia_prima', 'sku')
                    ->ignore($materia_prima->id)
                    ->whereNull('deleted_at'),
            ],
            'unidad'           => 'required|string|max:20',
            'stock_minimo'     => 'nullable|numeric|min:0',
            'costo_unitario'   => 'nullable|numeric|min:0',
            'proveedor'        => 'nullable|string|max:255',
            'lote_compra'      => 'nullable|string|max:100',
            'fecha_vencimiento'=> 'nullable|date',
        ]);

        $oldValues = $materia_prima->toArray();

        DB::transaction(function () use ($materia_prima, $validated, $oldValues) {
            $materia_prima->update([
                'nombre'           => $validated['nombre'],
                'sku'              => strtoupper(trim($validated['sku'])),
                'unidad'           => $validated['unidad'],
                'stock_minimo'     => $validated['stock_minimo'] ?? $materia_prima->stock_minimo,
                'costo_unitario'   => $validated['costo_unitario'] ?? $materia_prima->costo_unitario,
                'proveedor'        => $validated['proveedor'] ?? null,
                'lote_compra'      => $validated['lote_compra'] ?? null,
                'fecha_vencimiento'=> $validated['fecha_vencimiento'] ?? null,
            ]);

            AuditHelper::log('update_materia_prima', $materia_prima, $oldValues, $materia_prima->fresh()->toArray());
        });

        return redirect()
            ->route('materia-prima.index')
            ->with('success', 'Materia prima actualizada correctamente.');
    }

    // =========================================================================
    // DESTROY (soft delete)
    // =========================================================================

    public function destroy(MateriaPrima $materia_prima): RedirectResponse
    {
        if ($materia_prima->stock_actual > 0) {
            return redirect()
                ->route('materia-prima.index')
                ->with('error', "No se puede eliminar \"{$materia_prima->nombre}\" porque tiene stock disponible ({$materia_prima->stock_actual} {$materia_prima->unidad}). Primero ajuste el stock a cero.");
        }

        DB::transaction(function () use ($materia_prima) {
            $oldValues = $materia_prima->toArray();
            $materia_prima->delete();
            AuditHelper::log('delete_materia_prima', $materia_prima, $oldValues, null);
        });

        return redirect()
            ->route('materia-prima.index')
            ->with('success', "Materia prima \"{$materia_prima->nombre}\" eliminada.");
    }

    // =========================================================================
    // MOVIMIENTOS (historial)
    // =========================================================================

    public function movimientos(Request $request, MateriaPrima $materia_prima): View
    {
        $movimientos = $materia_prima
            ->movimientos()
            ->with('usuario')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $totalEntradas = $materia_prima->movimientos()->where('tipo', 'entrada')->sum('cantidad');
        $totalSalidas  = $materia_prima->movimientos()->where('tipo', 'salida')->sum('cantidad');

        return view('materia_prima.movimientos',
            compact('materia_prima', 'movimientos', 'totalEntradas', 'totalSalidas'));
    }

    // =========================================================================
    // ENTRADA (compra / recepción)
    // =========================================================================

    public function entradaForm(MateriaPrima $materia_prima): View
    {
        return view('materia_prima.entrada', compact('materia_prima'));
    }

    public function registrarEntrada(Request $request, MateriaPrima $materia_prima): RedirectResponse
    {
        $validated = $request->validate([
            'cantidad'          => 'required|numeric|min:0.0001',
            'costo_unitario'    => 'nullable|numeric|min:0',
            'lote_compra'       => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'observaciones'     => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($materia_prima, $validated) {
                $costo = $validated['costo_unitario'] ?? $materia_prima->costo_unitario;

                $camposActualizar = [];

                if (!empty($validated['costo_unitario'])) {
                    $camposActualizar['costo_unitario'] = $validated['costo_unitario'];
                }
                if (!empty($validated['lote_compra'])) {
                    $camposActualizar['lote_compra'] = $validated['lote_compra'];
                }
                if (!empty($validated['fecha_vencimiento'])) {
                    $camposActualizar['fecha_vencimiento'] = $validated['fecha_vencimiento'];
                }

                if (!empty($camposActualizar)) {
                    $materia_prima->update($camposActualizar);
                }

                $obs = $validated['observaciones'] ?? '';
                if (!empty($validated['lote_compra'])) {
                    $obs = ($obs ? $obs . ' — ' : '') . 'Lote: ' . $validated['lote_compra'];
                }

                $materia_prima->registrarEntrada(
                    (float) $validated['cantidad'],
                    'compra',
                    $obs ?: null,
                    $costo
                );

                // Verificar stock bajo después de la entrada (usando EVENTO)
                $this->verificarStockBajo($materia_prima);

                AuditHelper::log('entrada_materia_prima', $materia_prima, null, [
                    'cantidad'       => $validated['cantidad'],
                    'costo'          => $costo,
                    'lote'           => $validated['lote_compra'] ?? null,
                    'stock_tras_mov' => $materia_prima->fresh()->stock_actual,
                ]);
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al registrar la entrada: ' . $e->getMessage());
        }

        return redirect()
            ->route('materia-prima.movimientos', $materia_prima)
            ->with('success', 'Compra registrada correctamente. Stock actualizado.');
    }

    // =========================================================================
    // SALIDA (ajuste, merma, vencimiento, etc.)
    // =========================================================================

    public function salidaForm(MateriaPrima $materia_prima): View
    {
        $motivos = self::MOTIVOS_SALIDA;
        return view('materia_prima.salida', compact('materia_prima', 'motivos'));
    }

    public function registrarSalida(Request $request, MateriaPrima $materia_prima): RedirectResponse
    {
        $validated = $request->validate([
            'cantidad'     => [
                'required',
                'numeric',
                'min:0.0001',
                function ($attribute, $value, $fail) use ($materia_prima) {
                    if ((float) $value > (float) $materia_prima->stock_actual) {
                        $fail("La cantidad ({$value}) supera el stock disponible ({$materia_prima->stock_actual} {$materia_prima->unidad}).");
                    }
                },
            ],
            'motivo'       => ['required', 'string', Rule::in(array_keys(self::MOTIVOS_SALIDA))],
            'observaciones'=> 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($materia_prima, $validated) {
                $materia_prima->registrarSalida(
                    (float) $validated['cantidad'],
                    $validated['motivo'],
                    $validated['observaciones'] ?? null
                );

                // Verificar stock bajo después de la salida (usando EVENTO)
                $this->verificarStockBajo($materia_prima);

                AuditHelper::log('salida_materia_prima', $materia_prima, null, [
                    'cantidad'       => $validated['cantidad'],
                    'motivo'         => $validated['motivo'],
                    'stock_tras_mov' => $materia_prima->fresh()->stock_actual,
                ]);
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('materia-prima.movimientos', $materia_prima)
            ->with('success', 'Salida registrada correctamente. Stock actualizado.');
    }

    // =========================================================================
    // POR VENCER (alertas de caducidad)
    // =========================================================================

    public function porVencer(): View
    {
        $hoy    = Carbon::today();
        $limite = $hoy->copy()->addDays(30);

        $vencidas = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', $hoy)
            ->where('stock_actual', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->get();

        $proximasAVencer = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoy, $limite])
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('materia_prima.por_vencer',
            compact('vencidas', 'proximasAVencer'));
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE NOTIFICACIONES CON EVENTOS
    // =========================================================================

    /**
     * Verificar stock bajo y disparar evento
     */
    private function verificarStockBajo(MateriaPrima $materia_prima): void
    {
        if ($materia_prima->stock_actual <= $materia_prima->stock_minimo && $materia_prima->stock_minimo > 0) {
            event(new StockBajoEvent(
                $materia_prima,
                'materia prima',
                $materia_prima->nombre,
                $materia_prima->stock_actual,
                $materia_prima->stock_minimo
            ));
        }
    }

    /**
     * Verificar productos vencidos y disparar eventos
     */
    private function verificarProductosVencidos(): void
    {
        $hoy = Carbon::today();
        
        $vencidos = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', $hoy)
            ->where('stock_actual', '>', 0)
            ->get();

        foreach ($vencidos as $producto) {
            event(new ProductoVencidoEvent($producto));
        }
    }
}