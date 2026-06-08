<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Eliminar las foreign keys que apuntan a products
        Schema::table('mermas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('controles_calidad', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        // 2. Renombrar la tabla
        Schema::rename('products', 'productos');
        
        // 3. Recrear las foreign keys apuntando a la nueva tabla
        Schema::table('mermas', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
        
        Schema::table('recetas', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
        
        Schema::table('producciones', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
        
        Schema::table('controles_calidad', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    public function down()
    {
        // 1. Eliminar las foreign keys
        Schema::table('mermas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        Schema::table('controles_calidad', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        // 2. Renombrar de vuelta
        Schema::rename('productos', 'products');
        
        // 3. Recrear las foreign keys
        Schema::table('mermas', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('products')->onDelete('cascade');
        });
        
        Schema::table('recetas', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('products')->onDelete('cascade');
        });
        
        Schema::table('producciones', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('products')->onDelete('cascade');
        });
        
        Schema::table('controles_calidad', function (Blueprint $table) {
            $table->foreign('producto_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};