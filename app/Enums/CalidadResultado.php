<?php

namespace App\Enums;

enum CalidadResultado: string
{
    case APROBADO = 'aprobado';
    case RECHAZADO = 'rechazado';
    case CUARENTENA = 'cuarentena';
    
    public function label(): string
    {
        return match($this) {
            self::APROBADO => '✅ Aprobado',
            self::RECHAZADO => '❌ Rechazado',
            self::CUARENTENA => '⚠️ Cuarentena',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::APROBADO => 'green',
            self::RECHAZADO => 'red',
            self::CUARENTENA => 'yellow',
        };
    }
}
