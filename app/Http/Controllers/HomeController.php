<?php

namespace App\Http\Controllers;

use App\Mail\ExternalBookingReceiptMail;
use App\Mail\NotificationEmail;
use App\Mail\OrderConfirmed;
use App\Mail\OrderPlaced;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\ExternalBooking;
use App\Models\Order;
use App\Models\Page;
use App\Models\Post;
use App\Models\Shop as ShopModel;
use App\Models\User;
use App\Payment\Two\TwoPayment;
use App\Services\SMS\SmsService;
use Error;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Spatie\Newsletter\Facades\Newsletter;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    // public function index()
    // {

    // $role = Auth::user()->role_id;
    // if ($role == 1) {
    // return redirect(filament_panel_url());
    // } elseif ($role == 2) {
    // if (session()->has('user_name')) {
    // return redirect(route('shop.home', ['user_name' => session('user_name')]));
    // }
    // return redirect(route('home'));
    // } elseif ($role == 3) {
    // return redirect(route('shop.dashboard'));
    // } elseif ($role == 4) {
    // return redirect(route('manager.dashboard'));
    // } elseif ($role == 5) {
    // return redirect(route('retailer.dashboard'));
    // } else {
    // return redirect(route('home'));
    // }
    // }
    public function contact()
    {
        $a = rand(1, 10);
        $b = rand(1, 10);
        session()->put('captcha', $a.'+'.$b);
        session()->put('captcha_result', $a + $b);

        return view('contact');
    }

    public function about()
    {
        return view('about');
    }

    public function faqs()
    {
        $post = Post::get();

        return view('faqs');
    }

    public function thankyou()
    {
        if (request()->order) {
            $order = Order::find(request()->order);
            if ($order->is_company && ! $order->status) {
                (new TwoPayment($order->shop, $order))->confirm();
            }
            $shop = $order->shop;
        } else {
            $order = new Order;
            $shop = new ShopModel;
        }

        return view('thankyou', compact('shop', 'order'));
    }

    public function send_order_notification(Request $request)
    {
        $request->validate([
            'email' => ['required'],
        ]);
        if ($request->order) {
            $order = Order::find($request->order);
            $message = 'Order placed on '.$order->created_at->format('M d, Y').' has been confirmed.';

            if ($order->create_a_account) {
                $mail_data = [
                    'subject' => __('words.signup_from_thankyou_subject'),
                    'body' => __('words.signup_from_thankyou_body'),
                    'button_link' => route('register', ['first_name' => $order->first_name, 'last_name' => $order->last_name, 'email' => $request->email]),
                    'button_text' => __('words.signup_from_thankyou_button'),
                    'emails' => [],
                ];
                $order->createMeta('email', $request->email);
                Mail::to($request->email)->send(new NotificationEmail($mail_data));
            }
            Mail::to($request->email)->send(new OrderConfirmed($order, $message));

            return redirect(route('thankyou', ['user_name' => $order->shop->user_name]))->with('success_msg', 'Email send successfully');
        }
        if ($request->booking) {
            $booking = Booking::find($request->booking);
            $message = 'Booking placed on '.$booking->created_at->format('M d, Y').' has been confirmed.';
            Mail::to($request->email)->send(new OrderConfirmed($booking, $message));
            Mail::to($booking->shop->email)->send(new OrderConfirmed($booking, $message));

            return redirect(route('thankyou', ['user_name' => $booking->shop->user_name]))->with('success_msg', 'Email send successfully');
        }
    }

    public function send_notification(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:external_bookings,id',
            ]);

            $externalBooking = ExternalBooking::findOrFail($request->order_id);

            // Handle email notification
            if ($request->has('email') && $request->email) {
                $request->validate([
                    'email' => 'required|email',
                ]);

                // Send email receipt
                Mail::to($request->email)->send(new ExternalBookingReceiptMail($externalBooking));

                return response()->json([
                    'success' => true,
                    'message' => 'Receipt sent to email successfully!',
                ]);
            }

            // Handle SMS notification
            if ($request->has('phone') && $request->phone) {
                $request->validate([
                    'phone' => 'required',
                ]);

                // Create SMS message content similar to the email receipt
                $message = "Payment Receipt\n";
                $message .= "Order ID: #{$externalBooking->booking_number}\n";
                $message .= 'Total: '.\Iziibuy::price($externalBooking->total)."\n";
                $message .= 'Payment Method: '.strtoupper($externalBooking->payment_method)."\n";
                $message .= "Company: {$externalBooking->paymentMethodAccess->company_name}\n";
                $message .= "Paid: {$externalBooking->paid_at->format('d/m/Y h:i A')}\n";
                $message .= 'Thank you for your payment!';

                // Send SMS
                $sms = new SmsService;
                $sms->send($request->phone, $message);

                return response()->json([
                    'success' => true,
                    'message' => 'Receipt sent to phone successfully!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Please provide either email or phone number',
            ], 400);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: '.$e->getMessage(),
            ], 500);
        }
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:40'],
            'email' => ['required', 'max:100', 'email'],
            'subject' => ['required', 'max:100'],
            'message' => ['required', 'max:2000'],
            'captcha' => 'required',
        ]);
        try {

            if ($request->captcha != session()->get('captcha_result')) {
                throw new Exception('Captcha Failed');
            }

            Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            $mail_data = [
                'subject' => $request->subject,
                'body' => $request->message.'<br> From:'.$request->name.' '.$request->email,
                'button_link' => '',
                'button_text' => 'Visit',
                'emails' => [],
            ];

            session()->forget('captcha');
            session()->forget('captcha_result');

            Mail::to(setting('site.email'))->send(new NotificationEmail($mail_data));

            return redirect()->back()->with('success', 'Message sent successfully');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        } catch (Error $e) {
            return redirect()->back()->withErrors($e->getMessage());

        }
    }

    public function newsletter(Request $request)
    {
        $sub = Newsletter::isSubscribed($request->email);

        if ($sub) {
            return redirect()->back()->with('success_msg', 'You already subscribed');
        } else {
            Newsletter::subscribe($request->email, listName: 'subscribers');
        }

        return redirect()->back()->with('success_msg', 'You Subscribed');
    }

    public function posts($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'PUBLISHED')->first();
        if (! $post) {
            abort(404);
        }

        return view('posts', compact('post'));
    }

    public function pages($slug)
    {
        $post = Page::where('slug', $slug)->where('status', 'ACTIVE')->first();
        if (! $post) {
            abort(404);
        }

        return view('posts', compact('post'));
    }

    public function resent_order_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $order = Order::find($request->order);
        Mail::to($request->email)->send(new OrderPlaced($order, 'A new order has been placed'));

        return redirect()->back()->with('success', 'Order email sent successfully');
    }
}
