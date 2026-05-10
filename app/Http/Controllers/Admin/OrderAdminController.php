<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NotificationEmail;
use App\Models\Shop;
use App\Payment\Two\TwoPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use Error;

class OrderAdminController extends Controller
{
    public function refundView(Order $order): \Illuminate\Contracts\View\View
    {
        return view('vendor.voyager.orders.refund', compact('order'));
    }

    public function refund(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'amount' => 'required|max:'.$order->maxRefund(),
            'reason' => 'nullable|string',
        ]);
        try {
            (new TwoPayment($order->shop, $order))->refund($request->amount, $request->reason);

            return redirect()->back()->with([
                'message' => 'Refund done',
                'alert-type' => 'success',
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        } catch (Error $e) {
            return redirect()->back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
