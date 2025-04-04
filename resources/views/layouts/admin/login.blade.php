<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('public/images/Ship_Cycle_Favicon.png') }}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ship Cycle</title>
    <!-- Styles -->
    <link href="{{ asset('public/admin/css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('public/admin/css/forms/icheck/icheck.css') }}" rel="stylesheet">
</head>
<body class="horizontal-layout bg-full-screen-image blank-page">
    <div class="main-page">
        @yield('content')
    </div>
     <!-- Scripts -->
    <script src="{{ asset('public/js/jquery-3.4.1.min.js') }}" defer></script>
    <script src="{{ asset('public/js/popper.min.js') }}" defer></script>
    <script src="{{ asset('public/css/bootstrap/js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('public/admin/js/forms/icheck/icheck.min.js') }}" defer></script>
    <script src="{{ asset('public/admin/js/form-login.js') }}" defer></script>
</body>
</html>
