<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="shortcut icon" href="{{ asset('public/images/Ship_Cycle_Favicon.png') }}" type="image/x-icon"> -->
    <link rel="shortcut icon" href="{{ asset('public/images/Ship_Cycle_Favicon.png') }}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }}</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,600,700|Quicksand:300,400,500,700" rel="stylesheet">
    <!-- Admin App CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/css/new-admin-app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/css/super-admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/css/chart/css/Chart.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/css/menu/new-vertical-menu-modern.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/datatable/css/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('public/css/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('public/admin/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/css/select2.min.css') }}">
    
    @stack('css')

    <!-- BEGIN Load JS-->
    {{-- <script type="text/javascript" src="{{ asset('js/jquery-3.4.1.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('public/admin/js/admin-vendors.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('public/admin/js/app.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('public/admin/js/app-menu.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('public/plugins/datatable/js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/plugins/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/css/bootstrap/js/bootstrap.min.js') }}"></script>

    {{-- ckeditor js --}}
    <script type="text/javascript" src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/ckeditor/adapters/jquery.js') }}"></script>

    {{-- toastr and others --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script type="text/javascript" src="{{ asset('public/plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/admin/js/function.js') }}" defer></script>

    @stack('scripts')
    @stack('js')
</head>
    <body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
        
        @include('includes.admin.header')
        @section('sidebar')
            @include('includes.admin.sidebar')
        @show
        
        @yield('content')
        @include('includes.admin.footer')
        
        <!-- Modal -->
        <div id="defaultModal" class="modal fade" role="dialog">
        </div>

        <input type="hidden" name="hf_base_url" id="hf_base_url" value="{{ url('/') }}">
    </body>
</html>


