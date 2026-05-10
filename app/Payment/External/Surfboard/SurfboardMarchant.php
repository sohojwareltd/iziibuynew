<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\PaymentMethodAccess;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class SurfboardMarchant
{
    private SurfboardPayment $surfboard;
    private PaymentMethodAccess $paymentMethodAccess;
    public function __construct(PaymentMethodAccess $paymentMethodAccess)
    {
        $this->paymentMethodAccess = $paymentMethodAccess;
        $this->surfboard = new SurfboardPayment();
    }

    public function createMarchant()
    {
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants";


        $data = [
            "country" => array_search(($this->paymentMethodAccess->country ?: $this->paymentMethodAccess->user->country)?:'Norway', Constants::COUNTRIES) ?? "SE",
            "organisation" => [
                "corporateId" => $this->paymentMethodAccess->company_registration ?: '5593726051',
                "legalName" => $this->paymentMethodAccess->company_name,
                "mccCode" => "5192",
                "address" => [
                    "careOf" => $this->paymentMethodAccess->user->fullName,
                    "addressLine1" => $this->paymentMethodAccess->address,
                    "addressLine2" => "",
                    "city" => $this->paymentMethodAccess->city,
                    "countryCode" => array_search(($this->paymentMethodAccess->country ?: $this->paymentMethodAccess->user->country)?:'Norway', Constants::COUNTRIES) ?? "SE",
                    "postalCode" => $this->paymentMethodAccess->post_code,
                ],
                "phone" => [
                    "code" => "+46",
                    "number" => $this->paymentMethodAccess->contact_phone,
                ],
                "email" => $this->paymentMethodAccess->contact_email,
            ],
            "controlFields" => [
                "store"=>[
                    "name" =>  $this->paymentMethodAccess->company_name,
                    "email" => $this->paymentMethodAccess->user->email,
                    "phoneNumber" => [
                        "code" => "+46",
                        "number" => $this->paymentMethodAccess->contact_phone,
                    ],
                    "address" => [
                        "careOf" => $this->paymentMethodAccess->user->fullName,
                        "addressLine1" => $this->paymentMethodAccess->street,
                        "addressLine2" => "",
                        "city" => $this->paymentMethodAccess->city,
                        "countryCode" => array_search(($this->paymentMethodAccess->country ?: $this->paymentMethodAccess->user->country)?:'Norway', Constants::COUNTRIES) ?? "SE",
                        "postalCode" => $this->paymentMethodAccess->post_code,
                    ],
                    "onlineInfo"=>[
                        "merchantWebshopURL" => url('/'),
                        "termsAndConditionsURL" => url('/'),
                        "privacyPolicyURL" => url('/'),
                    ]
                ],
                "redirectUrl" => route('external.contract'),
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
        ])->post($url, $data);

        return $response->json(); // Return response as JSON
    }
    public function statusCheck()
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$this->paymentMethodAccess->surfboard_application_id}/status";

        // Make the GET request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'Content-Type' => 'application/json',
        ])->get($url);

        // Return the response (can be logged or handled further)
        return $response;
    }
    public function marchantList()
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/applications";

        // Make the GET request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'Content-Type' => 'application/json',
        ])->get($url);

        // Return the response (can be logged or handled further)
        return $response->json();
    }
    public function marchantDetails(string $merchantId)
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/{$merchantId}";

        // Make the GET request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);

        // Return the response (can be logged or handled further)
        return $response->json();
    }
    public function updateMerchant(string $merchantId)
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$merchantId}";
        $data = [
            'email' => 'test@gmail.com',
            'phoneNumber' => [
                'code' => 46,
                'number' => '771890089',
            ],
            'merchantLogoUrl' => 'https://merchant.com/image',
        ];
        // Send the PUT request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->put($url, $data);  // Send the data in the PUT request

        // Return the response
        return $response;
    }
}
