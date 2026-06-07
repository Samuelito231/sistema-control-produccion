<?php

namespace App\Services;

use App\Models\ControlCalidad;
use App\Models\Produccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalidadService
{
    /**
     * Registrar una nueva inspección de calidad
     */
    public function registrarInspeccion(array $datos): ControlCalidad
    {
        return DB::transaction(function () use ($datos) {
            // Crear la inspección
            $inspeccion = ControlCalidad::create([
                'produccion_id' => $datos['produccion_id'],
                'producto_id' => $datos['producto_id'],
                'fecha_inspeccion' => now(),
                'resultado' => $datos['resultado'],
                'motivo_rechazo' => $datos['motivo_rechazo'] ?? null,
                'observaciones' => $datos['observaciones'],
                'inspector_id' => auth()->id(),
            ]);
            
            // Manejar según el resultado
            if ($datos['resultado'] === 'rechazado') {
                $this->manejarRechazo($inspeccion);
            } elseif ($datos['resultado'] === 'cuarentena') {
                $this->manejarCuarentena($inspeccion);
            } else {
                $this->manejarAprobacion($inspeccion);
            }
            
            // Registrar en log
            Log::info('Inspección de calidad registrada', [
                'id' => $inspeccion->id,
                'resultado' => $datos['resultado'],
                'inspector' => auth()->id()
            ]);
            
            return $inspeccion;
        });
    }
    
    /**
     * Manejar producto rechazado
     */
    public function manejarRechazo(ControlCalidad $inspeccion): void
    {
        // Actualizar estado de producción
        if ($inspeccion->produccion) {
            $inspeccion->produccion->update([
                'estado' => 'rechazado_calidad',
                'fecha_rechazo' => now(),
            ]);
        }
        
        // Disparar evento o notificación aquí
        // event(new ProductoRechazado($inspeccion));
    }
    
    /**
     * Manejar producto en cuarentena
     */
    public function manejarCuarentena(ControlCalidad $inspeccion): void
    {
        if ($inspeccion->produccion) {
            $inspeccion->produccion->update([
                'estado' => 'cuarentena',
                'fecha_cuarentena' => now(),
            ]);
        }
    }
    
    /**
     * Manejar producto aprobado
     */
    public function manejarAprobacion(ControlCalidad $inspeccion): void
    {
        if ($inspeccion->produccion && $inspeccion->produccion->estado === 'cuarentena') {
            $inspeccion->produccion->update([
                'estado' => 'aprobado',
                'fecha_aprobacion' => now(),
            ]);
        }
    }
    
    /**
     * Obtener estadísticas de calidad
     */
    public function getEstadisticas(): array
    {
        return [
            'total' => ControlCalidad::count(),
            'aprobados' => ControlCalidad::where('resultado', 'aprobado')->count(),
            'rechazados' => ControlCalidad::where('resultado', 'rechazado')->count(),
            'cuarentena' => ControlCalidad::where('resultado', 'cuarentena')->count(),
        ];
    }
}
