<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producciones', function (Blueprint $table) {
            $table->id();
            $table->string('lote')->nullable();
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->decimal('cantidad_producida', 12, 4);
            $table->date('fecha_produccion');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producciones');
    }
};