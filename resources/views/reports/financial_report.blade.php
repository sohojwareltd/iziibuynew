<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financial Report</title>
    <style>
        html,
        body {
            width: 100%;
        }

        @page {
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 8px;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 6px 0;
        }

        p {
            margin: 0 0 8px 0;
        }

        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 4px 6px;
            border: 1px solid #000;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        th {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .mt-8 {
            margin-top: 6px;
        }

        /* Prevent rows from splitting across pages and keep header repeated on new pages */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr, td, th { page-break-inside: avoid; }
        table { page-break-inside: auto; }

        /* Allow horizontal scroll in browsers if content exceeds width */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        @media print {
            .table-container { overflow: visible; }
        }
    </style>
</head>

<body>
    <h1>Financial report</h1>
    <p>Period: {{ $from ? $from->format('Y-m-d') : 'N/A' }} to {{ $to ? $to->format('Y-m-d') : 'N/A' }}</p>

    @isset($summary)
        <table class="mt-8" style="width: 95%;margin:0 auto;">
            <tr>
                <th>Total amount</th>
                <td class="right">{{ number_format($summary['total_amount'] ?? 0, 2) }}</td>
                <th>Total entries</th>
                <td class="right">{{ $summary['total_count'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>Charges amount</th>
                <td class="right">{{ number_format($summary['total_charges_amount'] ?? 0, 2) }}</td>
                <th>Subscription amount</th>
                <td class="right">{{ number_format($summary['total_subscription_amount'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Unique customers</th>
                <td class="right">{{ $summary['unique_customers'] ?? 0 }}</td>
                <th></th>
                <td></td>
            </tr>
        </table>
    @endisset

    <div class="table-container mt-8">
    <table style="width: 95%;margin:0 auto;">
        <thead>
            <tr>
                <th style="width:10%">Customer name</th>
                <th style="width:5%">Amount</th>
                <th style="width:12%">Invoice details</th>
                <th style="width:9%">Payment date</th>
                <th style="width:6%">Server <br> ID</th>
                <th style="width:6%">Customer <br> ID</th>
                <th style="width:8%">Shop (slug)</th>
                <th style="width:6%">Card</th>
                <th style="width:5%">Period</th>
                <th style="width:7%">Bet period</th>
                <th style="width:9%">Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
            
                <tr>
                    <td style="width:10%">{{ $row['customer_name'] }}</td>
                    <td style="width:5%">{{ number_format($row['amount'], 2) }}</td>
                    <td style="width:12%">{{ $row['invoice_details'] }}</td>
                    <td style="width:9%">{{ Carbon\Carbon::parse($row['payment_date'])->format('d-m-Y') }}</td>
                    <td style="width:6%">{{ $row['server_id'] }}</td>
                    <td style="width:6%">{{ $row['customer_id'] }}</td>
                    <td style="width:8%">{{ $row['shop_slug'] }}</td>
                    <td style="width:6%">{{ $row['card_number'] }}</td>
                    <td style="width:5%">{{ $row['period'] }}</td>
                    <td style="width:6%">{{ $row['bet_period'] }}</td>
                    <td style="width:6%">{{ $row['email'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</body>

</html>
