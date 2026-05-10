<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class SurfboardOrder
{
    protected Order $order;
    private SurfboardPayment $surfboard;
    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->surfboard = new SurfboardPayment(merchantId: $this->order->shop->surfboard_merchantId, storeId: $this->order->shop->surfboard_storeId);
    }

    public function  getPaymentLink()
    {
        $order = $this->createOrder();

        if ($order['status'] == "SUCCESS") {
            return [
                'status' => true,
                'code' => 200,
                'data' => [
                    'payment_id' => $order["data"]["orderId"],
                    'url' => $order["data"]["paymentPageLink"]
                ]
            ];
        } else {
            return [
                'status' => false,
                'code' => 500,
                'data' => [
                    'message' => $order["message"]
                ]
            ];
        }
    }

    public function getOrderStatus()
    {
        $url = "{$this->surfboard->apiUrl}/orders/{$this->order->payment_id}/status";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }


    public function makeVoid()
    {
        $url = "{$this->surfboard->apiUrl}/payments/{$this->order->surfboard_transaction_id}/void";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->put($url);

        return $response->json();
    }
    public function getPayment()
    {
        $url = "{$this->surfboard->apiUrl}/payments/{$this->order->surfboard_transaction_id}";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }
    public function capturePayment()
    {

        $url = "{$this->surfboard->apiUrl}/payments/{$this->order->surfboard_transaction_id}/capture";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'amount' => $this->order->total * 100
        ]);


        return $response->json();
    }
    public function capturePaymentStatus()
    {

        $url = "{$this->surfboard->apiUrl}/payments/{$this->order->surfboard_transaction_id}/capture";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url, [
            'paymentId' => $this->order->surfboard_transaction_id
        ]);



        return $response->json();
    }

    protected function createOrder()
    {
        // Define the API endpoint
        $url = "{$this->surfboard->apiUrl}/orders";

        $orderData = [
            'terminal$id' => $this->order->shop->surfboard_terminalId,
            "purchaseOrderId" => $this->order->id,
            "companyPurchase" => false,
            "company" => [
                "id" => $this->order->shop->company_registration,
                "vatId" => "",
                "poDetails" => $this->order->shop->post_code,
            ],
            "type" => "PURCHASE",
            "referenceId" => (string) $this->order->id,
            "customer" => [
                "person" => [
                    "id" => "p_Nc5OJBUxZf3-Y5CufUgw1",
                    "name" => $this->order->first_name . " " . $this->order->last_name,
                    "email" => $this->order->email,
                    "phone" => $this->order->phone,
                ],
                // "billing" => [
                //     "careOf" => $this->order->first_name . " " . $this->order->last_name,
                //     "address1" => $this->order->address,
                //     "city" => $this->order->city,
                //     "postalCode" => $this->order->post_code,
                //     "country" => array_search($this->order->country, Constants::COUNTRIES) ? array_search($this->order->country, Constants::COUNTRIES) : 'NO',
                // ],
                // "shipping" => [
                //     "name" => $this->order->first_name . " " . $this->order->last_name,
                //     "email" => $this->order->email,
                //     "phone" => $this->order->phone,
                //     "careOf" => $this->order->first_name . " " . $this->order->last_name,
                //     "address1" => $this->order->address,
                //     "city" => $this->order->city,
                //     "postalCode" => $this->order->post_code,
                //     "country" => array_search($this->order->country, Constants::COUNTRIES) ? array_search($this->order->country, Constants::COUNTRIES) : 'NO' ,
                // ],
            ],
            "orderLines" => $this->order->products->map(function ($product) {

                return [
                    "id" => (string) $product->id,
                    "name" => $product->name,
                    "quantity" => $product->pivot->quantity,
                    "itemAmount" => [
                        "regular" => (int) $product->pivot->price * 100,
                        "total" => (int) $product->pivot->price * 100,
                        "currency" => "NOK",
                        "tax" => [
                            [
                                "amount" => (int) ($product->pivot->price - ($product->pivot->price / (1 + ($product->tax / 100)))) * 100,
                                "percentage" => $product->tax,
                                "type" => "vat",
                            ],
                        ],
                    ]
                ];
            })->toArray(),

            "adjustments" => [],
            "totalOrderAmount" => [
                "regular" => (int) $this->order->total * 100,
                "total" => (int) $this->order->total * 100,
                "currency" => "NOK",
                "campaign" => null,
                "shipping" => null,
                "tax" => [
                    [
                        "amount" => (int) $this->order->tax * 100,
                        "percentage" => (int) $this->order->taxPercentage(),
                        "type" => "VAT",
                    ],
                ],
            ],
            "metaData" => null,
            "controlFunctions" => [
                "includeAdjustments" => null,
                "orderLineLevelCalculation" => false,
                "callBackUrl" => route('surfboard.callback'),
                "readTags" => "NONE",
                "authMode" => "AUTH",
                "lockToPaymentMethods" => null,
                "storeId" => null,
                "online" => [
                    "delayCapture" => false,
                    "enforceTokenization" => null,
                    "enforce3DSecure" => null,
                    "paymentPageValidFor" => "1d",
                    "delayPayout" => "DEFAULT",
                    "redirectUrl" => route('surfboard.callback'),
                    "generateShortLink" => true,
                    "accountNameVerification" => "UNVERIFIED",
                    "errorIfTokenizationFails" => false,
                ],
                "recurring" => [],
            ],
        ];
        // Send the POST request
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->post($url, $orderData);

        // Return the response
        return $response->json();
    }
}
