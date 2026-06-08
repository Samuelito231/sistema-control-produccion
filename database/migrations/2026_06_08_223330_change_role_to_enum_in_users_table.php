<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Verificar si el tipo ENUM ya existe
        DB::statement("DO $$ BEGIN
            CREATE TYPE user_role AS ENUM ('admin', 'operario', 'auditor', 'analista', 'empaquetador');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");
        
        // Cambiar la columna a usar el ENUM solo si no es ya del tipo correcto
        DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE user_role USING role::text::user_role");
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'operario'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'operario'");
        DB::statement("DROP TYPE IF EXISTS user_role");
    }
};