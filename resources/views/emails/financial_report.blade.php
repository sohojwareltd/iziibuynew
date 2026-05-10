<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial report</title>
</head>
<body>
    <p>Hello,</p>
    <p>Please find attached the financial report for the period {{ $from->format('Y-m-d') }} to {{ $to->format('Y-m-d') }}.</p>

    <p>Summary (first 5 rows):</p>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows->take(5) as $row)
                <tr>
                    <td>{{ $row['customer_name'] }}</td>
                    <td>{{ number_format($row['amount'], 2) }}</td>
                    <td>{{ $row['payment_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Regards,<br>Iziibuy</p>
</body>
<html>


