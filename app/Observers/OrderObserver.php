<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Coupon;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        if ($order->isDirty('payment_status') && $order->payment_status == 1 && $order->discount_code) {
            $coupon = Coupon::where('code', $order->discount_code)->first();
            if ($coupon) {
                $coupon->increment('used');
            }
        }
    }
} 