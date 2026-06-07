<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'requested_role' => 'required|in:operario,empaquetador,auditor,analista',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'operario', // rol temporal, será reemplazado al aprobar
            'status' => 'pending',
            'requested_role' => $request->requested_role,
        ]);

        return redirect()->route('login')->with('success', 'Tu cuenta ha sido creada. Espera la aprobación del administrador.');
    }
}