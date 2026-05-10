<?php

namespace App\Payment\Surfboard;

use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class SurfboardTerminal
{
    private SurfboardPayment $surfboard;
    private Shop $shop;
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->surfboard = new SurfboardPayment();

    }
    public function createTerminal( string $onlineTerminalMode = 'PaymentPage')
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/merchants/{$this->shop->surfboard_merchantId}/stores/{$this->shop->surfboard_storeId}/online-terminals";

        // Request payload
        $payload = [
            'onlineTerminalMode' => $onlineTerminalMode,
        ];

        // Send the POST request
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->shop->surfboard_merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Return the response
        return $response;
    }
   
}