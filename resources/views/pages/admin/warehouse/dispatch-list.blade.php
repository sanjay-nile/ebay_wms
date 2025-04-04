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
    .rack-info-box .btn-red { background: #ff4961;}
    .required-field::before {content: "*";color: red;}
    .rg-pack-card {background: #f3f3f3;margin-bottom: 10px;position: relative;border-radius: 10px;padding: 15px 15px 0px;}
    .web-image { display:flex; gap:5px }
    .web-image .edit-form-value-img {
        width: 25px;
        height: 25px;
        border-radius: 2px;
        border: 1px solid #cacfe7;
    }

    .bg-purple {
        background-color: rebeccapurple !important;
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
    $('.dt').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

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
       "timeOut": "5000",
    }

    $("#create-form, #process-save").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.add-btn");

        var wegt = $('#weight').val();
        if(wegt <= 1){
            $("#mySelect").val('USPS');
        }

        if(wegt > 1){
            $("#mySelect").val('UPS');
        }

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
                    if(response.label_url){
                        window.open(response.label_url, '_blank');
                    }
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

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    let com_dis =`
        <div class="rg-pack-card mt-1">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Scan Packages ID</label>
                        <select name="pallet_id" class="form-control select2 assigncountry">
                            <option value="">-- Package Id --</option>
                            @forelse($p_orders as $pid)
                                <option value="{{ $pid->scan_i_package_id }}">{{ $pid->scan_i_package_id }}</option>
                            @empty
                            @endforelse
                        </select>
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
                        <label for="">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" placeholder="" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Add Photo</label>
                        <input type="file" class="form-control" name="images[]" multiple required>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <input type="hidden" name="form_type" value="com_dis">
                        <button class="btn-Submit add-btn btn-sm mt-2" type="submit">Submit</button>
                    </div>
                </div>
            </div>
        </div>`;

    let dim_wgt =`
        <div class="rg-pack-card mt-1">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Scan Location ID</label>
                        <input type="text" class="form-control" name="scan_i_location_id" placeholder="A001-001-001" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Length (in)</label>
                        <input type="text" id="length" name="length" value="10" placeholder="Length" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Width (in)</label>
                        <input type="text" id="width" name="width" value="8" class="form-control" placeholder="Width">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Height (in)</label>
                        <input type="text" id="height" name="height" value="1" class="form-control" placeholder="Height">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Weight (Pound)</label>
                        <input type="text" id="weight" name="weight" value="0.5" class="form-control" placeholder="Weight">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Carrier</label>
                        <select name="carrier" class="form-control" id="mySelect">
                            <option value="">-- Carrier Id --</option>
                            @forelse($Carrier as $k => $cr)
                                <option value="{{ $cr->name }}" @if($k == 0) selected @endif>{{ $cr->name }} ({{ $cr->unit_type}})</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Add Photo</label>
                        <input type="file" class="form-control" name="images[]" multiple>
                    </div>
                </div>
                <div class="col-md-12 mb-1">
                    <div class="action-captured-image-card">
                        <div class="form-group1">
                            <input type="hidden" name="form_type" value="com_dis">
                            <div id="gn-webcam-field"></div>
                            <button class="btn btn-Reset add-btn" type="submit">Submit</button>
                            <button type="button" class="btn btn-Search" data-toggle="modal" data-target="#webcamModal">Open Camera</button>
                        </div>
                    
                        <div id="gn-captured-image" class="d-flex captured-image-list"></div>
                    </div>
                </div>
            </div>
        </div>`;

    $("#combined").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#append_div").html(dim_wgt);
        }
    });

    let cancel =`
        <div class="rg-pack-card">
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

    $(document).on('change', '#weight',function(e){
        let wegt = $(this).val();
        if(wegt <= 1){
            $("#mySelect").val('USPS');
        }

        if(wegt > 1){
            $("#mySelect").val('UPS');
        }
    });
});

$(document).ready(function(){
    $('.assigncountry').select2({
      placeholder: 'Select Package Id',
      allowClear: true
    });
})
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
<script src="https://cdn.jsdelivr.net/npm/webcamjs/webcam.min.js"></script>
<!-- Configure a few settings and attach camera -->
<script language="JavaScript">
    Webcam.set({
        width: 470,
        height: 370,
        image_format: 'png',
        jpeg_quality: 90
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
                        <li class="breadcrumb-item active">Dispatch Package List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')

                @if($status == 'new')
                    <div class="card rack-info-box">
                        <div class="card-header">
                            <h5 class="card-title">Dispatch Package New</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.store.dispatch') }}" method="post" enctype="multipart/form-data" id="create-form">
                                @csrf
                                <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                                <input type="hidden" name="create_system_time" value="" id="system_time">
                                <div id="webcam-field"></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Scan Packages ID</label>
                                            <select name="scan_i_package_id[]" class="form-control select2 assigncountry" multiple>
                                                <option value="">-- Package Id --</option>
                                                @forelse($p_orders as $pid)
                                                    <option value="{{ $pid->scan_i_package_id }}">{{ $pid->scan_i_package_id }}</option>
                                                @empty
                                                @endforelse
                                            </select>
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
                                            <label for="">Tracking Number</label>
                                            <input type="text" class="form-control" name="tracking_number" placeholder="Tracking Number" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Length (in)</label>
                                            <input type="text" name="length" value="10" placeholder="Length" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Width (in)</label>
                                            <input type="text" name="width" value="8" class="form-control" placeholder="Width">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Height (in)</label>
                                            <input type="text" name="height" value="1" class="form-control" placeholder="Height">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Weight (Pound)</label>
                                            <input type="text" name="weight" value="0.5" class="form-control" placeholder="Weight">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Shipping Carrier Code</label>
                                            <input type="text" name="carrier_code" value="" class="form-control" placeholder="Ex. USPS UPS Fedex">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Add Photo</label>
                                            <input type="file" class="form-control" name="images[]" multiple>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="action-captured-image-card">
                                            <div class="form-group1">
                                                <button class="btn btn-Reset add-btn" type="submit">Submit</button>
                                                <button type="button" class="btn btn-Search" data-toggle="modal" data-target="#webcamModal">Open Camera</button>
                                            </div>
                                        
                                            <div id="captured-image" class="d-flex captured-image-list"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal webcamModal fade" id="webcamModal" tabindex="-1" role="dialog" aria-labelledby="webcamModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="webcamModal-open">
                                        <h5 class="modal-title" id="webcamModalLabel">Capture Image</h5>
                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    
                                        <div class="webcamModal-camera">
                                            <div id="camera"></div>
                                        </div>
                                    
                                        <button type="button" class="btn-Save" data-dismiss="modal">Save</button>
                                        <button type="button" class="btn-bl1" onclick="takeSnapshot()">Capture</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Dispatch Package Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control" placeholder="eBay From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control" placeholder="eBay To Date">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <input type="text" name="so_from_date" value="{{ Request::get('so_from_date') }}" class="form-control dt" placeholder="Scan Out From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="so_to_date" value="{{ Request::get('so_to_date') }}" class="form-control dt" placeholder="Scan Out To Date">
                                        </div>
                                        @if($status != 'new')
                                            <div class="form-group col-md-3">
                                                <input type="text" name="dis_from_date" value="{{ Request::get('dis_from_date') }}" class="form-control dt" placeholder="Dispatch From Date">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" name="dis_to_date" value="{{ Request::get('dis_to_date') }}" class="form-control dt" placeholder="Dispatch To Date">
                                            </div>
                                        @endif
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_i_package_id" value="{{ Request::get('scan_i_package_id') }}" class="form-control" placeholder="Package ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="scan_i_location_id" value="{{ Request::get('scan_i_location_id') }}" class="form-control" placeholder="Location ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="{{ Request::get('order_id') }}" class="form-control" placeholder="#ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_number" value="{{ Request::get('order_number') }}" class="form-control" placeholder="eBay ID">
                                        </div>
                                        @if($status != 'new')
                                            <div class="form-group col-md-3">
                                                <input type="text" name="tracking_number" value="{{ Request::get('tracking_number') }}" class="form-control" placeholder="Tracking ID">
                                            </div>
                                        @endif
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
                                            <select class="form-control" name="per_page">
                                                <option value="">-- Per Page-- </option>
                                                <option value="100">100</option>
                                                <option value="200">200</option>
                                                <option value="250">250</option>
                                                <option value="500">500</option>
                                                <option value="750">750</option>
                                                <option value="1000">1000</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.dispatch.list', $status) }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Dispatch Package Lists</h5>
                    </div>

                    <div class="card-body booking-info-box">
                        <div class="alert alert-primary">
                            @if(count($orders)>0) Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders @endif
                        </div>

                        @if($status == 'new')
                            <div class="text-left">
                                <button type="button" class="btn btn-sm btn-red mb-0" id="cancel-btn">Cancel Order</button>
                                <button type="button" id="combined" class="btn btn-blue btn-sm">Generate Label</button>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.combined.dispatch') }}" method="post" id="process-save" enctype="multipart/form-data">
                            @csrf
                            <div id="append_div"></div>
                            <div class="table-responsive ">
                                <table  class="table table-striped table-bordered nowrap avn-defaults table-sm dataTable">
                                    <thead>
                                        <tr>
                                            @if($status == 'new')
                                                <th class="ws">
                                                    <input name="select_all" value="1" id="select-all" type="checkbox">
                                                </th>
                                            @endif
                                            <th class="ws">Action</th>
                                            <th>#Id</th>
                                            <th>Assigned Operator</th>
                                            <th>Package ID</th>
                                            <th>#eBay ID</th>
                                            <th>Customer Address</th>
                                            <th>Location ID</th>
                                            <th>Title</th>
                                            <th>Tracking Number</th>
                                            <th>eBay Order Received Date</th>
                                            <th>Scan Out Date</th>
                                            <th>Scan Out User</th>
                                            @if($status != 'new')
                                                <th>Dispatch Date</th>
                                                <th>Dispatch User</th>
                                                <th>Weight</th>
                                                <th>Dims</th>
                                            @else
                                                <th>Order Age</th>
                                                <th>Order OverDue</th>
                                            @endif
                                            <th>Status</th> 
                                            <th>Photos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $row)
                                            @php
                                                $currentDate = \Carbon\Carbon::now();
                                                $givenDate = \Carbon\Carbon::parse(date('Y-m-d', strtotime($row->sale_date))); // Replace with your date

                                                /*$startDate = $givenDate;
                                                $endDate = $currentDate;
                                                $start = \Carbon\Carbon::parse($startDate);
                                                $end = \Carbon\Carbon::parse($endDate);
                                                $period = \Carbon\CarbonPeriod::create($start, $end);
                                                $businessDays = 0;
                                                foreach ($period as $date) {
                                                    if (!$date->isWeekend()) { // Excludes Saturdays and Sundays
                                                        $businessDays++;
                                                    }
                                                }*/

                                                $daysDifference = $currentDate->diffInDays($givenDate);
                                                $difference = $daysDifference - 3;
                                                $cl = '';
                                                if($difference == 1){
                                                    $cl = 'table-warning';
                                                } elseif($difference >= 2){
                                                    $cl = 'table-danger';
                                                }
                                            @endphp
                                            <tr class="@if($row->order_type == 'combined') bg-purple text-white @endif @if($status == 'new') {{ $cl }} @endif">
                                                @if($row->order_status == 'IS-03' && $status == 'new')
                                                    <td style="text-align: center;"><input name="order_ids[]" value="{{ $row->id }}" type="checkbox" class="selectone" /></td>
                                                @endif
                                                <td class="ws" style="text-align: center;">
                                                    <a href="{{ route('admin.new_ebay_order_details', $row->id) }}" class="btn btn-edit" target="_blank">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.order_invoice', $row->id) }}" class="btn btn-view" target="_blank">
                                                        <i class="fa fa-tags"></i>
                                                    </a>
                                                    @if(!empty($row->label_url))
                                                        <a href="{{ $row->label_url }}" class="btn btn-edit" target="_blank">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="ws">{{ $row->id ?? '' }}</td>
                                                <td class="ws">{{ $row->user->name ?? '' }}</td>
                                                <td class="ws">{{ $row->scan_i_package_id ?? '' }}</td>
                                                <td class="ws">{{ $row->order_number ?? '' }}</td>
                                                <td class="ws">{{ $row->ship_to_address_1 ?? '' }}, {{ $row->ship_to_city ?? '' }} {{ $row->ship_to_state ?? '' }} {{ $row->ship_to_zip ?? '' }} {{ $row->ship_to_country ?? '' }}</td>
                                                <td class="ws">{{ $row->scan_i_location_id ?? '' }}</td>
                                                <td class="ws">{{ $row->location_name ?? '' }}</td>
                                                <td class="ws">{{ $row->tracking_number ?? '' }}</td>
                                                <td class="ws" style="white-space: nowrap;">@if(!empty($row->sale_date)) {!! date('d-m-Y H:i:s', strtotime($row->sale_date)) !!} @endif</td>
                                                <td class="ws" style="white-space: nowrap;">@if(!empty($row->scan_out_date)) {!! date('d-m-Y', strtotime($row->scan_out_date)) !!} {!! date('H:i:s', strtotime($row->scan_out_time)) !!} @endif</td>
                                                <td class="ws">{{ $row->scan_out_user ?? '' }}</td>
                                                @if($status != 'new')
                                                    <td class="ws" style="white-space: nowrap;">
                                                        @if(!empty($row->scan_dispatch_date)) {!! date('d-m-Y', strtotime($row->scan_dispatch_date)) !!} @endif
                                                        @if(!empty($row->scan_dispatch_time)) {!! date('H:i:s', strtotime($row->scan_dispatch_time)) !!} @endif
                                                    </td>
                                                    <td class="ws">{{ $row->scan_dispatch_user ?? '' }}</td>
                                                    <td class="ws">{{ $row->weight ?? '' }}</td>
                                                    <td class="ws">@if(!empty($row->length)) {{ $row->length ?? '' }} X {{ $row->width ?? '' }} X {{ $row->height ?? '' }} @endif</td>
                                                @else
                                                    <td class="ws" style="white-space: nowrap;">
                                                        +{{ $daysDifference }} Days
                                                    </td>
                                                    <td class="ws" style="white-space: nowrap;">
                                                        {{ sprintf('%+d', $difference) }} Days
                                                    </td>
                                                @endif
                                                <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(order_status($row->order_status)) }}"> {{ order_status($row->order_status) }} </span></td>
                                                <td class="ws">
                                                    <div class="web-image"> 
                                                        @if(!empty($row->scan_in_images))
                                                            @forelse(json_decode($row->scan_in_images) as $k)
                                                                <div class="edit-form-value-img">
                                                                    <a href="{{ route('admin.package.image', $row->id) }}" target="_blank"><img src="{{ asset('public/uploads/'.$k)}}"></a>
                                                                </div>
                                                            @empty
                                                            @endforelse
                                                        @endif
                                                        @if(!empty($row->dispatch_images))
                                                            @forelse(json_decode($row->dispatch_images) as $k)
                                                                <div class="edit-form-value-img">
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#webcamModal').on('shown.bs.modal', function () {
            Webcam.reset();
            Webcam.attach( '#camera' );
        });

        $('#webcamModal').on('hidden.bs.modal', function () {
            Webcam.reset();
        });
    });

    let maxImages = 4; // Maximum number of images allowed
    let capturedImages = []; // Store captured images

    // Capture and display image
    function takeSnapshot() {
        if (Webcam.loaded) {
            if (capturedImages.length >= maxImages) {
                alert(`You can only capture up to ${maxImages} images.`);
                return;
            }

            Webcam.snap(function(data_uri) {
                // Add the captured image to the list
                capturedImages.push(data_uri);

                // Display the captured image
                // document.getElementById('captured-image').append = '<img src="'+data_uri+'" class="img-thumbnail mt-3"/>';
                $("#captured-image").append('<div class="captured-image-thumb"><img src="'+data_uri+'" class="img-thumbnail "/></div>');
                $("#gn-captured-image").append('<div class="captured-image-thumb"><img src="'+data_uri+'" class="img-thumbnail "/></div>');

                // Save image in the hidden input field
                // document.getElementById('webcam_image').value = data_uri;
                $("#webcam-field").append('<input type="hidden" name="webcam_image[]" id="webcam_image" value="'+data_uri+'">');
                $("#gn-webcam-field").append('<input type="hidden" name="webcam_image[]" id="webcam_image" value="'+data_uri+'">');

                // Close the modal
                // $('#webcamModal').modal('hide');
            });
        } else {
            console.error('Webcam is not loaded yet.');
        }
    }
</script>
@endpush