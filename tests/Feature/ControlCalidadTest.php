<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use App\Models\Produccion;
use App\Models\ControlCalidad;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControlCalidadTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_puede_crear_inspeccion()
    {
        $user = User::create([
            'name' => 'Operario',
            'email' => 'operario@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'operario',
            'status' => 'active'
        ]);
        
        $producto = Producto::create([
            'nombre' => 'Producto Test',
            'sku' => 'TEST001',
            'categoria' => 'Prueba',
            'unidad' => 'kg',
            'stock_actual' => 100,
            'stock_minimo' => 10
        ]);
        
        $produccion = Produccion::create([
            'producto_id' => $producto->id,
            'cantidad_producida' => 50,
            'fecha_produccion' => now(),
            'usuario_id' => $user->id
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post('/control-calidad', [
            'produccion_id' => $produccion->id,
            'resultado' => 'aprobado',
            'observaciones' => 'Test inspección'
        ]);
        
        $response->assertRedirect('/control-calidad');
        $this->assertDatabaseHas('controles_calidad', [
            'produccion_id' => $produccion->id,
            'resultado' => 'aprobado'
        ]);
    }
}
