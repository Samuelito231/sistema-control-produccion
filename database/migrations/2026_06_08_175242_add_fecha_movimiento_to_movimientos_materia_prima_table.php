<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movimientos_materia_prima', function (Blueprint $table) {
            // Agregar columna fecha_movimiento después de observaciones
            $table->timestamp('fecha_movimiento')->nullable()->after('observaciones');
        });

        // Actualizar los registros existentes con la fecha de created_at
        DB::statement('UPDATE movimientos_materia_prima SET fecha_movimiento = created_at WHERE fecha_movimiento IS NULL');
    }

    public function down()
    {
        Schema::table('movimientos_materia_prima', function (Blueprint $table) {
            $table->dropColumn('fecha_movimiento');
        });
    }
};