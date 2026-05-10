<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ __('words.payment_receipt') }}</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap');

        /* Language Dropdown Styles */
        .language-selector {
            position: relative;
            display: inline-block;
            margin: 20px auto;
        }

        .language-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #1565C0, #1E88E5);
            color: #ffffff;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
            transition: all 0.3s ease;
        }

        .language-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(21, 101, 192, 0.4);
        }

        .language-toggle::after {
            content: '▼';
            font-size: 10px;
            transition: transform 0.3s ease;
        }

        .language-toggle.active::after {
            transform: rotate(180deg);
        }

        .language-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            min-width: 180px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
        }

        .language-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .language-option {
            display: block;
            width: 100%;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #424242;
            text-align: left;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .language-option:last-child {
            border-bottom: none;
        }

        .language-option:hover {
            background: linear-gradient(90deg, #E3F2FD, #BBDEFB);
            color: #1565C0;
            padding-left: 25px;
        }

        .language-option.active {
            background: linear-gradient(135deg, #1565C0, #1E88E5);
            color: #ffffff;
            font-weight: 600;
        }

        .language-option.active::before {
            content: '✓ ';
            margin-right: 8px;
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Open Sans', Arial, sans-serif; color: #333333; background-color: #f7f7f7;">
    <!-- Main Container -->
    <div class="" style="width: 420px; margin: 0 auto;text-align: center;">
        @if (App\Constants\Constants::LANGUAGES['status'])
            <div class="language-selector">
                <button class="language-toggle" type="button" aria-expanded="false">
                    🌐 {!! strip_tags(__('words.languageer')) !!} ({{ strtoupper(app()->getLocale()) }})
                </button>
                <div class="language-dropdown">
                    @foreach (App\Constants\Constants::LANGUAGES['list'] as $language => $key)
                        <button class="language-option @if (app()->getLocale() == $key) active @endif"
                            onclick="lan('{{ $key }}')">{{ $language }}</button>
                    @endforeach
                </div>
            </div>
        @endif



    </div>
    <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#ECEFF1"
        style="font-family: Arial, sans-serif; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 420px; background-color: #ECEFF1;">

                    <!-- Header Card -->
                    <tr>
                        <td
                            style="border-radius: 8px; background: linear-gradient(90deg,#1565C0,#1E88E5); padding: 20px; color: #ffffff;">
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 22px; font-weight: bold;">{{ __('words.payment_receipt') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 4px; font-size: 14px; color: #E8EAF6;">
                                        {{ __('words.thank_you_for_payment') }} | {{ __('words.booking_number') }}:
                                        {{ $externalBooking->booking_number }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Spacer -->
                    <tr>
                        <td style="height: 16px;"></td>
                    </tr>

                    <!-- Receipt Card -->
                    <tr>
                        <td
                            style="background: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">

                            <!-- Trip Summary Title -->
                            <table width="100%">
                                <tr>
                                    <td
                                        style="font-size: 18px; font-weight: bold; color: #1565C0; padding-bottom: 12px;">
                                        {{ __('words.booking_summary') }}</td>
                                </tr>
                            </table>

                            <!-- Fare Breakdown -->
                            <table width="100%" style="margin-bottom: 8px;">
                                <tr>
                                    <td style="font-size: 16px; color: #424242;">{{ __('words.total') }}</td>
                                    <td align="right" style="font-size: 16px; font-weight: bold; color: #424242;">
                                        {{ Iziibuy::price($externalBooking->total, currency: $externalBooking->currency) }}
                                    </td>
                                </tr>
                            </table>
                            <!-- Divider -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-bottom: 1px solid #E0E0E0; height: 12px;"></td>
                                </tr>
                            </table>

                            <!-- Total Fare -->
                            <table width="100%"
                                style="margin-top: 12px; background-color: #F5F5F5; border-radius: 6px;"
                                cellpadding="10">
                                <tr>
                                    <td style="font-size: 20px; font-weight: bold; color: #1565C0;">
                                        {{ __('words.total') }}</td>
                                    <td align="right" style="font-size: 24px; font-weight: bold; color: #1565C0;">
                                        {{ Iziibuy::price($externalBooking->total, currency: $externalBooking->currency) }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Spacer -->
                            <table width="100%">
                                <tr>
                                    <td style="height: 20px;"></td>
                                </tr>
                            </table>
                            <!-- Payment Method -->
                            <table width="100%" style="background-color: #FAFAFA; border-radius: 6px;"
                                cellpadding="12">
                                <tr>
                                    <td colspan="2"
                                        style="font-size: 16px; font-weight: bold; color: #1565C0; padding-bottom: 8px;">
                                        {{ __('words.payment_method') }}</td>
                                </tr>
                                @if ($externalBooking->payment_method == 'elavon')
                                    <tr>

                                        <td style="font-size: 16px; font-weight: bold; color: #424242;">
                                            {{ $response->card->getBrand() }}
                                            ***{{ $response->card->getLast4() }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($externalBooking->payment_method == 'surfboard')
                                    <tr>
                                        <td style="font-size: 16px; font-weight: bold; color: #424242;">
                                            {{ __('words.surfboard') }}
                                            ***{{ __('words.n_a') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="font-size: 14px; color: #757575;">
                                        {{ $externalBooking->paid_at->format('d/m/Y h:i A') }}</td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr>
                                    <td style="height: 20px;"></td>
                                </tr>
                            </table>
                            <table width="100%" style="margin-bottom: 12px;">
                                <tr>
                                    <td style="font-size: 16px; font-weight: bold; color: #1565C0;">
                                        {{ __('words.company_name') }}:</td>
                                    <td align="right" style="font-size: 16px; font-weight: bold; color: #424242;">
                                        {{ $externalBooking->paymentMethodAccess->company_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 6px; font-size: 14px; color: #757575;">
                                        {{ __('words.thank_you_for_your_payment') }}
                                    </td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr>
                                    <td style="height: 20px;"></td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 8px;">
                                        <a href="{{ route('paymentcompleted', $externalBooking) }}"
                                            style="display: inline-block; width: 48%; padding: 12px 0; background-color: #1565C0; color: #ffffff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">{{ __('words.how_would_you_like_your_receipt') }}</a>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

<script>
    function lan(e) {
        var currentUrl = window.location.href;
        var url = new URL(currentUrl);
        url.searchParams.set("lang", e);
        var newUrl = url.href;
        window.location = newUrl;
    }

    // Language dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.querySelector('.language-toggle');
        var dropdown = document.querySelector('.language-dropdown');

        if (toggle && dropdown) {
            // Toggle dropdown on click
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isOpen = dropdown.classList.contains('show');
                dropdown.classList.toggle('show');
                toggle.classList.toggle('active');
                toggle.setAttribute('aria-expanded', !isOpen);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    toggle.classList.remove('active');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            // Close dropdown when selecting a language
            var options = dropdown.querySelectorAll('.language-option');
            options.forEach(function(option) {
                option.addEventListener('click', function() {
                    dropdown.classList.remove('show');
                    toggle.classList.remove('active');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });
        }
    });
</script>

</html>
