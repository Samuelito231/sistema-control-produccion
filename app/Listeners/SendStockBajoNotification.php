<?php

namespace App\Listeners;

use App\Events\StockBajoEvent;
use App\Helpers\NotificacionHelper;

class SendStockBajoNotification
{
    public function handle(StockBajoEvent $event)
    {
        NotificacionHelper::stockBajo(
            $event->modelo,
            $event->tipo,
            $event->nombre,
            $event->stockActual,
            $event->stockMinimo
        );
    }
}