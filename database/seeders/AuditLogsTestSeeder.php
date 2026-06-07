<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AuditLogsTestSeeder extends Seeder
{
    public function run()
    {
        $producto = Producto::first() ?? Producto::create([
            'sku' => 'DEMO',
            'nombre' => 'Producto Demo',
            'categoria' => 'Demo',
            'stock_actual' => 100,
            'stock_minimo' => 10,
            'unidad' => 'kg',
            'precio_unitario' => 10,
        ]);

        $users = User::whereIn('id', [1,4,5,6,7])->get();
        $fechas = [
            Carbon::now()->subDays(5),
            Carbon::now()->subDays(4),
            Carbon::now()->subDays(3),
            Carbon::now()->subDays(2),
            Carbon::now()->subDays(1),
            Carbon::now(),
        ];

        foreach ($users as $user) {
            $numLogs = rand(2, 4);
            for ($i = 0; $i < $numLogs; $i++) {
                if ($user->role == 'admin') {
                    $accion = rand(0,1) ? 'approve_user' : 'create_producto';
                    $extra = $accion == 'approve_user'
                        ? ['assigned_role' => 'operario', 'previous_status' => 'pending', 'ip' => '192.168.1.10', 'user_agent' => 'Chrome']
                        : ['sku' => 'NEW-'.rand(100,999), 'nombre' => 'Nuevo producto', 'ip' => '192.168.1.10', 'user_agent' => 'Chrome'];
                    $modelType = $accion == 'approve_user' ? 'App\Models\User' : 'App\Models\Producto';
                    $modelId = $accion == 'approve_user' ? 4 : $producto->id;
                } elseif (in_array($user->role, ['operario', 'empaquetador'])) {
                    $accion = 'merma_registrada';
                    $tipo = ($user->role == 'operario') ? (rand(0,1) ? 'produccion' : 'empaquetado') : 'empaquetado';
                    $extra = [
                        'tipo_merma' => $tipo,
                        'cantidad' => rand(1, 10),
                        'causa' => $tipo == 'produccion' ? 'Sobrepeso' : 'Sellado defectuoso',
                        'lote' => 'LOT-'.rand(100,999),
                        'stock_restante' => rand(50,200),
                        'ip' => '192.168.1.'.rand(20,30),
                        'user_agent' => 'Firefox',
                    ];
                    $modelType = 'App\Models\Producto';
                    $modelId = $producto->id;
                } else {
                    $accion = 'view_report';
                    $extra = [
                        'ip' => '192.168.1.'.rand(40,50),
                        'user_agent' => 'Edge',
                    ];
                    $modelType = null;
                    $modelId = null;
                }

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $accion,
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'old_values' => null,
                    'new_values' => null,
                    'extra' => json_encode($extra),
                    'created_at' => $fechas[array_rand($fechas)],
                ]);
            }
            $this->command->info("Logs insertados para {$user->email}");
        }
        $this->command->info("Total logs: " . AuditLog::count());
    }
}