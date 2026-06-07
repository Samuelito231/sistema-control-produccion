<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumoMateriaPrima extends Model
{
    use HasFactory;

    protected $fillable = [
        'produccion_id', 'materia_prima_id', 'cantidad_consumida', 'costo_unitario_momento'
    ];

    public function produccion()
    {
        return $this->belongsTo(Produccion::class);
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }
}