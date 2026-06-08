<?php

namespace App\Events;

use App\Models\MateriaPrima;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductoVencidoEvent
{
    use Dispatchable, SerializesModels;

    public $materiaPrima;

    public function __construct(MateriaPrima $materiaPrima)
    {
        $this->materiaPrima = $materiaPrima;
    }
}