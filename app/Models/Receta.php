<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $fillable = ['producto_id', 'materia_prima_id', 'cantidad_necesaria'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }
}