@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .rack-info-box .card-body {padding: 1.0rem; }
    .rack-info-box .card-header{display: flex;     align-items: center;justify-content: space-between; padding: 1.0rem;}
    .btn-cancel {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-Submit {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-bl-outline {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }

    a.btn-gr-fill {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }
    .web-image { display:flex; gap:5px }
    .web-image .edit-form-value-img {
        width: 25px;
        height: 25px;
        border-radius: 2px;
        border: 1px solid #cacfe7;
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

    $('input[name="from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="eb_from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="eb_to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('.dt').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#search-btn").click(function () {
        $('#export_to').val('');
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('scan');
        $("#filter-frm").submit();
    });
});
</script>
@endpush

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-12 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">All Scan Out List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
                
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Scan Data Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="Scan In From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="Scan In To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="eb_from_date" value="{{ Request::get('eb_from_date') }}" class="form-control datepicker" placeholder="eBay From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="eb_to_date" value="{{ Request::get('eb_to_date') }}" class="form-control datepicker" placeholder="eBay To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="so_from_date" value="{{ Request::get('so_from_date') }}" class="form-control dt" placeholder="Scan Out From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="so_to_date" value="{{ Request::get('so_to_date') }}" class="form-control dt" placeholder="Scan Out To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="dis_from_date" value="{{ Request::get('dis_from_date') }}" class="form-control dt" placeholder="Dispatch From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="dis_to_date" value="{{ Request::get('dis_to_date') }}" class="form-control dt" placeholder="Dispatch To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_i_package_id" value="{{ Request::get('scan_i_package_id') }}" class="form-control datepicker" placeholder="Package ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_i_location_id" value="{{ Request::get('scan_i_location_id') }}" class="form-control datepicker" placeholder="Location ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="location_name" value="{{ Request::get('location_name') }}" class="form-control datepicker" placeholder="Location Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="{{ Request::get('order_id') }}" class="form-control" placeholder="#ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_number" value="{{ Request::get('order_number') }}" class="form-control" placeholder="eBay ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="tracking_number" value="{{ Request::get('tracking_number') }}" class="form-control" placeholder="Tracking ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="customer_address" value="{{ Request::get('customer_address') }}" class="form-control" placeholder="Customer Address">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="zip_code" value="{{ Request::get('zip_code') }}" class="form-control" placeholder="Customer Zip Code">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_out_user" value="{{ Request::get('scan_out_user') }}" class="form-control" placeholder="Scan Out User">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select class="form-control" name="sort">
                                                <option value="">-- Select Sorting-- </option>
                                                <option value="DESC">Newest to Oldest</option>
                                                <option value="ASC">Oldest to Newest</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select class="form-control" name="order_status">
                                                <option value="">-- Select Order Status-- </option>
                                                @forelse(order_status() as $st => $sv)
                                                    <option value="{{ $st }}" {{ Request::get('order_status') == $st ? "selected" : "" }}> {{ $sv }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select class="form-control" name="user_id">
                                                <option value="">-- Select Operator -- </option>
                                                @foreach($operators as $k => $code)
                                                    <option value="{{ $code->id }}">{{ $code->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.all.scan.data') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Scan Data Lists</h5>
                    </div>
                    <div class="card-body booking-info-box">
                        <div class="alert alert-primary">
                            @if(count($orders)>0)
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                            @endif
                        </div>
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="dt-buttons btn-group">
                                    <button class="btn btn-secondary buttons-excel buttons-html5 btn-primary" id="item-excel-btn" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                        <span><i class="la la-file-excel-o"></i> Download Excel</span>
                                    </button>
                                </div>
                                <table class="table table-striped table-bordered table-hover nowrap avn-default table-sm dataTable no-footer" id="DataTables_Table_0" role="grid">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>#Id</th>
                                            <th>Assigned Operator</th>
                                            <th>Package ID</th>
                                            <th>#eBay ID</th>
                                            <th>Status</th>
                                            <th>Location ID</th>
                                            <th>Location Title</th>
                                            <th>Scan In Date</th>
                                            <th>eBay Order Received Date</th>
                                            <th>Scan In User</th>
                                            <th>Scan Out Date</th>
                                            <th>Scan Out User</th>
                                            <th>Photos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $row)
                                            <tr>
                                                <td class="ws" style="white-space:nowrap;">
                                                    <a class="btn btn-edit" href="{{ route('admin.remove.package', $row->id) }}" onclick="return confirm('Are you sure you want to remove this?')">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="ws">{{ $row->id ?? '' }}</td>
                                                <td class="ws">{{ $row->user->name ?? '' }}</td>
                                                <td class="ws">{{ $row->scan_i_package_id ?? '' }}</td>
                                                <td class="ws">{{ $row->order_number ?? '' }}</td>
                                                <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(order_status($row->order_status)) }}"> {{ order_status($row->order_status) }} </span></td>
                                                <td class="ws">{{ $row->scan_i_location_id ?? '' }}</td>
                                                <td class="ws">{{ $row->location_name ?? '' }}</td>
                                                <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y H:i:s', strtotime($row->created_at)) !!}</td>
                                                <td class="ws" style="white-space: nowrap;">@if(!empty($row->sale_date)) {!! date('d-m-Y H:i:s', strtotime($row->sale_date)) !!} @endif</td>
                                                <td class="ws">{{ $row->authorized_by ?? '' }}</td>
                                                <td class="ws" style="white-space: nowrap;">
                                                    @if(!empty($row->scan_out_date))
                                                        {!! date('d-m-Y', strtotime($row->scan_out_date)) !!} {!! date('H:i:s', strtotime($row->scan_out_time)) !!}
                                                    @endif
                                                </td>
                                                <td class="ws" style="white-space: nowrap;">{!! $row->scan_out_user ?? '' !!}</td>
                                                <td class="ws">
                                                    <div class="web-image">
                                                        @if(!empty($row->scan_in_images))
                                                            @forelse(json_decode($row->scan_in_images) as $k)
                                                                <div class="edit-form-value-img ml-1">
                                                                    <a href="{{ route('admin.package.image', $row->id) }}" target="_blank"><img src="{{ asset('public/uploads/'.$k)}}"></a>
                                                                </div>
                                                            @empty
                                                            @endforelse
                                                       @endif
                                                   </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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