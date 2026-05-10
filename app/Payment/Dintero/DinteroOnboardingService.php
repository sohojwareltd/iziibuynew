<?php

namespace App\Payment\Dintero;

use App\Models\Shop;

class DinteroOnboardingService
{
    public function createOnboardingSession(Shop $shop): array
    {
        $baseUrl = 'https://onboarding.dintero.com';
    
        if (!$baseUrl) {
            return [
                'status' => 'FAILED',
                'message' => 'Dintero onboarding URL is not configured.',
            ];
        }

        $query = http_build_query([
            'signup_reference' => $shop->id,
            'partner' => env('DINTERO_PARTNER_ACCOUNT_ID'),
        ]);

        return [
            'status' => 'SUCCESS',
            'onboardingUrl' => str_contains($baseUrl, '?') ? "{$baseUrl}&{$query}" : "{$baseUrl}?{$query}",
            'applicationStatus' => 'PENDING',
        ];
    }
}
