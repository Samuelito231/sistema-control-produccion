<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consumos_materia_prima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produccion_id')->constrained('producciones')->onDelete('cascade');
            $table->foreignId('materia_prima_id')->constrained('materia_prima');
            $table->decimal('cantidad_consumida', 12, 4);
            $table->decimal('costo_unitario_momento', 12, 4);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumos_materia_prima');
    }
};