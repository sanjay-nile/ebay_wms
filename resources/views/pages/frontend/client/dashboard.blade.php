@include('pages.frontend.client.breadcrumb', ['title' => 'Dashboard'])

<style type="text/css">
    .wrapper-body .container{max-width: 1240px;}
</style>

<div class="overview-section">
    <div class="row">                
        <div class="col-md-3 col-sm-3 col-lg-3">
            <div class="card-box bg-card">
                <div class="card-box-media">
                    <i class="icon-group"></i>
                </div>
                <div class="card-box-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ic-bg bg-white">
                                <img src="{{ asset('public/images/orders-box.svg') }}" height="38px;">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="order-text text-center">
                                <p>Total eBay Orders</p>
                                <h2>{{ $total_order }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-3 col-lg-3">
            <a href="{{ route('sub-admin') }}">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ic-bg bg-white">
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
                                <div class="ic-bg bg-white">
                                    <img src="{{ asset('images/approved-orders.svg') }}" height="38px;">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="order-text text-center">
                                    <p>Approved Orders</p>
                                    <h2>0</h2>
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
                                <div class="ic-bg bg-white">
                                    <img src="{{ asset('images/rejected-orders.svg') }}" height="38px;">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="order-text text-center">
                                    <p>Rejected Orders</p>
                                    <h2>0</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- <div class="overview-section">
    <div class="row">
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg bg-orange">
                                    <img src="{{ asset('images/truck.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Processed Return Orders</p>
                                    <h2>{{ $intransit_order }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg bg-orange">
                                    <img src="{{ asset('images/truck.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Failed Return Orders</p>
                                    <h2>{{ $new_order }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg bg-orange">
                                    <img src="{{ asset('images/truck.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Actual Failed Orders</p>
                                    <h2>{{ $actual_failed }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-newtips"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg">
                                    <img src="{{ asset('images/ic-total-orders.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Returns received at Hub</p>
                                    <h2>{{ $recieve_order }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg">
                                    <img src="{{ asset('images/ic-intransit-Orders.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Returns received at first In-scan</p>
                                    <h2>{{ count($inscan) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="#">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-group"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg">
                                    <img src="{{ asset('images/ic-intransit-Orders.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Cancelled Return Orders</p>
                                    <h2>{{ count($cancel) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
            <a href="{{ route('client-user-list') }}">
                <div class="card-box bg-card">
                    <div class="card-box-media">
                        <i class="icon-subscribe1"></i>
                    </div>
                    <div class="card-box-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="ic-bg bg-green">
                                    <img src="{{ asset('images/ic-admin-rep.png') }}" height="50px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="order-text">
                                    <p>Total Client Users</p>
                                    <h2>{{ $total_client_user }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div> --}}