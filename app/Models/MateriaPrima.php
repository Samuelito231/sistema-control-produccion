<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MateriaPrima extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materia_prima';

 protected $fillable = [
    'nombre', 'sku', 'unidad', 'stock_actual', 'stock_minimo',
    'costo_unitario', 'proveedor', 'lote_compra', 'fecha_vencimiento'
];
    protected $casts = [
        'stock_actual' => 'decimal:4',
        'stock_minimo' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
        'fecha_vencimiento' => 'date',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoMateriaPrima::class);
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }

    /**
     * Registrar una entrada (compra, recepción)
     */
    public function registrarEntrada($cantidad, $motivo, $observaciones = null, $costoUnitario = null)
    {
        if ($cantidad <= 0) {
            throw new \Exception('La cantidad debe ser mayor a cero.');
        }

        $costo = $costoUnitario ?? $this->costo_unitario;
        
        // Actualizar stock
        $this->stock_actual += $cantidad;
        $this->save();

        // Crear movimiento
        return $this->movimientos()->create([
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'costo_unitario_momento' => $costo,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
            'fecha_movimiento' => now(),
        ]);
    }

    /**
     * Registrar una salida (consumo, merma, ajuste, vencimiento)
     */
    public function registrarSalida($cantidad, $motivo, $observaciones = null)
    {
        if ($cantidad <= 0) {
            throw new \Exception('La cantidad debe ser mayor a cero.');
        }

        if ($cantidad > $this->stock_actual) {
            throw new \Exception('Stock insuficiente');
        }

        // Actualizar stock
        $this->stock_actual -= $cantidad;
        $this->save();

        // Crear movimiento
        return $this->movimientos()->create([
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'costo_unitario_momento' => $this->costo_unitario,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
            'fecha_movimiento' => now(),
        ]);
    }
}