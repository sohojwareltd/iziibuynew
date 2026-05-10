<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([
            \App\Http\Middleware\ExtractUsernameFromRoute::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
        ]);
        $middleware->alias([
            'admin.user' => \App\Http\Middleware\EnsureUserIsAdminStaff::class,
            'role' => \App\Http\Middleware\CheckYourRoleHasPermission::class,
            'permission' => \App\Http\Middleware\PermisssionMiddleware::class,
            'shopCheck' => \App\Http\Middleware\ShopCheck::class,
            'canProvideService' => \App\Http\Middleware\SubscribedForService::class,
            'isPersonalTrainer' => \App\Http\Middleware\PersonalTrainerMiddleware::class,
            'personalClient' => \App\Http\Middleware\PersonalClientMiddleware::class,
            'Paid' => \App\Http\Middleware\Paid::class,
            'ExternalPaid' => \App\Http\Middleware\ExternalPaid::class,
            'EnterprisePaid' => \App\Http\Middleware\EnterprisePaid::class,
            'cartIsntEmpty' => \App\Http\Middleware\CartIsEmpty::class,
            'checkShop' => \App\Http\Middleware\ShopValidation::class,
            'checkMaster' => \App\Http\Middleware\checkMaster::class,
            'checkIfPaid' => \App\Http\Middleware\PaymentMethodAccessPaymentCheck::class,
            'protectedLink' => \App\Http\Middleware\ProtectedLink::class,
            'BlockAccess' => \App\Http\Middleware\BlockAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
