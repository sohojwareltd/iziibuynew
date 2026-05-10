<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Payment\Surfboard\SurfboardOrder;
use App\Payment\Two\TwoPayment;
use Error;
use Exception;
use Illuminate\Http\Request;

class TwoPaymentController extends Controller
{
    public function refundView(Order $order)
    {
        return view('auth.shop.dashboard.order.refund', compact('order'));
    }
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|max:' . $order->maxRefund(),
            'reason' => 'nullable|string'
        ]);
        try {
            (new TwoPayment($order->shop, $order))->refund($request->amount, $request->reason);
            return redirect()->back()->with('success', 'Refund done');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        } catch (Error $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    public function orderCancel(Order $order)
    {
        try {

            if ($order->payment_method == 'two') {
                if ($order->two_payment_status == 1 && $order->status != 2) {
                    $res = (new TwoPayment($order->shop, $order))->cancel();
                } else {
                    throw new Exception('Order cant be canceld');
                }
            } elseif ($order->payment_method == 'surfboard') {
                $res = (new SurfboardOrder($order))->makeVoid();
                if ($res['status'] == 'SUCCESS') {
                    $order->status = 2;
                    $order->save();
                } else {
                    throw new Exception($res['message']);
                }
            }
            return redirect()->back()->with([
                'message'    => 'Order canceled',
                'alert-type' => 'success',
            ]);
        } catch (Exception | Error $e) {
            return redirect()->back()->with([
                'message'    => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function orderFulfilled(Order $order)
    {
        try {
            if ($order->two_payment_status == 1) {
                $res = (new TwoPayment($order))->fulfilled();
                return redirect()->back()->with('success', 'Order is fulfilled');
            } else {
                throw new Exception('Order cant be fulfiled');
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        } catch (Error $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
