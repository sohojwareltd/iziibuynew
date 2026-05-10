<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Former Voyager `admin.user` middleware: staff with legacy Admin role_id or Filament access.
 */
class EnsureUserIsAdminStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $user = auth()->user();
        if ((int) $user->role_id === \App\Models\User::ROLES['Admin']) {
            return $next($request);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin', 'web')) {
            return $next($request);
        }

        abort(403);
    }
}
