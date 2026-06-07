<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductosSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public function collection()
    {
        return Producto::withTrashed()->get()->map(function ($p) {
            return [
                $p->sku,
                $p->nombre,
                $p->categoria,
                $p->stock_actual,
                $p->stock_minimo,
                $p->unidad,
                $p->precio_unitario,
                $p->created_at->format('d/m/Y'),
                $p->deleted_at ? 'Eliminado' : 'Activo',
                $p->stock_actual <= $p->stock_minimo ? 'Sí' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return ['SKU', 'Producto', 'Categoría', 'Stock actual', 'Stock mínimo', 'Unidad', 'Precio unitario', 'Fecha creación', 'Estado', '¿Stock crítico?'];
    }

    public function title(): string
    {
        return 'Productos';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'J';

                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'CONTROL INTERNO KHALEESITAS - PRODUCTOS');
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
                    $critical = $sheet->getCell("J{$i}")->getValue();
                    if ($critical == 'Sí') {
                        $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCCCC']],
                        ]);
                    } else {
                        $bgColor = ($i % 2 == 0) ? 'F9F2E6' : 'FFFFFF';
                        $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                        ]);
                    }
                }

                $sheet->getStyle('G4:G' . $rowEnd)->getNumberFormat()->setFormatCode('"$"#,##0.00');
            },
        ];
    }
}