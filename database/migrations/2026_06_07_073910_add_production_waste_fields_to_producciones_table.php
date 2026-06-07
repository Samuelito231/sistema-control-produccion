<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('producciones', function (Blueprint $table) {
            // Materia prima desechada (kg/uds)
            $table->decimal('materia_prima_desechada', 12, 4)->default(0)->after('cantidad_producida');
            // Producto terminado desechado (kg/uds)
            $table->decimal('producto_desechado', 12, 4)->default(0)->after('materia_prima_desechada');
            // Observaciones de calidad
            $table->text('calidad_observaciones')->nullable()->after('observaciones');
            // Eficiencia de producción (calculada)
            $table->decimal('eficiencia', 5, 2)->nullable()->after('calidad_observaciones');
        });
    }

    public function down()
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropColumn(['materia_prima_desechada', 'producto_desechado', 'calidad_observaciones', 'eficiencia']);
        });
    }
};