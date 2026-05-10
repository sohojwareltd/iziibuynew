<?php

namespace App\Payment\Elavon;

use App\Elavon\Converge2\Client\ClientConfig;
use App\Elavon\Converge2\Converge2;
use App\Elavon\Converge2\Response\OrderResponse;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Log;

class ElavonEnterpriseHostedSubscription
{
    public $endpoint;

    protected $elavon;

    public $keys;

    protected $enterprise;

    public function __construct(Enterprise $enterprise)
    {
        $this->enterprise = $enterprise;
        $this->keys = $this->resolveKeys();
        $this->endpoint = $this->keys['sandbox']
            ? 'https://uat.hpp.converge.eu.elavonaws.com'
            : 'https://hpp.eu.convergepay.com';

        $this->elavon = new Converge2($this->config());
    }

    /**
     * @return array{mercahantAlias:string,publicKey:string,secretKey:string,sandbox:bool}
     */
    protected function resolveKeys(): array
    {
        $merchant = config('services.enterprise_elavon.merchant_alias');
        $public = config('services.enterprise_elavon.public_key');
        $secret = config('services.enterprise_elavon.secret_key');
        $sandbox = (bool) config('services.enterprise_elavon.sandbox');

        return [
            'mercahantAlias' => str_replace(' ', '', (string) $merchant),
            'publicKey' => str_replace(' ', '', (string) $public),
            'secretKey' => str_replace(' ', '', (string) $secret),
            'sandbox' => $sandbox,
        ];
    }

    protected function config(): ClientConfig
    {
        $config = new ClientConfig();
        $config->setMerchantAlias($this->keys['mercahantAlias']);
        $config->setPublicKey($this->keys['publicKey']);
        $config->setSecretKey($this->keys['secretKey']);
        if ($this->keys['sandbox']) {
            $config->setSandboxMode();
        }

        return $config;
    }

    protected function shopperEmail(): string
    {
        $raw = $this->enterprise->domain ?? '';
        $host = parse_url($raw, PHP_URL_HOST) ?: preg_replace('#^https?://#i', '', rtrim($raw, '/'));
        $host = $host ?: 'localhost';

        return 'enterprise-' . $this->enterprise->unqid . '@' . $host;
    }

    protected function billTo(): array
    {
        return [
            'fullName' => $this->enterprise->enterprise_name ?? 'Enterprise',
            'company' => $this->enterprise->enterprise_name ?? '',
            'postalCode' => '',
            'street1' => $this->enterprise->domain ?? '',
            'street2' => '',
            'city' => '',
            'countryCode' => 'NOR',
            'primaryPhone' => '',
            'email' => $this->shopperEmail(),
        ];
    }

    protected function makeOrderCreateBody(float $amountNok): array
    {
        return [
            'total' => (object) [
                'amount' => $amountNok,
                'currencyCode' => 'NOK',
            ],
            'description' => sprintf('Enterprise subscription — %s', $this->enterprise->enterprise_name ?? $this->enterprise->unqid),
            'items' => null,
            'shipTo' => [
                'fullName' => $this->enterprise->enterprise_name ?? 'Enterprise',
                'company' => $this->enterprise->enterprise_name ?? '',
                'postalCode' => '',
                'street1' => $this->enterprise->domain ?? '',
                'street2' => '',
                'city' => '',
                'countryCode' => 'NOR',
                'primaryPhone' => '',
                'email' => $this->shopperEmail(),
            ],
            'shopperEmailAddress' => $this->shopperEmail(),
            'shopperReference' => (string) $this->enterprise->unqid,
            'customFields' => [
                'vendor_id' => env('APP_NAME'),
                'vendor_app_name' => env('APP_NAME'),
                'vendor_app_version' => '1.0.0',
                'php_version' => phpversion(),
            ],
        ];
    }

    protected function makePaymentSessionCreateBody(OrderResponse $response, string $returnUrl, string $cancelUrl): array
    {
        return [
            'order' => $response->getId(),
            'billTo' => $this->billTo(),
            'returnUrl' => $returnUrl,
            'cancelUrl' => $cancelUrl,
            'originUrl' => url('/'),
            'defaultLanguageTag' => 'en-US',
            'customFields' => [
                'vendor_id' => env('APP_NAME'),
                'vendor_app_name' => env('APP_NAME'),
                'vendor_app_version' => '1.0.0',
                'php_version' => phpversion(),
            ],
            'doCreateTransaction' => true,
            'doThreeDSecure' => 1,
            'hppType' => 'fullPageRedirect',
        ];
    }

    /**
     * @return array{status:bool,code?:int,data:array<string,mixed>}
     */
    public function getPaymentLink(float $amountNok, string $returnUrl, string $cancelUrl): array
    {
        if ($this->keys['mercahantAlias'] === '' || $this->keys['publicKey'] === '' || $this->keys['secretKey'] === '') {
            Log::warning('Elavon enterprise: missing platform credentials (services.enterprise_elavon / env).');

            return [
                'status' => false,
                'code' => 400,
                'data' => ['message' => 'Elavon enterprise payment is not configured.'],
            ];
        }

        $order_create_response = $this->elavon->createOrder($this->makeOrderCreateBody($amountNok));
        if (! $order_create_response->isSuccess()) {
             $data = $order_create_response->getData();
            $failures = (is_object($data) && isset($data->failures)) ? $data->failures : [];

            return $this->failureFromResponse(is_object($data) && isset($data->status) ? (int) $data->status : 500, $failures);
        }

        $session_body = $this->makePaymentSessionCreateBody($order_create_response, $returnUrl, $cancelUrl);
        $payment_session_create_response = $this->elavon->createPaymentSession($session_body);

        if ($payment_session_create_response->isSuccess()) {
            $sessionId = $payment_session_create_response->getId();

            return [
                'status' => true,
                'code' => 200,
                'data' => [
                    'payment_id' => $sessionId,
                    'url' => $this->endpoint . '/?merchantAlias=' . $this->keys['mercahantAlias'] . '&publicApiKey=' . $this->keys['publicKey'] . '&sessionId=' . $sessionId,
                ],
            ];
        }

        $sessData = $payment_session_create_response->getData();
        $sessFailures = (is_object($sessData) && isset($sessData->failures)) ? $sessData->failures : [];

        return $this->failureFromResponse(is_object($sessData) && isset($sessData->status) ? (int) $sessData->status : 500, $sessFailures);
    }

    protected function failureFromResponse(int $code, $failures): array
    {
        $message = '';
        if (is_iterable($failures)) {
            foreach ($failures as $failure) {
                $message .= ' | ' . (is_object($failure) && method_exists($failure, 'getDescription') ? $failure->getDescription() : '');
            }
        }
        $message = $message !== '' ? trim($message, ' |') : 'Elavon payment session failed.';

        return [
            'status' => false,
            'code' => $code,
            'data' => ['message' => $message],
        ];
    }
}
