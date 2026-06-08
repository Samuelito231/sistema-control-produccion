<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_puede_crear_producto()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post('/productos', [
            'nombre' => 'Producto Test',
            'sku' => 'TEST001',
            'categoria' => 'Prueba',
            'unidad' => 'kg',
            'stock_actual' => 100,
            'stock_minimo' => 10,
        ]);
        
        $response->assertRedirect('/inventario');
        $this->assertDatabaseHas('productos', ['sku' => 'TEST001']);
    }
    
    public function test_puede_listar_productos()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/inventario');
        $response->assertStatus(200);
    }
}
