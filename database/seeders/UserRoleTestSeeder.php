<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleTestSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'email' => 'admin@test.com', 'role' => 'admin'],
            ['name' => 'Operador', 'email' => 'operador@test.com', 'role' => 'operador'],
            ['name' => 'Empaquetador', 'email' => 'empaque@test.com', 'role' => 'empaquetador'],
            ['name' => 'Analista', 'email' => 'analista@test.com', 'role' => 'analista'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('password'),
                'role' => $u['role'],
            ]);
        }
    }
}