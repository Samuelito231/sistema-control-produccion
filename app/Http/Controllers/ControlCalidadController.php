<?php

namespace App\Http\Controllers;

use App\Models\ControlCalidad;
use App\Models\Produccion;
use App\Events\CalidadRechazadaEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlCalidadController extends Controller
{
    public function index()
    {
        $inspecciones = ControlCalidad::with(['produccion.producto', 'inspector'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        $stats = [
            'total' => ControlCalidad::count(),
            'aprobados' => ControlCalidad::where('resultado', 'aprobado')->count(),
            'rechazados' => ControlCalidad::where('resultado', 'rechazado')->count(),
            'cuarentena' => ControlCalidad::where('resultado', 'cuarentena')->count(),
            'tasa_aprobacion' => 0,
        ];
        
        if ($stats['total'] > 0) {
            $stats['tasa_aprobacion'] = round(($stats['aprobados'] / $stats['total']) * 100, 2);
        }
        
        return view('control_calidad.index', compact('inspecciones', 'stats'));
    }
    
    public function create()
    {
        $producciones = Produccion::with('producto')
                        ->orderBy('created_at', 'desc')
                        ->limit(50)
                        ->get();
        
        return view('control_calidad.create', compact('producciones'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produccion_id' => 'required|exists:producciones,id',
            'resultado' => 'required|in:aprobado,rechazado,cuarentena',
            'motivo_rechazo' => 'required_if:resultado,rechazado|nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            $inspeccion = ControlCalidad::create([
                'produccion_id' => $validated['produccion_id'],
                'fecha_inspeccion' => now(),
                'resultado' => $validated['resultado'],
                'motivo_rechazo' => $validated['motivo_rechazo'] ?? null,
                'observaciones' => $validated['observaciones'],
                'inspector_id' => auth()->id(),
            ]);
            
            // Si el producto es RECHAZADO, disparar evento
            if ($validated['resultado'] === 'rechazado') {
                $produccion = Produccion::with('producto')->find($validated['produccion_id']);
                $productoNombre = $produccion->producto->nombre ?? 'Producto desconocido';
                $motivo = $validated['motivo_rechazo'] ?? 'No especificado';
                
                event(new CalidadRechazadaEvent($inspeccion, $productoNombre, $motivo));
            }
            
            DB::commit();
            
            $mensaje = match($validated['resultado']) {
                'aprobado' => '✅ Producto aprobado correctamente',
                'rechazado' => '❌ Producto rechazado por calidad. Se ha enviado una notificación.',
                'cuarentena' => '⚠️ Producto en cuarentena',
                default => 'Inspección guardada'
            };
            
            return redirect()->route('control-calidad.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    
    public function show(ControlCalidad $controlCalidad)
    {
        $controlCalidad->load(['produccion.producto', 'inspector']);
        return view('control_calidad.show', compact('controlCalidad'));
    }
}