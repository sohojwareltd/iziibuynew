<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Shop;
use App\Facades\Cart;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
	public function add(request $request)
	{
		$shop = Shop::where('user_name', request('user_name'))->first();
		$coupon = Coupon::where('code', $request->coupon_code)
			// ->where('shop_id', $shop->id)
			->first();

		if (!$coupon) {
			return back()->withErrors('Incorrect coupon code');
		}
		if (Carbon::create($coupon->expire_at) < now()) {
			return back()->withErrors('Coupon has been expired');
		}
		if ($coupon->limit <= $coupon->used) {
			return back()->withErrors('Coupon has been expired');
		}
		if (Cart::session(request('user_name'))->getSubTotal() < $coupon->minimum_cart) {
			return back()->withErrors('Minimum cart required to use this coupon ' . $coupon->minimum_cart);
		}
		Session::put('discount_' . request('user_name'), $coupon->discount);
		Session::put('discount_code_' . request('user_name'), $coupon->code);
		

		return back()->with('success_msg_cart', 'Coupon has been applied successfully');
	}
	public function destroy()
	{
		session()->forget('discount');
		session()->forget('discount_code');
		session()->forget('discount_shop');
		session()->forget('discount_code_shop');
		return back()->with('success_msg', 'Coupon removed successfully');
	}
	public function shopCoupon(Request $request)
	{
		$request->validate([
			'coupon_code' => ['required', 'max:30', 'string']
		]);
		$coupon = Coupon::where('code', $request->coupon_code)->where('shop_id', null)->first();
		if ($coupon) {
			Session::put('discount_shop', $coupon->discount);
			Session::put('discount_code_shop', $coupon->code);
			return back()->with('success_msg', 'Coupon has been applied successfully');
		}
		return back()->withErrors('Coupon Not found');
	}
}
