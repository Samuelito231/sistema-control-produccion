<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioProducto extends Model
{
    protected $table = 'envio_productos';

    protected $fillable = [
        'envio_id', 'productable_type', 'productable_id',
        'cantidad', 'unidad', 'precio_unitario_momento', 'subtotal', 'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio_unitario_momento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }

    public function productable()
    {
        return $this->morphTo();
    }
}