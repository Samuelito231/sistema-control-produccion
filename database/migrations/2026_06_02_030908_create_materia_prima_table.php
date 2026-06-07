<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('materia_prima', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sku')->unique();
            $table->string('unidad')->default('kg');
            $table->decimal('stock_actual', 12, 4)->default(0);
            $table->decimal('stock_minimo', 12, 4)->default(0);
            $table->decimal('costo_unitario', 12, 4)->nullable();
            $table->string('proveedor')->nullable();
            $table->timestamps();
            $table->softDeletes(); // borrado lógico
        });
    }

    public function down()
    {
        Schema::dropIfExists('materia_prima');
    }
};