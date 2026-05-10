<!DOCTYPE html>
<html lang="en">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{__('words.payment_completed')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
       <style>
        .bg-blue {
            background-color: #185196;
        }

        .receipt-card {
            background-color: #185196;
            max-width: 500px;
            border: none;
            color: white;
        }

        .btn-option {
            background-color: #032d61;
            color: white;
            text-align: left;
            margin-bottom: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            margin-top: 1px;
            border-bottom: 4px solid rgba(255, 255, 255, 0.2);
        }

        .btn-option:hover {
            background-color: #041f42;
            color: white;
        }

        .btn-option i {
            margin-right: 10px;
        }

        .divider {
            border-top: 2px solid rgba(255, 255, 255, 0.922);
            margin: 20px 0;
        }

        .check-circle {
            background-color: #1a4b8c;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
        }
    </style>
    <style>
        .bg-blue {
            background-color: #185196;
        }
        .receipt-card {
            background-color: #185196;
            max-width: 500px;
            border: none;
            color: white;
            position: relative; /* added for absolute close button */
        }
        .close-btn {
            position: absolute;
            top: 0px;
            right: 0px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            border: none;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        .close-btn:hover {
            background: rgba(255,255,255,0.18);
        }
    </style>
</head>

<body class="">
        
        <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="receipt-card mx-auto p-4 bg-blue">
                
                        <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="m-0">{{__('words.payment_completed')}}</h2>
            </div>

            <div class="text-center">
                <div class="check-circle">
                    <i class="fas fa-check fs-1 text-white"></i>
                </div>
                <p class="fs-5 my-3">{{__('words.your_payment_was_successful')}}</p>
            </div>

            <p class="text-center mb-1">{{__('words.order_id')}}: #{{ $externalBooking->booking_number }}</p>
            <p class="text-center mb-4">{{__('words.total_amount')}}: {{ Iziibuy::price($externalBooking->total, currency: $externalBooking->currency) }}</p>

            <div class="divider"></div>

            <h5 class="mb-3">{{__('words.how_would_you_like_your_receipt')}}</h5>

            <button type="button" class="btn-option text-center rounded-0" data-bs-toggle="modal"
                data-bs-target="#email_modal">
                <i class="fas fa-envelope"></i> {{__('words.send_to_email')}}
            </button>

            <button type="button" class="btn-option text-center rounded-0" data-bs-toggle="modal"
                data-bs-target="#phone_modal">
                <i class="fas fa-comment-alt"></i> {{__('words.send_sms')}}
            </button>

            <a href="{{ route('externalbookinginvoice', $externalBooking) }}"
                class="btn-option mt-4 text-center rounded-0 w-100 d-block">
                {{__('words.show_receipt')}}
            </a>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="email_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="emailForm" action="{{ route('send.notification') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $externalBooking->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('words.email_receipt')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="email" class="form-control" placeholder="Enter your email address" name="email"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('words.close')}}</button>
                        <button type="submit" class="btn btn-primary" id="emailSubmitBtn">{{__('words.send_receipt')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="phone_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="phoneForm" action="{{ route('send.notification') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $externalBooking->id }}">
                    <input type="hidden" name="phone" id="full_phone"> <!-- This is now the actual 'phone' field -->

                    <div class="modal-header">
                        <h5 class="modal-title">{{__('words.sms_receipt')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="d-flex gap-2 mb-3">
                            <div style="flex: 0 0 120px;">
                                <input type="text" class="form-control" placeholder="+47" id="country_code" value="+47" required>
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" class="form-control" placeholder="Enter your phone number" id="phone_input" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('words.close')}}</button>
                        <button type="submit" class="btn btn-primary" id="phoneSubmitBtn">{{__('words.send_sms')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    
    <script>
        (function () {
            // push a new state so back goes to this same page
            if (history && history.pushState) {
                history.pushState(null, null, location.href);
                // when user tries to go back, redirect them away (home)
                window.addEventListener('popstate', function () {
                    window.location.replace("https://taxi.iziibuy.com");
                });
            }

            // Extra safety: replace and push to make sure going "back" stays here
            try {
                history.replaceState({}, document.title, location.href);
                history.pushState({}, document.title, location.href);
            } catch (e) {
                // ignore in older browsers
            }

            // Prevent caching in some browsers (helps avoid back->cached payment page)
            (function(){
                var m1 = document.createElement('meta');
                m1.httpEquiv = "Cache-Control"; m1.content = "no-cache, no-store, must-revalidate";
                var m2 = document.createElement('meta');
                m2.httpEquiv = "Pragma"; m2.content = "no-cache";
                var m3 = document.createElement('meta');
                m3.httpEquiv = "Expires"; m3.content = "0";
                document.getElementsByTagName('head')[0].appendChild(m1);
                document.getElementsByTagName('head')[0].appendChild(m2);
                document.getElementsByTagName('head')[0].appendChild(m3);
            })();
        })();
    </script>

    <script>
        const closeBtn = document.getElementById('closeReceipt');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        // Try to close tab/window (works if opened via window.open)
                        window.close();

                        // Fallback: redirect to homepage if window not closed
                        setTimeout(function () {
                            // If still open, navigate away
                            if (!window.closed) {
                                window.location.href = "https://taxi.iziibuy.com";
                            }
                        }, 250);
                    });
                }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countryCodeSelect = document.getElementById('country_code');
            const phoneInput = document.getElementById('phone_input');
            const fullPhoneInput = document.getElementById('full_phone');

            function updateFullPhone() {
                const code = countryCodeSelect.value;
                const phone = phoneInput.value.trim();
                fullPhoneInput.value = code + phone;
                console.log('Full phone number:', fullPhoneInput.value);
            }

            // Update on input and select changes
            countryCodeSelect.addEventListener('input', updateFullPhone);
            phoneInput.addEventListener('input', updateFullPhone);

            // Handle email form submission
            const emailForm = document.getElementById('emailForm');
            const emailSubmitBtn = document.getElementById('emailSubmitBtn');
            
            emailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const originalText = emailSubmitBtn.innerHTML;
                emailSubmitBtn.disabled = true;
                emailSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                const formData = new FormData(emailForm);
                
                fetch(emailForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('{{__('words.receipt_sent_to_email_successfully')}}');
                        bootstrap.Modal.getInstance(document.getElementById('email_modal')).hide();
                        emailForm.reset();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{__('words.an_error_occurred_please_try_again')}}');
                })
                .finally(() => {
                    emailSubmitBtn.disabled = false;
                    emailSubmitBtn.innerHTML = originalText;
                });
            });

            // Handle phone form submission
            const phoneForm = document.getElementById('phoneForm');
            const phoneSubmitBtn = document.getElementById('phoneSubmitBtn');
            
            phoneForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Update full phone number before submission
                updateFullPhone();
                
                const originalText = phoneSubmitBtn.innerHTML;
                phoneSubmitBtn.disabled = true;
                phoneSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                const formData = new FormData(phoneForm);
                
                fetch(phoneForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Receipt sent to phone successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('phone_modal')).hide();
                        phoneForm.reset();
                        countryCodeSelect.value = '+47';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{__('words.an_error_occurred_please_try_again')}}');
                })
                .finally(() => {
                    phoneSubmitBtn.disabled = false;
                    phoneSubmitBtn.innerHTML = originalText;
                });
            });
        });
    </script>
</body>

</html>
