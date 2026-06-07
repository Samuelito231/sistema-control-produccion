<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->role === 'admin') {
                return true;
            }
        });

        Gate::define('manage-products', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('register-waste', function ($user) {
            return in_array($user->role, ['admin', 'operario']);
        });

        Gate::define('view-audit', function ($user) {
            return in_array($user->role, ['admin', 'auditor', 'operario', 'analista', 'empaquetador']);
        });
    }
}