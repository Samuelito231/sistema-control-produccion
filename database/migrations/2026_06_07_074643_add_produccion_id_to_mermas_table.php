<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mermas', function (Blueprint $table) {
            $table->foreignId('produccion_id')->nullable()->constrained('producciones')->onDelete('set null')->after('producto_id');
        });
    }

    public function down()
    {
        Schema::table('mermas', function (Blueprint $table) {
            $table->dropForeign(['produccion_id']);
            $table->dropColumn('produccion_id');
        });
    }
};