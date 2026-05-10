@extends('voyager::master')

@section('page_title', 'Invoice - Subscription Charge #' . $dataTypeContent->id)

@php
    $messages = [
        '20000' => 'Approved',
        '40000' => 'Declined',
        '40110' => 'Invalid card number',
        '40111' => 'Unsupported card scheme',
        '40120' => 'Invalid CSC',
        '40130' => 'Invalid expire date',
        '40135' => 'Card expired',
        '40140' => 'Invalid currency',
        '40150' => 'Invalid text on statement',
        '40200' => 'Clearhaus rule violation',
        '40300' => '3-D Secure problem',
        '40310' => '3-D Secure authentication failure',
        '40400' => 'Backend problem',
        '40410' => 'Declined by issuer or card scheme',
        '40411' => 'Card restricted',
        '40412' => 'Card lost or stolen',
        '40413' => 'Insufficient funds',
        '40414' => 'Suspected fraud',
        '40415' => 'Amount limit exceeded',
        '40416' => 'Additional authentication required',
        '40420' => 'Merchant blocked by cardholder',
        '50000' => 'Clearhaus error',
    ];
    
    $charge = $dataTypeContent;
    $subscription = $charge->subscription ?? null;
    $subscribable = $subscription->subscribable ?? null;
    $chargeDetails = $charge->charge_details ? json_decode($charge->charge_details) : null;
    $paymentDetails = $charge->payment_details ? json_decode($charge->payment_details) : null;
@endphp

@section('page_header')
    <div class="container-fluid hidden-print">
        <h1 class="page-title">
            <i class="voyager-receipt"></i> Invoice - Charge #{{ $charge->id }}
        </h1>
        <a href="{{ route('voyager.' . $dataType->slug . '.show', $charge->id) }}" class="btn btn-warning">
            <i class="voyager-eye"></i> View Details
        </a>
        <a href="{{ route('voyager.' . $dataType->slug . '.index') }}" class="btn btn-info">
            <i class="voyager-list"></i> Back to List
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="voyager-print"></i> Print Invoice
        </button>
        <button onclick="window.print(); return false;" class="btn btn-success" id="download-pdf">
            <i class="voyager-download"></i> Download PDF
        </button>
    </div>
@stop

