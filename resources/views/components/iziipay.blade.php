<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm"
        crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
        integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous">
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
            background-color: #365dab;
        }

        .container {

            background-color: #0d317a;
            padding: 20px;
            box-shadow: 10px 10px #010f2c;
            height: 95vh;
            overflow-y: scroll;
            scrollbar-width: thin;
            /* for Firefox */
            scrollbar-color: #1a4fb7 #0d317a;
            /* thumb color | track color */
        }

        .card {
            background-color: #02184456;
        }

        label {
            color: #fff;
            font-weight: 600;
            font-size: 18px;
        }

        .form-control {
            height: 50px;


            border-color: #04266873;
        }

        th,
        td {
            color: #fff;
            font-weight: 600;
            font-size: 18px;
        }
    </style>

    <title>Iziipay</title>
</head>

<body>
    <main>



        {{ $slot }}
    </main>
    <form action="" method="post" id="delete-form" style="dislplay:none">
        @csrf
        @method('delete')
    </form>

    <!-- SweetAlert2 JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.js"></script>
    <!-- Global SweetAlert Messages Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle success messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Handle error messages
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#dc3545'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });
    </script>
    <script>
        function cskDelete(url) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let deleteForm = document.getElementById('delete-form');
                    deleteForm.action = url;
                    deleteForm.submit();
                }
            })
        }
    </script>
</body>

</html>
