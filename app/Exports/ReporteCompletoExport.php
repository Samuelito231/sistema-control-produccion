<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteCompletoExport implements WithMultipleSheets
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function sheets(): array
    {
        return [
            new ResumenGeneralSheet($this->from, $this->to),
            new ProductosSheet(),
            new MermasSheet($this->from, $this->to),
            new AuditoriaSheet(),
        ];
    }
}