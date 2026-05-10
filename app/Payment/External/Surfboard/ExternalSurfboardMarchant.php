<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\EnterpriseOnboarding;
use App\Models\PaymentMethodAccess;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class ExternalSurfboardMarchant
{
    private ExternalSurfboardPayment $surfboard;
    private PaymentMethodAccess $paymentMethodAccess;
    public function __construct(PaymentMethodAccess $paymentMethodAccess)
    {
        $this->paymentMethodAccess = $paymentMethodAccess;
        $this->surfboard = new ExternalSurfboardPayment();
    }

    public function createMarchant()
    {
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants";


        $data = [
            "country" => array_search(@$this->paymentMethodAccess->company_address->country ?: $this->paymentMethodAccess->user->country, Constants::COUNTRIES) ?? "SE",
            "organisation" => [
                "corporateId" => $this->paymentMethodAccess->company_registration ?: '5593726051',
                "legalName" => $this->paymentMethodAccess->company_name,
                "mccCode" => "5192",
                "address" => [
                    "careOf" => $this->paymentMethodAccess->user->fullName,
                    "addressLine1" => @$this->paymentMethodAccess->company_address->street,
                    "addressLine2" => "",
                    "city" => @$this->paymentMethodAccess->company_address->city,
                    "countryCode" => array_search(@$this->paymentMethodAccess->company_address->country ?: $this->paymentMethodAccess->user->country, Constants::COUNTRIES) ?? "SE",
                    "postalCode" => @$this->paymentMethodAccess->company_address->zip,
                ],
                "phone" => [
                    "code" => @$this->paymentMethodAccess->company_address->country == "Norway" ? "+47" : "+46",
                    "number" => @$this->paymentMethodAccess->company_address->contact_number,
                ],
                "email" => $this->paymentMethodAccess->company_email,
            ],
            "controlFields" => [
                "store" => [
                    "name" =>  $this->paymentMethodAccess->company_name,
                    "email" => $this->paymentMethodAccess->company_email,
                    "phoneNumber" => [
                        "code" => @$this->paymentMethodAccess->company_address->country == "Norway" ? "+47" : "+46",
                        "number" =>  @$this->paymentMethodAccess->company_address->contact_number,
                    ],
                    "address" => [
                        "careOf" => $this->paymentMethodAccess->user->fullName,
                        "addressLine1" => @$this->paymentMethodAccess->company_address->street,
                        "addressLine2" => "",
                        "city" =>  @$this->paymentMethodAccess->company_address->city,
                        "countryCode" => array_search(@$this->paymentMethodAccess->company_address->country ?: $this->paymentMethodAccess->user->country, Constants::COUNTRIES) ?? "SE",
                        "postalCode" =>  @$this->paymentMethodAccess->company_address->zip,
                    ],
                    "onlineInfo" => [
                        "merchantWebshopURL" => $this->paymentMethodAccess->company_domain,
                        "termsAndConditionsURL" => $this->paymentMethodAccess->company_domain . '/terms',
                        "privacyPolicyURL" => $this->paymentMethodAccess->company_domain . '/terms',
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
