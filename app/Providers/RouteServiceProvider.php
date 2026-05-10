<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public const Admin = '/admin';

    public const Vendor = '/my-shop-dashboard';

    public const Manager = '/my-manager-dashboard';

    public const Retailer = '/my-retailer-dashboard';

    public const External = '/my-external-dashboard';

    public const Eneterprise = '/my-enterprise-dashboard';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api/iziipay')
                ->group(base_path('routes/api/iziipay.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin/web.php'));

            Route::middleware(['web', 'auth', 'role:admin,user,vendor,manager,retailer'])
                ->prefix('shop/{user_name}/my-dashboard')
                ->as('user.')
                ->group(base_path('routes/dashboard/user/web.php'));

            Route::middleware(['web', 'auth', 'role:vendor'])
                ->prefix('my-shop-dashboard')
                ->as('shop.')
                ->group(base_path('routes/dashboard/shop/web.php'));

            Route::middleware(['web', 'auth', 'role:external'])
                ->prefix('my-external-dashboard')
                ->as('external.')
                ->group(base_path('routes/dashboard/external/web.php'));

            Route::middleware(['web', 'auth', 'role:enterprise'])
                ->prefix('my-enterprise-dashboard')
                ->as('enterprise.')
                ->group(base_path('routes/dashboard/enterprise/web.php'));

            Route::middleware(['web', 'auth', 'role:manager,vendor'])
                ->prefix('my-manager-dashboard')
                ->as('manager.')
                ->group(base_path('routes/dashboard/manager/web.php'));

            Route::middleware(['web', 'auth', 'role:retailer'])
                ->prefix('my-retailer-dashboard')
                ->as('retailer.')
                ->group(base_path('routes/dashboard/retailer/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/shop/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('payment-api', function (Request $request) {
            return [
                Limit::perMinute(6000)->by($request->ip()),
                Limit::perDay(100000)->by($request->ip()),
            ];
        });
    }
}
