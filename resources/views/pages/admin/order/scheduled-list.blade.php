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

    $("#search-btn").click(function () {
        $('#export_to').val('');
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('schedule-item');
        $("#filter-frm").submit();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
                        <li class="breadcrumb-item active">eBay Scheduled List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Fillters</h5>
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
                                            <select name="pallet_id" class="form-control select2 assigncountry">
                                                <option value="">-- Pallet Id --</option>
                                                @forelse($PalletDeatil as $pid)
                                                    <option value="{{ $pid->pallet_id }}" {{ (request('pallet_id') == $pid->pallet_id) ? 'selected' : '' }}>{{ $pid->pallet_id }}</option>
                                                @empty
                                                @endforelse
                                            </select>
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
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.scheduled.item') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Scheduled Lists</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary">
                            @if(count($orders)>0)
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                            @endif
                        </div>

                        <div class="text-left">
                            {{-- <button type="button" id="add-to-warehouse" class="btn btn-red btn-sm mb-1">Send to Scheduled</button> --}}
                            <button type="button" class="btn btn-sm btn-blue pull-left mb-1 mr-1" id="item-excel-btn">Export To Excel</button>
                        </div>

                        <div class="table-responsive booking-info-box" style="padding: 0;">
                            <form action="{{ route('admin.bulk.ebay.draft') }}" method="post" id="process-save">
                                @csrf
                                <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
                                    <thead>
                                        <tr>
                                            <th class="ws">Date Rcvd in Warehouse</th>
                                            <th class="ws">Item Status</th>
                                            <th class="ws">Order Ref. Number</th>
                                            <th class="ws">Item Ref. Number</th>
                                            <th class="ws">EVTN Number</th>
                                            <th class="ws">Name</th>
                                            <th class="ws">Item Sku</th>
                                            <th class="ws">Title</th>
                                            <th class="ws">Condition</th>
                                            <th class="ws">Expected Qty</th>
                                            <th class="ws">Received Qty</th>
                                            <th class="ws">Pallet Id</th>
                                            <th class="ws">Pallet Type</th>
                                            <th class="ws">Cable & Accessory</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $row)
                                            <tr>
                                                <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->ps_created_at)) !!}</td>
                                                <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status_value($row->status)) }}"> {{ inception_status_value($row->status) }} </span></td>
                                                <td class="ws">{!! $row->reference_number ?? $row->id !!}</td>
                                                <td class="ws">{!! $row->package_id ?? '' !!}</td>
                                                <td class="ws">{!! $row->evtn_number ?? '' !!}</td>
                                                <td class="ws">{!! $row->customer_name ?? '' !!}</td>
                                                <td class="ws">{{ $row->itemSku ?? '' }}</td>
                                                <td class="ws">{{ $row->title ?? '' }}</td>
                                                <td class="ws">{{ $row->condition ?? '' }}</td>
                                                <td class="ws">{{ $row->expected_quantity ?? $row->itemQuantity }}</td>
                                                <td class="ws">
                                                    @if($row->match_quantity == 'Yes')
                                                        {{ $row->itemQuantity ?? '1' }}
                                                    @else
                                                        {{ $row->actual_quantity ?? 'TBC' }}
                                                    @endif
                                                </td>
                                                <td class="ws">{{ $row->pallet_id ?? '' }}</td>
                                                <td class="ws">{{ $row->pallet_type ?? '' }}</td>
                                                <td class="ws">
                                                    @if($row->cable_access == 'Yes') Item includes cable & accessory @else Item does NOT includes cable & accessory @endif
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>
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