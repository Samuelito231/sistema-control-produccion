<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controles_calidad', function (Blueprint $table) {
            // Eliminar la foreign key primero
            $table->dropForeign(['producto_id']);
            // Eliminar la columna
            $table->dropColumn('producto_id');
        });
    }

    public function down(): void
    {
        Schema::table('controles_calidad', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }
};