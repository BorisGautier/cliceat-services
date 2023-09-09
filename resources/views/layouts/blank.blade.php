<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/installation')}}/assets/img/favicon.svg">

    <!-- Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('assets/installation')}}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('assets/installation')}}/assets/css/style.css">
</head>

<body>
<section style="background-image: url('{{asset('assets/installation')}}/assets/img/page-bg.png')"
         class="w-100 min-vh-100 bg-img position-relative py-5">

    <!-- Logo -->
    <div class="logo">
        <img src="{{asset('assets/installation')}}/assets/img/favicon.svg" alt="">
    </div>

    <div class="custom-container">
        @yield('content')

        <!-- Footer -->
        <footer class="footer py-3 mt-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 align-items-center">
                <div class="footer-logo">
                    <img src="{{asset('assets/installation')}}/assets/img/logo.svg" alt="">
                </div>
                <p class="copyright-text mb-0">© 2023 | All Rights Reserved</p>
            </div>
        </footer>
    </div>
</section>
</body>

<!-- Script Goes Here -->
<script src="{{asset('assets/installation')}}/assets/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('assets/installation')}}/assets/js/script.js"></script>
{!! Toastr::message() !!}

<script>
    const passwordField = document.getElementById('password');
    const confirmationField = document.getElementById('confirm_password');

    confirmationField.addEventListener('input', () => {
        if (confirmationField.value === '') {
            confirmationField.setCustomValidity('');
            return;
        }

        if (passwordField.value === confirmationField.value) {
            confirmationField.setCustomValidity('');
        } else {
            confirmationField.setCustomValidity('The passwords do not match');
        }
    });
</script>

</html>
