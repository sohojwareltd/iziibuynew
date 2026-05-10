<?php

namespace App\Payment\Enterprise\Surfboard;

use App\Models\EnterpriseOnboarding;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;

class EnterpriseSurfboardTerminal
{
    private EnterpriseSurfboardPayment $surfboard;
    private EnterpriseOnboarding $enterprise;
    public function __construct(EnterpriseOnboarding $enterprise)
    {
        $this->enterprise = $enterprise;
        $this->surfboard = new EnterpriseSurfboardPayment();

    }
    public function createTerminal( string $onlineTerminalMode = 'PaymentPage')
    {
        // Build the API URL
        $url = "{$this->surfboard->apiUrl}/merchants/{$this->enterprise->surfboard_merchantId}/stores/{$this->enterprise->surfboard_storeId}/online-terminals";

        // Request payload
        $payload = [
            'onlineTerminalMode' => $onlineTerminalMode,
        ];

        // Send the POST request
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->enterprise->surfboard_merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Return the response
        return $response;
    }
   
}