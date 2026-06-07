<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_materia_prima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_id')->constrained('materia_prima')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->decimal('cantidad', 12, 4);
            $table->string('motivo'); // compra, ajuste_inventario, merma_produccion, consumo_produccion
            $table->string('referencia_tipo')->nullable(); // ej: 'compra', 'produccion'
            $table->unsignedBigInteger('referencia_id')->nullable(); // id del registro relacionado
            $table->decimal('costo_unitario_momento', 12, 4)->nullable(); // coste histórico
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();

            $table->index(['materia_prima_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_materia_prima');
    }
};