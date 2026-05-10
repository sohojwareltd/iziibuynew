<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Payment\Dintero\DinteroOnboardingService;
use Illuminate\Http\RedirectResponse;

class DinteroOnboardingController extends Controller
{
    public function __construct(private DinteroOnboardingService $onboardingService)
    {
    }

    public function setup(): RedirectResponse
    {
        $shop = auth()->user()->shop;
        $session = $this->onboardingService->createOnboardingSession($shop);

        if (($session['status'] ?? 'FAILED') !== 'SUCCESS') {
            return redirect()->route('shop.complete.signup')->withErrors($session['message'] ?? 'Unable to start Dintero onboarding.');
        }

        $existingGatewayContracts = json_decode((string) $shop->gateway_contract_signed, true) ?? [];
        $existingGatewayContracts['dintero'] = 1;

        $shop->createMetas([
            'gateway_contract_signed' => $existingGatewayContracts,
            'dintero_onboarding_url' => $session['onboardingUrl'],
            'dintero_onboarding_status' => $session['applicationStatus'] ?? 'PENDING',
        ]);

        return redirect($session['onboardingUrl']);
    }
}
