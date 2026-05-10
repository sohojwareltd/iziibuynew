<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\PaymentMethodAccess;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class SurfboardStore
{
    private SurfboardPayment $surfboard;
    private PaymentMethodAccess $paymentMethodAccess;
    public function __construct(PaymentMethodAccess $paymentMethodAccess)
    {
        $this->paymentMethodAccess = $paymentMethodAccess;
        $this->surfboard = new SurfboardPayment();
    }

    public function createStore(string $merchantId)
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$merchantId}/stores";
        $storeData = [
            'storeName' => $this->paymentMethodAccess->name,
            'email' => $this->paymentMethodAccess->contact_email,
            'phoneNumber' => [
                "code" => "+47",
                "number" => $this->paymentMethodAccess->contact_phone,
            ],
            'address' => $this->paymentMethodAccess->address,
            'city' => $this->paymentMethodAccess->city,
            'zipCode' => $this->paymentMethodAccess->post_code,
            'country' => array_search($this->paymentMethodAccess->country ?? $this->paymentMethodAccess->user->country, Constants::COUNTRIES) ?? "NO",
            'onlineInfo' => [
                'merchantWebshopURL' => 'https://testmerchantportal.com/home',
                'paymentPageHostURL' => 'https://testmerchantportal.com/payment',
                'termsAndConditionsURL' => 'https://testmerchantportal.com/terms',
                'privacyPolicyURL' => 'https://testmerchantportal.com/privacy',
            ],
        ];
        // Send the POST request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, $storeData);  // Send the data in the POST request

        // Return the response
        return $response;
    }
    public function getStoreDetails(string $merchantId, string $storeId)
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$merchantId}/stores/{$storeId}";

        // Send the GET request to the API
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);  // Send the GET request

        // Return the response
        return $response;
    }
    public function updateStore(string $merchantId, string $storeId)
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/partners/{$this->surfboard->partnerId}/merchants/{$merchantId}/stores/{$storeId}";
        $data = [
            'storeName' => 'Trial Store',
            'email' => 'TS@gmail.com'
        ];
        // Send the PUT request to the API with the provided data
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->put($url, $data);  // Send the PUT request with data

        // Return the response
        return $response;
    }
}
