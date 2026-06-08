<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('titulo');
            $table->text('mensaje');
            $table->enum('nivel', ['info', 'warning', 'danger'])->default('info');
            $table->boolean('leida')->default(false);
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('referencia_tipo')->nullable();
            $table->timestamp('fecha_alerta');
            $table->timestamps();
            
            $table->index(['usuario_id', 'leida']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};