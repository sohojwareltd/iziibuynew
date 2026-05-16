<?php

use App\Providers\AppServiceProvider;
use App\Providers\CmsNavigationServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\RouteServiceProvider;

return [
    AppServiceProvider::class,
    RouteServiceProvider::class,
    AdminPanelProvider::class,
    CmsNavigationServiceProvider::class,
];
