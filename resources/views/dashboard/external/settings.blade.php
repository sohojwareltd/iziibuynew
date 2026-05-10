<x-dashboard.external>
    <div class="d-flex justify-content-between flex-wrap gap-2">
        <div>

        </div>
        <div class="d-flex flex-column gap-2">
            <a href="{{ route('external.download.plugin') }}" class="btn  btn-outline-primary ">

                <i class="fa fa-download"></i> <span> {{ __('words.download_wordpress_plugin') }}</span>
            </a>

            <a href="{{ route('external.edit') }}" class="btn  btn-outline-primary ">

                <i class="fa fa-edit"></i> <span> {{ __('words.edit') }}</span>
            </a>
            @if ($paymentMethodAccess->subscription->status == 1)
                <a href="{{ route('external.cancel-subscription', $paymentMethodAccess->subscription) }}"
                    class="btn  btn-outline-danger " onclick="return confirm('Are you sure ?')">

                    <i class="fa fa-times"></i> <span> {{ __('words.cancel_subscription') }}</span>
                </a>
            @else
                <a href="{{ route('external.start-subscription', $paymentMethodAccess->subscription) }}"
                    class="btn  btn-outline-success " onclick="return confirm('Are you sure ?')">
                    <i class="fa fa-play"></i> <span> {{ __('words.start_subscription') }}</span>
                </a>
            @endif


        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h4>
                {{ __('words.profile_sec_title') }}
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('external.settings.update') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input type='text' name='name' label="{{ __('words.name') }}"
                            value='{{ auth()->user()->name }}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' name='last_name' label="{{ __('words.last_name') }}"
                            value='{{ auth()->user()->last_name }}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' name='company_name' label="{{ __('words.company_name') }}"
                            value='{{ $paymentMethodAccess->company_name }}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' name='company_domain'
                            label="{{ __('words.company_website_url') }}"
                            value='{{ $paymentMethodAccess->company_domain }}' />
                    </div>
                    <div class="col-md-12">
                        <x-form.input type='text' name='company_registration'
                            label="{{ __('words.company_registration') }}"
                            value='{{ $paymentMethodAccess->company_registration }}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' name='company_email' label="{{ __('words.company_email') }}"
                            value='{{ $paymentMethodAccess->company_email }}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="text" name="company_address[city]"
                            label="{{ __('words.company_address_city') }}" :value="@$paymentMethodAccess->company_address->city" />
                    </div>

                    <div class="col-md-6">
                        <x-form.input type="text" name="company_address[street]"
                            label="{{ __('words.company_address_street') }}" :value="@$paymentMethodAccess->company_address->street" />
                    </div>

                    <div class="col-md-6">
                        <x-form.input type="text" name="company_address[zip]"
                            label="{{ __('words.company_address_zip') }}" :value="@$paymentMethodAccess->company_address->zip" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input type="tel" name="company_address[contact_number]"
                            label="{{ __('words.company_phone') }}" :value="old(
                                'company_address[contact_number]',
                                @$paymentMethodAccess->company_address->contact_number,
                            )" />
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="country">{{ __('words.invoice_country') }}</label>
                            <select name="company_address[country]" id="country" class="form-control">
                                @foreach (App\Constants\Constants::COUNTRIES as $country)
                                    <option @if (@$paymentMethodAccess->company_address->country == $country) selected @endif>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-form.input type="tel" name="meta[booking_phone_prefix]"
                            label="{{ __('words.booking_phone_prefix') }}" :value="old(
                                'meta[booking_phone_prefix]',
                                @$paymentMethodAccess->booking_phone_prefix ?? '+47',
                            )" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency">{{ __('words.currency') }}</label>
                            <select name="meta[currency]" id="currency" class="form-control">
                                <option value="">{{ __('words.choose_a_currency') }}</option>
                                <optgroup label="Europe">
                                    <option value="EUR" @if (@$paymentMethodAccess->currency == 'EUR') selected @endif>EUR - Euro
                                    </option>
                                    <option value="GBP" @if (@$paymentMethodAccess->currency == 'GBP') selected @endif>GBP -
                                        British Pound</option>
                                    <option value="NOK"
                                        @if (@$paymentMethodAccess->currency == 'NOK') selected @elseif($paymentMethodAccess->currency == null) selected @endif>
                                        NOK - Norwegian Krone</option>
                                    <option value="SEK" @if (@$paymentMethodAccess->currency == 'SEK') selected @endif>SEK -
                                        Swedish Krona</option>
                                    <option value="DKK" @if (@$paymentMethodAccess->currency == 'DKK') selected @endif>DKK -
                                        Danish Krone</option>
                                    <option value="PLN" @if (@$paymentMethodAccess->currency == 'PLN') selected @endif>PLN -
                                        Polish Zloty</option>
                                    <option value="CHF" @if (@$paymentMethodAccess->currency == 'CHF') selected @endif>CHF -
                                        Swiss Franc</option>
                                    <option value="CZK" @if (@$paymentMethodAccess->currency == 'CZK') selected @endif>CZK -
                                        Czech Koruna</option>
                                    <option value="HUF" @if (@$paymentMethodAccess->currency == 'HUF') selected @endif>HUF -
                                        Hungarian Forint</option>
                                </optgroup>
                                <optgroup label="America">
                                    <option value="USD" @if (@$paymentMethodAccess->currency == 'USD') selected @endif>USD - US
                                        Dollar</option>
                                    <option value="CAD" @if (@$paymentMethodAccess->currency == 'CAD') selected @endif>CAD -
                                        Canadian Dollar</option>
                                    <option value="MXN" @if (@$paymentMethodAccess->currency == 'MXN') selected @endif>MXN -
                                        Mexican Peso</option>
                                    <option value="BRL" @if (@$paymentMethodAccess->currency == 'BRL') selected @endif>BRL -
                                        Brazilian Real</option>
                                    <option value="ARS" @if (@$paymentMethodAccess->currency == 'ARS') selected @endif>ARS -
                                        Argentine Peso</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fallback_payment_method">{{ __('words.fallback_payment_method') }}</label>
                            <select name="meta[fallback_payment_method]" class="form-control" id="">
                                <option value="">{{ __('words.choose_a_fallback_method') }}</option>
                                <option @if (@$paymentMethodAccess->fallback_payment_method == 'surfboard') selected @endif value="surfboard">
                                    Surfboard</option>
                                <option @if (@$paymentMethodAccess->fallback_payment_method == 'elavon') selected @endif value="elavon">Elavon
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-form.input type="textarea" name="meta[sms_text]" label="{{ __('words.sms_text') }}"
                                :value="old(
                                    'meta[sms_text]',
                                    @$paymentMethodAccess->sms_text ??
                                        'Dear customer, please complete your payment of {TOTAL} for booking {BOOKING_NUMBER}. Pay securely here: {LINK}
                                                                                                            
                                                                                                More text for 2izii: https://iziibuy.com',
                                )" />
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-form.input type="textarea" name="meta[booking_create_page_title]"
                                label="{{ __('words.booking_create_page_title') }}" :value="old(
                                    'meta[booking_create_page_title]',
                                    @$paymentMethodAccess->booking_create_page_title,
                                )" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-form.input type="number" name="meta[tax_percentage]"
                                label="{{ __('words.tax_percentage') }}" :value="old('meta[tax_percentage]', @$paymentMethodAccess->tax_percentage)" />
                        </div>
                    </div>

                </div>
                <button class="btn btn-primary"> <i class="fa fa-save"></i> Update</button>
            </form>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4>
                {{ __('words.password_change_sec_title') }}
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('external.password.update') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input type='password' name='old_pass' label="{{ __('words.old_password') }}"
                            value='' />
                        @error('old_pass')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='password' name='new_pass' label="{{ __('words.new_password') }}"
                            value='' />
                    </div>


                </div>
                <button class="btn btn-primary"> <i class="fa fa-save"></i>
                    "{{ __('words.contract_order_btn') }}"</button>
            </form>
        </div>
    </div>
</x-dashboard.external>
