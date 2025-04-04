@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $("#add-to-warehouse").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#process-save").submit();
        }
    });

    var parcel_table = $('.parcel-level').DataTable({
        processing: true,
        serverSide: true,
        "searching": false,
        "lengthChange": true,
        "ordering": false,
        "bPaginate": true,
        "bInfo": true,
        autoWidth: true,
        "pageLength": 100,
        "lengthMenu": [ [100,150,250,500,1000], [100,150,250,500,1000] ],
        ajax: {
            url: "{{ route('all.return.orders.list') }}",
            data: function (d) {
                d.client = $('#client option:selected').val(),
                d.order_type = $('#order_type option:selected').val(),
                d.by_country = $('#by_country option:selected').val(),
                d.by_warehouse = $('#by_warehouse option:selected').val(),
                d.refund_status = $('#refund_status option:selected').val(),
                d.customer_name = $('#customer_name').val(),
                d.customer_email = $('#customer_email').val(),
                d.way_bill_number = $('#way_bill_number').val(),
                d.sku = $('#sku').val(),
                d.tracking_id = $('#tracking_id').val(),
                d.hs_code = $('#hs_code').val(),
                d.start = $('#start').val(),
                d.end = $('#end').val()
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'action', name: 'Action'},
            {data: 'client_name', name: 'Client Name'},
            {data: 'source', name: 'Source'},
            {data: 'source_name', name: 'Source Name'},
            {data: 'order_type', name: 'Order Type'},
            {data: 'return_option', name: 'Return Option'},
            {data: 'id', name: 'RG Order ID'},
            {data: 'way_bill_number', name: 'Customer Order ID'},
            {data: 'customer_name', name: 'Customer Name'},
            {data: 'customer_email', name: 'Customer Email'},
            {data: 'return_date', name: 'Request Date'},
            {data: 'country', name: 'Country'},
            {data: 'carrier', name: 'Carrier'},
            {data: 'shipping', name: 'Expected Time of Delivery'},
            {data: 'tracking_id', name: 'Tracking ID'},
            {data: 'warehouse', name: 'Warehouse'},            
        ],
        dom: 'Blfrtip',
        buttons: [{
            extend:'excel', text: '<i class="la la-file-excel-o"></i> Excel', exportOptions: {
                columns: ':not(:last-child)'
            }
        }],
    });
    
    $(".frm-sb-btn").click(function () {
        parcel_table.draw();
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
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                            <li class="breadcrumb-item active">All Return Orders List</li>
                        </ol>
                      </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12 ">
                @include('includes/admin/notify')
                <div class="card">
                    <div class="card-header avn-card-header">
                        <form class="form-horizontal fiter-form" method="get" id="srch-frm">
                            <div class="">
                                <div class="col-md-12">
                                    <ul class="row nav nav-tabs" role="tablist">
                                        <li class="p-0 col-md-6 nav-item tabs-section">
                                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Filters
                                            </a>
                                        </li>
                                        <li class="p-0 col-md-6 nav-item tabs-section">
                                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Search</a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        {{-- fillters --}}
                                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="client" id="client" class="form-control">
                                                            <option value="">-- Select Client --</option>
                                                            @forelse($clients as $client)
                                                                <option value="{{ $client->id }}" {{ (app('request')->input('client')==$client->id)?"selected":'' }}>{{ $client->name }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="order_type" class="form-control" id="order_type">
                                                            <option value="">-- Select Return Type --</option>
                                                            <option value="intransit">Processed Returns</option>
                                                            <option value="new">Failed Returns</option>
                                                            <option value="inscan">InScan Returns</option>
                                                            <option value="cancel">Cancelled Returns</option>
                                                            <option value="at_hub">Received at Hub Returns</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="by_country" class="form-control" id="by_country">
                                                            <option value="">-- Select By Country --</option>
                                                            @forelse($country as $k => $v)
                                                                <option value="{!! $v->sortname !!}">{!! $v->name !!}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="by_warehouse" class="form-control" id="by_warehouse">
                                                            <option value="">-- Select By Warehouse --</option>
                                                            @forelse(getWareHouse() as $k => $v)
                                                                <option value="{!! $k !!}">{!! $v !!}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="refund_status" class="form-control" id="refund_status">
                                                            <option value="">-- Select Refund Status --</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 ">
                                                    <div class="" style="margin-bottom: 16px;">
                                                        <button type="button" class="btn btn-cyan frm-sb-btn">
                                                            <i class="la la-search"></i> Search
                                                        </button>
                                                        <a href="{{ route('all.return.orders') }}" class="btn btn-refresh  reset">
                                                            <i class="la la-refresh"></i> Reset
                                                        </a>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>

                                        {{-- search --}}
                                        <div class="tab-pane" id="tabs-2" role="tabpanel">
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Customer Name</label>
                                                        <input type="text" name="customer_name" id="customer_name" class="form-control datatable-input" value="{{ app('request')->input('customer_name') }}" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Email ID</label>
                                                        <input type="text" name="customer_email" id="customer_email" class="form-control datatable-input" value="{{ app('request')->input('customer_email') }}" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Customer Order No.</label>
                                                        <input type="text" name="way_bill_number" id="way_bill_number" class="form-control datatable-input" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>#SKU</label>
                                                        <input type="text" name="sku" id="sku" class="form-control" value="{{ app('request')->input('sku') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>HS Code</label>
                                                        <input type="text" name="hs_code" id="hs_code" class="form-control" value="{{ app('request')->input('hs_code') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Tracking ID</label>
                                                        <input type="text" name="tracking_id" id="tracking_id" class="form-control datatable-input" value="{{ app('request')->input('tracking_id') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Select From Date</label>
                                                        <input type="text" name="start" id="start" class="form-control" value="{{ app('request')->input('start') }}" autocomplete="off" />
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Select To Date</label>
                                                        <input type="text" name="end" id="end" class="form-control" value="{{ app('request')->input('end') }}" autocomplete="off" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 pull-right">
                                                    <div class="" style="margin-bottom: 16px;">
                                                        <button type="button" class="btn btn-cyan frm-sb-btn" id="search-btn">
                                                            <i class="la la-search"></i> Search
                                                        </button>
                                                        <a href="{{ route('all.return.orders') }}" class="btn btn-refresh reset">
                                                            <i class="la la-refresh"></i> Reset
                                                        </a>
                                                    </div>
                                                </div>                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->

        <section class="card table-card-section">
            <div class="row">
                <div class="col-xs-12 col-md-12 ">
                    <div class="card">
                        <div class="card-content collapse show">                            
                            <div class="card-body booking-info-box card-dashboard table-responsive">
                                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active tab-inactive" id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="true">Parcel level</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link tab-inactive" id="pills-item-tab" data-toggle="pill" href="#pills-item" role="tab" aria-controls="pills-item" aria-selected="false">Item level</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    {{-- parcel level --}}
                                    <div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
                                        <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm parcel-level">
                                            <thead>
                                                <tr>
                                                    <th>S no.</th>
                                                    <th class="not-export-column">Action</th>
                                                    <th>Client Name</th>
                                                    <th>Source</th>
                                                    <th>Source Name</th>
                                                    <th>Order Type</th>
                                                    <th>Return Option</th>
                                                    <th>RG Order ID</th>
                                                    <th>Customer Order ID</th>
                                                    <th>Customer Name</th>
                                                    <th>Email</th>
                                                    <th>Request Date</th>
                                                    <th>Country</th>
                                                    <th>Carrier</th>
                                                    <th>Expected Time of Delivery</th>
                                                    <th>Tracking ID</th>
                                                    <th>Warehouse</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- item level --}}
                                    <div class="tab-pane fade" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
                                        <div class="text-right px-1 mt-2">
                                            <button type="button" id="add-to-warehouse" class="btn btn-danger btn-sm">Add to Warehouse</button>
                                        </div>
                                        <form action="{{ route('process.orders') }}" method="post" id="process-save">
                                            @csrf
                                            <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm item-level">
                                                <thead>
                                                    <tr>
                                                        <th>S no.</th>
                                                        <th><input name="select_all" value="1" id="select-all" type="checkbox" /></th>
                                                        <th class="not-export-column">Action</th>
                                                        <th>Client Name</th>
                                                        <th>Source</th>
                                                        <th>Source Name</th>
                                                        <th>Order Type</th>
                                                        <th>Exception</th>
                                                        <th>Return Option</th>
                                                        <th>RG Order ID</th>
                                                        <th>Customer Order ID</th>
                                                        <th>Customer Name</th>
                                                        <th>Email</th>
                                                        <th>Request Date</th>
                                                        <th>Reason of Return</th>
                                                        <th>Sku #</th>
                                                        <th>Country</th>
                                                        <th>Carrier</th>
                                                        <th>Expected Time of Delivery</th>
                                                        <th>Tracking ID</th>
                                                        <th>Package Weight</th>
                                                        <th>Package Dimensions</th>
                                                        <th>Warehouse</th>
                                                        <th>HS Code</th>
                                                        <th>Refunded Status</th>
                                                        <th>Confirm Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                                                
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div><!-- /.content-wrapper -->
</div>

@endsection
