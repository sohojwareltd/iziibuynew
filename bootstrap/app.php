<?php

use App\Http\Middleware\BlockAccess;
use App\Http\Middleware\CartIsEmpty;
use App\Http\Middleware\checkMaster;
use App\Http\Middleware\CheckYourRoleHasPermission;
use App\Http\Middleware\EnsureUserIsAdminStaff;
use App\Http\Middleware\EnterprisePaid;
use App\Http\Middleware\ExternalPaid;
use App\Http\Middleware\ExtractUsernameFromRoute;
use App\Http\Middleware\Localization;
use App\Http\Middleware\Paid;
use App\Http\Middleware\PaymentMethodAccessPaymentCheck;
use App\Http\Middleware\PermisssionMiddleware;
use App\Http\Middleware\PersonalClientMiddleware;
use App\Http\Middleware\PersonalTrainerMiddleware;
use App\Http\Middleware\ProtectedLink;
use App\Http\Middleware\ShopCheck;
use App\Http\Middleware\ShopValidation;
use App\Http\Middleware\SubscribedForService;
use App\Http\Middleware\VoyagerBreadPermission;
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
            ExtractUsernameFromRoute::class,
        ]);
        $middleware->web(append: [
            Localization::class,
        ]);
        $middleware->alias([
            'admin.user' => EnsureUserIsAdminStaff::class,
            'role' => CheckYourRoleHasPermission::class,
            'permission' => PermisssionMiddleware::class,
            'shopCheck' => ShopCheck::class,
            'canProvideService' => SubscribedForService::class,
            'isPersonalTrainer' => PersonalTrainerMiddleware::class,
            'personalClient' => PersonalClientMiddleware::class,
            'Paid' => Paid::class,
            'ExternalPaid' => ExternalPaid::class,
            'EnterprisePaid' => EnterprisePaid::class,
            'cartIsntEmpty' => CartIsEmpty::class,
            'checkShop' => ShopValidation::class,
            'checkMaster' => checkMaster::class,
            'checkIfPaid' => PaymentMethodAccessPaymentCheck::class,
            'protectedLink' => ProtectedLink::class,
            'BlockAccess' => BlockAccess::class,
            'voyager.permission' => VoyagerBreadPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
