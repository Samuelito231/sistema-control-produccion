<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // El constructor con middleware no es necesario porque las rutas ya lo protegen con 'role:admin'
    // Si lo prefieres, puedes dejarlo comentado o eliminarlo.

   public function index(Request $request)
{
    $query = User::query();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    $usuarios = $query->latest()->paginate(20);
    return view('admin.usuarios.index', compact('usuarios'));
}
    public function approveForm(User $user)
    {
        if ($user->status !== 'pending') {
            return redirect()->route('admin.usuarios')->with('error', 'Este usuario ya fue procesado.');
        }
        return view('admin.usuarios.approve', compact('user'));
    }

    public function approve(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,operario,auditor,empaquetador,analista',
        ]);

        $user->update([
            'role' => $request->role,
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        AuditHelper::log('approve_user', $user, null, [
            'assigned_role' => $request->role,
            'previous_status' => 'pending',
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario aprobado correctamente.');
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user->update([
            'status' => 'suspended',
            'rejection_reason' => $request->reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditHelper::log('reject_user', $user, null, ['reason' => $request->reason]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario rechazado.');
    }

    public function toggleSuspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes suspender tu propia cuenta.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $accion = $newStatus === 'suspended' ? 'suspend_user' : 'activate_user';
        $mensaje = $newStatus === 'suspended' ? 'suspendido' : 'activado';

        AuditHelper::log($accion, $user, null, []);

        return back()->with('success', "Usuario {$mensaje} correctamente.");
    }

    public function historial(User $user)
    {
        $logs = \App\Models\AuditLog::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('admin.usuarios.historial', compact('user', 'logs'));
    }
}