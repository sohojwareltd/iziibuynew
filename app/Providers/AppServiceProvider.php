<?php

namespace App\Providers;

use App\Cart\LegacyCartManager;
use App\Enterprise\Permissions;
use App\Facades\Cart as CartFacade;
use App\Facades\Iziibuy;
use App\Facades\IziibuyFacades;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Support\Voyager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('iziibuy', fn () => new Iziibuy);
        $this->app->singleton('cart', fn () => new LegacyCartManager);

        $this->app->bind('permission', fn () => new Permissions);
    }

    public function boot(): void
    {
        if (! class_exists('Iziibuy', false)) {
            class_alias(IziibuyFacades::class, 'Iziibuy');
        }

        if (! class_exists('Cart', false)) {
            class_alias(CartFacade::class, 'Cart');
        }

        if (! class_exists('Voyager', false)) {
            class_alias(Voyager::class, 'Voyager');
        }

        if (! class_exists('Permission', false)) {
            class_alias(Permissions::class, 'Permission');
        }

        Order::observe(OrderObserver::class);

        Blade::if('vendor', function () {
            return Auth::check() && Auth::user()->role_id == 3;
        });

        Blade::if('AddService', function () {
            return true;
        });

        Blade::if('permission', function ($feature, $action) {
            return Permissions::check($feature, $action);
        });

        Paginator::useBootstrap();

        Validator::excludeUnvalidatedArrayKeys();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Blade::if('personalTrainer', function () {
            return Auth::user()->role->name === 'manager' && Auth::user()->trainee == '1';
        });

        Blade::if('CanProvideService', function ($shop) {
            return $shop->can_provide_service;
        });

        Blade::if('HasTrainer', function ($shop) {
            return auth()->check()
                && ! empty(auth()->user()->trainer($shop)->id)
                && ! empty($shop->defaultoption);
        });

        Blade::if('HasSubscription', function ($shop) {
            return $shop->boxes()->count();
        });

        Blade::if('Dev', function () {
            return env('MODE') == 'dev';
        });

        Blade::if('Menu', function ($bool) {
            return $bool;
        });
    }
}
