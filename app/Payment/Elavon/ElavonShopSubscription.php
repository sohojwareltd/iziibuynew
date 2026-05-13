<?php

namespace App\Payment\Elavon;

use App\Elavon\Converge2\Client\ClientConfig;
use App\Elavon\Converge2\Converge2;
use App\Elavon\Converge2\Response\PaymentSessionResponse;
use App\Elavon\Converge2\Response\StoredCardResponse;
use App\Elavon\Converge2\Response\ShopperResponse;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Iziibuy;

class ElavonShopSubscription
{
    protected Converge2 $elavon;

    protected Shop $shop;

    /**
     * @var array{mercahantAlias: string, publicKey: string, secretKey: string, sandbox: bool}
     */
    protected array $convergeKeys;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->convergeKeys = $this->resolveConvergeKeys();
        $this->elavon = new Converge2($this->config());
    }

    /**
     * @return array{mercahantAlias: string, publicKey: string, secretKey: string, sandbox: bool}
     */
    protected function resolveConvergeKeys(): array
    {
        $merchant = (string) ($this->shop->elavon_merchant_alias ?? '');
        $public = (string) ($this->shop->elavon_public_key ?? '');
        $secret = (string) ($this->shop->elavon_secret_key ?? '');

        if ($merchant !== '' && $public !== '' && $secret !== '') {
            return [
                'mercahantAlias' => str_replace(' ', '', $merchant),
                'publicKey' => str_replace(' ', '', $public),
                'secretKey' => str_replace(' ', '', $secret),
                'sandbox' => ($this->shop->site_mode ?? '') === 'test',
            ];
        }

        $merchant = (string) config('services.enterprise_elavon.merchant_alias', '');
        $public = (string) config('services.enterprise_elavon.public_key', '');
        $secret = (string) config('services.enterprise_elavon.secret_key', '');
        $sandbox = (bool) config('services.enterprise_elavon.sandbox');

        return [
            'mercahantAlias' => str_replace(' ', '', $merchant),
            'publicKey' => str_replace(' ', '', $public),
            'secretKey' => str_replace(' ', '', $secret),
            'sandbox' => $sandbox,
        ];
    }

    protected function config(): ClientConfig
    {
        $config = new ClientConfig;
        $config->setMerchantAlias($this->convergeKeys['mercahantAlias']);
        $config->setPublicKey($this->convergeKeys['publicKey']);
        $config->setSecretKey($this->convergeKeys['secretKey']);
        if ($this->convergeKeys['sandbox']) {
            $config->setSandboxMode();
        }

        return $config;
    }

    protected function getShopperCreateReqBody(): array
    {
        return [
            'fullName' => $this->shop->user->fullName,
            'company' => $this->shop->company_name,
            'primaryAddress' => [
                'street1' => $this->shop->street,
                'street2' => '',
                'city' => $this->shop->city,
                'region' => $this->shop->city,
                'postalCode' => $this->shop->post_code,
                'countryCode' => 'NOR',
            ],
            'primaryPhone' => $this->shop->contact_phone,
            'email' => $this->shop->contact_email ?: $this->shop->user->email,
        ];
    }

    protected function getStoreCardReqBody(): array
    {
        $shopper = $this->makeNewShopper();

        return [
            'shopper' => $shopper->getId(),
            'card' => [
                'holderName' => $this->shop->user->fullName,
                'number' => $this->shop->card_number,
                'expirationMonth' => $this->shop->expiration_month,
                'expirationYear' => $this->shop->expiration_year,
                'securityCode' => $this->shop->ccv,
                'billTo' => [
                    'fullName' => $this->shop->user->fullName,
                    'street1' => $this->shop->street,
                    'street2' => '',
                    'city' => $this->shop->city,
                    'region' => $this->shop->city,
                    'postalCode' => $this->shop->post_code,
                    'countryCode' => 'NOR',
                    'primaryPhone' => $this->shop->contact_phone,
                ],
            ],
        ];
    }

    protected function getTransactionCreateReqBody(float $amount): array
    {
        return [
            'type' => 'sale',
            'total' => [
                'amount' => $amount,
                'currencyCode' => 'NOK',
            ],
            'doCapture' => 1,
            'shopperInteraction' => 'telephoneOrder',
            'shipTo' => [
                'fullName' => $this->shop->user->fullName,
                'company' => $this->shop->company_name,
                'postalCode' => $this->shop->post_code,
                'street1' => $this->shop->street,
                'street2' => '',
                'city' => $this->shop->city,
                'countryCode' => 'NOR',
                'primaryPhone' => $this->shop->contact_phone,
                'email' => $this->shop->contact_email ?: $this->shop->user->email,
            ],
            'shopperEmailAddress' => $this->shop->user->email,
            'doSendReceipt' => null,
            'shopperIpAddress' => request()->ip() ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
            'shopperReference' => $this->shop->id,
            'shopperStatement' => [
                'name' => $this->shop->user->fullName,
                'phone' => $this->shop->contact_phone,
                'url' => null,
            ],
            'description' => sprintf('Charged via card for shop : %s', $this->shop->user_name),
            'shopperLanguageTag' => app()->getLocale(),
            'shopperTimeZone' => config('app.timezone'),
            'customFields' => [
                'vendor_id' => env('APP_NAME'),
                'vendor_app_name' => env('APP_NAME'),
                'vendor_app_version' => '1.0.0',
                'php_version' => phpversion(),
            ],
            'createdBy' => env('APP_NAME'),
            'orderReference' => uniqid(),
            'storedCard' => $this->shop->subscription_id,
        ];
    }

    protected function makeNewShopper(): ShopperResponse
    {
        return $this->elavon->createShopper($this->getShopperCreateReqBody());
    }

    protected function storeACard()
    {
        return $this->elavon->createStoredCard($this->getStoreCardReqBody());
    }

    protected function hasPlainCardDetailsForVaulting(): bool
    {
        return filled($this->shop->card_number)
            && filled($this->shop->expiration_month)
            && filled($this->shop->expiration_year)
            && filled($this->shop->ccv);
    }

    protected function parseUrl(?string $url): string
    {
        if ($url === null || $url === '') {
            return '';
        }

        $path = parse_url($url, PHP_URL_PATH);
        if ($path === null || $path === '') {
            return '';
        }

        $parts = explode('/', $path);

        return (string) end($parts);
    }

    /**
     * @return array{status: bool, code?: int, data: array<string, mixed>}
     */
    public function createSubscription(): array
    {
        if ($this->hasPlainCardDetailsForVaulting()) {
            $response = $this->storeACard();

            if ($response->isSuccess()) {
                return [
                    'status' => true,
                    'code' => 200,
                    'data' => [
                        'shopperId' => $this->parseUrl($response->getShopper()),
                        'cardId' => $response->getId(),
                    ],
                ];
            }
            $message = '';
            foreach ($response->getData()->failures as $failure) {
                $message .= ' | '.$failure->getDescription();
            }

            return [
                'status' => false,
                'code' => $response->getData()->status,
                'data' => [
                    'message' => $message,
                ],
            ];
        }

        $hosted = new ElavonShopHostedSubscription($this->shop);
        $amount = (float) Iziibuy::round_num($this->shop->subscriptionFee());
        $result = $hosted->getPaymentLink(
            $amount,
            route('shop.subscription.elavon.return', absolute: true),
            route('shop.subscription.payment', absolute: true),
        );

        if (! $result['status']) {
            return [
                'status' => false,
                'code' => $result['code'] ?? 500,
                'data' => $result['data'],
            ];
        }

        return [
            'status' => true,
            'code' => 200,
            'data' => [
                'requires_hpp' => true,
                'url' => $result['data']['url'],
                'payment_id' => $result['data']['payment_id'],
            ],
        ];
    }

    /**
     * After hosted checkout, persist stored-card and shopper ids on the shop.
     *
     * @return array{status: bool, data: array<string, mixed>}
     */
    public function finalizeHostedSubscriptionFromSession(string $sessionId): array
    {
        $session = $this->loadPaymentSessionWithRetry($sessionId);
        if (! $session->isSuccess()) {
            return [
                'status' => false,
                'data' => ['message' => 'Could not load payment session.'],
            ];
        }

        $shopperId = $this->resolveShopperIdFromPaymentSession($session);

        $cardId = $this->resolveStoredCardIdFromPaymentSession($session);
        if ($cardId === '') {
            $hostedCardRef = $this->resolveHostedCardReferenceFromPaymentSession($session);
            if ($hostedCardRef !== '' && $shopperId !== '') {
                $cardId = $this->createStoredCardIdFromHostedInstrument($shopperId, $hostedCardRef);
            }
        }

        if ($cardId === '') {
            return [
                'status' => false,
                'data' => ['message' => 'Card was not saved. Complete payment on the hosted page, or try again.'],
            ];
        }

        $this->shop->subscription_id = $cardId;
        if ($shopperId !== '') {
            $this->shop->shopperId = $shopperId;
        }
        $this->shop->payment_url = null;
        $this->shop->save();

        return [
            'status' => true,
            'data' => ['cardId' => $cardId],
        ];
    }

    /**
     * Hosted checkout can take a short time to attach transaction / card links.
     */
    protected function loadPaymentSessionWithRetry(string $sessionId): PaymentSessionResponse
    {
        $attempts = 3;
        $delayMicros = 400_000;
        $last = $this->elavon->getPaymentSession($sessionId);

        for ($i = 1; $i < $attempts; $i++) {
            if (
                $last->isSuccess()
                && (
                    $last->getStoredCard()
                    || $last->getHostedCard()
                    || $last->getTransaction()
                )
            ) {
                return $last;
            }
            usleep($delayMicros);
            $last = $this->elavon->getPaymentSession($sessionId);
        }

        return $last;
    }

    protected function resolveStoredCardIdFromPaymentSession(PaymentSessionResponse $session): string
    {
        $href = $session->getStoredCard();
        if ($href) {
            return $this->parseUrl((string) $href);
        }

        $txHref = $session->getTransaction();
        if (! $txHref) {
            return '';
        }

        $tx = $this->elavon->getTransaction($this->parseUrl((string) $txHref));
        if (! $tx->isSuccess()) {
            return '';
        }

        $stored = $tx->getStoredCard();
        if (! $stored) {
            return '';
        }

        return $this->parseUrl((string) $stored);
    }

    protected function resolveShopperIdFromPaymentSession(PaymentSessionResponse $session): string
    {
        $href = $session->getShopper();
        if ($href) {
            return $this->parseUrl((string) $href);
        }

        $txHref = $session->getTransaction();
        if (! $txHref) {
            return '';
        }

        $tx = $this->elavon->getTransaction($this->parseUrl((string) $txHref));
        if (! $tx->isSuccess()) {
            return '';
        }

        $shopper = $tx->getShopper();
        if (! $shopper) {
            return '';
        }

        return $this->parseUrl((string) $shopper);
    }

    /**
     * Hosted checkout usually exposes a hostedCard (not storedCard) until vaulted.
     */
    protected function resolveHostedCardReferenceFromPaymentSession(PaymentSessionResponse $session): string
    {
        $href = $session->getHostedCard();
        if ($href) {
            return (string) $href;
        }

        $txHref = $session->getTransaction();
        if (! $txHref) {
            return '';
        }

        $tx = $this->elavon->getTransaction($this->parseUrl((string) $txHref));
        if (! $tx->isSuccess()) {
            return '';
        }

        $hosted = $tx->getHostedCard();
        if (! $hosted) {
            return '';
        }

        return (string) $hosted;
    }

    /**
     * Create a stored-card token from a hosted-card reference (post-HPP).
     */
    protected function createStoredCardIdFromHostedInstrument(string $shopperId, string $hostedCardReference): string
    {
        $parsedHosted = $this->parseUrl($hostedCardReference);
        $shopperCandidates = array_values(array_unique(array_filter([
            $shopperId,
            $this->shop->shopperId,
        ])));

        $hostedCandidates = array_values(array_unique(array_filter([
            $parsedHosted,
            $hostedCardReference,
        ])));

        foreach ($shopperCandidates as $shopper) {
            foreach ($hostedCandidates as $hosted) {
                /** @var StoredCardResponse $response */
                $response = $this->elavon->createStoredCard([
                    'shopper' => $shopper,
                    'hostedCard' => $hosted,
                ]);

                if ($response->isSuccess()) {
                    return $response->getId();
                }

                $message = '';
                foreach ($response->getData()->failures ?? [] as $failure) {
                    $message .= ' | '.$failure->getDescription();
                }
                Log::warning('Elavon shop subscription: createStoredCard from hostedCard failed', [
                    'shop_id' => $this->shop->id,
                    'shopper' => $shopper,
                    'hostedCard' => $hosted,
                    'message' => trim($message, ' |'),
                ]);
            }
        }

        return '';
    }

    /**
     * @return array{status: bool, code?: int, data: array<string, mixed>}
     */
    public function chargeViaCard(float $amount): array
    {
        $response = $this->elavon->createSaleTransaction($this->getTransactionCreateReqBody($amount));

        if ($response->isSuccess()) {
            return [
                'status' => true,
                'code' => 200,
                'data' => [
                    'status' => $response->getState()->isAuthorized() || $response->getState()->isCaptured(),
                    'id' => $response->getId(),
                ],
            ];
        }
        $message = '';
        foreach ($response->getData()->failures as $failure) {
            $message .= ' | '.$failure->getDescription();
        }

        return [
            'status' => false,
            'code' => $response->getData()->status,
            'data' => [
                'message' => $message,
            ],
        ];
    }

    public function getSubscription()
    {
        return $this->elavon->getStoredCard($this->shop->subscription_id);
    }
}
