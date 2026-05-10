<x-dashboard.enterprise>
   
    <div class="row mt-5">
        @if ($enterprise->contract_signed == false)
            <div class="col-md-12 mb-2">
                <div class="card ">
                    <div class="card-header">
                        {!! __('words.external_contract_sec_pera') !!}
                    </div>
                    <div class="card-body">
                        <p class="bg-success p-2 text-light">
                            {!! __('words.external_contract_massage_sec_subtitle') !!}
                        </p>
                        <h3>{!! __('words.external_ccontarct_msg_sec_title') !!}</h3>
                        <p>{!! __('words.external_contract_msg_sec_pera') !!}
                          
                        </p>
                        <form action="{{ route('enterprise.signContract', $enterprise) }}"
                            method="post">
                            @csrf
                            @php
                            $paymentMethods = [
                                'visa' => 'Visa',
                                'mastercard' => 'Mastercard',
                                'amex' => 'Amex',
                                'vipps' => 'Vipps',
                                'googlepay' => 'Google Pay',
                                'applepay' => 'Apple Pay',
                            ];
                        @endphp

                        <div class="form-group">
                            @foreach ($paymentMethods as $key => $value)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="paymentMethods[]"
                                        value="{{ $key }}" id="{{ $key }}">
                                    <label class="form-check-label" for="{{ $key }}">
                                        {{ $value }}
                                    </label>
                                </div>
                            @endforeach

                        </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">{!! __('words.external_contract_order_btn') !!}</button>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms" value="terms"
                                        id="terms" checked>
                                    <label class="form-check-label" for="terms">
                                        {!! __('words.external_contract_terms') !!}

                                        <x-termsandservices/>
                               
                                    </label>
                                </div>
                                <p>{!! __('words.external_contract_footer') !!} </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else

        <p>
            <h1>{!! __('words.contract_has_panding') !!} </h1>
            </p>
            <p>{{ __('words.contract_has_panding_pera_2') }} </p>

            <div class="row g-3">
                @foreach (json_decode($enterprise->gateway_contract_signed) as $gateway => $status)
                    <div class="col-12">
                        @include("dashboard.enterprise.contract.$gateway", ['status' => $status])
                    </div>
                @endforeach
            </div>


            {{-- <div class="col-md-12 mb-2">
                <div class="card">
                    <div class="card-header">
                        {{ __('words.contract') }}
                    </div>
                    <div class="card-body">
                        @if ($enterprise->contract_status == 0)
                            <p>
                            <h1>{!! __('words.external_contract_has_panding') !!} </h1>
                            </p>
                            <p>{{ __('words.external_contract_has_panding_pera_2') }} </p>
                            <div class="btn btn-danger">{{ __('words.external_contract_has_panding_pera_3') }}</div>
                        @else
                            <p>
                            <h1> {!! __('words.external_contract_has_panding') !!} </h1>
                            </p>
                            <p> {!! __('words.external_contract_has_submit') !!} </p>
                            <div class="btn btn-success">{{ __('words.external_contract_has_submit_2') }}</div>
                        @endif

                        @if ($enterprise->contract_url || $enterprise->contract_status)
                            <div class="card mt-5">
                                <div class="card-body">
                                    <div class="row">
                                        <div
                                            class=" mb-2 col-12 col-md-6 d-flex flex-column justify-content-center align-items-start">
                                            <div class="w-100">
                                                <h2 class="ml-5 rounded-circle bg-dark text-light d-flex justify-content-center align-items-center"
                                                    style="height:50px;width:50px">
                                                    1
                                                </h2>
                                                <div class="text-left">
                                                 
                                                    <h3>{{ __('words.sign_contract_title') }}</h3>
                                                 
                                                    <h6>{{ __('words.sign_contract_sub_title') }}</h6>
                                                </div>
                                            </div>


                                        </div>
                                        <div
                                            class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center">

                                            <a class="btn btn-success btn-block w-50 mb-1" target="_blank"
                                                href="{{ $enterprise->contract_url ? $enterprise->contract_url : 'javascript:void(0)' }}">{{ __('words.contract') }}</a>
                                            <div
                                                class="{{ $enterprise->contract_status ? 'border border-success text-success p-1 w-50 text-center' : 'bg-dark text-light p-1 w-50 text-center' }}">
                                                {{ $enterprise->contract_status ? __('words.approved') : __('words.sign_contract_button') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div
                                            class=" mb-2 col-12 col-md-6 d-flex flex-column justify-content-center align-items-start">
                                            <div class="w-100 ">
                                                <h2 class="ml-5 rounded-circle bg-dark text-light d-flex justify-content-center align-items-center"
                                                    style="height:50px;width:50px">
                                                    2
                                                </h2>

                                                <div class="text-left">
                                              
                                                    <h3>{{ __('words.sign_kyc_title') }}</h3>
                                                 
                                                    <h6>{{ __('words.sign_kyc_sub_title') }}</h6>
                                                </div>
                                            </div>


                                        </div>
                                        <div
                                            class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center">

                                            <a class="btn btn-success btn-block w-50 mb-1" target="_blank"
                                                href="{{ $enterprise->kyc_status ? 'javascript:void(0)' : 'https://submit.anyday.io/iziibuy-kyc?pluginid=' . $enterprise->key }}">{{ __('words.kyc') }}</a>
                                            <div
                                                class="{{ $enterprise->kyc_status ? 'border border-success text-success p-1 w-50 text-center' : 'bg-dark text-light p-1 w-50 text-center' }}">
                                                {{ $enterprise->kyc_status ? __('words.approved') : __('words.sign_kyc_button') }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div> --}}
        @endif
       
    </div>
  
   
   
   <!-- Optional: Place to the bottom of scripts -->
   <script>
    const myModal = new bootstrap.Modal(document.getElementById('modalId'), options)
   
   </script>
</x-dashboard.enterprise>
