<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Cart\LegacyCartInstance session(?string $instance = null)
 * @method static int getTotalQuantity()
 * @method static void clear(?string $instance = null)
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cart';
    }
}
