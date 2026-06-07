<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ResumenGeneralSheet implements FromArray, WithTitle, WithEvents
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        $totalMerma = \App\Models\Merma::whereBetween('fecha', [$this->from, $this->to])->sum('cantidad');
        $costoMerma = \App\Models\Merma::whereBetween('fecha', [$this->from, $this->to])
            ->join('productos', 'mermas.producto_id', '=', 'productos.id')
            ->sum(\DB::raw('mermas.cantidad * productos.precio_unitario'));
        $totalProductos = \App\Models\Producto::count();
        $productosActivos = \App\Models\Producto::whereNull('deleted_at')->count();
        $productosEliminados = \App\Models\Producto::onlyTrashed()->count();
        $eficiencia = $this->calcularEficiencia($totalMerma);

        return [
            ['RESUMEN GENERAL - PERÍODO', ''],
            ['Desde:', $this->from, 'Hasta:', $this->to],
            ['Fecha de generación:', now()->format('d/m/Y H:i:s'), '', ''],
            [],
            ['INDICADOR', 'VALOR'],
            ['Total productos registrados', $totalProductos],
            ['Productos activos', $productosActivos],
            ['Productos eliminados (soft delete)', $productosEliminados],
            ['Total merma (kg/uds)', number_format($totalMerma, 2)],
            ['Costo total de merma', '$ ' . number_format($costoMerma, 2)],
            ['Eficiencia estimada', $eficiencia . '%'],
        ];
    }

    private function calcularEficiencia($totalMerma)
    {
        $produccionEsperada = 50000;
        $eficiencia = $produccionEsperada > 0 ? round((1 - ($totalMerma / $produccionEsperada)) * 100, 2) : 98.5;
        return max(0, min(100, $eficiencia));
    }

    public function title(): string
    {
        return 'Resumen General';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'D';

                // Título principal
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'CONTROL INTERNO KHALEESITAS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'C29E75']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Encabezados de tabla (fila 5)
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C29E75']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Autoajuste
                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Filas alternadas
                $rowStart = 6;
                $rowEnd = $sheet->getHighestRow();
                for ($i = $rowStart; $i <= $rowEnd; $i++) {
                    $fillColor = ($i % 2 == 0) ? 'F9F2E6' : 'FFFFFF';
                    $sheet->getStyle("A{$i}:D{$i}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                    ]);
                }

                // Formato moneda
                $sheet->getStyle('D6:D' . $rowEnd)->getNumberFormat()->setFormatCode('"$"#,##0.00');
            },
        ];
    }
}