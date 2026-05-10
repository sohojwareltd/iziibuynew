<?php

namespace App\Services\Contract;

use App\Mail\NotificationEmail;
use App\Models\Shop;
use App\Payment\Surfboard\SurfboardMarchant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Iziibuy;
class Contract
{

    protected Shop $shop;
    protected array $paymentMethods;
    protected array $gateways;

    protected bool $elavon = false;
    protected bool $quickpay = false;
    protected bool $surfboard = false;

    protected array $response;



    public function __construct($shop)
    {

        $this->shop = $shop;
        $this->paymentMethods = json_decode($shop->selected_payment_methods, true);

        // $this->quickpay = isSubset(['googlepay', 'applepay', 'vipps'], $this->paymentMethods);
        $this->elavon = Iziibuy::isSubset(['mastercard', 'visa', 'amex'], $this->paymentMethods);
        $this->surfboard = Iziibuy::isSubset(['googlepay', 'applepay', 'vipps'], $this->paymentMethods);
    }



    public function sign()
    {
        if ($this->surfboard) $this->signSurfboardContract();
        if ($this->quickpay) $this->signQuickPayContract();
        if ($this->elavon) $this->signElavonContract();
    }



    public function getGateWays(): array
    {

        if ($this->elavon) $this->gateways[] = 'elavon';
        if ($this->surfboard) $this->gateways[] = 'surfboard';
        if ($this->quickpay) $this->gateways[] = 'quickpay';

        return $this->gateways;
    }

    public function paymentMethods(): array
    {
        return $this->paymentMethods;
    }

    private function signElavonContract() {}
    private function signSurfboardContract()
    {

        $createMarchant = (new SurfboardMarchant($this->shop))->createMarchant();

        if ($createMarchant['status'] == "SUCCESS") {
            $this->shop->createMetas([
                'surfboard_webKybUrl' => $createMarchant['data']['webKybUrl'],
                'surfboard_application_id' => $createMarchant['data']['applicationId'],
                'surfboard_application_status' => @$createMarchant['data']['applicationStatus'] ?? false
            ]);
            $this->response['surfboard'] = $createMarchant;
        }
        $this->response['surfboard'] = $createMarchant;
    }


    private function signQuickPayContract()
    {
        $subject = sprintf("%s has submited payment methods", $this->shop->name);

        $message  = sprintf(
            "Company name: %s <br> Org number: %s <br> Company email: %s <br> Contract person: %s <br> Phone: %s <br> <b> Selected payment methods: </b> %s",
            $this->shop->company_name,
            $this->shop->company_registration,
            $this->shop->user->email,
            $this->shop->user->name . ' ' . $this->shop->user->last_name,
            $this->shop->user->phone,
            implode(" <br> ", array_map(fn($item) => ucfirst($item), $this->paymentMethods))
        );

        $mail_data = [
            'subject' => $subject,
            'body' => $message,
            'button_link' => route('shop.home', ['user_name' => $this->shop->user_name]),
            'button_text' => 'View Shop',
            'emails' => [],
        ];


        Mail::to(setting('site.email'))->cc('no-reply@iziimail.com')->send(new NotificationEmail($mail_data));

        Http::post('https://hooks.zapier.com/hooks/catch/2912165/3mz0sl1/', [
            'shop_id' => $this->shop->id,
            'first_name' => $this->shop->user->name,
            'last_name' => $this->shop->user->last_name,
            'shop_name' => $this->shop->name,
            'title' => $this->shop->title,
            'company_name' => $this->shop->company_name,
            'company_url' =>  $this->shop->company_url ? $this->shop->company_url : route('shop.home', $this->shop->user_name),
            'company_registration' => $this->shop->company_registration,
            'email' => $this->shop->user->email,
            'phone' => $this->shop->user->phone,
            'city' => $this->shop->city,
            'street' => $this->shop->street,
            'post_code' => $this->shop->post_code,
            'visa' => in_array('visa', $this->paymentMethods),
            'mastercard' => in_array('mastercard', $this->paymentMethods),
            'vipps' => in_array('vipps', $this->paymentMethods)
        ]);
    }
}
