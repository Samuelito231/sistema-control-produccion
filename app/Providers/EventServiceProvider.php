<?php

namespace App\Providers;

use App\Events\StockBajoEvent;
use App\Events\CalidadRechazadaEvent;
use App\Events\ProductoVencidoEvent;
use App\Listeners\SendStockBajoNotification;
use App\Listeners\SendCalidadRechazadaNotification;
use App\Listeners\SendProductoVencidoNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StockBajoEvent::class => [
            SendStockBajoNotification::class,
        ],
        CalidadRechazadaEvent::class => [
            SendCalidadRechazadaNotification::class,
        ],
        ProductoVencidoEvent::class => [
            SendProductoVencidoNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}