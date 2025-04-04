<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/home.css') }}" rel="stylesheet">
    @stack('css')

    <!-- Scripts -->
    {{-- <script src="{{ asset('public/js/app.js') }}"></script> --}}
    <script type="text/javascript" src="{{ URL::asset('public/js/jquery-1.10.2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/css/bootstrap/js/bootstrap.min.js') }}"></script>

    {{-- toaster css --}}
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    {{-- toaster js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ asset('public/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('public/js/custom-script.js?v=') }}{{ time() }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('js')
</head>
<body>
    <!-- <div id="app">
        <div class="header-lonsdale-info">
            <div class="container">
                <div class="row">
                    <div class="col-6"> -->
                        <!-- <div class="header-logo-1">
                            <img src="{{  asset('public/images/ecom-global-colour-logo.png') }}" height="36px;">
                        </div> -->
                    </div> 
                    <!-- <div class="col-6">
                        <div class="header-logo-1">
                            <img src="{{  asset('public/images/stanley-black-decker.png') }}">
                        </div>
                    </div> -->
                   <!--  <div class="col-6 justify-content-end d-flex">
                        <img src="{{  asset('public/images/Lonsdale-Logo.png') }}" height="36px;">
                        @if(!Auth::guest())
                            <a class="nav-link loginButton" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div> -->

            @yield('content')

        <!-- @include('includes.frontend.footer') -->
    </div>
</body>
</html>
