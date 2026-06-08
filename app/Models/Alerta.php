<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';
    
    protected $fillable = [
        'tipo', 'titulo', 'mensaje', 'nivel', 'leida', 
        'usuario_id', 'referencia_id', 'referencia_tipo', 'fecha_alerta'
    ];
    
    protected $casts = [
        'leida' => 'boolean',
        'fecha_alerta' => 'datetime',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }
    
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}