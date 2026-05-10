<?php

namespace App\Payment\Surfboard;

use App\Constants\Constants;
use App\Models\ExternalOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class SurfboardOrderApi
{
    protected ExternalOrder $order;
    private SurfboardPayment $surfboard;
    public function __construct(ExternalOrder $order)
    {
        $this->order = $order;

        $sandbox = $this->order->paymentMethodAccess->site_mode == 'live' ? false : true;
        $this->surfboard = new SurfboardPayment(merchantId: $this->order->paymentMethodAccess->surfboard_merchantId, storeId: $this->order->paymentMethodAccess->surfboard_storeId, sandbox: $sandbox);
    }

    public function  getPaymentLink()
    {
        $order = $this->createOrder();

        if (isset($order['status']) && $order['status'] == "SUCCESS") {
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
    public function getPayment($payment_id)
    {
        $url = "{$this->surfboard->apiUrl}/payments/{$payment_id}";
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
            'terminal$id' => $this->order->paymentMethodAccess->surfboard_terminalId,
            "purchaseOrderId" => $this->order->id,
            "companyPurchase" => false,
            "company" => [
                "id" => $this->order->paymentMethodAccess->company_registration,
                "vatId" => "",
                "poDetails" => $this->order->paymentMethodAccess->company_address?->zip,
            ],
            "type" => "PURCHASE",
            "referenceId" => (string) $this->order->id,
            "customer" => [
                "person" => [
                    "id" => "p_Nc5OJBUxZf3-Y5CufUgw1",
                    "name" => $this->order->customer_name . " " . $this->order->customer_name,
                    "email" => $this->order->customer_email,
                    "phone" => $this->order->customer_phone,
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
            "orderLines" => [
                                [
                                    "id" => "101",
                                    "name" => "product 1",
                                    "quantity" => 1,
                                    "itemAmount" => [
                                        "regular" => (int) $this->order->amount * 100,
                                        "total" =>(int) $this->order->amount * 100,
                                        "currency" => "NOK",
                                        "tax" => [
                                            [
                                                "amount" => (int) ($this->order->taxTotal * 100),
                                                "percentage" => (float) $this->order->taxValue,
                                                "type" => "vat",
                                            ],
                                        ],
                                    ],
                                ],
                            ],

            "adjustments" => [],
            "totalOrderAmount" => [
                "regular" => (int) $this->order->amount * 100,
                "total" => (int) $this->order->amount * 100,
                "currency" => "NOK",
                "campaign" => null,
                "shipping" => null,
                "tax" => [
                    [
                        "amount" => (int) ($this->order->taxTotal * 100),
                        "percentage" => (float) $this->order->taxValue,
                        "type" => "VAT",
                    ],
                ],
            ],
            "metaData" => null,
            "controlFunctions" => [
                "includeAdjustments" => null,
                "orderLineLevelCalculation" => false,
                "callBackUrl" => route('callback.api.surfboard.payment.success'),
                "readTags" => "NONE",
                "authMode" => "AUTH",
                "lockToPaymentMethods" => null,
                "storeId" => null,
                "online" => [
                    "delayCapture" => false,
                    "enforceTokenization" => null,
                    "enforce3DSecure" => null,
                    "paymentPageValidFor" => "60d",
                    "delayPayout" => "DEFAULT",
                    "redirectUrl" => route('callback.api.surfboard.payment.redirect'),
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
    
    public function cancelOrder()
    {
        $url = "{$this->surfboard->apiUrl}/orders/{$this->order->payment_id}";
        
        try {
            $request = Http::withHeaders([
                'API-KEY' => $this->surfboard->apiKey, 
                'API-SECRET' => $this->surfboard->apiSecret, 
                'MERCHANT-ID' => $this->surfboard->merchantId, 
                'Content-Type' => 'application/json'
            ])->timeout(30)->delete($url);
            
            // Check if request failed
            if ($request->failed()) {
                return [
                    'status' => false,
                    'code' => $request->status(),
                    'data' => 'Failed to connect to payment gateway',
                    'message' => 'Failed to connect to payment gateway. HTTP Status: ' . $request->status()
                ];
            }
            
            $response = $request->json();
            
            // Handle case where response is null or not an array
            if (!is_array($response)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'data' => 'Invalid response format from payment gateway',
                    'message' => 'Payment gateway returned an invalid response format. Please try again later.'
                ];
            }
            
            // Check if response has status field
            if (!isset($response['status'])) {
                return [
                    'status' => false,
                    'code' => 500,
                    'data' => $response['message'] ?? $response['data'] ?? 'Unknown error from payment gateway',
                    'message' => 'Payment gateway returned an unexpected response format. Response: ' . json_encode($response)
                ];
            }
            
            if($response['status'] == 'SUCCESS') {
                return [
                    'status' => true,
                    'code' => 200,
                    'data' => $response['data'] ?? null,
                    'message' => $response['message'] ?? 'Order canceled successfully in payment gateway'
                ];
            } else {
                // Handle case where order is already canceled or other error
                $message = $response['message'] ?? ($response['data'] ?? 'Unable to cancel order');
                
                // Normalize message to string
                if (is_array($message)) {
                    $message = json_encode($message);
                } elseif (!is_string($message)) {
                    $message = (string) $message;
                }
                
                // Check if the message indicates order is already canceled
                $lowerMessage = strtolower($message);
                if (stripos($lowerMessage, 'cancelled') !== false || 
                    stripos($lowerMessage, 'canceled') !== false || 
                    stripos($lowerMessage, 'already') !== false ||
                    stripos($lowerMessage, 'not found') !== false) {
                    return [
                        'status' => false,
                        'code' => 200, // Treat as success since order is already canceled
                        'data' => $message,
                        'message' => 'Order is already canceled or does not exist in payment gateway: ' . $message
                    ];
                }
                
                return [
                    'status' => false,
                    'code' => $response['code'] ?? 500,
                    'data' => $message,
                    'message' => 'Failed to cancel order in payment gateway: ' . $message
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'code' => 500,
                'data' => 'Exception occurred while canceling order',
                'message' => 'An error occurred while communicating with payment gateway: ' . $e->getMessage()
            ];
        }
    }
 
}
