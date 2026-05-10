<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\EnterpriseOnboarding;
use App\Models\PaymentMethodAccess;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class ExternalSurfboardStore
{
    private ExternalSurfboardPayment $surfboard;
    private PaymentMethodAccess $paymentMethodAccess;
    public function __construct(PaymentMethodAccess $paymentMethodAccess)
    {
        $this->paymentMethodAccess = $paymentMethodAccess;
        $this->surfboard = new ExternalSurfboardPayment();
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
                'merchantWebshopURL' => $this->paymentMethodAccess->company_domain,
                'paymentPageHostURL' => $this->paymentMethodAccess->company_domain.'/payment',
                'termsAndConditionsURL' => $this->paymentMethodAccess->company_domain.'/terms',
                'privacyPolicyURL' => $this->paymentMethodAccess->company_domain.'/privacy',
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
