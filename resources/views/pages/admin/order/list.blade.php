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
    $('input[name="from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="date_invoiced"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});
    $('input[name="dd_in"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});
    $('input[name="dd_out"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});
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

    $("#search-btn").click(function () {
        $('#export_to').val('');
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('change','.cat-list',function(){
        let id = $('.cat-list option:selected').attr('data-id');;
        $.ajax({
            type:'get',
            url : "{{ route('admin.fillter.sub.categories') }}",
            data:{cat_id:id, level: 1},
            dataType : 'json',
            success : function(data){
                $(".sub-cat-list").replaceWith(data.html);
            }
        })
    });

    $(document).on('change','.sub_category_name',function(){
        let id = $('.sub_category_name option:selected').attr('data-id');;
        $.ajax({
            type:'get',
            url : "{{ route('admin.fillter.sub.categories') }}",
            data:{cat_id:id, level: 2},
            dataType : 'json',
            success : function(data){
                $(".sub-cat-list_1").replaceWith(data.html);
            }
        })
    });

    $(document).on('change','.sub_category_name_1',function(){
        let id = $('.sub_category_name_1 option:selected').attr('data-id');;
        $.ajax({
            type:'get',
            url : "{{ route('admin.fillter.sub.categories') }}",
            data:{cat_id:id, level: 3},
            dataType : 'json',
            success : function(data){
                $(".sub-cat-list_2").replaceWith(data.html);
            }
        })
    });

});
</script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.assigncountry').select2({
              placeholder: 'Select Pallet Id',
              allowClear: true
            });
        })
    </script>
@endpush

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">eBay {{ $type }} List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">{{ $type }} Fillters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="evtn_number" value="{{ Request::get('evtn_number') }}" class="form-control" placeholder="EVTN Number">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="{{ Request::get('order_id') }}" class="form-control" placeholder="Order Id">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="customer_name" value="{{ Request::get('customer_name') }}" class="form-control" placeholder="Customer Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="eq_id" value="{{ Request::get('eq_id') }}" class="form-control" placeholder="Order Ref. Id">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="package_id" value="{{ Request::get('package_id') }}" class="form-control" placeholder="Item Ref. Id">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="tracking_number" value="{{ Request::get('tracking_number') }}" class="form-control" placeholder="Tracking Number / AWB">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="seller_name" value="{{ Request::get('seller_name') }}" class="form-control" placeholder="Seller Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="seller_country" value="{{ Request::get('seller_country') }}" class="form-control" placeholder="Seller Country">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="in_level" class="form-control" id="myselect">
                                                <option value="">-- Select Inspection Level --</option>
                                                <option value="L1" {{ (request('in_level') == 'L1') ? 'selected' : '' }}>Level 1</option>
                                                <option value="L2" {{ (request('in_level') == 'L2') ? 'selected' : '' }}>Level 2</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="warehouse_name" class="form-control">
                                                <option value="">-- Select Warehouse --</option>
                                                @forelse($Warehouse as $pid)
                                                    <option value="{{ $pid->id }}" {{ (request('warehouse_name') == $pid->id) ? 'selected' : '' }}>{{ $pid->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="pallet_id" class="form-control select2 assigncountry">
                                                <option value="">-- Pallet Id --</option>
                                                @forelse($PalletDeatil as $pid)
                                                    <option value="{{ $pid->pallet_id }}" {{ (request('pallet_id') == $pid->pallet_id) ? 'selected' : '' }}>{{ $pid->pallet_id }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="return_reason" class="form-control" id="myselect">
                                                <option value="">-- Select Reason of Return --</option>
                                                @forelse($reason as $ror)
                                                    <option value="{{ $ror['reason_of_return'] }}" {{ (request('return_reason') == $ror['reason_of_return']) ? 'selected' : '' }}>{{ $ror['reason_of_return'] }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="category_name" class="form-control cat-list">
                                                <option value="">---Select SC Main Category ---</option>
                                                @forelse($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ (request('category_name') == $cat->id) ? 'selected' : '' }} data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list">
                                            <select name="sub_category_name" class="form-control sub_category_name">
                                                <option value="">---Select Category Tier 1 ---</option>
                                                @forelse($sub_categories as $cat)
                                                    <option value="{{ $cat->code }}" {{ (request('sub_category_name') == $cat->code) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list_1">
                                            <select name="sub_category_name_1" class="form-control sub_category_name_1">
                                                <option value="">---Select Category Tier 2 ---</option>
                                                @forelse($sub_categories_2_tier as $cat)
                                                    <option value="{{ $cat->code }}" {{ (request('sub_category_name_1') == $cat->code) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list_2">
                                            <select name="sub_category_name_2" class="form-control">
                                                <option value="">---Select Category Tier 3 ---</option>
                                                @forelse($sub_categories_3_tier as $cat)
                                                    <option value="{{ $cat->code }}" {{ (request('sub_category_name_2') == $cat->code) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <select name="ins_status" class="form-control">
                                                <option value=""> -- Inspection Status --</option>
                                                @forelse(inception_status() as $st => $sv)
                                                    <option value="{{ $st }}" {{ (request('ins_status') == $st) ? 'selected' : '' }}>{{$sv}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="code" class="form-control">
                                                <option value="">-- Select Condition--</option>
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}" {{ (request('code') == $code) ? 'selected' : '' }}>{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="sales_incoterm" id="" class="form-control">
                                                <option value="">-- Original Sales Incoterm --</option>
                                                <option value="EXPORTS_DDU" {{ (request('sales_incoterm') == 'EXPORTS_DDU') ? 'selected' : '' }}>EXPORTS DDU</option>
                                                <option value="EXPORTS_DDP" {{ (request('sales_incoterm') == 'EXPORTS_DDP') ? 'selected' : '' }}>EXPORTS DDP</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="username" value="{{ Request::get('username') }}" class="form-control" placeholder="Username">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="date_invoiced" value="{{ Request::get('date_invoiced') }}" class="form-control" placeholder="Date Invoiced">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="invoice_number" value="{{ Request::get('invoice_number') }}" class="form-control" placeholder="Invoice Number">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="ovrsize" id="" class="form-control">
                                                <option value="">-- Oversized packages --</option>
                                                <option value="Yes" {{ (request('ovrsize') == 'Yes') ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ (request('ovrsize') == 'No') ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="empty_box" id="" class="form-control">
                                                <option value="">-- Empty Box --</option>
                                                <option value="Yes" {{ (request('empty_box') == 'Yes') ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ (request('empty_box') == 'No') ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="date_format" class="form-control">
                                                <option value="m-d-Y" selected>-- US Format --</option>
                                                <option value="d-m-Y">-- UK Format --</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.order.list') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <div class="float-left">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                @if($type == 'Order')
                                    <li class="nav-item">
                                        <a class="nav-link active tab-inactive btn-sm" id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="true">Order level</a>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link active tab-inactive btn-sm" id="pills-item-tab" data-toggle="pill" href="#pills-item" role="tab" aria-controls="pills-item" aria-selected="false">Item level</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ $type }} Lists</h5>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            @if($type == 'Order')
                                @include('pages.admin.order.order-list')
                            @else
                                @include('pages.admin.order.item-list')
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function(){
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

        })
    </script>
@endpush