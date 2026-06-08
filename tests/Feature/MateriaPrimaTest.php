<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MateriaPrima;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MateriaPrimaTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_puede_crear_materia_prima()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post('/materia-prima', [
            'nombre' => 'Harina',
            'sku' => 'H001',
            'unidad' => 'kg',
            'stock_actual' => 500,
            'stock_minimo' => 50,
            'costo_unitario' => 2.5,
            'proveedor' => 'Proveedor Test'
        ]);
        
        $response->assertRedirect('/materia-prima');
        $this->assertDatabaseHas('materia_prima', ['sku' => 'H001']);
    }
}
