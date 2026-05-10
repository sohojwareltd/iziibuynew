<div class="card">
    <div class="card-body">
        <h5>
            {{ __('words.elavon_contract_title') }}

        </h5>
        <p class="w-50">
            {{ __('words.elavon_contract_desc') }}
        </p>


        @if ($status == 0)
            <a class="btn btn-primary" href="{{ route('shop.setup_elavon_payment') }}"> <i class="fa fa-check-circle"></i>
                {{ __('words.sign_contract') }}</a>
        @else
            <a class="btn btn-success"> <i class="fa fa-check-circle"></i>
                {{ __('words.contract_signed') }}</a>
        @endif
    </div>
</div>
