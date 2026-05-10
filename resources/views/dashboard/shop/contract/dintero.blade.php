<div class="card">
    <div class="card-body">
        <h5>Dintero contract</h5>
        <p class="w-50">
            Complete Dintero onboarding to activate Google Pay, Apple Pay and Vipps for your shop.
        </p>

        @if ($status == 0)
            <a class="btn btn-primary" href="{{ route('shop.setup_dintero_payment') }}">
                <i class="fa fa-check-circle"></i> Start onboarding
            </a>
        @else
            <ul class="list-group">
                <li class="list-group-item">
                    Onboarding status: {{ auth()->user()->shop->dintero_onboarding_status ?? 'PENDING' }}
                </li>
            </ul>
            @if (auth()->user()->shop->dintero_onboarding_url)
                <a class="btn btn-info mt-2" href="{{ auth()->user()->shop->dintero_onboarding_url }}">
                    Continue onboarding
                </a>
            @endif
        @endif
    </div>
</div>
