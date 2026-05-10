<?php

namespace App\Payment\External\Surfboard;

use App\Constants\Constants;
use App\Models\ExternalBooking;
use Illuminate\Support\Facades\Http;

class ExternalBookingSurfboardApi
{
    protected ExternalBooking $booking;
    private SurfboardPayment $surfboard;

    public function __construct(ExternalBooking $booking)
    {
        $this->booking = $booking;

        $sandbox = $this->booking->paymentMethodAccess->site_mode == 'live' ? false : true;
    
        $this->surfboard = new SurfboardPayment(
            merchantId: $this->booking->paymentMethodAccess->surfboard_merchantId,
            storeId: $this->booking->paymentMethodAccess->surfboard_storeId,
            sandbox: $sandbox
        );
    }

    public function getPaymentLink()
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
        $url = "{$this->surfboard->apiUrl}/orders/{$this->booking->payment_id}/status";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }

    public function getTransaction(){
        $url = "{$this->surfboard->apiUrl}/orders/{$this->booking->payment_id}";
        $response = Http::withHeaders([
            'API-KEY' => $this->surfboard->apiKey,
            'API-SECRET' => $this->surfboard->apiSecret,
            'MERCHANT-ID' => $this->surfboard->merchantId,
            'Content-Type' => 'application/json',
        ])->get($url);
        return $response->json();
    }

    protected function createOrder()
    {
        // Define the API endpoint
        $url = "{$this->surfboard->apiUrl}/orders";

        $customerDetails = $this->booking->customer_details;
        $serviceDetails = $this->booking->service_details;

        $orderData = [
            'terminal$id' => $this->booking->paymentMethodAccess->surfboard_terminalId,
            "purchaseOrderId" => $this->booking->id,
            "companyPurchase" => false,
            "company" => [
                "id" => $this->booking->paymentMethodAccess->company_registration ?? '',
                "vatId" => "",
                "poDetails" => $this->booking->paymentMethodAccess->company_address?->zip ?? '',
            ],
            "type" => "PURCHASE",
            "referenceId" => (string) $this->booking->id,
            "customer" => [
                "person" => [
                    "id" => "p_" . $this->booking->id,
                    "name" => 'john doe',
                    "email" => 'john@doe.com',
                    "phone" => $this->booking->phone_number ?? '',
                ],
            ],
            "orderLines" => [
                "id" => (string) $this->booking->id,
                "name" => $this->booking->booking_number ?? 'Booking',
                "quantity" => 1,
                "itemAmount" => [
                    "regular" => (int) $this->booking->total * 100,
                    "total" => (int) $this->booking->total * 100,
                    "currency" => $this->booking->currency,
                    "tax" => [
                        [
                            "amount" => 0,
                            "percentage" => 0,
                            "type" => "vat",
                        ],
                    ],
                ]
            ],
            "adjustments" => [],
            "totalOrderAmount" => [
                "regular" => (int) $this->booking->total * 100,
                "total" => (int) $this->booking->total * 100,
                "currency" => $this->booking->currency,
                "campaign" => null,
                "shipping" => null,
                "tax" => [
                    [
                        "amount" => 0,
                        "percentage" => 0,
                        "type" => "VAT",
                    ],
                ],
            ],
            "metaData" => null,
            "controlFunctions" => [
                "includeAdjustments" => null,
                "orderLineLevelCalculation" => false,
                "callBackUrl" => route('callback.plugin.externalbooking.surfboard.success'),
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
                    "redirectUrl" => route('callback.plugin.externalbooking.surfboard.redirect'),
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

        return $response->json();
    }
}
