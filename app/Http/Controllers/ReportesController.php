<?php

namespace App\Http\Controllers;

use App\Models\Merma;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReporteCompletoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $productoId = $request->get('producto_id');
        $causa = $request->get('causa');
        $tipoMerma = $request->get('tipo_merma');
        $usuarioId = $request->get('usuario_id');

        $productos = Producto::withTrashed()->orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $causasDisponibles = Merma::distinct('causa')->pluck('causa');
        $tiposDisponibles = Merma::distinct('tipo_merma')->pluck('tipo_merma');

        $mermasQuery = Merma::whereBetween('fecha', [$from, $to])
            ->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($causa, fn($q) => $q->where('causa', $causa))
            ->when($tipoMerma, fn($q) => $q->where('tipo_merma', $tipoMerma))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId));

        $movimientosMes = $mermasQuery->count();
        $totalMerma = $mermasQuery->sum('cantidad');
        $lotesCerrados = $mermasQuery->distinct('lote')->count('lote');

        $costoMerma = Merma::whereBetween('fecha', [$from, $to])
            ->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($causa, fn($q) => $q->where('causa', $causa))
            ->when($tipoMerma, fn($q) => $q->where('tipo_merma', $tipoMerma))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId))
            ->join('products', 'mermas.producto_id', '=', 'products.id')
            ->selectRaw('SUM(mermas.cantidad * COALESCE(products.precio_unitario, 0)) as total')
            ->first()->total ?? 0;

        $produccionEsperada = 50000;
        $eficiencia = $produccionEsperada > 0 ? round((1 - ($totalMerma / $produccionEsperada)) * 100, 2) : 98.5;
        $eficiencia = max(0, min(100, $eficiencia));

        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        $movimientosLastMonth = Merma::whereBetween('fecha', [$lastMonthStart, $lastMonthEnd])->count();
        $mermaLastMonth = Merma::whereBetween('fecha', [$lastMonthStart, $lastMonthEnd])->sum('cantidad');
        $costoLastMonth = Merma::whereBetween('fecha', [$lastMonthStart, $lastMonthEnd])
            ->join('products', 'mermas.producto_id', '=', 'products.id')
            ->sum(DB::raw('mermas.cantidad * products.precio_unitario'));
        $lotesLastMonth = Merma::whereBetween('fecha', [$lastMonthStart, $lastMonthEnd])->distinct('lote')->count('lote');

        $movimientosVariacion = $movimientosLastMonth ? round((($movimientosMes - $movimientosLastMonth) / $movimientosLastMonth) * 100, 1) : 0;
        $mermaVariacion = $mermaLastMonth ? round((($totalMerma - $mermaLastMonth) / $mermaLastMonth) * 100, 1) : 0;
        $costoVariacion = $costoLastMonth ? round((($costoMerma - $costoLastMonth) / $costoLastMonth) * 100, 1) : 0;
        $lotesVariacion = $lotesLastMonth ? round((($lotesCerrados - $lotesLastMonth) / $lotesLastMonth) * 100, 1) : 0;

        $topProductos = Merma::whereBetween('fecha', [$from, $to])
            ->when($causa, fn($q) => $q->where('causa', $causa))
            ->when($tipoMerma, fn($q) => $q->where('tipo_merma', $tipoMerma))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId))
            ->select('producto_id', DB::raw('SUM(cantidad) as total_merma'))
            ->groupBy('producto_id')
            ->orderByDesc('total_merma')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $producto = Producto::withTrashed()->find($item->producto_id);
                $nombre = 'Producto eliminado';
                if ($producto) {
                    $nombre = $producto->nombre;
                    if ($producto->trashed()) $nombre .= ' (eliminado)';
                }
                return (object) [
                    'nombre' => $nombre,
                    'total' => $item->total_merma,
                    'unidad' => $producto->unidad ?? 'kg'
                ];
            });

        $topCausas = Merma::whereBetween('fecha', [$from, $to])
            ->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($tipoMerma, fn($q) => $q->where('tipo_merma', $tipoMerma))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId))
            ->select('causa', DB::raw('SUM(cantidad) as total'))
            ->groupBy('causa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $mermaPorTipo = Merma::whereBetween('fecha', [$from, $to])
            ->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($causa, fn($q) => $q->where('causa', $causa))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId))
            ->select('tipo_merma', DB::raw('SUM(cantidad) as total'))
            ->groupBy('tipo_merma')
            ->get()
            ->keyBy('tipo_merma');

        $mermaProduccion = $mermaPorTipo->get('produccion', (object)['total' => 0])->total;
        $mermaEmpaquetado = $mermaPorTipo->get('empaquetado', (object)['total' => 0])->total;

        $mermaPorDia = Merma::whereBetween('fecha', [now()->subDays(30), now()])
            ->select(DB::raw('fecha'), DB::raw('SUM(cantidad) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $mermasRecientes = Merma::whereBetween('fecha', [$from, $to])
            ->with(['usuario', 'producto' => fn($q) => $q->withTrashed()])
            ->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($causa, fn($q) => $q->where('causa', $causa))
            ->when($tipoMerma, fn($q) => $q->where('tipo_merma', $tipoMerma))
            ->when($usuarioId, fn($q) => $q->where('usuario_id', $usuarioId))
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $mermasRecientes->getCollection()->transform(function ($merma) {
            $nombreProducto = 'Producto eliminado';
            $estado = '';
            if ($merma->producto) {
                $nombreProducto = $merma->producto->nombre;
                if ($merma->producto->trashed()) $estado = ' (eliminado)';
            }
            $merma->nombre_estado = $nombreProducto . $estado;
            return $merma;
        });

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('reportes._content', compact(
                'from', 'to',
                'movimientosMes', 'totalMerma', 'costoMerma', 'lotesCerrados', 'eficiencia',
                'movimientosVariacion', 'mermaVariacion', 'costoVariacion', 'lotesVariacion',
                'topProductos', 'topCausas',
                'mermaProduccion', 'mermaEmpaquetado',
                'mermaPorDia',
                'mermasRecientes',
                'productos', 'usuarios', 'causasDisponibles', 'tiposDisponibles'
            ));
        }

        return view('reportes.index', compact(
            'from', 'to',
            'movimientosMes', 'totalMerma', 'costoMerma', 'lotesCerrados', 'eficiencia',
            'movimientosVariacion', 'mermaVariacion', 'costoVariacion', 'lotesVariacion',
            'topProductos', 'topCausas',
            'mermaProduccion', 'mermaEmpaquetado',
            'mermaPorDia',
            'mermasRecientes',
            'productos', 'usuarios', 'causasDisponibles', 'tiposDisponibles'
        ));
    }

    public function exportPdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $mermas = Merma::whereBetween('fecha', [$from, $to])
            ->with(['producto' => fn($q) => $q->withTrashed(), 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $totalMermaPeriodo = $mermas->sum('cantidad');
        $costoMermaPeriodo = Merma::whereBetween('fecha', [$from, $to])
            ->join('products', 'mermas.producto_id', '=', 'products.id')
            ->sum(DB::raw('mermas.cantidad * products.precio_unitario'));
        $lotesCerradosPeriodo = $mermas->unique('lote')->count();

        $produccionEsperada = 50000;
        $eficienciaPeriodo = $produccionEsperada > 0 ? round((1 - ($totalMermaPeriodo / $produccionEsperada)) * 100, 2) : 98.5;
        $eficienciaPeriodo = max(0, min(100, $eficienciaPeriodo));

        $pdf = Pdf::loadView('reportes.pdf_reporte', compact(
            'from', 'to', 'mermas', 'totalMermaPeriodo', 'costoMermaPeriodo',
            'lotesCerradosPeriodo', 'eficienciaPeriodo'
        ));
        return $pdf->download('reporte_mermas_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $mermas = Merma::whereBetween('fecha', [$from, $to])
            ->with(['producto' => fn($q) => $q->withTrashed(), 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $fileName = 'reporte_mermas_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, ['Fecha', 'Producto', 'Cantidad', 'Unidad', 'Causa', 'Tipo', 'Lote', 'Registrado por']);

        foreach ($mermas as $m) {
            fputcsv($handle, [
                $m->fecha,
                $m->producto->nombre ?? 'Producto eliminado',
                $m->cantidad,
                $m->unidad,
                $m->causa,
                ucfirst($m->tipo_merma),
                $m->lote ?? '-',
                $m->usuario->name ?? 'N/A',
            ]);
        }
        fclose($handle);
        exit;
    }

    public function exportExcel(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        return Excel::download(new ReporteCompletoExport($from, $to), 'reporte_khaleesitas_' . now()->format('Ymd_His') . '.xlsx');
    }
}