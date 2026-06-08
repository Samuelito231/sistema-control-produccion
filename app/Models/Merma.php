<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merma extends Model
{

use HasFactory, SoftDeletes;

    use HasFactory;

    protected $table = 'mermas';

    protected $fillable = [
        'producto_id',
        'produccion_id',      // ← NUEVO: relación con producción
        'cantidad',
        'unidad',
        'causa',
        'tipo_merma',
        'lote',
        'fecha',
        'usuario_id',
        'observaciones'
    ];

     protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha' => 'date',
        'deleted_at' => 'datetime', 

    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // NUEVA RELACIÓN: una merma puede pertenecer a una producción
    public function produccion()
    {
        return $this->belongsTo(Produccion::class);
    }
}
