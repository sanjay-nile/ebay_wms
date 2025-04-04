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
                    <a href="#">
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-group"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-orange">
                                            <img src="{{ asset('public/images/return-order.svg') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Total Return Orders</p>
                                            <h2>{{ $total_reverse_order }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <a href="{{ route('admin.client') }}">
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-subscribe1"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-green">
                                            <img src="{{ asset('public/images/total-client.svg') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Total Clients</p>
                                            <h2>{{ $total_client }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <a href="{{ route('admin.client-user') }}">
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-subscribe1"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-green">
                                            <img src="{{ asset('public/images/total-client-user.svg') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Total Clients Users</p>
                                            <h2>{{ $total_client_user }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <a href="#">
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-subscribe1"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-orange">
                                            <img src="{{ asset('public//images/truck.png') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Actual Failed Orders</p>
                                            <h2>{{ $actual_failed }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <a href="#">
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-subscribe1"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-orange">
                                            <img src="{{ asset('public//images/truck.png') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Failed Return Orders</p>
                                            <h2>{{ $failed }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        @forelse($users as $user)
            <div class="overview-section">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <h2 class="head-title alert alert-info"><i class="la la-users"></i> {{ $user->name }}</h2>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="#">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-orange">
                                                <img src="{{ asset('images/truck.png') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Processed Return Orders</p>
                                                <h2>{{ getClientProcessedOrder($user->id) }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="#">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-orange">
                                                <img src="{{ asset('images/truck.png') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Failed Return Orders</p>
                                                <h2>{{ getClientFailedOrder($user->id) }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="#">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-newtips"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-green-cl">
                                                <img src="{{ asset('images/ic-total-orders.png') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Returns received at Hub</p>
                                                <h2>{{ getClientReceivedOrder($user->id) }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="#">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-orange-cl">
                                                <img src="{{ asset('images/ic-intransit-Orders.png') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Returns received at first In-scan</p>
                                                <h2>{{ getClientInscanOrder($user->id) }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="#">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-red">
                                                <img src="{{ asset('images/ic-intransit-Orders.png') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Cancelled Return Orders</p>
                                                <h2>{{ getClientCancelOrder($user->id) }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
</div>
@endsection