@include('pages.frontend.client-user.breadcrumb', ['title' => 'Dashboard'])

<style type="text/css">
    .wrapper-body .container{max-width: 1240px;}
</style>

<div class="overview-section">
    <div class="row">        
        <div class="col-md-4 col-sm-4 col-lg-4">
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
                                <p>Total Orders Received</p>
                                <h2>{{ $total_reverse_order }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <h2>{{ $failed }}</h2>
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
        
    </div>
</div>