<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('includes.admin.head')

        <link rel="stylesheet" href="{{ URL::asset('public/css/lonsdale.css') }}" />
    </head>
    <body id="admin_panel" class="skin-blue sidebar-mini wysihtml5-supported">
        <div class="wrapper">

            @yield('content')
            
            <!-- Modal -->
            <div id="defaultModal" class="modal fade" role="dialog"></div>

            <input type="hidden" name="hf_base_url" id="hf_base_url" value="{{ url('/') }}">
            <input type="hidden" name="lang_code" id="lang_code" value="en">
            <input type="hidden" name="site_name" id="site_name" value="admin">
            <div class="ajax-request-response-msg" style="display: none; background-color: #333;padding:20px 0px;position:fixed;width:100%;color:#DDD;bottom: 0px;z-index: 999;text-align: center;left: 0px; font-size:16px;"></div>
        </div>
    </body>
</html>


