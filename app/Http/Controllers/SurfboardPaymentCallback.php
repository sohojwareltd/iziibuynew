<?php

namespace App\Http\Controllers;

use App\Events\PurchaseComplete;
use App\Mail\OrderConfirmed;
use App\Mail\OrderDelivered;
use App\Models\Order;
use App\Payment\Surfboard\SurfboardOrder;
use App\Services\RetailerCommission;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SurfboardPaymentCallback extends Controller
{
    public function __invoke(Request $request)
    {

        $request->validate([
            'orderId' => 'required|exists:orders,payment_id'
        ]);

        $order = Order::where('payment_id', $request->orderId)->firstOrFail();
        try {
            Log::info('Surfboard callback', [
                'order_id' => $order->id,
                'payment_id' => $order->payment_id,
            ]);

            $surfboard =  new SurfboardOrder($order);
            $response = $surfboard->getOrderStatus();

            // Check if response has 'data' key
            if (!isset($response['data']) || !is_array($response['data'])) {
                throw new Exception('Invalid API response: missing data key. Response: ' . json_encode($response));
            }

            $orderStatus = Arr::get($response, 'data.orderStatus');
            if ($orderStatus != "PAYMENT_COMPLETED" && $orderStatus != "PARTIAL_PAYMENT_COMPLETED") {
                throw new Exception('Payment failed. Order status: ' . ($orderStatus ?? 'unknown'));
            }

            $paymentIds = Arr::get($response, 'data.paymentIds');
            if (empty($paymentIds) || !isset($paymentIds[0])) {
                throw new Exception('Invalid API response: missing paymentIds');
            }

            $order->createMeta('surfboard_transaction_id', $paymentIds[0]);
            if ($order->shop->retailer_id) {
                RetailerCommission::commission_from_sales($order)->pay();
            }
            $order->status = 5;
            $captured = true;
            $order->payment_status = true;
            $order->paid_at = now();
            $order->save();
            if ($order->is_vcard) {
                $message =  "Order has been confirmed . Download details from here: <a href='" . route('order.managers-csv', $order->id) . "'>Download CSV</a>";
                if ($captured) {
                    Mail::to($order->shop->user->email)->send(new OrderConfirmed($order, $message, false));
                    Mail::to($order->shop->user->email)->later(now()->addMinutes(10), new OrderDelivered($order, $message, $captured));
                } else {
                    Mail::to($order->shop->user->email)->send(new OrderConfirmed($order, $message, $captured));
                }
            } else {
                if ($captured) {
                    Mail::to($order->shop->user->email)->send(new OrderConfirmed($order, 'Order has been confirmed', false));
                    Mail::to($order->shop->user->email)->later(now()->addMinutes(10), new OrderDelivered($order, 'Order has been confirmed', $captured));
                } else {
                    Mail::to($order->shop->user->email)->send(new OrderConfirmed($order, 'Order has been confirmed', $captured));
                }
            }

            PurchaseComplete::dispatch($order);

            $message =  'Order placed on ' . $order->created_at->format('M d, Y') . ' has been confirmed.';
            Mail::to($order->email)->send(new OrderConfirmed($order, $message, $captured));

            return redirect()->route('thankyou', ['user_name' => $order->shop->user_name, 'order' => $order]);
        } catch (Exception | Error $e) {
            Log::error('Surfboard callback processing error', [
                'order_id' => $order->id ?? null,
                'payment_id' => $order->payment_id ?? null,
                'error' => $e->getMessage(),
                'time' => now(),
            ]);
            return redirect()->route('shop.home', $order->shop->user_name)->withErrors($e->getMessage());
        }
    }
}
