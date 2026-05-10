<x-iziipay>
    <style>
        :root {
            --page-bg: #2a6495;
            --page-bg-end: #295e78;
            --panel-bg: #04345c;
            --status-approved: #39b54a;
            --status-pending: #f5a623;
            --status-pending-text: #3a2a08;
            --status-declined: #ef4b4b;
            --white: #ffffff;
            --header-text: #0b4e6a;
            --cell-text: #0e4b67;
            --table-sep: #0f5b78;
            --txn-color: #2a6495;
            --pagination-border: #ccc;
            --pagination-active-bg: #093645;
            --pagination-active-color: #fff;
            --btn-bg: #fff;
            --btn-color: #2a6495;
            --btn-hover-bg: #2a6495;
            --btn-hover-color: #fff;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: linear-gradient(180deg, var(--page-bg), var(--page-bg-end) 120%);
            color: var(--white);
        }

        .page-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .panel {
            width: 100%;
            max-width: 800px;
            /* Adjusted max-width for a single detail view */
            background: var(--panel-bg);
            border-radius: 12px;
            padding: 34px;
        }

        .panel-header h1 {
            font-weight: 700;
            font-size: 22px;
            margin: 0 0 10px 0;
        }

        .panel-header p {
            color: #accbd8;
            font-size: 13px;
            margin: 0;
        }

        .info-card {
            background-color: #6d93df73;
            /* Using a lighter shade for the info box */
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #04266873;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            /* Lighter separator */
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #accbd8;
            /* Lighter color for label */
        }

        .detail-value {
            color: var(--white);
            font-weight: 600;
        }

        .detail-value.total {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 999px;
            color: var(--white);
            font-weight: 600;
            font-size: 13px;
        }

        .badge-approved {
            background: var(--status-approved);
        }

        .badge-pending {
            background: var(--status-pending);
            color: var(--status-pending-text);
        }

        .badge-declined {
            background: var(--status-declined);
        }

        /* Buttons from index.blade.php */
        .btn-pill {
            padding: 10px 18px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            background: var(--btn-bg);
            color: var(--btn-color);
            min-width: 120px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-pill:hover {
            background: var(--btn-hover-bg);
            color: var(--btn-hover-color);
            cursor: pointer;
        }

        .btn-pill.btn-success-payment {
            /* Overriding to match a 'Pay Now' button feel */
            background: var(--status-approved);
            color: var(--white);
        }

        .btn-pill.btn-success-payment:hover {
            background: #218838;
            /* Slightly darker green */
        }


        @media print {
            .panel {
                box-shadow: none !important;
            }

            .btn,
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="page-wrap">
        <div class="panel">
            <div class="panel-header mb-4">
                <h1>{{__('words.booking_payment')}}</h1>
                <p>{{__('words.details_for_booking')}} #{{ $externalBooking->booking_number }}</p>
            </div>

            <div
                class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light border-opacity-25">
                <div>
                    <h4 class="mb-1">{{ $externalBooking->total ? Iziibuy::price($externalBooking->total, currency: $externalBooking->currency) : 'N/A' }}
                    </h4>
                    <p class="mb-0">{{ $externalBooking->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <span
                        class="badge-status {{ $externalBooking->status == 'PENDING' ? 'badge-pending' : ($externalBooking->status == 'DECLINED' ? 'badge-declined' : 'badge-approved') }}">
                        {{ $externalBooking->status }}
                    </span>
                </div>
            </div>

            <div class="info-card mb-4">
                <h6 class="text-light mb-3 fw-bold">
                    <i class="fas fa-info-circle me-2"></i>{{__('words.booking_information')}}
                </h6>
                <div class="detail-row">
                    <span class="detail-label">{{__('words.booking_number')}}:</span>
                    <span class="detail-value">{{ $externalBooking->booking_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{__('words.phone_number')}}:</span>
                    <span class="detail-value">{{ $externalBooking->phone_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{__('words.created_at')}}:</span>
                    <span class="detail-value">{{ $externalBooking->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if ($externalBooking->paid_at)
                    <div class="detail-row">
                        <span class="detail-label">{{__('words.paid_at')}}:</span>
                        <span class="detail-value">{{ $externalBooking->paid_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">{{__('words.total_amount_due')}}:</span>
                    <span class="detail-value total">{{ Iziibuy::price($externalBooking->total, currency: $externalBooking->currency) }}</span>
                </div>
            </div>

            <div class="text-center">
                @if ($externalBooking->payment_status != 'PAID')
                    <div class="p-4 rounded-3 mb-4" style="background: rgba(255, 255, 255, 0.05);">
                        <p class="mb-3 text-light fw-bold h5">{{__('words.payment_required')}}</p>
                        <p class="mb-3 text-light opacity-75">{{__('words.complete_your_payment_to_confirm_this_booking')}}</p>
                        <a href="{{ route('external-payment', $externalBooking->ulid) }}"
                            class="btn-pill btn-success-payment btn-lg">
                            <i class="fas fa-credit-card me-2"></i>{{__('words.pay_now')}} -
                            {{ Iziibuy::price($externalBooking->total, currency: $externalBooking->currency ) }}
                        </a>
                    </div>
                @elseif($externalBooking->payment_status == 'PAID')
                    <div class="alert alert-success p-4 rounded-3">
                        <i class="fas fa-check-circle me-2 fa-2x"></i>
                        <strong class="h5 d-block mt-2">{{__('words.payment_completed')}}</strong>
                        <small>{{__('words.thank_you_for_your_payment_your_booking_has_been_confirmed')}}</small>
                    </div>
                @endif
                <button class="btn-pill mt-3" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> {{__('words.print_details')}}
                </button>
            </div>

        </div>
    </div>
</x-iziipay>
