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
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="shipment_date"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom left"
    });

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#add-to-warehouse").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#process-save").submit();
        }
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $("#parcel-excel-btn").click(function () {
        $('#export_to').val('parcel-excel');
        $("#filter-frm").submit();
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('item-excel');
        $("#filter-frm").submit();
    });

    $(".search-btn").click(function () {
        $('#export_to').val('');
    });

    $(".up-data").click(function (e) {
        e.preventDefault();
        var obj = $(this);
        var id = $(obj).attr('data-id');        
        var cl = $('#'+id+'-claim-id').val();
        var dt = $('#'+id+'_shipment_date').val();
        var sh = $('#'+id+'-shipment-status :selected').val();
        //if (cl == '' && sh == '') {
            //alert('please fill at least one from the shipment status or claim id');
        //} else {
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url: "{{ route('update-shipment') }}",
                method: "post",
                dataType: 'JSON',
                data:{orer_id:id , shipment_status:sh, claim_id:cl, shipment_date:dt},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // console.log(response);
                    $(obj).prop('disabled', false);
                    $(obj).html('update');
                    alert(response.msg);
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('update');
                }
            });
        //}
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
                            <li class="breadcrumb-item active">All Previous Returns</li>
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
                        <form class="form-horizontal" id="filter-frm">                            
                            <div class="">
                                <div class="col-md-12">
                                    {{-- <ul class="row nav nav-tabs" role="tablist">
                                        <li class="p-0 col-md-6 nav-item tabs-section">
                                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Filters
                                            </a>
                                        </li>
                                        <li class="p-0 col-md-6 nav-item tabs-section">
                                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Search</a>
                                        </li>
                                    </ul> --}}
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="client" id="" class="form-control">
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
                                                        <select name="order_type" class="form-control">
                                                            <option value="">-- Select Return Type --</option>
                                                            <option value="intransit" {{ (app('request')->input('order_type')=='intransit')?"selected":'' }}>Processed</option>
                                                            <option value="new" {{ (app('request')->input('order_type')=='new')?"selected":'' }}>Failed Label</option>
                                                            <option value="inscan" {{ (app('request')->input('order_type')=='inscan')?"selected":'' }}>InScan</option>
                                                            <option value="cancel" {{ (app('request')->input('order_type')=='cancel')?"selected":'' }}>Cancelled</option>
                                                            <option value="at_hub" {{ (app('request')->input('order_type')=='at_hub')?"selected":'' }}>Received at Hub</option>
                                                            <option value="Delivered" {{ (app('request')->input('order_type')=='Delivered')?"selected":'' }}>Delivered</option>
                                                            <option value="Shipment completed" {{ (app('request')->input('order_type')=='Shipment completed')?"selected":'' }}>Shipment completed</option>
                                                            <option value="Processed for return" {{ (app('request')->input('order_type')=='Processed for return')?"selected":'' }}>Processed for return</option>
                                                        </select>
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="by_country" class="form-control">
                                                            <option value="">-- Select By Country --</option>
                                                            @forelse($country as $k => $v)
                                                                <option value="{!! $v->sortname !!}" {{ (app('request')->input('by_country')==$v->sortname)?"selected":'' }}>{!! $v->name !!}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="by_warehouse" class="form-control">
                                                            <option value="">-- Select By Warehouse --</option>
                                                            @forelse(getWareHouse() as $k => $v)
                                                                <option value="{!! $k !!}" {{ (app('request')->input('by_warehouse')==$k)?"selected":'' }}>{!! $v !!}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="refund_status" class="form-control">
                                                            <option value="">-- Select Refund Status --</option>
                                                            <option value="Yes" {{ (app('request')->input('refund_status')=='Yes')?"selected":'' }}>Yes</option>
                                                            <option value="No" {{ (app('request')->input('refund_status')=='No')?"selected":'' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="shipment_status" class="form-control">
                                                            <option value="">-- select shipment_status--</option>
                                                            <option value="Lost" @if(app('request')->input('shipment_status') == 'Lost') selected @endif>Lost</option>
                                                            <option value="Damaged in transit" @if(app('request')->input('shipment_status') == 'Damaged in transit') selected @endif>Damaged in transit</option>
                                                            <option value="Destroyed" @if(app('request')->input('shipment_status') == 'Destroyed') selected @endif>Destroyed </option>
                                                            <option value="Undeliverable" @if(app('request')->input('shipment_status') == 'Undeliverable') selected @endif>Undeliverable </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="customer_name" class="form-control" value="{{ app('request')->input('customer_name') }}" autocomplete="off" placeholder="Customer Name" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="customer_email" class="form-control" value="{{ app('request')->input('customer_email') }}" autocomplete="off" placeholder="Email ID" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="way_bill_number" class="form-control" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" placeholder="Customer Order No." />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="sku" class="form-control" value="{{ app('request')->input('sku') }}" placeholder="#SKU" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="hs_code" class="form-control" value="{{ app('request')->input('hs_code') }}" placeholder="HS Code" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="start" class="form-control" value="{{ app('request')->input('start') }}" autocomplete="off" placeholder="Select From Date" />
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="end" class="form-control" value="{{ app('request')->input('end') }}" autocomplete="off" placeholder="Select To Date" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="tracking_id" class="form-control" value="{{ app('request')->input('tracking_id') }}" placeholder="Tracking ID" />
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="shippingboxbarcode" class="form-control" value="{{ app('request')->input('shippingboxbarcode') }}" placeholder="Box BarCode" />
                                                    </div>
                                                </div>

                                                <div class="col-md-12 pull-right">
                                                    <input type="hidden" name="export_to" id="export_to" value="">
                                                    <div class="" style="margin-bottom: 16px;">
                                                        <button type="submit" class="btn btn-search btn-sm search-btn" id="search-btn">
                                                            <i class="la la-search"></i> Search
                                                        </button>
                                                        <a href="{{ route('get.return.data') }}" class="btn cl-orange reset btn-sm">
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
            @if(!empty($lists))
                <div class="alert alert-success">
                    Showing {{ $lists->firstItem() }} to {{ $lists->lastItem() }} of Total {{ $lists->total() }} Orders</strong>
                </div>
            @endif
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
                                <div class="tab-content mt-1" id="pills-tabContent">

                                    @include('pages.admin.return-orders.html.parcel-order')
                                    @include('pages.admin.return-orders.html.item-order')

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
