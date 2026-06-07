<?php

namespace App\Exports;

use App\Models\Merma;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MermasSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return Merma::whereBetween('fecha', [$this->from, $this->to])
            ->with('producto', 'usuario')
            ->get()
            ->map(function ($m) {
                return [
                    $m->id,
                    $m->fecha,
                    $m->producto->nombre ?? 'Eliminado',
                    $m->cantidad,
                    $m->unidad,
                    $m->causa,
                    ucfirst($m->tipo_merma),
                    $m->lote ?? '—',
                    $m->usuario->name ?? 'N/A',
                    $m->producto ? $m->producto->stock_actual : '—',
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Producto', 'Cantidad', 'Unidad', 'Causa', 'Tipo', 'Lote', 'Usuario', 'Stock restante'];
    }

    public function title(): string
    {
        return 'Mermas';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'J';

                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'CONTROL INTERNO KHALEESITAS - REGISTRO DE MERMAS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'C29E75']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C29E75']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $rowStart = 4;
                $rowEnd = $sheet->getHighestRow();
                for ($i = $rowStart; $i <= $rowEnd; $i++) {
                    $tipo = $sheet->getCell("G{$i}")->getValue();
                    $cantidad = (float) $sheet->getCell("D{$i}")->getValue();

                    if ($tipo == 'Produccion') $bgColor = 'D9EAD3';
                    elseif ($tipo == 'Empaquetado') $bgColor = 'CFE2F0';
                    else $bgColor = ($i % 2 == 0) ? 'F9F2E6' : 'FFFFFF';

                    if ($cantidad > 10) $bgColor = 'FCE4D6';

                    $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    ]);
                }

                $sheet->getStyle('D4:D' . $rowEnd)->getNumberFormat()->setFormatCode('#,##0.00');
            },
        ];
    }
}