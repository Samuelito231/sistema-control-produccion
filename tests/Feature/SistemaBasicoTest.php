<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SistemaBasicoTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_la_pagina_de_login_carga()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
    
    public function test_usuario_puede_crearse()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
}
