<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MateriaPrima;
use App\Models\Produccion;
use App\Models\Merma;
use App\Models\Envio;
use App\Models\MovimientoMateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrazabilidadController extends Controller
{
    public function index(Request $request)
    {
        $producto = null;
        $materiaPrima = null;
        $trazabilidad = collect();
        $tipo = $request->tipo ?? 'producto';

        if ($request->filled('buscar')) {
            if ($tipo === 'producto') {
                $producto = Producto::withTrashed()->find($request->buscar);
                if ($producto) {
                    $trazabilidad = $this->getTrazabilidadProducto($producto->id);
                }
            } else {
                $materiaPrima = MateriaPrima::withTrashed()->find($request->buscar);
                if ($materiaPrima) {
                    $trazabilidad = $this->getTrazabilidadMateriaPrima($materiaPrima->id);
                }
            }
        }

        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'sku']);
        $materiasPrimas = MateriaPrima::orderBy('nombre')->get(['id', 'nombre', 'sku']);

        return view('trazabilidad.index', compact('producto', 'materiaPrima', 'trazabilidad', 'productos', 'materiasPrimas', 'tipo'));
    }

    private function getTrazabilidadProducto($productoId)
    {
        $movimientos = collect();

        // 1. Producción (entradas)
        $producciones = Produccion::where('producto_id', $productoId)
            ->with('usuario')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'fecha' => $item->fecha_produccion,
                    'tipo' => 'entrada',
                    'concepto' => 'Producción',
                    'detalle' => "Lote: {$item->lote} - Cantidad producida: {$item->cantidad_producida} unidades",
                    'cantidad' => $item->cantidad_producida,
                    'stock_resultante' => null,
                    'usuario' => $item->usuario->name ?? 'N/A',
                    'referencia_id' => $item->id,
                    'referencia_tipo' => 'produccion'
                ];
            });

        // 2. Envíos (salidas)
        $envios = DB::table('envio_productos')
            ->join('envios', 'envio_productos.envio_id', '=', 'envios.id')
            ->join('users', 'envios.usuario_id', '=', 'users.id')
            ->where('envio_productos.productable_type', Producto::class)
            ->where('envio_productos.productable_id', $productoId)
            ->select(
                'envios.fecha_envio as fecha',
                'envio_productos.cantidad',
                'envios.numero_guia',
                'envios.destinatario_nombre',
                'users.name as usuario'
            )
            ->get()
            ->map(function ($item) {
                return (object)[
                    'fecha' => $item->fecha,
                    'tipo' => 'salida',
                    'concepto' => 'Envío',
                    'detalle' => "Guía: {$item->numero_guia} - Destinatario: {$item->destinatario_nombre}",
                    'cantidad' => $item->cantidad,
                    'stock_resultante' => null,
                    'usuario' => $item->usuario,
                    'referencia_id' => null,
                    'referencia_tipo' => 'envio'
                ];
            });

        // 3. Mermas (salidas)
        $mermas = Merma::where('producto_id', $productoId)
            ->with('usuario')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'fecha' => $item->fecha,
                    'tipo' => 'salida',
                    'concepto' => 'Merma',
                    'detalle' => "Tipo: {$item->tipo_merma} - Causa: {$item->causa}",
                    'cantidad' => $item->cantidad,
                    'stock_resultante' => null,
                    'usuario' => $item->usuario->name ?? 'N/A',
                    'referencia_id' => $item->id,
                    'referencia_tipo' => 'merma'
                ];
            });

        // Unir todos los movimientos
        $movimientos = $producciones->concat($envios)->concat($mermas);
        
        // Calcular stock resultante (ordenado por fecha)
        $movimientos = $movimientos->sortBy('fecha');
        $stockAcumulado = 0;
        
        foreach ($movimientos as $mov) {
            if ($mov->tipo === 'entrada') {
                $stockAcumulado += $mov->cantidad;
            } else {
                $stockAcumulado -= $mov->cantidad;
            }
            $mov->stock_resultante = max(0, $stockAcumulado);
        }

        return $movimientos->sortByDesc('fecha')->values();
    }

    private function getTrazabilidadMateriaPrima($materiaPrimaId)
    {
        $movimientos = MovimientoMateriaPrima::where('materia_prima_id', $materiaPrimaId)
            ->with('usuario')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($item) {
                $tipo = $item->tipo === 'entrada' ? 'entrada' : 'salida';
                $concepto = $this->getConceptoMovimiento($item->motivo);
                
                return (object)[
                    'fecha' => $item->created_at,
                    'tipo' => $tipo,
                    'concepto' => $concepto,
                    'detalle' => "Motivo: {$item->motivo} - " . ($item->observaciones ?? ''),
                    'cantidad' => $item->cantidad,
                    'stock_resultante' => null,
                    'usuario' => $item->usuario->name ?? 'N/A',
                    'referencia_id' => $item->id,
                    'referencia_tipo' => 'movimiento'
                ];
            });

        // Calcular stock resultante
        $stockAcumulado = 0;
        foreach ($movimientos as $mov) {
            if ($mov->tipo === 'entrada') {
                $stockAcumulado += $mov->cantidad;
            } else {
                $stockAcumulado -= $mov->cantidad;
            }
            $mov->stock_resultante = max(0, $stockAcumulado);
        }

        return $movimientos->sortByDesc('fecha')->values();
    }

    private function getConceptoMovimiento($motivo)
    {
        $conceptos = [
            'compra' => 'Compra',
            'compra_inicial' => 'Stock Inicial',
            'consumo_produccion' => 'Consumo en Producción',
            'ajuste' => 'Ajuste de Inventario',
            'merma_produccion' => 'Merma en Producción',
            'merma_empaquetado' => 'Merma en Empaquetado',
            'vencimiento' => 'Producto Vencido',
            'devolucion' => 'Devolución a Proveedor',
        ];

        return $conceptos[$motivo] ?? ucfirst($motivo);
    }

    public function buscar(Request $request)
    {
        $tipo = $request->tipo;
        $query = $request->q;

        if ($tipo === 'producto') {
            $resultados = Producto::where('nombre', 'ILIKE', "%{$query}%")
                ->orWhere('sku', 'ILIKE', "%{$query}%")
                ->limit(10)
                ->get(['id', 'nombre', 'sku']);
        } else {
            $resultados = MateriaPrima::where('nombre', 'ILIKE', "%{$query}%")
                ->orWhere('sku', 'ILIKE', "%{$query}%")
                ->limit(10)
                ->get(['id', 'nombre', 'sku']);
        }

        return response()->json($resultados);
    }
}