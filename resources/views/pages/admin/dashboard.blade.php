@extends('layouts.admin.layout')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="we-page-title">
                <div class="row">
                    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                        <div class="row breadcrumbs-top d-inline-block">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
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
                        <div class="card-box bg-card">
                            <div class="card-box-media">
                                <i class="icon-group"></i>
                            </div>
                            <div class="card-box-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="ic-bg bg-gray">
                                            <img src="{{ asset('public/images/orders-box.svg') }}" height="38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="order-text text-center">
                                            <p>Total Scan In Orders</p>
                                            <h2>{{ $total_scan_in }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <a href="{{ route('admin.sub-admin') }}">
                            <div class="card-box bg-card">
                                <div class="card-box-media">
                                    <i class="icon-group"></i>
                                </div>
                                <div class="card-box-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/admin-rep.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Admin Rep</p>
                                                <h2>{{ $total_sub_admin }}</h2>
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
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/approved-orders.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Scan Out Orders</p>
                                                <h2>{{ $total_scan_out }}</h2>
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
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/rejected-orders.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Dispatch Orders</p>
                                                <h2>{{ $total_dispatch }}</h2>
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
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/rejected-orders.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Pending Dispatch</p>
                                                <h2>{{ $pending_dispatch }}</h2>
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
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/rejected-orders.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Cancelled Order</p>
                                                <h2>{{ $cancelled }}</h2>
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
                                            <div class="ic-bg bg-gray">
                                                <img src="{{ asset('public/images/admin-rep.svg') }}" height="38px;">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="order-text text-center">
                                                <p>Total Operator Rep</p>
                                                <h2>{{ $total_opreator }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection