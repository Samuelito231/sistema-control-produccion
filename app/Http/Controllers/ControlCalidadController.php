<?php

namespace App\Http\Controllers;

use App\Models\ControlCalidad;
use App\Models\Produccion;
use App\Models\Producto;
use App\Services\CalidadService;
use App\Http\Requests\CalidadRequest;
use Illuminate\Http\Request;

class ControlCalidadController extends Controller
{
    protected $calidadService;
    
    public function __construct(CalidadService $calidadService)
    {
        $this->calidadService = $calidadService;
    }
    
    public function index()
    {
        $inspecciones = ControlCalidad::with(['produccion', 'producto', 'inspector'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        $stats = $this->calidadService->getEstadisticas();
        
        if ($stats['total'] > 0) {
            $stats['tasa_aprobacion'] = round(($stats['aprobados'] / $stats['total']) * 100, 2);
        } else {
            $stats['tasa_aprobacion'] = 0;
        }
        
        return view('control_calidad.index', compact('inspecciones', 'stats'));
    }
    
    public function create()
    {
        $producciones = Produccion::with('producto')
                        ->orderBy('created_at', 'desc')
                        ->limit(50)
                        ->get();
        
        $productos = Producto::orderBy('nombre')->get();
        
        return view('control_calidad.create', compact('producciones', 'productos'));
    }
    
    public function store(CalidadRequest $request)
    {
        try {
            $inspeccion = $this->calidadService->registrarInspeccion($request->validated());
            
            $mensaje = match($request->resultado) {
                'aprobado' => '✅ Producto aprobado correctamente',
                'rechazado' => '❌ Producto rechazado por calidad. Se ha actualizado el estado de producción.',
                'cuarentena' => '⚠️ Producto en cuarentena. Requiere revisión adicional.',
                default => 'Inspección registrada correctamente'
            };
            
            return redirect()->route('control-calidad.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    
    public function show(ControlCalidad $controlCalidad)
    {
        $controlCalidad->load(['produccion', 'producto', 'inspector']);
        return view('control_calidad.show', compact('controlCalidad'));
    }
}
