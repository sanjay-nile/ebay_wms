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
        bottom: 0;    margin: 0px 0px 7px 10px;}

    .info-list-section .col-md-3, .col-md-9{margin-top: 14px;}
    .card-body .bg-light{background:#fef0f2 !important;     border-bottom: 1px solid #ffdee3; border-radius: 3px;}
    .card-body form{ padding: 0px 16px; }
    .card-body .navbar .navbar-brand{    font-weight: 600; font-size: 15px; color: #000;}
    .attachment-sec{ background: #f9f7f7; border: 1px solid #f1ecec; border-radius: 4px;}
    .attachment-sec-1{ border: 1px solid #f1ecec; border-radius: 4px;}
    .Card-detail .row p{ font-size: 14px; font-weight: 300; }
    .Card-detail .row label{ color: ##2B335E; font-weight: 500; }
    .Head-track{color: #000;}
    .Head-secondary{font-size: 13px;}
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
    });

    function upload_img(id){
        var formData = new FormData();
        let TotalImages = $('#image-upload-'+id)[0].files.length;  //Total Images
        let images = $('#image-upload-'+id)[0];
        for (let i = 0; i < TotalImages; i++) {
            formData.append('images[]', images.files[i]);
        }
        formData.append('TotalImages', TotalImages);
        formData.append('_token', '{{csrf_token()}}');
        formData.append('package_id', id);
        var status = $( "#status_"+id+" option:selected" ).val();
        var custom_price = $( "#custom_price_"+id).val();
        if(status){
            formData.append('status', status);
        }
        if(custom_price){
            formData.append('custom_price', custom_price);
        }
        $.ajax({
            url: "{{route('package-image-upload')}}",
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
    }

    function save_pallet(id){
        var p_id = $('#pallet_id').val();
        if(p_id){
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            formData.append('pallet_id', p_id);
            formData.append('waywill_id', id);
            $.ajax({
                url: "{{route('save-pallet-id')}}",
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
        } else{
            alert('Enter the Pallet Id.');
        }
    }

    function remove_image(img_url, id){
        if(id){
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            formData.append('img_url', img_url);
            formData.append('package_id', id);
            $.ajax({
                url: "{{route('remove-image')}}",
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
        } else{
            alert('Order Id Not found.');
        }
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
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">
                            <i class="la la-dashboard"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active">Reverse Order Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-md-12">
                @include('includes/admin/notify')
            </div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ ($waybill && $waybill->status=='Pending')? route('new-reverse-logistic') : route('customer-reverse-logistic') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                        </h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            @if($waybill)
                                <div class="Information">
                                    <nav class="navbar navbar-light bg-light">
                                        <a class="navbar-brand" href="#">Customer Details</a>
                                        <a class="Head-secondary pull-right" href="#">Order ID / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}</a>
                                    </nav>
                                    <div class="Card-detail">
                                        <div class="row mt-2">
                                            <div class="col-lg-3">
                                                <label>Name</label>
                                                <p>{{ $waybill->meta->_customer_name }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Email</label>
                                                <p>{{ $waybill->meta->_customer_email }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label> Address</label>
                                                <p>{{ $waybill->meta->_customer_address }}, {{ get_country_name_by_id($waybill->meta->_customer_country) }} - {{ $waybill->meta->_customer_state }}-{{ $waybill->meta->_customer_pincode }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Mobile </label>
                                                <p>{{ $waybill->meta->_customer_phone }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="Shipment mt-4">
                                    @php
                                        $tracking_detail = ($waybill->meta->_order_tracking_id)?? NULL; 
                                        $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                        $new_array = [];
                                        $track_id = 'Not Generated Yet';
                                        $label_url = 'NA';
                                        if($tracking_data){
                                            foreach($tracking_data as $t){
                                                $d = date('Y-m-d', strtotime($t->modifiedOn));
                                                $track_id = $t->carrierWaybillNumber;
                                                $label_url = (isset($t->carrierWaybillURL) && !empty($t->carrierWaybillURL)) ? $t->carrierWaybillURL : 'NA';
                                                if (!isset($new_array[$d])) {
                                                    $new_array[$d] = [$t];
                                                } else{
                                                    array_push($new_array[$d], $t);
                                                }
                                            }
                                        }
                                    @endphp
                                    <nav class="navbar navbar-light bg-light">
                                        <a class="navbar-brand" href="#">Shipment Details</a>
                                    </nav>
                                    <div class="row mt-1 attachment-sec-1">
                                        <div class="col-md-4 mt-1">
                                            <a class="Head-track" href="javascript:void(0)"> Get Tracking Details :
                                                <button type="button" class="btn btn-sm btn-warning"  @if($waybill->meta->_order_current_status) id="track-detail" data-id="{{ $track_id }}" @else disabled="true"  @endif>
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </a>
                                        </div>
                                        <div class="col-md-4 mt-1">
                                            <a class="Head-track" href="#"> Tracking ID : {{ $track_id }}</a>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="dt-buttons btn-group pull-right">                                                   
                                                @if($label_url!='NA')
                                                    <a href="{{ $label_url }}" target="_blank" class="btn btn-secondary buttons-excel buttons-html5 btn-primary"><span><i class="la la-download"></i> Download Label</span></a>
                                                @else
                                                    <p class="text-center">Label not created yet</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="Card-detail">
                                        <div class="row mt-2">
                                            <div class="col-lg-3">
                                                <label>Client Name</label>
                                                <p>{{ $waybill->client->name }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Warehouse Name</label>
                                                <p>{{ $waybill->meta->_consignee_name }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label> Shipment Type</label>
                                                <p>{{ $waybill->meta->_shipment_name }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Sell Rate </label>
                                                <p>{!! ($waybill->meta->_currency)?get_currency_symbol($waybill->meta->_currency):get_currency_symbol('USD') !!}{{ $waybill->meta->_rate}}</p>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-3">
                                                <label> Carrier</label>
                                                <p>{{ $waybill->meta->_carrier_name }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Payment Mode</label>
                                                <p>{{ $waybill->payment_mode }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label> Number Of Packages</label>
                                                <p>{{ $waybill->meta->_number_of_packages }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Weight Unit Type</label>
                                                <p>{{ $waybill->meta->_weight_unit_type }}</p>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-3">
                                                <label>Actual Weight</label>
                                                <p>{{ $waybill->meta->_actual_weight }}</p>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Charged Weight</label>
                                                <p>{{ $waybill->meta->_charged_weight }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="Packages mt-4">
                                    <nav class="navbar navbar-light bg-light">
                                        <a class="navbar-brand" href="#">Packages</a>
                                    </nav>
                                    @forelse($waybill->packages as $package)
                                        <div class="Card-detail">
                                            <div class="row mt-2">
                                                <div class="col-lg-3">
                                                    <label>Bar Code</label>
                                                    <p>{{ $package->bar_code??"N/A" }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Title</label>
                                                    <p>{{ $package->title??"N/A" }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> Length / Width / Height (In)</label>
                                                    <p>{{ $package->length }} / {{ $package->width }} / {{ $package->height }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Weight / Charged Weight (kg)</label>
                                                    <p>{{ $package->weight }} / {{ $package->charged_weight }}</p>
                                                </div>
                                            </div>  
                                            <div class="row attachment-sec mt-1">
                                                <div class="col-md-4">
                                                    <div class="info-list-box info-mt">
                                                        <label>Image</label>
                                                        <input class="form-control" id="image-upload-{{ $package->id }}" type="file" accept="image/*" multiple="multiple">
                                                    </div>                                   
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-list-box info-mt">
                                                        <label>Product Condition</label>                                                        
                                                        <select id="status_{{ $package->id }}" class="form-control">
                                                            <option value="">-- Select --</option>
                                                            <option value="Defected">Defected</option>
                                                            <option value="Ok">Product is Ok</option>
                                                        </select>
                                                    </div>                                   
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-list-box info-mt">
                                                        <label>Custom Price</label>                                                        
                                                        <input type="text" class="form-control" id="custom_price_{{ $package->id }}" placeholder="Enter Custom Price" value="{{ $package->custom_price }}">
                                                    </div> 
                                                </div>
                                                <div class="col-md-2">
                                                    <button class="btn btn-dlt" type="button" onclick="upload_img({{ $package->id }});">Upload
                                                    </button>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="info-list-box Img-attachment">
                                                        <ul id="product-gallery-{{ $package->id }}">
                                                            @if($package->hasMeta('_package_images'))
                                                                @forelse($package->meta->_package_images as $img)
                                                                    <li>
                                                                        <img src="{{ asset($img) }}" width="75" height="75">
                                                                        <button class="btn-dlt" type="button" onclick="remove_image('{{ $img }}', {{ $package->id }});">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </li>
                                                                @empty
                                                                @endforelse
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <h2 class="text-center mt-2">Record not found</h2>
                                    @endforelse
                                </div>
                            @endif                        
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection
