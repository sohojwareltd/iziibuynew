<?php

namespace App\Payment\External\Surfboard;

use App\Models\Order;


class ExternalSurfboardPayment
{
    public string $apiUrl;
    public string $apiKey;
    public string $apiSecret;
    public string $partnerId;
    public string $merchantId;
    public string $storeId;
    public function __construct($merchantId = null, $storeId = null)
    {
        $this->apiUrl = env('SURFBOARD_API_URL');
        $this->apiKey = env('SURFBOARD_API_KEY');
        $this->apiSecret = env('SURFBOARD_API_SECRET'); 
        $this->partnerId = env('SURFBOARD_PARTNER_ID');
        $this->merchantId = $merchantId ?? env('SURFBOARD_MERCHANT_ID');
        $this->storeId = $storeId ??  env('SURFBOARD_STORE_ID');
        // $this->apiUrl = 'https://lithium.surfgw.com/api';
        // $this->apiKey = 'ex2vlacjexeawnywpojghc0l3_iugg.service.zpzq7@surfboard.service';
        // $this->apiSecret = 'KUs2d3if7bVY0m4aBUBYa7kHqCvu7t';
        // $this->partnerId = '82ade0f0013ec80309';
        // $this->merchantId = $merchantId ?? '82ade587195008050e';
        // $this->storeId = $storeId ?? '82ade5873a5890070f';
    }
}
