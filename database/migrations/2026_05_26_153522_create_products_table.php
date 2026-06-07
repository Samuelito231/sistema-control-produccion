<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sku')->unique();
            $table->string('categoria');
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->string('unidad')->default('kg');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};