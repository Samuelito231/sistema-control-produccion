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
        'costo_unitario', 'proveedor'
    ];

    protected $casts = [
        'stock_actual' => 'decimal:4',
        'stock_minimo' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoMateriaPrima::class);
    }

    public function recetas()
{
    return $this->hasMany(Receta::class);
}

    // Método para registrar una entrada (compra)
    public function registrarEntrada($cantidad, $motivo, $observaciones = null, $costoUnitario = null)
    {
        $costo = $costoUnitario ?? $this->costo_unitario;
        $this->stock_actual += $cantidad;
        $this->save();

        return $this->movimientos()->create([
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'costo_unitario_momento' => $costo,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
        ]);
    }

    // Método para registrar una salida (consumo, merma, etc.)
    public function registrarSalida($cantidad, $motivo, $observaciones = null)
    {
        if ($cantidad > $this->stock_actual) {
            throw new \Exception('Stock insuficiente');
        }

        $this->stock_actual -= $cantidad;
        $this->save();

        return $this->movimientos()->create([
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'costo_unitario_momento' => $this->costo_unitario,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
        ]);
    }
}