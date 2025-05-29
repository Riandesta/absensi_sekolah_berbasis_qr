<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HasAllRoles
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        foreach ($roles as $role) {
            if (!$user || !$user->hasRole($role)) {
                abort(403, 'Access denied');
            }
        }

        return $next($request);
    }
}
