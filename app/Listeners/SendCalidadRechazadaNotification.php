<?php

namespace App\Listeners;

use App\Events\CalidadRechazadaEvent;
use App\Helpers\NotificacionHelper;

class SendCalidadRechazadaNotification
{
    public function handle(CalidadRechazadaEvent $event)
    {
        NotificacionHelper::calidadRechazada(
            $event->controlCalidad,
            $event->productoNombre,
            $event->motivo
        );
    }
}