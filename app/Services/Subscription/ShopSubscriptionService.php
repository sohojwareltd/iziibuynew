<?php

namespace App\Services\Subscription;

use App\Models\Shop;

class ShopSubscriptionService
{
    use ElavonSubscriptionServiceTrait;

    protected Shop $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public static function createSubscription(Shop $shop): string
    {
        return (new self($shop))->subscibe();
    }

    public static function confirmSubscription(Shop $shop)
    {
        return (new self($shop))->confirm();
    }

    protected function subscibe(): string
    {
        return $this->createSubscriptionWithElavon();
    }

    protected function confirm()
    {
        return $this->confirmSubscriptionWithElavon();
    }
}
