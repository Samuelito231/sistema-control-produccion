<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Envio extends Model
{
    use SoftDeletes;

    protected $table = 'envios';

    protected $fillable = [
    'numero_guia',
    'fecha_envio',
    'fecha_estimada_entrega',
    'fecha_real_entrega',
    'estado_envio',
    'destinatario_nombre',
    'destinatario_telefono',
    'destinatario_email',
    'direccion',
    'ciudad',
    'municipio',
    'estado_region',
    'codigo_postal',
    'transportista',
    'numero_guia_transportista',
    'costo_envio',
    'costo_pagado_por',
    'observaciones',
    'usuario_id',
    'autorizado_por'
];

    protected $casts = [
        'fecha_envio' => 'date',
        'fecha_estimada_entrega' => 'date',
        'fecha_real_entrega' => 'date',
        'costo_envio' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function autorizador()
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function productos()
    {
        return $this->hasMany(EnvioProducto::class);
    }

    // Generar número de guía automático
    public static function generarNumeroGuia()
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->count();
        $number = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "GUI-{$year}-{$number}";
    }

    // Actualizar stock al crear envío
    public function actualizarStock()
    {
        foreach ($this->productos as $item) {
            $producto = $item->productable;
            if ($producto) {
                $producto->decrement('stock_actual', $item->cantidad);
            }
        }
    }
}