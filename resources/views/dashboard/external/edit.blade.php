<x-dashboard.external>

   
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
        <div class="card-body">
            <form action="{{ route('external.update') }}" method="post">
                @csrf
                <div class="row">
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
                    <div class="col-8">

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
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="site_mode">App Mode</label>
                        <select name="site_mode" class="form-control" id="site_mode">
                            <option @if (@$paymentMethodAccess->site_mode == 'test') selected @endif value="test">Test</option>
                            <option @if (@$paymentMethodAccess->site_mode == 'live') selected @endif value="live">Live</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary"> <i class="fa fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</x-dashboard.external>
