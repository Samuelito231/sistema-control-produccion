<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('controles_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produccion_id')->constrained('producciones')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->date('fecha_inspeccion');
            $table->enum('resultado', ['aprobado', 'rechazado', 'cuarentena']);
            $table->string('motivo_rechazo')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('inspector_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('controles_calidad');
    }
};