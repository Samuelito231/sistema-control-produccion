<?php

namespace App\Events;

use App\Models\MateriaPrima;
use App\Models\Producto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBajoEvent
{
    use Dispatchable, SerializesModels;

    public $modelo;
    public $tipo;
    public $nombre;
    public $stockActual;
    public $stockMinimo;

    public function __construct($modelo, $tipo, $nombre, $stockActual, $stockMinimo)
    {
        $this->modelo = $modelo;
        $this->tipo = $tipo;
        $this->nombre = $nombre;
        $this->stockActual = $stockActual;
        $this->stockMinimo = $stockMinimo;
    }
}