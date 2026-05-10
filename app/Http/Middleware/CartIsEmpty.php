<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Cart;
use Illuminate\Http\Request;

class CartIsEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userName = $request->route('user_name') ?? $request->input('user_name');
        $qty = is_string($userName) && $userName !== ''
            ? Cart::session($userName)->getTotalQuantity()
            : Cart::getTotalQuantity();

        if ($qty > 0) {
            return $next($request);
        } else {
            return abort(403, 'You do not have anyting on cart');
        }
    }
}
