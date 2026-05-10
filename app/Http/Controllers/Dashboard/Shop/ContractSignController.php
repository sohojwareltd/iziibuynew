<?php

namespace App\Http\Controllers\Dashboard\Shop;

use Iziibuy;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Mail\paymentCapture;
use App\Models\ProtectedLink;
use App\Payment\Surfboard\SurfboardMarchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ContractSignController extends Controller
{
    public function selectPaymentMethods()
    {
        return  view('dashboard.shop.complete-signup');
    }

    public function assignGatewayByPaymentMethods(Request $request)
    {
        $shop = auth()->user()->shop;
        $selectedMethods = $request->input('paymentMethods', []);

        $gateways = [];
        $paymentMethod = [];
        //elavon
        if (Iziibuy::isSubset(['mastercard', 'visa', 'amex'], $selectedMethods)) {
            $gateways['elavon'] = 0;
            array_push($paymentMethod, 'elavon');
        };

        //wallet onboarding (legacy shops stay on surfboard; all others go to dintero)
        if (Iziibuy::isSubset(['googlepay', 'applepay', 'vipps'], $selectedMethods)) {
            $walletGateway = $this->isLegacySurfboardShop($shop) ? 'surfboard' : 'dintero';
            $gateways[$walletGateway] = 0;
            array_push($paymentMethod, $walletGateway);
        };

        $existingGatewayContracts = json_decode((string) $shop->gateway_contract_signed, true) ?? [];
        $gatewayContracts = array_merge($existingGatewayContracts, $gateways);

        $shop->createMetas([
            'gateway_contract_signed' => $gatewayContracts,
            'selected_payment_methods' => $selectedMethods,
        ]);

        $shop->update([
            'paymentMethod' => implode(',', $paymentMethod),
            'contract_signed' => 1
        ]);
        return redirect()->route('shop.complete.signup');
    }

    private function isLegacySurfboardShop(Shop $shop): bool
    {
        return in_array('surfboard', explode(',', (string) $shop->paymentMethod), true);
    }



    public function setup_surfboard_payment()
    {
        $shop = auth()->user()->shop;


        $createMarchant = (new SurfboardMarchant($shop))->createMarchant();

        if ($createMarchant['status'] == "SUCCESS") {

            $shop->createMetas([
                'surfboard_webKybUrl' => $createMarchant['data']['webKybUrl'],
                'surfboard_application_id' => $createMarchant['data']['applicationId'],
                'surfboard_application_status' => @$createMarchant['data']['applicationStatus'] ?? false
            ]);


            $contracts['surfboard'] = 1;

            $shop->createMetas(['gateway_contract_signed' => $contracts]);
            return redirect($createMarchant['data']['webKybUrl']);
        } else {

            return redirect()->route('shop.completeProfile')->withErrors($createMarchant['message']);
        }
    }


    public function setup_elavon_payment()
    {
        $shop = auth()->user()->getShop();
        if (in_array('elavon', explode(',', $shop->paymentMethod))  && $shop->elavon_payment_setup == true || $shop->elavon_details_verified_by_shop == true) return redirect()->route('shop.dashboard');
        return view('dashboard.shop.payments.elavon_setup');
    }

    public function store_setup_elavon_payment(Request $request)
    {

        $shop = auth()->user()->shop;

        if (in_array('elavon', explode(',', $shop->paymentMethod)) && $shop->elavon_payment_setup == true || $shop->elavon_details_verified_by_shop == true) return redirect()->route('shop.dashboard');

        $imageData = $request->input('signature');
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $imageData = base64_decode($imageData);
        $filename = 'signature/signature_' . uniqid() . '.png';

        Storage::disk('s3')->put($filename, $imageData);
        $meta = $request->meta;
        $meta['customer_profile'] = json_encode($meta['customer_profile']);
        $meta['authrized'] = json_encode($meta['authrized']);
        $meta['financial'] = json_encode($meta['financial']);
        $meta['report'] = json_encode($meta['report']);
        $meta['customerDetails'] = json_encode($meta['customerDetails']);
        $meta['trading'] = json_encode($meta['trading']);
        $meta['partner'] = json_encode($meta['partner']);
        $meta['productId'] = json_encode($meta['productId']);

        $meta['signature'] = $filename;
        $meta['ip_address'] = $request->ip();
        $meta['date'] = now();
        $shop->createMetas($meta);

        $protectedLink = ProtectedLink::updateOrCreate(['link' => route('view_payment_data', ['id' => $shop->id, 'type' => 'shop'])], [
            'link' => route('view_payment_data', ['id' => $shop->id, 'type' => 'shop']),
            'uid' => uniqid(),
            'password' => uniqid()
        ]);

        $shop->createMeta('elavon_details_verified_by_shop', false);

        $viewLink = route('view_payment_data', ['id' => $shop->id, 'uid' => $protectedLink->uid, 'password' => $protectedLink->password, 'type' => 'shop']);
        $contactMail = $request->meta['contact_email'];
        Mail::to($contactMail)->send(new paymentCapture($shop, $viewLink));

        return redirect()->route('shop.dashboard')->with('success', 'Your details have been successfully submitted. Please check your email for confirmation . Thank you .');
    }
}
