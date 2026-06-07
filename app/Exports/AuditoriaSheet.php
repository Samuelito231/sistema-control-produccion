<?php

namespace App\Exports;

use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AuditoriaSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public function collection()
    {
        return AuditLog::with('user')->latest()->limit(2000)->get()->map(function ($log) {
            return [
                $log->created_at,
                $log->user->name ?? 'Sistema',
                $log->action,
                $log->model_type,
                $log->model_id,
                json_encode($log->old_values),
                json_encode($log->new_values),
                $log->extra['ip'] ?? '',
                $log->extra['user_agent'] ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Fecha/Hora', 'Usuario', 'Acción', 'Modelo', 'ID', 'Valores antiguos', 'Valores nuevos', 'IP', 'Navegador'];
    }

    public function title(): string
    {
        return 'Auditoría';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'I';

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'CONTROL INTERNO KHALEESITAS - AUDITORÍA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'C29E75']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A3:I3')->applyFromArray([
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
                    $accion = $sheet->getCell("C{$i}")->getValue();
                    switch ($accion) {
                        case 'create_producto': $bgColor = 'D9EAD3'; break;
                        case 'update_producto': $bgColor = 'FFF2CC'; break;
                        case 'delete_producto': $bgColor = 'F4CCCC'; break;
                        case 'merma_registrada': $bgColor = 'CFE2F0'; break;
                        default: $bgColor = ($i % 2 == 0) ? 'F9F2E6' : 'FFFFFF';
                    }
                    $sheet->getStyle("A{$i}:I{$i}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    ]);
                }

                $sheet->getStyle('A4:A' . $rowEnd)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');
            },
        ];
    }
}