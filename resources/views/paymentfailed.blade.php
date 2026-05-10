<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
    </style>
    <link rel="icon" href="/favicon.ico">
    <meta name="robots" content="noindex">
    <meta name="description" content="Payment completed confirmation">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light only">
    <meta name="theme-color" content="#198754">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Payment Completed">
    <link rel="apple-touch-icon" href="/favicon.ico">
</head>
<body>
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <div class="mb-3">
                            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="#fdecea"/>
                                <path d="M9 9l6 6M15 9l-6 6" stroke="#dc3545" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h1 class="h3 fw-bold mb-2 text-danger">Payment failed</h1>
                        <p class="text-muted mb-4">We couldn't complete your payment. Please review the details below and try again.</p>

                        @if (session('error'))
                            <div class="alert alert-danger text-start" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('errors'))
                            <div class="alert alert-danger text-start" role="alert">
                                @if (is_array(session('errors')))
                                    <ul class="mb-0">
                                        @foreach(session('errors') as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ session('errors') }}
                                @endif
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger text-start" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="/" class="btn btn-outline-secondary px-4">Go to homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
