<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\VoyagerPermissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage: Route::middleware('voyager.permission:browse,posts')
 */
class VoyagerBreadPermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $actionAndSlug): Response
    {
        $parts = array_map('trim', explode(',', $actionAndSlug, 2));
        if (count($parts) < 2) {
            abort(500, 'voyager.permission middleware expects "action,slug" (e.g. browse,posts).');
        }

        [$action, $slug] = $parts;
        VoyagerPermissions::authorize($request->user(), $action, $slug);

        return $next($request);
    }
}
