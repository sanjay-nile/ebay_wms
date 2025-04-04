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

    toastr.options ={
       "closeButton" : true,
       "progressBar" : true,
       "disableTimeOut" : true,
    }

    $("#create-form").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.add-btn");
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend : function(){
                curSubmit.html(`Sending.. <i class="la la-spinner la-spin"></i>`).attr('disabled',true);
            },
            success : function(response){                    
                if(response.status==201){
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                    return false;
                }

                if(response.status==200){                   
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.error(response.message);
                    return false;
                }
            },
            error : function(data){
                if(data.status==422){
                    let li_htm = '';
                    $.each(data.responseJSON.errors,function(k,v){
                        const $input = form.find(`input[name=${k}],select[name=${k}],textarea[name=${k}]`);
                        if($input.next('small').length){
                            $input.next('small').html(v);
                            if(k == 'type_of_place' || k == 'safety' || k == 'p_value' || k == 'amenities' || k == 'features'){
                                $('.'+k).html(`<small class='text-danger'>${v[0]}</small>`);
                            }
                        }else{
                            $input.after(`<small class='text-danger'>${v}</small>`);
                            if(k == 'type_of_place' || k == 'safety' || k == 'p_value' || k == 'amenities' || k == 'features'){
                                $('.'+k).html(`<small class='text-danger'>${v[0]}</small>`);
                            }
                        }
                        li_htm += `<li>${v}</li>`;
                    });
                    curSubmit.html(`Submit`).attr('disabled',false);
                    return false;
                }else{                  
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.error(data.statusText);
                    return false;
                }
            }
        });
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
                        <li class="breadcrumb-item active">Move To Location List</li>
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
                        <h5 class="card-title">Move To Location New</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.store.move.location') }}" method="post" enctype="multipart/form-data" id="create-form">
                            @csrf
                            <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="create_system_time" value="" id="system_time">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Scan Package ID</label>
                                        <input type="text" class="form-control" name="scan_i_package_id" placeholder="ORD-0-8763874237-0000037" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Scan Location ID</label>
                                        <input type="text" class="form-control" name="scan_i_location_id" placeholder="A001-001-001" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Scan New Location ID</label>
                                        <input type="text" class="form-control" name="new_location_id" placeholder="A001-001-001" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Add Photo</label>
                                        <input type="file" class="form-control" name="images[]" multiple required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button class="btn-Submit add-btn btn-sm" type="submit">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Move To Location Filters</h5>
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
                                            <input type="text" name="scan_i_package_id" value="{{ Request::get('scan_i_package_id') }}" class="form-control datepicker" placeholder="Package ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_i_location_id" value="{{ Request::get('scan_i_location_id') }}" class="form-control datepicker" placeholder="Location ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="{{ Request::get('order_id') }}" class="form-control" placeholder="#ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select class="form-control" name="sort">
                                                <option value="">-- Select Sorting-- </option>
                                                <option value="DESC">Newest to Oldest</option>
                                                <option value="ASC">Oldest to Newest</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.add.scan.in') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Move To Location Lists</h5>
                    </div>
                    <div class="card-body booking-info-box">
                       <div class="table-responsive ">
                            <table  class="table table-striped table-bordered nowrap avn-defaults table-sm dataTable ">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>#ID</th>
                                        <th>Scan In Date</th>
                                        <th>Scan In Age</th>
                                        <th>Scan In User</th>
                                        <th>Package ID</th>
                                        <th>Location ID</th>
                                        <th>Active</th>
                                        <th>Photos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $row)
                                        <tr>
                                            <td class="ws" style="white-space:nowrap;">
                                                {{-- <a class="btn btn-view" href="{{ route('admin.scan.in.out', $row->id) }}" onclick="return confirm('Are you sure you want to scan out this?')">
                                                    <i class="fa fa-qrcode" aria-hidden="true"></i> Scan Out
                                                </a> --}}
                                                <a class="btn btn-edit" href="{{ route('admin.remove.package', $row->id) }}" onclick="return confirm('Are you sure you want to remove this?')">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                            <td class="ws">{{ $row->id ?? '' }}</td>
                                            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y H:i:s', strtotime($row->created_at)) !!}</td>
                                            <td class="ws" style="white-space: nowrap;">{{ $row->created_at->startOfDay()->diffInDays(now()->startOfDay()) }} Days</td>
                                            <td class="ws">{{ $row->authorized_by ?? '' }}</td>
                                            <td class="ws">{{ $row->scan_i_package_id ?? '' }}</td>
                                            <td class="ws">{{ $row->scan_i_location_id ?? '' }}</td>
                                            <td class="ws">{{ ($row->post_status == 1) ? 'Yes' : 'No' }}</td>
                                            <td class="ws"><a href="{{ route('admin.package.image', $row->id) }}" target="_blank">View <i class="fa fa-picture-o" aria-hidden="true"></i></a></td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
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