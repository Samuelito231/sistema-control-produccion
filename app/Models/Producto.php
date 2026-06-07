<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'sku', 'nombre', 'categoria', 'stock_actual', 'unidad',
        'precio_unitario', 'stock_minimo'
    ];

    public function mermas()
    {
        return $this->hasMany(Merma::class, 'producto_id');
    }

    public function recetas()
{
    return $this->hasMany(Receta::class);
}

    public function descontarStock($cantidad)
    {
        $this->stock_actual -= $cantidad;
        $this->save();
    }

    // Generar código QR para merma rápida
    public function getQrCodeAttribute()
    {
        return QrCode::size(120)->generate(route('produccion.rapida', $this->id));
    }
}