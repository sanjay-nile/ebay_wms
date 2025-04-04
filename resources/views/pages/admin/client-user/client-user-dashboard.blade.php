@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Dashboard</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                      <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                      </div>
                    </div>
                </div>
            </div>
        </div>
       
        <section class="overview-section">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <a href="{{ route('reverse-logistic') }}">
                        <div class="card-box bg-success">
                            <div class="card-box-media">
                                <i class="icon-newtips"></i>
                            </div>
                            <div class="card-box-content">
                                <p>Total Reverse Order</p>
                               <h2>{{ $total_reverse_order }}</h2>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
        <section class="chart-section">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <div class="card tips-card">
                        <div class="card-header">
                            <h4 class="card-title">Reverse Orders Distributions</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="Distribution" height="100" width="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-8 col-lg-8">
                    <div class="card tips-card">
                        <div class="card-header">
                            <h4 class="card-title">Monthly Reverse Orders</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="Bookings"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
@endsection
@push('scripts')
<script src="{{ asset('admin/css/chart/js/Chart.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/css/chart/js/Chart.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/js/dashboard_chart.js') }}" type="text/javascript"></script>
@endpush