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
    // ─── Query base reutilizable ───────────────────────────────────────────────
    private function mermasQuery(string $from, string $to, Request $request)
    {
        return Merma::whereBetween('fecha', [$from, $to])
            ->when($request->producto_id, fn($q) => $q->where('producto_id', $request->producto_id))
            ->when($request->causa,       fn($q) => $q->where('causa', $request->causa))
            ->when($request->tipo_merma,  fn($q) => $q->where('tipo_merma', $request->tipo_merma))
            ->when($request->usuario_id,  fn($q) => $q->where('usuario_id', $request->usuario_id));
    }

    // ─── Estadísticas del período ──────────────────────────────────────────────
    private function getEstadisticasPeriodo(string $from, string $to, Request $request): array
    {
        $base = $this->mermasQuery($from, $to, $request);

        $totalMerma      = (clone $base)->sum('cantidad');
        $movimientosMes  = (clone $base)->count();
        $lotesCerrados   = (clone $base)->distinct('lote')->count('lote');
        $costoMerma      = (clone $base)
            ->join('productos', 'mermas.producto_id', '=', 'productos.id')
            ->selectRaw('SUM(mermas.cantidad * COALESCE(productos.precio_unitario, 0)) as total')
            ->first()->total ?? 0;

        $produccionEsperada = 50000;
        $eficiencia = $produccionEsperada > 0
            ? max(0, min(100, round((1 - ($totalMerma / $produccionEsperada)) * 100, 2)))
            : 98.5;

        return compact('totalMerma', 'movimientosMes', 'lotesCerrados', 'costoMerma', 'eficiencia');
    }

    // ─── Variaciones respecto al mes anterior ─────────────────────────────────
    private function getVariaciones(array $actual): array
    {
        $lastStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastEnd   = now()->subMonth()->endOfMonth()->toDateString();

        $movimientosLastMonth = Merma::whereBetween('fecha', [$lastStart, $lastEnd])->count();
        $mermaLastMonth       = Merma::whereBetween('fecha', [$lastStart, $lastEnd])->sum('cantidad');
        $lotesLastMonth       = Merma::whereBetween('fecha', [$lastStart, $lastEnd])->distinct('lote')->count('lote');
        $costoLastMonth       = Merma::whereBetween('fecha', [$lastStart, $lastEnd])
            ->join('productos', 'mermas.producto_id', '=', 'productos.id')
            ->sum(DB::raw('mermas.cantidad * productos.precio_unitario'));

        $variacion = fn($actual, $anterior) => $anterior
            ? round((($actual - $anterior) / $anterior) * 100, 1)
            : 0;

        return [
            'movimientosVariacion' => $variacion($actual['movimientosMes'], $movimientosLastMonth),
            'mermaVariacion'       => $variacion($actual['totalMerma'], $mermaLastMonth),
            'costoVariacion'       => $variacion($actual['costoMerma'], $costoLastMonth),
            'lotesVariacion'       => $variacion($actual['lotesCerrados'], $lotesLastMonth),
        ];
    }

    // ─── Datos para gráficas ───────────────────────────────────────────────────
    private function getDatosGraficas(string $from, string $to, Request $request): array
    {
        $topProductos = $this->mermasQuery($from, $to, $request)
            ->select('producto_id', DB::raw('SUM(cantidad) as total_merma'))
            ->groupBy('producto_id')
            ->orderByDesc('total_merma')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $producto = Producto::withTrashed()->find($item->producto_id);
                $nombre = $producto
                    ? $producto->nombre . ($producto->trashed() ? ' (eliminado)' : '')
                    : 'Producto eliminado';
                return (object) ['nombre' => $nombre, 'total' => $item->total_merma, 'unidad' => $producto->unidad ?? 'kg'];
            });

        $topCausas = $this->mermasQuery($from, $to, $request)
            ->select('causa', DB::raw('SUM(cantidad) as total'))
            ->groupBy('causa')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $mermaPorTipo = $this->mermasQuery($from, $to, $request)
            ->select('tipo_merma', DB::raw('SUM(cantidad) as total'))
            ->groupBy('tipo_merma')
            ->get()
            ->keyBy('tipo_merma');

        $mermaPorDia = Merma::whereBetween('fecha', [now()->subDays(30), now()])
            ->select(DB::raw('fecha'), DB::raw('SUM(cantidad) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return [
            'topProductos'    => $topProductos,
            'topCausas'       => $topCausas,
            'mermaProduccion' => $mermaPorTipo->get('produccion', (object)['total' => 0])->total,
            'mermaEmpaquetado'=> $mermaPorTipo->get('empaquetado', (object)['total' => 0])->total,
            'mermaPorDia'     => $mermaPorDia,
        ];
    }

    // ─── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $stats      = $this->getEstadisticasPeriodo($from, $to, $request);
        $variaciones = $this->getVariaciones($stats);
        $graficas   = $this->getDatosGraficas($from, $to, $request);

        $mermasRecientes = $this->mermasQuery($from, $to, $request)
            ->with(['usuario', 'producto' => fn($q) => $q->withTrashed()])
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $mermasRecientes->getCollection()->transform(function ($merma) {
            $merma->nombre_estado = $merma->producto
                ? $merma->producto->nombre . ($merma->producto->trashed() ? ' (eliminado)' : '')
                : 'Producto eliminado';
            return $merma;
        });

        $filtros = [
            'productos'          => Producto::withTrashed()->orderBy('nombre')->get(),
            'usuarios'           => User::orderBy('name')->get(),
            'causasDisponibles'  => Merma::distinct('causa')->pluck('causa'),
            'tiposDisponibles'   => Merma::distinct('tipo_merma')->pluck('tipo_merma'),
        ];

        $view = $request->ajax() ? 'reportes._content' : 'reportes.index';

        return view($view, array_merge(
            compact('from', 'to', 'mermasRecientes'),
            $stats,
            $variaciones,
            $graficas,
            $filtros
        ));
    }

    // ─── EXPORT PDF ────────────────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $mermas = Merma::whereBetween('fecha', [$from, $to])
            ->with(['producto' => fn($q) => $q->withTrashed(), 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $totalMermaPeriodo  = $mermas->sum('cantidad');
        $lotesCerradosPeriodo = $mermas->unique('lote')->count();
        $costoMermaPeriodo  = Merma::whereBetween('fecha', [$from, $to])
            ->join('productos', 'mermas.producto_id', '=', 'productos.id')
            ->sum(DB::raw('mermas.cantidad * productos.precio_unitario'));

        $produccionEsperada = 50000;
        $eficienciaPeriodo  = max(0, min(100,
            $produccionEsperada > 0
                ? round((1 - ($totalMermaPeriodo / $produccionEsperada)) * 100, 2)
                : 98.5
        ));

        $pdf = Pdf::loadView('reportes.pdf_reporte', compact(
            'from', 'to', 'mermas', 'totalMermaPeriodo',
            'costoMermaPeriodo', 'lotesCerradosPeriodo', 'eficienciaPeriodo'
        ));

        return $pdf->download('reporte_mermas_' . now()->format('Ymd_His') . '.pdf');
    }

    // ─── EXPORT CSV ────────────────────────────────────────────────────────────
    public function exportCsv(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $mermas = Merma::whereBetween('fecha', [$from, $to])
            ->with(['producto' => fn($q) => $q->withTrashed(), 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->streamDownload(function () use ($mermas) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
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
        }, 'reporte_mermas_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─── EXPORT EXCEL ──────────────────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        return Excel::download(
            new ReporteCompletoExport($from, $to),
            'reporte_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}