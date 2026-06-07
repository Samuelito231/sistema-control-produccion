<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->withErrors('Tu cuenta está pendiente de aprobación o ha sido suspendida. Contacta al administrador.');
        }
        return $next($request);
    }
}