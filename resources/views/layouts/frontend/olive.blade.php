<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ReverseGear') }}</title>
    
    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,600,700|Quicksand:300,400,500,700" rel="stylesheet">

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/front/olive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/front/front.css') }}" rel="stylesheet">
    <link href="{{ asset('css/front/app-new.css') }}" rel="stylesheet">
    <link href="{{ asset('css/customer.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/new-admin-app.css') }}" rel="stylesheet">

    @stack('css')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('css/bootstrap/js/bootstrap.min.js') }}"></script>
    @stack('js')

</head>
<body>
    <div id="main-wrapper">
        @yield('content')
    </div>
</body>
</html>