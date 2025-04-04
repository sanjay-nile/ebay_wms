@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .rack-info-box .card-body {padding: 1.5rem; }
    .rack-info-box .card-header{display: flex;     align-items: center;justify-content: space-between; padding: 1.5rem;}
    .btn-cancel {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-Submit {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-bl-outline {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }

    .btn-gr-fill {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }
      a.btnblicon {padding: 1px 3px; color: #fff; background-color: #3d2a67 !important; border-color: #3d2a67 !important; border-radius: 2px; }
    a.btnreicon {padding: 1px 3px; color: #fff; background-color: #ff052f !important; border-color: #ff052f !important; border-radius: 2px; }

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

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#search-btn").click(function () {
        $('#export_to').val('');
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $("#combined").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#form_type").val('label');
            $("#process-save").submit();
        }
    });

    $("#sync-data").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#form_type").val('syncdata');
            $("#process-save").submit();
        }
    });
});
</script>
<script type="text/javascript">
    function setClientDateTime() {
        var currentDate = new Date();
        var formattedDateTime = currentDate.getFullYear() + '-' +
                                (currentDate.getMonth() + 1).toString().padStart(2, '0') + '-' +
                                currentDate.getDate().toString().padStart(2, '0') + ' ' +
                                currentDate.getHours().toString().padStart(2, '0') + ':' +
                                currentDate.getMinutes().toString().padStart(2, '0') + ':' +
                                currentDate.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('system_time').value = formattedDateTime;
    }

    // Call this function before the form is submitted
    window.onload = setClientDateTime;
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
                        <li class="breadcrumb-item active">Rack List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')

                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Rack Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="{{ Request::get('order_id') }}" class="form-control" placeholder="#ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="location_id" value="{{ Request::get('location_id') }}" class="form-control" placeholder="Location ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="client_id" class="form-control">
                                                <option value="">-- Select Client --</option>
                                                @forelse($clients as $cid)
                                                    <option value="{{ $cid->id }}" {{ (request('client_id') == $cid->id) ? 'selected' : '' }}>{{ $cid->name }}</option>
                                                @empty
                                                @endforelse
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
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.rack.list') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Rack Lists</h5>
                    </div>
                    <div class="card-body booking-info-box">
                        <div class="text-left mb-1">
                            <button type="button" id="combined" class="btn btn-sm btn-red mb-0">Print Bulk Label</button>
                            <button type="button" id="sync-data" class="btn btn-blue btn-sm">Sync Data</button>
                        </div>
                        <form action="{{ route('admin.location.bulk.invoice') }}" method="post" id="process-save" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="form_type" value="" id="form_type">
                            <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="create_system_time" value="" id="system_time">
                            <div class="alert alert-primary">
                                @if(count($orders)>0)
                                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Locations
                                @endif
                            </div>
                            <div class="table-responsive ">
                                <table  class="table table-striped table-bordered nowrap avn-defaults table-sm dataTable ">
                                    <thead>
                                        <tr>
                                            <th class="ws">
                                                <input name="select_all" value="1" id="select-all" type="checkbox">
                                            </th>
                                            <th>Action</th>
                                            <th>#ID</th>
                                            <th>Date</th>
                                            <th>Client Name</th>
                                            <th>Warehouse Name</th>
                                            <th>Measurement Type</th>
                                            <th>Location ID</th>
                                            <th>Sync Status</th>
                                            <th>Title</th>
                                            <th>Label</th>
                                            <th>Shelves</th>
                                            <th>Dimension</th>
                                            <th>Max Weight</th>
                                            <th>Active</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $row)
                                            <tr>
                                                <td style="text-align: center;"><input name="order_ids[]" value="{{ $row->id }}" type="checkbox" class="selectone" /></td>
                                                <td class="ws" style="text-align: center;">
                                                    <a class="btn btn-edit" href="{{ route('admin.add.rack', $row->id) }}" target="_blank">
                                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="btn btn-view" href="{{ route('admin.remove.package', $row->id) }}" onclick="return confirm('Are you sure you want to remove this?')">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="btn btn-edit" href="{{ route('admin.location.invoice', $row->id) }}" target="_blank">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="ws">{{ $row->id ?? '' }}</td>
                                                <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->created_at)) !!}</td>
                                                <td class="ws">{{ $row->client->name ?? '' }}</td>
                                                <td class="ws">{{ $row->warehouse->name ?? '' }}</td>
                                                <td class="ws">{{ $row->measurement ?? '' }}</td>
                                                <td class="ws">{{ $row->org_location_id ?? '' }}</td>
                                                <td class="ws">{{ (!empty($row->sync_status)) ? $row->sync_status : 'Pending' }}</td>
                                                <td class="ws">{{ $row->post_title }}</td>
                                                <td class="ws">{{ $row->level ?? '' }}</td>
                                                <td class="ws">{{ $row->shelves ?? '' }}</td>
                                                <td class="ws">{{ $row->length }} X {{ $row->width }} X {{ $row->height }}</td>
                                                <td class="ws">{{ $row->weight ?? '' }}</td>
                                                <td class="ws">{{ ($row->post_status == 1) ? 'Yes' : 'No' }}</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
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