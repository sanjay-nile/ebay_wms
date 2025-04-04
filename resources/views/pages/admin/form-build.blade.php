@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .nav.nav-tabs.nav-underline {
        border-bottom: 1px solid #ffdfe4 !important;
        margin-bottom: 26px !important;
        background: #fff1f3;
    }
    .align-col .row .col-md-2, .col-md-1{padding: 0px 5px;}
</style>
@endpush

@section('content')

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="we-page-title">
                <div class="row">
                    <div class="col-md-8 align-self-left">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                            <li class="breadcrumb-item active">Form Build</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.content-wrapper -->

        <div class="content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    
                </div>
            </div>
        </div>
    </div>

@endsection