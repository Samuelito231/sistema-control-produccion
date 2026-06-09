<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_guia')->unique();
            $table->date('fecha_envio');
            $table->date('fecha_estimada_entrega')->nullable();
            $table->date('fecha_real_entrega')->nullable();
            $table->enum('estado_envio', ['pendiente', 'en_transito', 'entregado', 'cancelado'])->default('pendiente');
            
            // Datos de destino
            $table->string('destinatario_nombre');
            $table->string('destinatario_telefono')->nullable();
            $table->string('destinatario_email')->nullable();
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('municipio');
            $table->string('estado_region');
            $table->string('codigo_postal')->nullable();
            
            // Datos de transporte
            $table->string('transportista');
            $table->string('numero_guia_transportista')->nullable();
            $table->decimal('costo_envio', 12, 2)->default(0);
            $table->enum('costo_pagado_por', ['empresa', 'cliente'])->default('empresa');
            
            // Auditoría
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('autorizado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('numero_guia');
            $table->index('estado_envio');
            $table->index('fecha_envio');
            $table->index('estado_region');
        });
    }

    public function down()
    {
        Schema::dropIfExists('envios');
    }
};