<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('materia_prima_id')->constrained('materia_prima')->onDelete('cascade');
            $table->decimal('cantidad_necesaria', 12, 4);
            $table->timestamps();

            $table->unique(['producto_id', 'materia_prima_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recetas');
    }
};