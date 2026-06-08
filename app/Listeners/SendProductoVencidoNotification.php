<?php

namespace App\Listeners;

use App\Events\ProductoVencidoEvent;
use App\Helpers\NotificacionHelper;

class SendProductoVencidoNotification
{
    public function handle(ProductoVencidoEvent $event)
    {
        NotificacionHelper::productoVencido($event->materiaPrima);
    }
}