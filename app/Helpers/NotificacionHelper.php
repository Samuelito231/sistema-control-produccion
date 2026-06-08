<?php

namespace App\Helpers;

use App\Models\Alerta;
use App\Models\User;

class NotificacionHelper
{
    /**
     * Crear una alerta de stock bajo
     */
    public static function stockBajo($modelo, $tipo, $nombre, $stockActual, $stockMinimo)
    {
        $titulo = "Stock bajo de {$tipo}";
        $mensaje = "El {$tipo} '{$nombre}' tiene stock actual de {$stockActual} unidades, por debajo del mínimo de {$stockMinimo}.";
        
        // Notificar a todos los administradores y operarios
        $usuarios = User::whereIn('role', ['admin', 'operario'])->get();
        
        foreach ($usuarios as $usuario) {
            self::crear(
                'stock_bajo',
                $titulo,
                $mensaje,
                'warning',
                $usuario->id,
                $modelo->id,
                get_class($modelo)
            );
        }
    }
    
    /**
     * Crear una alerta de calidad rechazada
     */
    public static function calidadRechazada($controlCalidad, $productoNombre, $motivo)
    {
        $titulo = "Producto Rechazado - Control de Calidad";
        $mensaje = "El producto '{$productoNombre}' fue rechazado en control de calidad. Motivo: {$motivo}";
        
        $usuarios = User::whereIn('role', ['admin', 'supervisor'])->get();
        
        foreach ($usuarios as $usuario) {
            self::crear(
                'calidad_rechazada',
                $titulo,
                $mensaje,
                'danger',
                $usuario->id,
                $controlCalidad->id,
                get_class($controlCalidad)
            );
        }
    }
    
    /**
     * Crear una alerta de producto vencido
     */
    public static function productoVencido($materiaPrima)
    {
        $titulo = "Materia Prima Vencida";
        $mensaje = "La materia prima '{$materiaPrima->nombre}' (Lote: {$materiaPrima->lote_compra}) ha vencido. Stock actual: {$materiaPrima->stock_actual} {$materiaPrima->unidad}.";
        
        $usuarios = User::whereIn('role', ['admin', 'operario'])->get();
        
        foreach ($usuarios as $usuario) {
            self::crear(
                'producto_vencido',
                $titulo,
                $mensaje,
                'danger',
                $usuario->id,
                $materiaPrima->id,
                get_class($materiaPrima)
            );
        }
    }
    
    /**
     * Crear una alerta genérica
     */
    public static function crear($tipo, $titulo, $mensaje, $nivel, $usuarioId, $referenciaId = null, $referenciaTipo = null)
    {
        return Alerta::create([
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'nivel' => $nivel,
            'usuario_id' => $usuarioId,
            'referencia_id' => $referenciaId,
            'referencia_tipo' => $referenciaTipo,
            'fecha_alerta' => now(),
        ]);
    }
    
    /**
     * Marcar alerta como leída
     */
    public static function marcarComoLeida($alertaId)
    {
        $alerta = Alerta::find($alertaId);
        if ($alerta) {
            $alerta->update(['leida' => true]);
        }
    }
}
