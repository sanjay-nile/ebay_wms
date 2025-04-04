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
                    $('#shw-msg').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>`);
                    setTimeout(function () {
                        // location.reload(true);
                        $('.emp-input').val(' ');
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

    $("#process-save").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.add-btn");
        if (confirm("Do you want to completed this action.?")) {
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
        }
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    let opr =`
        <div class="rg-pack-card mt-1">
            <div class="row">
                <div class="col-md-3">
                    <label> Select Operator <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="user_id" class="form-control" id="user_id">
                            <option value="">-- Select --</option>
                            @foreach($operators as $k => $code)
                                <option value="{{ $code->id }}">{{ $code->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="hidden" name="form_type" value="operator">
                    <button class="btn-Submit add-btn btn-sm mt-2" type="submit">Submit</button>
                </div>
            </div>
        </div>`;

    $("#operator-btn").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#append_div").html(opr);
        }
    });

    let cancel =`
        <div class="rg-pack-card mt-1">
            <div class="row">
                <div class="col-md-3">
                    <label>Reason for Cancellation <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="cancel_reason" class="form-control" id="cancel_reason">
                            <option value="">-- Select --</option>
                            @foreach(cancel_reason() as $k => $code)
                                <option value="{{ $k }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="hidden" name="form_type" value="cancel">
                    <button class="btn-Submit add-btn btn-sm mt-2" type="submit">Submit</button>
                </div>
            </div>
        </div>`;

    $("#cancel-btn").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#append_div").html(cancel);
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
                        <li class="breadcrumb-item active">Scan Out List</li>
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
                        <h5 class="card-title">Scan Out New</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.store.scan.out') }}" method="post" enctype="multipart/form-data" id="create-form">
                            @csrf
                            <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="create_system_time" value="" id="system_time">
                            <input type="hidden" name="form_type" id="form_type" value="combined">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Scan Location ID</label>
                                        <input type="text" class="form-control emp-input" name="scan_i_location_id" placeholder="A001-001-001" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Scan Package ID</label>
                                        <input type="text" class="form-control emp-input" name="scan_i_package_id" placeholder="ORD-0-8763874237-0000037" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <div class="form-group">
                                        <button class="btn-Submit add-btn btn-sm" type="submit">Save</button>
                                    </div>
                                </div>
                                <div class="col-md-12" id="shw-msg"></div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Scan Out Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="eBay From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="eBay To Date">
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
                                            <select class="form-control" name="sort">
                                                <option value="">-- Select Sorting-- </option>
                                                <option value="DESC">Newest to Oldest</option>
                                                <option value="ASC">Oldest to Newest</option>
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
                                            <a href="{{ route('admin.add.scan.out') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Scan Out Lists</h5>
                    </div>
                    <div class="card-body booking-info-box">
                        <div class="alert alert-primary">
                            @if(count($orders)>0)
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                            @endif
                        </div>

                        @if(Auth::user()->user_type_id == 1 || Auth::user()->user_type_id == 2)
                            <div class="text-left">
                                <button type="button" class="btn btn-sm btn-red mb-0" id="operator-btn">Assign Items to Operator</button>
                                <button type="button" class="btn btn-sm btn-blue mb-0" id="cancel-btn">Cancel Order</button>
                            </div>
                        @endif

                        <form action="{{ route('admin.assign.operator') }}" method="post" id="process-save" enctype="multipart/form-data">
                            @csrf
                            <div id="append_div"></div>
                            <div class="table-responsive ">
                                <table  class="table table-striped table-bordered nowrap avn-defaults table-sm dataTable ">
                                    <thead>
                                        <tr>
                                            <th class="ws">
                                                <input name="select_all" value="1" id="select-all" type="checkbox">
                                            </th>
                                            <th class="ws">Action</th>
                                            <th>#Id</th>
                                            <th>Assigned Operator</th>
                                            <th>Package ID</th>
                                            <th>#eBay ID</th>
                                            <th>Customer Address</th>
                                            <th>Location ID</th>
                                            <th>Title</th>
                                            {{-- <th>Order Received Date</th> --}}
                                            <th>eBay Order Date</th>
                                            <th>Scan In User</th>
                                            <th>Status</th>
                                            <th>Order Age</th>
                                            <th>Order OverDue</th>
                                            <th>Photos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $row)
                                            @php
                                                $currentDate = \Carbon\Carbon::now();
                                                $givenDate = \Carbon\Carbon::parse(date('Y-m-d', strtotime($row->sale_date))); // Replace with your date
                                                $daysDifference = $currentDate->diffInDays($givenDate);
                                                $difference = $daysDifference - 3;
                                                $cl = '';
                                                if($difference == 1){
                                                    $cl = 'table-warning';
                                                } elseif($difference >= 2){
                                                    $cl = 'table-danger';
                                                }
                                            @endphp
                                            <tr class="{{ $cl }}">
                                                <td style="text-align: center;">
                                                    <input name="order_ids[]" value="{{ $row->id }}" type="checkbox" class="selectone" />
                                                </td>
                                                <td class="ws" style="text-align: center;">
                                                    <a href="{{ route('admin.order_invoice', $row->id) }}" class="btn btn-view" target="_blank">
                                                        <i class="fa fa-tags"></i>
                                                    </a>
                                                </td>
                                                <td class="ws">{{ $row->id ?? '' }}</td>
                                                <td class="ws">{{ $row->user->name ?? '' }}</td>
                                                <td class="ws">{{ $row->scan_i_package_id ?? '' }}</td>
                                                <td class="ws">{{ $row->order_number ?? '' }}</td>
                                                <td class="ws">{{ $row->ship_to_address_1 ?? '' }}, {{ $row->ship_to_city ?? '' }} {{ $row->ship_to_state ?? '' }} {{ $row->ship_to_zip ?? '' }} {{ $row->ship_to_country ?? '' }}</td>
                                                <td class="ws">{{ $row->scan_i_location_id ?? '' }}</td>
                                                <td class="ws">{{ $row->location_name ?? '' }}</td>
                                                {{-- <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y H:i:s', strtotime($row->created_at)) !!}</td> --}}
                                                <td class="ws" style="white-space: nowrap;">@if(!empty($row->sale_date)) {!! date('d-m-Y H:i:s', strtotime($row->sale_date)) !!} @endif</td>
                                                <td class="ws">{{ $row->authorized_by ?? '' }}</td>
                                                <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(order_status($row->order_status)) }}"> {{ order_status($row->order_status) }} </span></td>
                                                <td class="ws" style="white-space: nowrap;">
                                                    +{{ $daysDifference }} Days
                                                </td>
                                                <td class="ws" style="white-space: nowrap;">
                                                    {{ sprintf('%+d', $difference) }} Days
                                                </td>
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