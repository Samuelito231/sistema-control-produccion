<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlCalidad extends Model
{
    protected $table = 'controles_calidad';
    
    protected $fillable = [
        'produccion_id',
        'producto_id',
        'fecha_inspeccion',
        'resultado',
        'motivo_rechazo',
        'observaciones',
        'inspector_id'
    ];
    
    protected $casts = [
        'fecha_inspeccion' => 'date',
    ];
    
    public function produccion()
    {
        return $this->belongsTo(Produccion::class, 'produccion_id');
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');  // ← Relación con Producto
    }
    
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}