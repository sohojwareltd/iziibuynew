<?php

namespace App\Payment\Surfboard;

use App\Constants\Constants;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class SurfboardMarchant
{
    private SurfboardPayment $surfboard;
    private Shop $shop;
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->surfboard = new SurfboardPayment();
    }

    public function createMarchant()
    {
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants";


        $data = [
            "country" => array_search($this->shop->country ?: $this->shop->user->country, Constants::COUNTRIES) ?? "SE",
            "organisation" => [
                "corporateId" => $this->shop->company_registration ?: '5593726051',
                "legalName" => $this->shop->company_name,
                "mccCode" => "5192",
                "address" => [
                    "careOf" => $this->shop->user->fullName,
                    "addressLine1" => $this->shop->address,
                    "addressLine2" => "",
                    "city" => $this->shop->city,
                    "countryCode" => array_search($this->shop->country ?: $this->shop->user->country, Constants::COUNTRIES) ?? "SE",
                    "postalCode" => $this->shop->post_code,
                ],
                "phone" => [
                    "code" => "+46",
                    "number" => $this->shop->contact_phone,
                ],
                "email" => $this->shop->contact_email,
            ],
            "controlFields" => [
                "store"=>[
                    "name" =>  $this->shop->company_name,
                    "email" => $this->shop->user->email,
                    "phoneNumber" => [
                        "code" => "+46",
                        "number" => $this->shop->contact_phone,
                    ],
                    "address" => [
                        "careOf" => $this->shop->user->fullName,
                        "addressLine1" => $this->shop->street,
                        "addressLine2" => "",
                        "city" => $this->shop->city,
                        "countryCode" => array_search($this->shop->country ?: $this->shop->user->country, Constants::COUNTRIES) ?? "SE",
                        "postalCode" => $this->shop->post_code,
                    ],
                    "onlineInfo"=>[
                        "merchantWebshopURL" => route('shop.home',['user_name'=>$this->shop->user_name]),
                        "termsAndConditionsURL" => route('shop.home',['user_name'=>$this->shop->user_name]),
                        "privacyPolicyURL" => route('shop.home',['user_name'=>$this->shop->user_name]),
                    ]
                ],
                "redirectUrl" => route('shop.complete.signup'),
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
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$this->shop->surfboard_application_id}/status";

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
