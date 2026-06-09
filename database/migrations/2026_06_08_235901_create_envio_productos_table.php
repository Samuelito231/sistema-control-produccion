<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('envio_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_id')->constrained('envios')->onDelete('cascade');
            $table->morphs('productable');
            $table->decimal('cantidad', 12, 4);
            $table->string('unidad')->default('kg');
            $table->decimal('precio_unitario_momento', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // El método morphs() ya crea el índice automáticamente
            // No agregar índice adicional
        });
    }

    public function down()
    {
        Schema::dropIfExists('envio_productos');
    }
};