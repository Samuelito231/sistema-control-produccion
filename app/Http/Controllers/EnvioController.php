<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\EnvioProducto;
use App\Models\Producto;
use App\Helpers\AuditHelper;
use App\Events\StockBajoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnvioController extends Controller
{
    public function index(Request $request)
    {
        $query = Envio::with(['usuario', 'productos']);

        if ($request->filled('estado')) {
            $query->where('estado_envio', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_guia', 'LIKE', "%{$search}%")
                  ->orWhere('destinatario_nombre', 'LIKE', "%{$search}%");
            });
        }

        $envios = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('envios.index', compact('envios'));
    }

    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('envios.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_envio' => 'required|date',
            'fecha_estimada_entrega' => 'nullable|date|after_or_equal:fecha_envio',
            'destinatario_nombre' => 'required|string|max:255',
            'destinatario_telefono' => 'nullable|string|max:20',
            'destinatario_email' => 'nullable|email|max:255',
            'direccion' => 'required|string',
            'ciudad' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'estado_region' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'transportista' => 'required|string|max:100',
            'numero_guia_transportista' => 'nullable|string|max:100',
            'costo_envio' => 'nullable|numeric|min:0',
            'costo_pagado_por' => 'required|in:empresa,cliente',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // Verificar stock
            foreach ($validated['productos'] as $item) {
                $producto = Producto::find($item['id']);
                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente de {$producto->nombre}. Disponible: {$producto->stock_actual}");
                }
            }

            // Crear envío
            $envio = Envio::create([
                'numero_guia' => Envio::generarNumeroGuia(),
                'fecha_envio' => $validated['fecha_envio'],
                'fecha_estimada_entrega' => $validated['fecha_estimada_entrega'],
                'estado_envio' => 'pendiente',
                'destinatario_nombre' => $validated['destinatario_nombre'],
                'destinatario_telefono' => $validated['destinatario_telefono'],
                'destinatario_email' => $validated['destinatario_email'],
                'direccion' => $validated['direccion'],
                'ciudad' => $validated['ciudad'],
                'municipio' => $validated['municipio'],
                'estado_region' => $validated['estado_region'],
                'codigo_postal' => $validated['codigo_postal'],
                'transportista' => $validated['transportista'],
                'numero_guia_transportista' => $validated['numero_guia_transportista'],
                'costo_envio' => $validated['costo_envio'] ?? 0,
                'costo_pagado_por' => $validated['costo_pagado_por'],
                'observaciones' => $validated['observaciones'],
                'usuario_id' => Auth::id(),
            ]);

            // Agregar productos y actualizar stock
            foreach ($validated['productos'] as $item) {
                $producto = Producto::find($item['id']);
                
                EnvioProducto::create([
                    'envio_id' => $envio->id,
                    'productable_type' => Producto::class,
                    'productable_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'unidad' => $producto->unidad,
                    'precio_unitario_momento' => $producto->precio_unitario,
                    'subtotal' => $producto->precio_unitario * $item['cantidad'],
                ]);

                // Actualizar stock
                $producto->decrement('stock_actual', $item['cantidad']);

                // Verificar stock bajo
                if ($producto->stock_actual <= $producto->stock_minimo && $producto->stock_minimo > 0) {
                    event(new StockBajoEvent(
                        $producto,
                        'producto',
                        $producto->nombre,
                        $producto->stock_actual,
                        $producto->stock_minimo
                    ));
                }
            }

            AuditHelper::log('envio_creado', $envio, null, $envio->toArray());

            DB::commit();

            return redirect()->route('envios.show', $envio)
                ->with('success', "Envío #{$envio->numero_guia} creado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Envio $envio)
    {
        $envio->load(['productos.productable', 'usuario', 'autorizador']);
        return view('envios.show', compact('envio'));
    }

    public function updateEstado(Request $request, Envio $envio)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_transito,entregado,cancelado',
            'fecha_real_entrega' => 'required_if:estado,entregado|nullable|date',
        ]);

        $oldEstado = $envio->estado_envio;
        $envio->update([
            'estado_envio' => $request->estado,
            'fecha_real_entrega' => $request->fecha_real_entrega,
        ]);

        AuditHelper::log('envio_estado_actualizado', $envio, ['estado' => $oldEstado], ['estado' => $request->estado]);

        return redirect()->route('envios.show', $envio)
            ->with('success', 'Estado del envío actualizado.');
    }

    public function historial(Request $request)
    {
        $envios = Envio::with(['usuario', 'productos'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $stats = [
            'total' => Envio::count(),
            'pendientes' => Envio::where('estado_envio', 'pendiente')->count(),
            'en_transito' => Envio::where('estado_envio', 'en_transito')->count(),
            'entregados' => Envio::where('estado_envio', 'entregado')->count(),
            'cancelados' => Envio::where('estado_envio', 'cancelado')->count(),
        ];

        return view('envios.historial', compact('envios', 'stats'));
    }
}