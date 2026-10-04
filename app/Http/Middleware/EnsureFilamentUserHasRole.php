<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureFilamentUserHasRole
{
    /**
     * Restrict Filament panel access to authorized school admin users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user || (! $user->hasRole('super-admin') && $user->getAllPermissions()->isEmpty())) {
            abort(403);
        }

        return $next($request);
    }
}
