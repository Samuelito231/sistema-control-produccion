<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Alerta;
use App\Helpers\NotificacionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificacionTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_puede_crear_notificacion()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $alerta = NotificacionHelper::crear(
            'prueba',
            'Test Notificación',
            'Mensaje de prueba',
            'info',
            $user->id
        );
        
        $this->assertDatabaseHas('alertas', [
            'titulo' => 'Test Notificación',
            'usuario_id' => $user->id
        ]);
    }
    
    public function test_puede_marcar_notificacion_como_leida()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        $alerta = Alerta::create([
            'tipo' => 'prueba',
            'titulo' => 'Test',
            'mensaje' => 'Mensaje',
            'nivel' => 'info',
            'usuario_id' => $user->id,
            'fecha_alerta' => now(),
            'leida' => false
        ]);
        
        $this->actingAs($user);
        
        $response = $this->post("/notificaciones/marcar/{$alerta->id}");
        
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('alertas', [
            'id' => $alerta->id,
            'leida' => true
        ]);
    }
}
