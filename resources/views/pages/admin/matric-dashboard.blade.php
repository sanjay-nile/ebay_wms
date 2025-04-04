@extends('layouts.admin.layout')

@push('css')
<style type="text/css">
   button.btn-Search-1 {
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #34bb63;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.ShowLiveData-btn{
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #3d2a67;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.btnExport {
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #34bb63;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

a.btnExport {
    border: none;
    position: relative;
    padding: 9px 10px;
    background: #3d2a67;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.order-text h2 {
    color: #605082;
}

.overview-section .card-box {
    min-height: 206px;
}

</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.dt').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

        function fetchData(page = 1, live_Data = '') {
            let startDate = $("#start_date").val();
            let endDate = $("#end_date").val();

            $.ajax({
                url: "{{ route('admin.runtime.dashboard') }}", // Change to your Laravel route
                type: "get",
                data : {
                    page: page,
                    start_date: startDate,
                    end_date: endDate,
                    dt_type: live_Data
                },
                dataType: 'json',
                beforeSend : function(){
                    $('#shw').html(`Sending.. <i class="la la-spinner la-spin"></i>`);
                },
                success: function(response) {
                    $('#shw').html(' ');
                    $('#opt-cnt').html(response.total_opreator);
                    $('#pending-dispatch').html(response.pending_dispatch);
                    $('#scan-out').html(response.total_scan_out);
                    $('#scan-in').html(response.total_scan_in);
                    $('#dispatch').html(response.total_dispatch);
                    $('#dis-hr').html(response.total_dispatch_hr);
                    $('#in-hr').html(response.total_scan_in_hr);
                    $('#out-hr').html(response.total_scan_out_hr);
                    $('#opt-ord').html(response.total_op_orders);
                    // console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + error);
                }
            });
        }

        // Call fetchData immediately on page load
        fetchData();

        // Set interval to call fetchData every 1 minute (60000 ms)
        setInterval(fetchData, 60000);

        // Handle pagination link clicks
        $(document).on("click", ".pagination a", function (event) {
            event.preventDefault();
            let page = $(this).attr("href").split("page=")[1];
            fetchData(page);
        });

        // Handle pagination link clicks
        $(document).on("click", "#search-btn", function (event) {
            fetchData(1);
        });

        // Handle pagination link clicks
        $(document).on("click", "#live-data-btn", function (event) {
            fetchData(1, 'live');
        });
    });
</script>
@endpush

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
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Performance Metrics Dashboard <span id="shw" style="color: red;"></span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row align-items-center">
                                    <div class="form-group col-md-4">
                                        <label>Search by Date (Default Display Today)</label>
                                        <input type="text" name="from_date" id="start_date" value="" class="form-control dt" placeholder="From Date">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>&nbsp;</label>
                                        <input type="text" name="to_date" id="end_date" value="" class="form-control dt" placeholder="To Date">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="button" class="btn-Search-1" id="search-btn">Search</button>
                                    </div>
                                    <div class="form-group col-md-2 mt-2">
                                        <a href="{{ route('admin.metric.dashboard') }}" class="btnExport">Reset</a>
                                    </div>
                                    {{-- <div class="form-group col-md-2">
                                        <button type="button" class="btnExport" id="excel-btn">Export To Excel</button>
                                    </div> --}}
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Total number of New Items added to Stock</p>
                                                    <h2 id="scan-in">{{ $total_scan_in }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Number of Pending Orders</p>
                                                    <h2 id="scan-out">{{ $total_scan_out }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <a href="#">
                                    <div class="card-box bg-card border border-success">
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
                                                        <p>Orders Pending Dispatch</p>
                                                        <h2 id="pending-dispatch">{{ $pending_dispatch }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <a href="#">
                                    <div class="card-box bg-card border border-success">
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
                                                        <p>Number of orders Processed </p>
                                                        <h2 id="dispatch">{{ $total_dispatch }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Total Packages Scanned in per hour – Put Away </p>
                                                    <h2 id="in-hr">{{ $total_scan_in_hr }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Number of items picked per hour</p>
                                                    <h2 id="out-hr">{{ $total_scan_out_hr }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Total number of processed packages per hour</p>
                                                    <h2 id="dis-hr">{{ $total_dispatch_hr }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-lg-3">
                                <div class="card-box bg-card border border-success">
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
                                                    <p>Total number of Overdue orders</p>
                                                    <h2>{{-- <span id="opt-cnt"></span>/ --}}<span id="opt-ord"></span></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection