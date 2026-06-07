<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materia_prima', function (Blueprint $table) {
            $table->string('lote_compra')->nullable()->after('sku');
            $table->date('fecha_vencimiento')->nullable()->after('stock_minimo');
        });
    }

    public function down()
    {
        Schema::table('materia_prima', function (Blueprint $table) {
            $table->dropColumn(['lote_compra', 'fecha_vencimiento']);
        });
    }
};