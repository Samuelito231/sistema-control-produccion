<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoMateriaPrima extends Model
{
    use HasFactory;

    protected $table = 'movimientos_materia_prima';

    protected $fillable = [
        'materia_prima_id', 'tipo', 'cantidad', 'motivo',
        'referencia_tipo', 'referencia_id', 'costo_unitario_momento',
        'observaciones', 'usuario_id', 'fecha_movimiento'  // ← Agrega esta línea
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'costo_unitario_momento' => 'decimal:4',
        'fecha_movimiento' => 'datetime',  // ← Agrega esta línea
    ];

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}