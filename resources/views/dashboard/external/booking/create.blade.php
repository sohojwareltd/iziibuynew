<x-iziipay>

    <style>
        :root {
            --bs-body-bg: #2A6495 !important;
            --bs-body-color: #f1f3f6 !important;
            --bs-border-color: #dee2e6 !important;
            --bs-form-bg: #04345C !important;
            --bs-button-bg: #2A6495 !important;
        }

        body {
            background-color: var(--bs-body-bg) !important;
        }

        .form-card-custom {
            background-color: var(--bs-form-bg) !important;
            border: none;
            border-radius: 1rem;
            max-width: 450px;
            margin: 0 auto;
            padding: 3rem !important;
        }

        .form-title-custom {
            color: #f1f3f6 !important;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .form-label-custom {
            color: #c9d2de !important;
            font-size: 0.9rem;
        }

        .form-input-custom {
            background-color: #f1f3f6;
            border: none;
            border-radius: 0.5rem;
            color: #313d5a;
            padding: 0.75rem 1rem;
            height: calc(2.25rem + 1.5rem + 2px);
        }

        .form-input-custom::placeholder {
            color: #aeb8c4;
            opacity: 1;
        }

        .form-input-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(49, 61, 90, 0.25);
            border-color: transparent;
        }

        .form-button-custom {
            background-color: var(--bs-button-bg);
            border-color: var(--bs-button-bg);
            color: var(--bs-body-color);
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            width: 100%;
            margin-top: 1rem;
        }

        .form-button-custom:hover {
            background-color: #496d9f;
            border-color: #496d9f;
        }

        .page-header-old {
            display: none;
        }

        .invalid-feedback {
            color: #ffc99c !important;
        }
    </style>
    <div class="row min-vh-100 d-flex justify-content-center">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('external.dashboard') }}" class="text-light">
                        <i class="fas fa-home me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('external.booking.index') }}" class="text-light">
                        <i class="fas fa-calendar-alt me-1"></i>Booking History
                    </a>
                </li>
                <li class="breadcrumb-item active text-light" aria-current="page">
                    <i class="fas fa-plus me-1"></i>Create Payment Request
                </li>
            </ol>
        </nav>
        <div class="col-12">

            <div class="card shadow-lg border-0 form-card-custom">

                <h2 class="text-center form-title-custom">{{ $paymentMethodAccess->booking_create_page_title }}</h2>

                <div class="card-body p-0">
                    <form id="bookingForm" action="{{ route('external.booking.store') }}" method="POST"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="booking_number"
                                class="form-label form-label-custom">{{ __('words.booking_number') }}</label>
                            <input name="booking_number" id="booking_number" type="text"
                                class="form-control form-control-lg form-input-custom"
                                placeholder="{{ __('words.enter_booking_number') }}" required>
                            <div class="invalid-feedback">
                                {{ __('words.please_enter_booking_number') }}
                            </div>
                            @error('booking_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number"
                                class="form-label form-label-custom">{{ __('words.phone_number') }}</label>
                            <div class="row g-0">
                                <div class="col-3 col-md-3 pe-2">
                                    <input name="country_code" id="country_code" type="tel"
                                        class="form-control form-control-lg form-input-custom" placeholder="+47"
                                        value="{{ $paymentMethodAccess->booking_phone_prefix ?: '+47' }}" required>
                                    @error('country_code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-9 col-md-9">
                                    <input name="phone_number" id="phone_number" type="tel"
                                        class="form-control form-control-lg form-input-custom"
                                        placeholder="{{ __('words.enter_phone_number') }}" required>
                                    <div class="invalid-feedback">
                                        {{ __('words.please_enter_valid_phone_number') }}
                                    </div>
                                    @error('phone_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="total" class="form-label form-label-custom">{{ __('words.amount') }}</label>
                            <input name="total" id="total" type="number"
                                class="form-control form-control-lg form-input-custom" step="0.01" min="0.01"
                                placeholder="{{ __('words.enter_amount') }}" required>
                            <div class="invalid-feedback">
                                {{ __('words.please_enter_valid_amount') }}
                            </div>
                            @error('total')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg form-button-custom" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('words.send_payment_request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bookingForm');
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;

            // --- Improved Feature: Auto-focus on Phone Number ---
            const countryCodeInput = document.getElementById('country_code');
            const phoneNumberInput = document.getElementById('phone_number');

            countryCodeInput.addEventListener('input', function() {
                // 1. Get the current value
                let value = this.value;

                // 2. Remove the leading '+' if it exists (using a regular expression for safety)
                let numericValue = value.replace(/^\+/, '');

                // 3. Check if the remaining numeric part has 2 or more characters
                // This ensures it waits for codes like '47' or '91' and ignores single digits.
                if (numericValue.length >= 2) {
                    phoneNumberInput.focus();
                }
            });
            // -----------------------------------------------------

            // Form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Form validation check is implicitly handled below, but we can stop
                // the AJAX request if the form is invalid before proceeding to the spinner.
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return; // Stop execution if validation fails
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';

                // Get form data
                const formData = new FormData(form);

                // Send AJAX request
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || formData.get('_token')
                        }
                    })
                    .then(response => {
                        // Handle non-200 responses for better error messages
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message ||
                                    'Server responded with an error.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                confirmButtonColor: '#28a745',
                                showCancelButton: true,
                                cancelButtonText: 'Create Another',
                                confirmButtonText: 'View Bookings'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        '{{ route('external.booking.index') }}';
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    form.reset();
                                    form.classList.remove(
                                        'was-validated'); // Reset validation state
                                }
                            });
                        } else {
                            // This handles server-side validation errors not caught by the response.ok check
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message ||
                                'An unexpected error occurred. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
            });

            // Form validation (using Bootstrap's built-in mechanism)
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</x-iziipay>
