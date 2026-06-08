<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropColumn(['materia_prima_desechada', 'producto_desechado']);
        });
    }

    public function down(): void
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->decimal('materia_prima_desechada', 12, 4)->default(0)->notNull();
            $table->decimal('producto_desechado', 12, 4)->default(0)->notNull();
        });
    }
};