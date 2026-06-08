<?php

namespace App\Events;

use App\Models\ControlCalidad;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalidadRechazadaEvent
{
    use Dispatchable, SerializesModels;

    public $controlCalidad;
    public $productoNombre;
    public $motivo;

    public function __construct(ControlCalidad $controlCalidad, $productoNombre, $motivo)
    {
        $this->controlCalidad = $controlCalidad;
        $this->productoNombre = $productoNombre;
        $this->motivo = $motivo;
    }
}