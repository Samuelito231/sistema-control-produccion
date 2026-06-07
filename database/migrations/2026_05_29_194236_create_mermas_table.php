<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mermas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad')->default('kg');
            $table->string('causa');
            $table->enum('tipo_merma', ['produccion', 'empaquetado']);
            $table->string('lote')->nullable();
            $table->date('fecha');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mermas');
    }
};