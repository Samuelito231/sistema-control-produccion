<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    use HasFactory;

    protected $table = 'producciones';

    protected $fillable = [
        'lote',
        'producto_id',
        'cantidad_producida',
        'materia_prima_desechada',
        'producto_desechado',
        'fecha_produccion',
        'observaciones',
        'calidad_observaciones',
        'eficiencia',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_produccion' => 'date',
        'eficiencia' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function consumos()
    {
        return $this->hasMany(ConsumoMateriaPrima::class);
    }

    public function controlesCalidad()
    {
        return $this->hasMany(ControlCalidad::class, 'produccion_id');
    }

    public function mermas()
    {
        return $this->hasMany(Merma::class, 'produccion_id');
    }
}