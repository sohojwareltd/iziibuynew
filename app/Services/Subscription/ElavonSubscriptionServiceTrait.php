<?php

namespace App\Services\Subscription;

use App\Mail\ShopInvoice;
use App\Models\Charge;
use App\Payment\Elavon\ElavonShopSubscription;
use App\Services\RetailerCommission;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Iziibuy;
use Illuminate\Support\Facades\Log;
trait ElavonSubscriptionServiceTrait
{
    protected function createSubscriptionWithElavon()
    {
        $subscription = (new ElavonShopSubscription($this->shop))->createSubscription();

        if ($subscription['status'] !== true) {
            throw new Exception($subscription['data']['message'] ?? 'Subscription failed');
        }

        if (! empty($subscription['data']['requires_hpp'])) {
            $this->shop->payment_url = $subscription['data']['url'];
            $this->shop->save();

            return $subscription['data']['url'];
        }

        $this->shop->shopperId = $subscription['data']['shopperId'];
        $this->shop->subscription_id = $subscription['data']['cardId'];
        $this->shop->save();

        return route('shop.confirm.subscription', $subscription['data']['cardId']);
    }

    protected function confirmSubscriptionWithElavon()
    {

        $subscription = (new ElavonShopSubscription($this->shop))->getSubscription();

        if ($subscription->isSuccess()) {

            if ($this->shop->status == 1) {
                return redirect(route('shop.complete.signup'))->with('Thank your for subscribe');
            }
            if ($this->shop->paid_at) {
                if ($this->shop->paid_at->isSameMonth(today())) {
                    $this->shop->status = 1;
                    $this->shop->establishment = 1;

                    return redirect(route('shop.dashboard'))->with('Thank your for subscribe');
                }
            }

            $amount = Iziibuy::round_num($this->shop->subscriptionFee());

            $charge_status = (new ElavonShopSubscription($this->shop))->chargeViaCard($amount);

            if ($charge_status['status'] && ($charge_status['data']['status'] ?? false)) {
try {
                Mail::to($this->shop->user->email)->send(new ShopInvoice($this->shop));
            } catch (Exception $e) {
                Log::error('Error sending shop invoice: ' . $e->getMessage());
            }
                Charge::create([
                    'shop_id' => $this->shop->id,
                    'order_id' => $charge_status['data']['id'],
                    'amount' => $amount,
                    'status' => true,
                    'comment' => 'subscription fee',
                    'details' => json_encode($this->shop->subscriptionFeeDetails()),
                ]);
                $this->shop->status = 1;
                $this->shop->establishment = 1;
                $this->shop->paid_at = Carbon::now();
                if ($this->shop->retailer_id) {
                    RetailerCommission::one_time_pay_out($this->shop)->pay();
                    RetailerCommission::commission_from_recurring_payments($this->shop)->pay();
                }
                $this->shop->save();

                return redirect(route('shop.complete.signup'))->with('Thank your for subscribe');
            }

            $detail = trim((string) ($charge_status['data']['message'] ?? ''));
            if ($detail === '' && ($charge_status['status'] ?? false) && ! ($charge_status['data']['status'] ?? false)) {
                $detail = 'Payment was not approved. Please try again or use another payment method.';
            }

            return redirect(route('shop.subscription.payment'))->withErrors(
                $detail !== '' ? $detail : 'There is a problem with your Payment method. Please try again later'
            );
        }

        return redirect(route('shop.subscription.payment'))->withErrors('There is a problem with your Payment method. Please try again later');
    }
}
