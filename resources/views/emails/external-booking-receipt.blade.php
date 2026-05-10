<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment Receipt</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap');
    </style>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Open Sans', Arial, sans-serif; color: #333333; background-color: #f7f7f7;">
    <!-- Main Container -->
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
                                    <td style="font-size: 22px; font-weight: bold;">Payment Receipt</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 4px; font-size: 14px; color: #E8EAF6;">Thank
                                        you
                                        for Payment | Order ID: {{ $externalBooking->booking_number }}</td>
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
                                        Order Summary</td>
                                </tr>
                            </table>

                            <!-- Fare Breakdown -->
                            <table width="100%" style="margin-bottom: 8px;">
                                <tr>
                                    <td style="font-size: 16px; color: #424242;">Total</td>
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
                                    <td style="font-size: 20px; font-weight: bold; color: #1565C0;">Total</td>
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
                                        Payment Method</td>
                                </tr>
                                <tr>
                                    @if ($response && is_object($response) && isset($response->card))
                                        <td style="font-size: 16px; font-weight: bold; color: #424242;">
                                            {{ $response->card->getBrand() }}
                                            ***{{ $response->card->getLast4() }}
                                        </td>
                                    @else
                                        <td style="font-size: 16px; font-weight: bold; color: #424242;">
                                            {{ strtoupper($externalBooking->payment_method) }}
                                        </td>
                                    @endif
                                </tr>
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
                                    <td style="font-size: 16px; font-weight: bold; color: #1565C0;">Company:</td>
                                    <td align="right" style="font-size: 16px; font-weight: bold; color: #424242;">
                                        {{ $externalBooking->paymentMethodAccess->company_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 6px; font-size: 14px; color: #757575;">
                                        Thank you for your payment.
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
                                        <a href="{{ route('externalbookinginvoice', $externalBooking) }}"
                                            style="display: inline-block; width: 48%; padding: 12px 0; background-color: #1565C0; color: #ffffff; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">VIEW
                                            RECEIPT</a>
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

</html>
