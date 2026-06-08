<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cambiar el valor por defecto de 'operador' a 'operario'
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operario')->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operador')->change();
        });
    }

    /**
 * Verificar si el usuario tiene uno de los roles especificados
 */
public function hasRole(string ...$roles): bool
{
    return in_array($this->role, $roles);
}

/**
 * Verificar si el usuario es administrador
 */
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

/**
 * Verificar si el usuario es operario
 */
public function isOperario(): bool
{
    return $this->role === 'operario';
}

/**
 * Verificar si el usuario es auditor
 */
public function isAuditor(): bool
{
    return $this->role === 'auditor';
}

/**
 * Verificar si el usuario es analista
 */
public function isAnalista(): bool
{
    return $this->role === 'analista';
}

/**
 * Verificar si el usuario es empaquetador
 */
public function isEmpaquetador(): bool
{
    return $this->role === 'empaquetador';
}
};