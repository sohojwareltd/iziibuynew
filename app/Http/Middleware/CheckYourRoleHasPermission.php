<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckYourRoleHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (! $user || ! $user->role) {
            abort(403, 'Permission Denied');
        }

        if (in_array($user->role->name, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Permission Denied');
    }
}
