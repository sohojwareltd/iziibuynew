<?php

namespace App\Payment\External\Surfboard;

use App\Models\PaymentMethodAccess;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class SurfboardTerminal
{
    private SurfboardPayment $surfboard;
    private PaymentMethodAccess $paymentMethodAccess;
    public function __construct(PaymentMethodAccess $paymentMethodAccess)
    {
        $this->paymentMethodAccess = $paymentMethodAccess;
        $this->surfboard = new SurfboardPayment();

    }
    public function createTerminal( string $onlineTerminalMode = 'PaymentPage')
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/merchants/{$this->paymentMethodAccess->surfboard_merchantId}/stores/{$this->paymentMethodAccess->surfboard_storeId}/online-terminals";

        // Request payload
        $payload = [
            'onlineTerminalMode' => $onlineTerminalMode,
        ];

        // Send the POST request
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->paymentMethodAccess->surfboard_merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Return the response
        return $response;
    }
   
}