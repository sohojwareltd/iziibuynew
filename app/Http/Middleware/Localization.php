<?php

namespace App\Http\Middleware;

use App\Constants\Constants;
use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Constants::LANGUAGES['status']) {
            return $next($request);
        }

        /** @var list<string> $allowed */
        $allowed = array_values(Constants::LANGUAGES['list']);

        if ($request->filled('lang')) {
            $candidate = (string) $request->query('lang');
            if (in_array($candidate, $allowed, true)) {
                session()->put('lang', $candidate);
                app()->setLocale($candidate);

                return $next($request);
            }
        }

        $sessionLang = session()->get('lang');
        if (is_string($sessionLang) && in_array($sessionLang, $allowed, true)) {
            app()->setLocale($sessionLang);

            return $next($request);
        }

        if ($request->user()?->default_language) {
            $userLang = $request->user()->default_language;
            app()->setLocale(in_array($userLang, $allowed, true) ? $userLang : Constants::LANGUAGES['default']);

            return $next($request);
        }

        $userName = $request->route('user_name') ?? $request->input('user_name');
        if (is_string($userName) && $userName !== '') {
            $shop = Cache::remember('shop-'.$userName, 900, fn (): ?Shop => Shop::query()->where('user_name', $userName)->first());
            if (! $shop) {
                abort(404);
            }
            $shopLang = ($shop->default_language && in_array($shop->default_language, $allowed, true))
                ? $shop->default_language
                : Constants::LANGUAGES['default'];
            app()->setLocale($shopLang);

            return $next($request);
        }

        app()->setLocale(Constants::LANGUAGES['default']);

        return $next($request);
    }
}
