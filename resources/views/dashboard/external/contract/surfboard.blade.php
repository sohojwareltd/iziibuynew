<div class="card">
    <div class="card-body">
        <h5>
            {{ __('words.surfboard_contract_title') }}

        </h5>
        <p class="w-50">
            {{ __('words.surfboard_contract_desc') }}
        </p>


        @if ($status == 0)
            <a class="btn btn-primary" href="{{ route('external.setup_surfboard_payment') }}"> <i
                    class="fa fa-check-circle"></i> {{ __('words.sign_contract') }}</a>
        @else
            <ul class="list-group">

                <li class="list-group-item"> Webkey Application Id : {{ auth()->user()->paymentMethodAccess->surfboard_application_id }}
                </li>
                <li class="list-group-item">Webkey Application Status :
                    {{ auth()->user()->paymentMethodAccess->surfboard_application_status }} </li>

            </ul>

            @if (auth()->user()->paymentMethodAccess->surfboard_application_status)
                <a class="btn btn-warning mt-2" href="{{ route('external.surfboardStatusCheck') }}">
                    {{ __('words.check_surfboard_progress') }}</a>
            @else
                <a class="btn btn-info mt-2"
                    href="{{ auth()->user()->paymentMethodAccess->surfboard_webKybUrl }}">{{ __('words.complete_signing') }}</a>
            @endif
        @endif
    </div>
</div>
