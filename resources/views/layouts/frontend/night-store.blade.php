<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <link rel="shortcut icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ReverseGear') }}</title>

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/front/night-store.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/front/responsive.css') }}" rel="stylesheet"> --}}

    @stack('css')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('css/bootstrap/js/bootstrap.min.js') }}"></script>

    @stack('js')

</head>
<body class="client-body">    
    @yield('content')
</body>
</html>