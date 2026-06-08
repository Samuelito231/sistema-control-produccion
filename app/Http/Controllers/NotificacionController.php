<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Helpers\NotificacionHelper;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Alerta::where('usuario_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(30);
            
        return view('notificaciones.index', compact('notificaciones'));
    }
    
    public function marcar($id)
    {
        NotificacionHelper::marcarComoLeida($id);
        return response()->json(['success' => true]);
    }
    
    public function marcarTodas()
    {
        Alerta::where('usuario_id', auth()->id())
            ->where('leida', false)
            ->update(['leida' => true]);
            
        return response()->json(['success' => true]);
    }
}