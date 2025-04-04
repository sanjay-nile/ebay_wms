<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <link rel="shortcut icon" href="{{ asset('public/images/Ship_Cycle_Favicon.png') }}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ship Cycle</title>

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('public/css/front/home.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('public/css/front/responsive.css') }}" rel="stylesheet"> --}}

    @stack('css')

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/css/bootstrap/js/bootstrap.min.js') }}"></script>

    @stack('js')

</head>
<body class="client-body">    
    @yield('content')
</body>
</html>