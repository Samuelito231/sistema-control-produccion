<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use App\Models\MovimientoMateriaPrima;
use App\Helpers\AuditHelper;
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
                // Ignora soft-deleted al verificar unicidad
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
            // Solo los campos del modelo base (sin stock_actual, que se gestiona
            // vía movimiento para mantener trazabilidad)
            $stockInicial = (float) $validated['stock_actual'];

            $mp = MateriaPrima::create([
                'nombre'           => $validated['nombre'],
                'sku'              => strtoupper(trim($validated['sku'])),
                'unidad'           => $validated['unidad'],
                'stock_actual'     => 0, // parte en 0, el movimiento lo sube
                'stock_minimo'     => $validated['stock_minimo'] ?? 0,
                'costo_unitario'   => $validated['costo_unitario'] ?? null,
                'proveedor'        => $validated['proveedor'] ?? null,
                'lote_compra'      => $validated['lote_compra'] ?? null,
                'fecha_vencimiento'=> $validated['fecha_vencimiento'] ?? null,
            ]);

            // Si hay stock inicial, lo registramos como movimiento de entrada
            // para que quede trazabilidad desde el primer día
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
        // NOTA: stock_actual NO está en el formulario de edición.
        // El stock se mueve SOLO mediante entradas/salidas.

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
        // Bloqueamos el borrado si tiene stock positivo para evitar pérdida de datos
        if ($materia_prima->stock_actual > 0) {
            return redirect()
                ->route('materia-prima.index')
                ->with('error', "No se puede eliminar \"{$materia_prima->nombre}\" porque tiene stock disponible ({$materia_prima->stock_actual} {$materia_prima->unidad}). Primero ajuste el stock a cero.");
        }

        DB::transaction(function () use ($materia_prima) {
            $oldValues = $materia_prima->toArray();
            $materia_prima->delete(); // soft delete
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

        // Totales del período visible
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

                // Actualizamos los metadatos de la materia prima si vienen nuevos datos
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

                // Construir observaciones enriquecidas
                $obs = $validated['observaciones'] ?? '';
                if (!empty($validated['lote_compra'])) {
                    $obs = ($obs ? $obs . ' — ' : '') . 'Lote: ' . $validated['lote_compra'];
                }

                // Registrar la entrada (actualiza stock + crea movimiento)
                $materia_prima->registrarEntrada(
                    (float) $validated['cantidad'],
                    'compra',
                    $obs ?: null,
                    $costo
                );

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
                // No puede superar el stock actual
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
                // registrarSalida() en el modelo ya valida stock y lanza excepción
                // si hay race condition entre la validación de arriba y el descuento
                $materia_prima->registrarSalida(
                    (float) $validated['cantidad'],
                    $validated['motivo'],
                    $validated['observaciones'] ?? null
                );

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

        // Vencidas
        $vencidas = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', $hoy)
            ->where('stock_actual', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->get();

        // Próximas a vencer (dentro de 30 días)
        $proximasAVencer = MateriaPrima::whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoy, $limite])
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('materia_prima.por_vencer',
            compact('vencidas', 'proximasAVencer'));
    }
}