@section('content')
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1.5cm 1.2cm;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: #000 !important;
                font-size: 10pt !important;
                line-height: 1.3 !important;
                font-family: "Times New Roman", Times, serif !important;
            }
            
            .hidden-print {
                display: none !important;
            }
            
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            
            .invoice-header {
                border-bottom: 2px solid #000 !important;
                padding-bottom: 10px !important;
                margin-bottom: 12px !important;
                page-break-inside: avoid;
            }
            
            .invoice-logo {
                font-size: 18pt !important;
                color: #000 !important;
                margin-bottom: 3px !important;
            }
            
            .invoice-title {
                font-size: 22pt !important;
                color: #000 !important;
                margin: 3px 0 !important;
            }
            
            .invoice-meta {
                font-size: 9pt !important;
                color: #000 !important;
            }
            
            .amount-box {
                background: #f5f5f5 !important;
                border: 2px solid #000 !important;
                padding: 12px !important;
                margin: 12px 0 !important;
                page-break-inside: avoid;
            }
            
            .amount-label {
                font-size: 9pt !important;
                color: #000 !important;
                margin-bottom: 2px !important;
            }
            
            .amount-value {
                font-size: 26pt !important;
                color: #000 !important;
            }
            
            .section-title {
                border-bottom: 1px solid #000 !important;
                color: #000 !important;
                font-size: 12pt !important;
                font-weight: bold !important;
                margin-bottom: 8px !important;
                padding-bottom: 4px !important;
                page-break-after: avoid;
            }
            
            .invoice-section {
                page-break-inside: avoid;
                margin-bottom: 12px !important;
            }
            
            .table-invoice {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: avoid;
                margin: 8px 0 !important;
                font-size: 9pt !important;
            }
            
            .table-invoice th {
                background: #f5f5f5 !important;
                border: 1px solid #000 !important;
                padding: 5px 6px !important;
                font-weight: bold !important;
                font-size: 9pt !important;
                text-align: left !important;
            }
            
            .table-invoice td {
                border: 1px solid #000 !important;
                padding: 5px 6px !important;
                font-size: 9pt !important;
            }
            
            .table-invoice tr {
                page-break-inside: avoid;
            }
            
            .two-columns {
                display: block !important;
                page-break-inside: avoid;
            }
            
            .two-columns > div {
                margin-bottom: 10px !important;
            }
            
            .info-row {
                margin-bottom: 4px !important;
                font-size: 9pt !important;
            }
            
            .info-label {
                font-weight: bold !important;
                color: #000 !important;
            }
            
            .info-value {
                color: #000 !important;
            }
            
            .footer-note {
                page-break-inside: avoid;
                margin-top: 15px !important;
                padding-top: 8px !important;
                border-top: 1px solid #000 !important;
                font-size: 8pt !important;
                color: #000 !important;
            }
            
            a {
                text-decoration: none !important;
                color: #000 !important;
            }
            
            .status-badge {
                border: 1px solid #000 !important;
                background: #f5f5f5 !important;
                color: #000 !important;
                padding: 2px 6px !important;
                font-size: 8pt !important;
            }
            
            pre {
                background: #f5f5f5 !important;
                border: 1px solid #000 !important;
                padding: 6px !important;
                font-size: 8pt !important;
                page-break-inside: avoid;
            }
            
            small {
                font-size: 8pt !important;
            }
            
            /* Avoid breaking important sections */
            .amount-box,
            .invoice-header,
            .invoice-section:first-of-type {
                page-break-after: avoid;
            }
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .invoice-header {
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .invoice-logo {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 0;
        }

        .invoice-meta {
            font-size: 11px;
            color: #000;
            margin-top: 5px;
        }

        .invoice-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .info-label {
            font-weight: 600;
            color: #000;
            width: 140px;
            flex-shrink: 0;
        }

        .info-value {
            color: #000;
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #000;
            background: #f5f5f5;
            color: #000;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #f5f5f5;
            color: #000;
            border: 1px solid #000;
        }

        .status-declined {
            background-color: #f5f5f5;
            color: #000;
            border: 1px solid #000;
        }

        .status-pending {
            background-color: #f5f5f5;
            color: #000;
            border: 1px solid #000;
        }

        .amount-box {
            background: #f5f5f5;
            color: #000;
            padding: 15px;
            border: 2px solid #000;
            text-align: center;
            margin: 15px 0;
        }

        .amount-label {
            font-size: 11px;
            margin-bottom: 3px;
            color: #000;
        }

        .amount-value {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #000;
        }

        .table-invoice {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        .table-invoice th {
            background-color: #f5f5f5;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            color: #000;
            border: 1px solid #000;
        }

        .table-invoice td {
            padding: 6px 8px;
            border: 1px solid #000;
            color: #000;
        }

        .table-invoice tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer-note {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eaeaea;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-size: 11px;
            overflow-x: auto;
            margin: 0;
        }

        @media print {
            pre {
                background: #f5f5f5 !important;
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
        }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
            .invoice-container {
                padding: 20px;
            }
        }
    </style>

    <div class="invoice-container">
        {{-- Invoice Header --}}
        <div class="invoice-header">
            <div class="invoice-logo">{{ setting('site.title', env('APP_NAME')) }}</div>
            <h1 class="invoice-title">INVOICE</h1>
            <div class="invoice-meta">
                Invoice #{{ $charge->id }} | 
                Date: {{ $charge->created_at->format('F d, Y') }} | 
                @if($transiction && isset($transiction->processed_at))
                    Processed: {{ \Carbon\Carbon::parse($transiction->processed_at)->format('F d, Y H:i') }}
                @endif
            </div>
        </div>

        {{-- Amount Box --}}
        <div class="amount-box">
            <div class="amount-label">Total Amount</div>
            <div class="amount-value">
                {{ number_format($charge->amount, 2) }} 
                {{ $transiction->currency ?? 'NOK' }}
            </div>
            @if($transiction && isset($transiction->status))
                <span class="status-badge @if($transiction->status->code == '20000') status-approved @elseif($transiction->status->code == '40000') status-declined @else status-pending @endif">
                    {{ $transiction->status->code == '20000' ? 'Approved' : ($messages[$transiction->status->code] ?? 'Pending') }}
                </span>
            @endif
        </div>

        {{-- Customer & Merchant Info --}}
        <div class="two-columns invoice-section">
            <div>
                <div class="section-title">Bill To</div>
                @if($subscribable)
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $subscribable->name ?? $subscribable->title ?? 'N/A' }}</div>
                    </div>
                    @if(isset($subscribable->email))
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value">{{ $subscribable->email }}</div>
                        </div>
                    @endif
                    @if(isset($subscribable->contact_email))
                        <div class="info-row">
                            <div class="info-label">Contact:</div>
                            <div class="info-value">{{ $subscribable->contact_email }}</div>
                        </div>
                    @endif
                    @if(isset($subscribable->company_domain) || isset($subscribable->domain))
                        <div class="info-row">
                            <div class="info-label">Domain:</div>
                            <div class="info-value">{{ $subscribable->company_domain ?? $subscribable->domain ?? 'N/A' }}</div>
                        </div>
                    @endif
                @else
                    <div class="info-row">
                        <div class="info-value">Subscription information not available</div>
                    </div>
                @endif
            </div>

            <div>
                <div class="section-title">Merchant Information</div>
                <div class="info-row">
                    <div class="info-label">Company:</div>
                    <div class="info-value">{{ setting('site.title', env('APP_NAME')) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Website:</div>
                    <div class="info-value">{{ url('/') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ setting('site.email', 'support@' . parse_url(url('/'), PHP_URL_HOST)) }}</div>
                </div>
            </div>
        </div>

        {{-- Charge Details --}}
        <div class="invoice-section">
            <div class="section-title">Charge Details</div>
            <table class="table-invoice">
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
                <tr>
                    <td>
                        <strong>Subscription Charge</strong><br>
                        <small style="color: #999;">
                            @if($subscription)
                                Subscription ID: {{ $subscription->id }}
                                @if($subscribable)
                                    | {{ class_basename($subscribable) }}
                                @endif
                            @endif
                        </small>
                    </td>
                    <td class="text-right">
                        <strong>{{ number_format($charge->amount, 2) }} {{ $transiction->currency ?? 'NOK' }}</strong>
                    </td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($charge->amount, 2) }} {{ $transiction->currency ?? 'NOK' }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Payment Information --}}
        @if($transiction)
        <div class="invoice-section">
            <div class="section-title">Payment Details</div>
            <table class="table-invoice">
                <tr>
                    <th style="width: 40%;">Payment Information</th>
                    <th style="width: 60%;">Details</th>
                </tr>
                <tr>
                    <td><strong>Transaction ID</strong></td>
                    <td>{{ $transiction->id ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Reference Number</strong></td>
                    <td>{{ $transiction->reference ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Method</strong></td>
                    <td>{{ $transiction->payment_method ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Transaction Type</strong></td>
                    <td>
                        <span class="status-badge @if(($transiction->type ?? '') == 'authorization') status-pending @else status-approved @endif">
                            {{ ucfirst($transiction->type ?? '-') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Card Information</strong></td>
                    <td>
                        @if(isset($transiction->card) && $transiction->card)
                            <strong>{{ $transiction->card->bin ?? '****' }} *** {{ $transiction->card->last4 ?? '****' }}</strong>
                            @if(isset($transiction->card->expires_at))
                                <br><small style="color: #999;">Expires: {{ $transiction->card->expires_at }}</small>
                            @endif
                        @elseif($chargeDetails && isset($chargeDetails->metadata->last4))
                            <strong>**** **** **** {{ $chargeDetails->metadata->last4 }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>3-D Secure Authentication</strong></td>
                    <td>
                        @if(isset($transiction->threed_secure) && $transiction->threed_secure)
                            <span class="status-badge status-approved">Enabled</span> - {{ $transiction->threed_secure }}
                        @else
                            <span class="status-badge status-pending">Not Required</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Retrieval Reference Number (RRN)</strong></td>
                    <td>{{ $transiction->rrn ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Acquirer Reference Number (ARN)</strong></td>
                    <td>{{ $transiction->arn ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Text on Statement</strong></td>
                    <td>{{ $transiction->text_on_statement ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Recurring Payment</strong></td>
                    <td>
                        @if(isset($transiction->recurring) && $transiction->recurring)
                            <span class="status-badge status-approved">Yes</span>
                        @else
                            <span class="status-badge status-pending">No</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Processed Date & Time</strong></td>
                    <td>
                        @if(isset($transiction->processed_at))
                            {{ \Carbon\Carbon::parse($transiction->processed_at)->format('F d, Y \a\t H:i:s') }}
                        @else
                            {{ $charge->created_at->format('F d, Y \a\t H:i:s') }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Charge Details (from charge_details JSON) --}}
        @if($chargeDetails)
        <div class="invoice-section">
            <div class="section-title">Charge Details</div>
            <table class="table-invoice">
                <tr>
                    <th style="width: 40%;">Field</th>
                    <th style="width: 60%;">Value</th>
                </tr>
                @if(isset($chargeDetails->id))
                    <tr>
                        <td><strong>Charge ID</strong></td>
                        <td>{{ $chargeDetails->id }}</td>
                    </tr>
                @endif
                @if(isset($chargeDetails->order_id))
                    <tr>
                        <td><strong>Order ID</strong></td>
                        <td>{{ $chargeDetails->order_id }}</td>
                    </tr>
                @endif
                @if(isset($chargeDetails->state))
                    <tr>
                        <td><strong>State</strong></td>
                        <td>
                            <span class="status-badge @if($chargeDetails->state == 'processed') status-approved @else status-pending @endif">
                                {{ ucfirst($chargeDetails->state ?? '-') }}
                            </span>
                        </td>
                    </tr>
                @endif
                @if(isset($chargeDetails->amount))
                    <tr>
                        <td><strong>Charge Amount</strong></td>
                        <td>{{ number_format($chargeDetails->amount / 100, 2) }} {{ $chargeDetails->currency ?? 'NOK' }}</td>
                    </tr>
                @endif
                @if(isset($chargeDetails->currency))
                    <tr>
                        <td><strong>Currency</strong></td>
                        <td>{{ $chargeDetails->currency }}</td>
                    </tr>
                @endif
                @if(isset($chargeDetails->created_at))
                    <tr>
                        <td><strong>Created At</strong></td>
                        <td>{{ \Carbon\Carbon::parse($chargeDetails->created_at)->format('F d, Y H:i:s') }}</td>
                    </tr>
                @endif
                @if(isset($chargeDetails->metadata))
                    <tr>
                        <td colspan="2"><strong>Metadata</strong></td>
                    </tr>
                    @if(isset($chargeDetails->metadata->brand))
                        <tr>
                            <td style="padding-left: 30px;">Card Brand</td>
                            <td>{{ $chargeDetails->metadata->brand }}</td>
                        </tr>
                    @endif
                    @if(isset($chargeDetails->metadata->country))
                        <tr>
                            <td style="padding-left: 30px;">Country</td>
                            <td>{{ $chargeDetails->metadata->country }}</td>
                        </tr>
                    @endif
                    @if(isset($chargeDetails->metadata->last4))
                        <tr>
                            <td style="padding-left: 30px;">Last 4 Digits</td>
                            <td>{{ $chargeDetails->metadata->last4 }}</td>
                        </tr>
                    @endif
                    @if(isset($chargeDetails->metadata->exp_month) || isset($chargeDetails->metadata->exp_year))
                        <tr>
                            <td style="padding-left: 30px;">Expiry</td>
                            <td>
                                @if(isset($chargeDetails->metadata->exp_month) && isset($chargeDetails->metadata->exp_year))
                                    {{ str_pad($chargeDetails->metadata->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $chargeDetails->metadata->exp_year }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endif
                @endif
                @if(isset($chargeDetails->operations))
                    <tr>
                        <td><strong>Operations</strong></td>
                        <td>
                            @foreach($chargeDetails->operations as $operation)
                                <div style="margin-bottom: 5px;">
                                    <strong>{{ $operation->type ?? 'Operation' }}</strong> - 
                                    @if(isset($operation->state))
                                        <span class="status-badge @if($operation->state == 'processed') status-approved @else status-pending @endif">
                                            {{ ucfirst($operation->state) }}
                                        </span>
                                    @endif
                                    @if(isset($operation->created_at))
                                        ({{ \Carbon\Carbon::parse($operation->created_at)->format('M d, Y H:i') }})
                                    @endif
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        @endif

        {{-- Payment Details (from payment_details JSON) --}}
        @if($paymentDetails)
        <div class="invoice-section">
            <div class="section-title">Payment Details</div>
            <table class="table-invoice">
                <tr>
                    <th style="width: 40%;">Field</th>
                    <th style="width: 60%;">Value</th>
                </tr>
                @if(isset($paymentDetails->enterprise))
                    <tr>
                        <td colspan="2"><strong>Enterprise Information</strong></td>
                    </tr>
                    @if(isset($paymentDetails->enterprise->uid))
                        <tr>
                            <td style="padding-left: 30px;">Enterprise UID</td>
                            <td>{{ $paymentDetails->enterprise->uid }}</td>
                        </tr>
                    @endif
                    @if(isset($paymentDetails->enterprise->name))
                        <tr>
                            <td style="padding-left: 30px;">Enterprise Name</td>
                            <td>{{ $paymentDetails->enterprise->name }}</td>
                        </tr>
                    @endif
                    @if(isset($paymentDetails->enterprise->domain))
                        <tr>
                            <td style="padding-left: 30px;">Domain</td>
                            <td>{{ $paymentDetails->enterprise->domain }}</td>
                        </tr>
                    @endif
                @endif
                @if(isset($paymentDetails->detials))
                    <tr>
                        <td colspan="2"><strong>Additional Details</strong></td>
                    </tr>
                    @if(is_object($paymentDetails->detials) || is_array($paymentDetails->detials))
                        @foreach($paymentDetails->detials as $key => $value)
                            <tr>
                                <td style="padding-left: 30px;">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                <td>
                                    @if(is_object($value) || is_array($value))
                                        <pre style="margin: 0; font-size: 11px;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="padding-left: 30px;">Details</td>
                            <td>{{ $paymentDetails->detials }}</td>
                        </tr>
                    @endif
                @endif
            </table>
        </div>
        @endif

        {{-- Transaction Status --}}
        @if($transiction && isset($transiction->status))
        <div class="invoice-section">
            <div class="section-title">Transaction Status</div>
            <div class="two-columns">
                <div>
                    <div class="info-row">
                        <div class="info-label">Status Code:</div>
                        <div class="info-value">{{ $transiction->status->code ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status Message:</div>
                        <div class="info-value">{{ $messages[$transiction->status->code] ?? 'Unknown' }}</div>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <div class="info-label">Recurring:</div>
                        <div class="info-value">{{ (isset($transiction->recurring) && $transiction->recurring) ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Text on Statement:</div>
                        <div class="info-value">{{ $transiction->text_on_statement ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Settlement Information --}}
        @if(isset($transiction->settlement))
        <div class="invoice-section">
            <div class="section-title">Settlement Details</div>
            <table class="table-invoice">
                <tr>
                    <th>Item</th>
                    <th class="text-right">Amount</th>
                </tr>
                <tr>
                    <td>Amount Gross</td>
                    <td class="text-right">{{ number_format($transiction->amount / 100, 2) }} {{ $transiction->settlement->currency ?? $transiction->currency }}</td>
                </tr>
                <tr>
                    <td>Fees</td>
                    <td class="text-right">{{ number_format($transiction->settlement->fees / 100, 2) }} {{ $transiction->settlement->currency ?? $transiction->currency }}</td>
                </tr>
                @if(isset($transiction->settlement->fee_details))
                    @foreach($transiction->settlement->fee_details as $fee)
                        <tr>
                            <td style="padding-left: 30px; color: #999;">{{ $fee->type ?? 'Fee' }}</td>
                            <td class="text-right" style="color: #999;">{{ number_format($fee->amount / 100, 2) }} {{ $transiction->settlement->currency ?? $transiction->currency }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr style="background-color: #f8f9fa;">
                    <td><strong>Amount Net</strong></td>
                    <td class="text-right">
                        <strong>
                            {{ number_format(($transiction->amount - ($transiction->settlement->fees ?? 0)) / 100, 2) }} 
                            {{ $transiction->settlement->currency ?? $transiction->currency }}
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer-note">
            <p>This is an automated invoice generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
            <p>For questions regarding this invoice, please contact our support team.</p>
            <p style="margin-top: 20px; font-size: 10px; color: #ccc;">
                Invoice ID: {{ $charge->id }} | 
                @if($transiction && isset($transiction->id))
                    Transaction ID: {{ $transiction->id }}
                @endif
            </p>
        </div>
    </div>

    <script>
        document.getElementById('download-pdf')?.addEventListener('click', function() {
            window.print();
        });
    </script>
@stop

