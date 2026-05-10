<x-dashboard.enterprise>
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
            <form action="{{route('enterprise.settings.update')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input type='text' name='name' label="{{ __('words.name') }}"
                            value='{{auth()->user()->name}}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' name='last_name' label="{{ __('words.last_name') }}"
                            value='{{auth()->user()->last_name}}' />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type='text' readonly="true" name='' label="{{ __('words.email') }}"
                            value='{{auth()->user()->email}}' />
                    </div>


                    <div class="col-md-6">
                        <x-form.input type="text" name="company_name"
                            label="{{ __('words.company_name') }}" :value="old('company_name',auth()->user()->enterpriseOnboarding->company_name)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="email" name="company_email"
                            label="{{ __('words.company_email') }}" :value="old('company_email',auth()->user()->enterpriseOnboarding->company_email)" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input type="url" placeholder="ex: https://www.example.com"
                            name="company_domain" label="{{ __('words.company_website_url') }}"
                            value="https://" :value="old('company_domain',auth()->user()->enterpriseOnboarding->company_domain)" />
                    </div>

                    <div class="col-md-6">
                        <x-form.input type="text" placeholder="" name="company_registration"
                            label="{{ __('words.company_registration') }}" :value="old('company_registration',auth()->user()->enterpriseOnboarding->company_registration)" />
                    </div>
                    <div class="col-md-6">
                   
                        <x-form.input type="text" name="company_address[city]"
                            label="{{ __('words.company_address_city') }}" :value="old('company_address[city]',@auth()->user()->enterpriseOnboarding->company_address->city)" />
                    </div>

                    <div class="col-md-4">
                        <x-form.input type="text" name="company_address[street]"
                            label="{{ __('words.company_address_street') }}" :value="old('company_address[street]',@auth()->user()->enterpriseOnboarding->company_address->street)" />
                    </div>

                    <div class="col-md-4">
                        <x-form.input type="text" name="company_address[zip]"
                            label="{{ __('words.company_address_zip') }}" :value="old('company_address[zip]',@auth()->user()->enterpriseOnboarding->company_address->zip)" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input type="tel" name="company_address[contact_number]"
                            label="{{ __('words.company_phone') }}" :value="old('company_address[contact_number]',@auth()->user()->enterpriseOnboarding->company_address->contact_number)" />
                    </div>
                    <div class="col-12">
                       
                       <div class="form-group">
                           <label for="country">{{ __('words.invoice_country') }}</label>
                           <select name="company_address[country]" id="country" class="form-control">
                               @foreach (App\Constants\Constants::COUNTRIES as $country)
                                   <option @if (@auth()->user()->enterpriseOnboarding->company_address->country == $country) selected @endif>
                                       {{ $country }}
                                   </option>
                               @endforeach
                           </select>
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
            <form action="{{route('enterprise.password.update')}}" method="post">
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
                <button class="btn btn-primary"> <i class="fa fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</x-dashboard.enterprise>