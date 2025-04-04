@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<style type="text/css">
    .info-list-box.info-mt{margin-top: 10px;}
    #image-upload-80{margin-right: 10px;}
    .info-list-box.info-mt .btn-upload{padding: 7px 20px; border-radius: 4px; margin-left: 10px; background: #c95d6f; 
        color: #fff;}
    .info-list-box{display: block;} 
    .info-list-box.Img-attachment{display: flex;}   
    .info-list-box ul{ list-style-type: none !important; display: contents; }
    .info-list-box ul li{margin: 10px 40px 0 20px;}
    .product-gallery-80 li img{ background-color: #e8e9ee; border:1px solid #f1f1f1;}
    .btn-dlt{background: #b52039; color: #fff; border: none; border-radius: 3px; position: absolute;
        bottom: 0;    margin: 0px 0px 7px 10px; padding: 9px 18px;
    }
    .info-list-section .col-md-3, .col-md-9{margin-top: 14px;}
    .card-body .bg-light{background:#fef0f2 !important;     border-bottom: 1px solid #ffdee3; border-radius: 3px;}
    .card-body form{ padding: 0px 16px; }
    .card-body .navbar .navbar-brand{    font-weight: 600; font-size: 15px; color: #000;}
    .attachment-sec{ background: #f9f7f7; border: 1px solid #f1ecec; border-radius: 4px;}
    .attachment-sec-1 {   border: 1px solid #fad7d7;
        border-radius: 4px;
        width: 100%;
        margin: 0 0 20px 0;
        padding: 5px;
        background: #fbf7f7;
    }
    .Card-detail .row p{ font-size: 14px; font-weight: 300; }
    .Card-detail .row label{ color: #2B335E; font-weight: 500; }
    .Head-track{color: #000;}
    .Head-secondary{font-size: 13px;}
    .info-list-section .row label{    font-size: 1rem; }
    .info-list-section h2 {font-size: 16px; color: #b51f37; font-weight: bold; }
    .address-info-text h2.name {font-size: 16px; color: #0e1036; font-weight: bold; padding: 0; }
    .info-list-section {border-radius: 5px; padding: 10px; border: 1px solid #d6d9e6; background: #fdfdff; margin-bottom: 20px; }
    .address-h {display: flow-root; }

    .address-content h3 {
        font-size: 15px;
        font-weight: bold;
    }
    .address-content p {
        font-size: 13px;
        margin-bottom: 10px;
    }
    .address-text h4,
    .hours-text h4{
        font-size: 13px;
        font-weight: bold;
    }

    .create-form-section {
        border: 1px solid #f4f5fa;
        border-radius: 5px;
        margin-bottom: 20px;
        border-radius: 12px;
        padding: 0px 0px 20px;
        background: #fff;
    }
    .create-form-section h2 {
        /*background: #fbe6e9;*/
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    .create-form-body {
        padding: 10px;
    }

    .info-list-inner {
        width: 100%;
        margin-top: 0;
        padding: 20px 4px 20px;
        border-radius: 4px;
        background-image: linear-gradient(#bbbdbf, #e7e9e9);
    }
</style>

<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#track-detail').click(function(){
            var obj = $(this);
            let carrierWaybill = obj.data('id');
            if(carrierWaybill=='undefined') {alert('Tracking Id Not Generated'); return false;}
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url:  '{!! url('/') !!}/get-tracking/'+carrierWaybill,
                method: "get",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    // console.log(response);
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="fa fa-eye"> Get Details</i>');
                    $('#track-data').html(response);
                    $('#myModal').modal({show:true});
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="fa fa-eye"> Get Details</i>');
                }
            });
        });

        $('#more-track-btn').click(function(){
            var obj = $(this);
            let carrierWaybill = obj.data('id');
            if(carrierWaybill=='undefined') {alert('Tracking Id Not Generated'); return false;}
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url:  '{!! url('/') !!}/get-more-tracking/'+carrierWaybill,
                method: "get",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    // console.log(response);
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="fa fa-eye"> Get More Details</i>');
                    $('#track-data').html(response);
                    $('#myModal').modal({show:true});
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="fa fa-eye"> Get More Details</i>');
                }
            });
        });

        $(document).on('click', '#show-frm', function(){
            $('#status-frm').toggle();
        });

        $(document).on('click','#status-history',function(){
            var id = $('#post_id').val();
            $.ajax({
                type:'get',
                url : "{{ route('admin.history.status') }}",
                data:{post_id:id},
                dataType : 'json',
                success : function(response){
                    console.log(response.history);
                    $('#history-data').html(response.history);
                    $('#historymyModal').modal({show:true});
                }
            });
        });
    });

    function updatePackage(package_id){
        let st_v = $('#estimated_value').val();
        let hs_code = $('#hs_code').val();
        let ac = $("#status option:selected").val();
        let rs = $("#refund_status option:selected").val();

        // if (st_v == '' || hs_code == '' || ac == '' || rs == '') {
        //     alert('Please fill the value');
        // } else{
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            if(st_v){
                formData.append('estimated_value', st_v);
            }
            if(hs_code){
                formData.append('hs_code', hs_code);
            }
            if(ac){
                formData.append('status', ac);
            }
            if(rs){
                formData.append('refund_status', rs);
            }

            formData.append('package_id', package_id);
            $.ajax({
                url: "{{route('update-package')}}",
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.fail) {
                        alert(response.error);
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        // }
    }
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
                        <li class="breadcrumb-item active">Return Order Details</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-md-12">
                @include('includes/admin/notify')
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="card booking-info-box">
                    <div class="card-header">
                        @php
                            $hrs = $waybill->getMeta('_happy_return_status');
                        @endphp
                        <h4 class="card-title">
                            <a href="{{ redirect()->back()->getTargetUrl() }}" class="btn btn-outline-info btn-sm"><i class="la la-arrow-left"></i> Back</a>
                            @if($hrs)
                                @php $hrsv = json_decode($hrs); @endphp
                                <a class="btn btn-sm btn-outline-success ml-2" href="{{ $hrsv->qr_code }}" target="_blank">
                                    <i class="la la-eye"></i> QR Code
                                </a>
                            @endif
                        </h4>
                    </div>
                    <div class="card-content">                        
                        <div class="card-body">
                            <div class="create-form-section">
                                <nav>
                                    <h2 class="navbar-text">Customer Details</h2>
                                    <a class="Head-secondary pull-right" href="javascript:void(0);">
                                        Order Number / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}
                                    </a>
                                </nav>
                                <div class="info-list-inner">
                                    <div class="create-form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">RG Order No.</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->id }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Name</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_name }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Email</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_email }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Address</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_address }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Country</div>
                                                    <div class="booking-value-info">
                                                        {{ get_country_name_by_id($waybill->meta->_customer_country) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">State</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_state }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">City</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_city }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Postal code</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_pincode }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Mobile</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_customer_phone }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="create-form-section">
                                <h2 style="font-size:18px;">Current Status:- @if($waybill->meta->_status_id) {!! getStatusValue($waybill->meta->_status_id) !!}-{!! $waybill->meta->_status_id !!} @endif, {!! $waybill->meta->_addition_info ?? '' !!}, @if($waybill->meta->_status_date) {{ date('d/m/Y', strtotime($waybill->meta->_status_date)) }} @endif / @if($waybill->meta->_status_time) {{ date('h:ia', strtotime($waybill->meta->_status_time)) }} @endif</h2>
                                <p class="pl-1"><button type="button" class="btn btn-red" id="show-frm">Edit Status</button> <button type="button" class="btn btn-blue add-history-btn" id="status-history">Status History</button></p>
                                <div class="info-list-inner" style="display:none;" id="status-frm">
                                    <div class="create-form-body">
                                        <form method="post" action="{{ route('admin.orders.status') }}" id="update-form">
                                            <input type="hidden" name="order_id" value="{{ $waybill->id }}" id="post_id">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control" name="status_id">
                                                            <option value="">-- Select --</option>
                                                            @foreach(getStatusList() as $k => $dt)
                                                                <option value="{{ $k }}" @if($k == $waybill->meta->_status_id ?? '') selected @endif>{{ $dt }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('status_id')
                                                            <div class="error">The field is required</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="addition_info" class="form-control" placeholder="Additional Info" value="{{ $waybill->meta->_addition_info ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="date" name="status_date" class="form-control" placeholder="Date" value="{{ $waybill->meta->_status_date ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="time" name="status_time" class="form-control" placeholder="Time" value="{{ $waybill->meta->_status_time ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-red add-save-btn">Update</button>
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-12 error-msg"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            @php
                                $track_id = 'Not Generated Yet';
                                $label_url = 'NA';

                                if ($waybill->hasMeta('_generate_waywill_status')) {
                                    # code...
                                    $tracking_detail = ($waybill->meta->_generate_waywill_status)?? NULL; 
                                    $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                    
                                    if($tracking_data){
                                        $track_id = $tracking_data->carrierWaybill ?? 'N/A';
                                        foreach($tracking_data->labelDetailList as $t){
                                            $label_url = (isset($t->artifactUrl) && !empty($t->artifactUrl)) ? $t->artifactUrl : 'NA';
                                        }
                                    }
                                }else{
                                    $tracking_detail = ($waybill->meta->_order_tracking_id)?? NULL; 
                                    $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;                                    
                                    if($tracking_data){
                                        foreach($tracking_data as $t){
                                            if (!empty($t->carrierWaybillNumber)) {
                                                $track_id = $t->carrierWaybillNumber;
                                            }
                                            $label_url = (isset($t->carrierWaybillURL) && !empty($t->carrierWaybillURL)) ? $t->carrierWaybillURL : 'NA';
                                        }
                                    }
                                }
                            @endphp

                            {{-- shipment detail --}}
                            <div class="create-form-section @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar') collapse @endif">
                                <h2>Shipment Details</h2>
                                <div class="info-list-inner">
                                    <div class="create-form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row attachment-sec-1">
                                                    <div class="col-md-4">
                                                        <a class="Head-track" href="javascript:void(0)"> Get Tracking Details :
                                                            <button type="button" class="btn btn-sm btn-warning"  @if($track_id) id="track-detail" data-id="{{ $track_id }}" @else disabled="true"  @endif>
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <a class="Head-track" href="#"> Tracking ID : {{ $track_id }}</a>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="dt-buttons btn-group pull-right">
                                                            @if($waybill->hasMeta('_attachment_pdf'))
                                                                @php
                                                                    $url = showPdf($waybill->id); 
                                                                @endphp
                                                                <a href="{{ asset($url) }}" target="_blank" class="btn btn-secondary buttons-excel buttons-html5 btn-primary"><span><i class="la la-download"></i> Download Label</span></a>
                                                            @else
                                                                @if($label_url != 'NA')
                                                                    <a href="{{ $label_url }}" target="_blank" class="btn btn-secondary buttons-excel buttons-html5 btn-primary"><span><i class="la la-download"></i> Download Label</span></a>
                                                                @else
                                                                    <div class="text-center mt-0 mb-0">Label not created yet</div>
                                                                @endif
                                                            @endif                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- The Modal -->
                                                <div aria-hidden="true" aria-labelledby="updater" class="modal fade" id="myModal" role="dialog" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content track-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Tracking Details</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <!-- Modal body -->
                                                            <div class="modal-body">
                                                                <h5>Tracking ID: <b>{{ $track_id }}</b></h5>
                                                                <div id="track-data"></div>
                                                                <div><button type="button" id="more-track-btn" class="btn btn-sm btn-red mt-2" @if($track_id) data-id="{{ $track_id }}" @endif><i class="fa fa-eye"> More Details</i></button></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Client Name</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->client->name }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Warehouse Name </div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_consignee_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Shipment Type</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_shipment_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Sell Rate</div>
                                                    <div class="booking-value-info">{!! ($waybill->meta->_currency)?get_currency_symbol($waybill->meta->_currency):get_currency_symbol('USD') !!}{{ (is_numeric($waybill->meta->_rate)) ? $waybill->meta->_rate : 0}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Carrier</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_carrier_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Payment Mode</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->payment_mode ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Number of Items</div>
                                                    <div class="booking-value-info">
                                                        @php $cnt = 0; @endphp
                                                        @forelse($waybill->packages as $package)
                                                            @php $cnt += $package->package_count; @endphp
                                                        @empty
                                                        @endforelse
                                                        {{-- {{ $waybill->meta->_number_of_packages??"0" }} --}}
                                                        {{ $cnt }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Weight Unit Type</div>
                                                    <div class="booking-value-info">
                                                        KGS
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Actual Weight</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_actual_weight ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Charged Weight</div>
                                                    <div class="booking-value-info">
                                                        {{ $waybill->meta->_charged_weight ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Remarks</div>
                                                    <div class="booking-value-info">{{ $waybill->meta->_remark??"N/A" }}</div>
                                                </div>
                                            </div>
                                            @php
                                            $meta = $waybill->getMetas();
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Shipment Status</div>
                                                    <div class="booking-value-info">{{ $meta['shipment_status'] ?? "N/A" }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Incident Date</div>
                                                    <div class="booking-value-info">{{ $meta['shipment_date'] ?? "N/A" }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">Claim ID</div>
                                                    <div class="booking-value-info">{{ $meta['claim_id'] ?? "N/A" }}</div>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            
                            {{-- package detail --}}
                            <div class="info-list-section">
                                <h2>Item Details</h2>
                                <div class="info-list-inner">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table id="client_user_list" class="table table-striped table-bordered table-sm">
                                                <tbody>
                                                    <tr>
                                                        <th>Item Bar Code</th>
                                                        <th>Title</th>
                                                        <th>Qty</th>
                                                        <th>Color</th>
                                                        <th>Size</th>
                                                        <th>Dimension</th>
                                                        <th>Pkg. Dimensions</th>
                                                        <th>Weight / Unit</th>
                                                        <th>Reason of Return</th>
                                                        <th>Value</th>
                                                        <th>HS Code</th>
                                                        
                                                        <th>Origin Country</th>
                                                        @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar')
                                                            <th>Rcvd Status at Return Bar™</th>
                                                            <th>Rcvd Qty at Return Bar™</th>
                                                            <th>Rcvd Date at Return Bar™</th>
                                                        @endif
                                                        <th>Estimated Value</th>
                                                        
                                                        <th>Confirm Action</th>
                                                        <th>Refunded Status</th>
                                                        <th>Action</th>
                                                        <th>Images &nbsp; &nbsp; &nbsp; </th>
                                                    </tr>
                                                    @forelse($waybill->packages as $package)
                                                        <tr>
                                                            <td>{{ $package->bar_code??"N/A" }}</td>
                                                            <td>{{ $package->title??"N/A" }}</td>
                                                            <td>{{ $package->package_count }}</td>
                                                            <td>{{ $package->color }}</td>
                                                            <td>{{ $package->size }}</td>
                                                            <td>{{ $package->dimension }}</td>
                                                            <td>
                                                                {{ $package->length }} / {{ $package->width }} / {{ $package->height }}
                                                            </td>
                                                            <td>{{ $package->weight }} / {{ $package->weight_unit_type }}</td>
                                                            <td>{{ $package->return_reason }}</td>
                                                            <td>{{ $package->cost ?? 'N/A' }}</td>
                                                            <td>
                                                                @if($package->hs_code)
                                                                    {{ $package->hs_code }}
                                                                @else
                                                                    <input type="text" class="form-control" id="hs_code" style="width: 100px;" value="">
                                                                @endif
                                                            </td>
                                                            
                                                            <td>{{ $package->country_of_origin ?? 'N/A' }}</td>
                                                            @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar')
                                                                <td>{{ $package->return_status ?? 'False' }}</td>
                                                                <td>{{ $package->hiting_count }}</td>
                                                                <td>{{ ($package->rcvd_date_at_returnbar) ? date('Y-m-d', strtotime($package->rcvd_date_at_returnbar)) : 'N/A' }}</td>
                                                            @endif
                                                            <td>
                                                                @if($package->estimated_value)
                                                                    {{ $package->estimated_value }}
                                                                @else
                                                                    <input type="text" class="form-control" id="estimated_value" value="">
                                                                @endif
                                                            </td>
                                                            
                                                            <td>
                                                                @if($package->status)
                                                                    {{ $package->status }}
                                                                @else
                                                                    <select id="status" class="form-control">
                                                                        <option value="">-- Select --</option>
                                                                        <option value="Charity">Charity</option>
                                                                        <option value="Discrepency">Discrepency</option>
                                                                        <option value="Restock">Restock</option>
                                                                        <option value="Resell">Resell</option>
                                                                        <option value="Return">Return</option>
                                                                        <option value="Redirect">Redirect</option>
                                                                        <option value="Recycle">Recycle</option>
                                                                        <option value="Other">Other</option>
                                                                        <option value="Short Shipment">Short Shipment</option>
                                                                    </select>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <select id="refund_status" class="form-control">
                                                                    <option value="">-- Select --</option>
                                                                    <option value="Yes" @if($package->refund_status == 'Yes') selected @endif>Yes</option>
                                                                    <option value="No" @if($package->refund_status == 'No') selected @endif>No</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                            	@if(empty($package->status) || empty($package->hs_code) || empty($package->estimated_value))
                                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="updatePackage({{ $package->id }});">
                                                                        <i class="la la-arrow-up"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="row">
                                                                    {{-- @php dd(json_decode($package->file_data)); @endphp --}}
                                                                    @if(!empty($package->file_data))
                                                                        @forelse(json_decode($package->file_data) as $image)
                                                                            <div class="col-md-8">
                                                                                <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                    <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="100">
                                                                                </a>
                                                                            </div>
                                                                        @empty
                                                                            <p class="col-md-6">N/A</p>
                                                                        @endforelse
                                                                    @else
                                                                        <p class="col-md-6">N/A</p>
                                                                    @endif
                                                                </div> 
                                                            </td>                      
                                                        </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8">Package not added</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>                                   
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

<!-- The Modal -->
<div aria-hidden="true" class="modal fade" id="historymyModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content track-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Status History
                </h4>
                <button class="close" data-dismiss="modal" type="button">×</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">                
                <div id="history-data"></div>
            </div>
        </div>
    </div>
</div>
@endsection
