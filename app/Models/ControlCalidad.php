<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlCalidad extends Model
{
    protected $table = 'controles_calidad';
    
    protected $fillable = [
        'produccion_id',
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
    
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
    
    // Accesor para obtener el producto a través de la producción
    public function getProductoAttribute()
    {
        return $this->produccion?->producto;
    }
}