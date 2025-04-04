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
    .attachment-sec-1{ border: 1px solid #f1ecec; border-radius: 4px;     width: 100%; margin: 0px auto 22px;}
    .Card-detail .row p{ font-size: 14px; font-weight: 300; }
    .Card-detail .row label{ color: ##2B335E; font-weight: 500; }
    .Head-track{color: #000;}
    .Head-secondary{font-size: 13px;}
    .info-list-section .row label{    font-size: 1rem; }
    .info-list-section .row p{     color: #b9b2b2; font-weight: 500;}
    .info-list-section h2 {font-size: 16px; color: #b51f37; font-weight: bold; }

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
                        <h4 class="card-title">
                            <a href="{{ redirect()->back()->getTargetUrl() }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                            {{-- <a href="{{ route('all.return.orders') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a> --}}
                        </h4>
                    </div>
                    <div class="card-content">                        
                        <div class="card-body">
                        	@if($waybill)
	                           	<div class="info-list-section">
                                    <nav>
                                        <h2 class="navbar-text">Customer Details</h2>
                                        <a class="Head-secondary pull-right" href="javascript:void(0);">
                                            Order Number / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}
                                        </a>
                                    </nav>
	                              	<div class="info-list-inner">
	                                 	<div class="row">
	                                    	<div class="col-md-6">
	                                       		<div class="info-list-box">
	                                          		<div class="booking-title-info">Name</div>
	                                          		<div class="booking-value-info">{{ $waybill->meta->_customer_name }}</div>
	                                       		</div>
	                                    	</div>
	                                    	<div class="col-md-6">
	                                       		<div class="info-list-box">
	                                          		<div class="booking-title-info">Email</div>
	                                          		<div class="booking-value-info">{{ $waybill->meta->_customer_email }}</div>
	                                       		</div>
	                                    	</div>
		                                    <div class="col-md-6">
		                                       	<div class="info-list-box">
		                                          	<div class="booking-title-info">Address</div>
		                                          	<div class="booking-value-info">{{ $waybill->meta->_customer_address }}</div>
		                                       	</div>
		                                    </div>
		                                    <div class="col-md-6">
		                                       	<div class="info-list-box">
		                                          	<div class="booking-title-info">Country</div>
		                                          	<div class="booking-value-info">{{ get_country_name_by_id($waybill->meta->_customer_country) }}</div>
		                                       	</div>
		                                    </div>
		                                    <div class="col-md-6">
		                                       	<div class="info-list-box">
		                                          	<div class="booking-title-info">State</div>
		                                          	<div class="booking-value-info">{{ $waybill->meta->_customer_state }}</div>
		                                       	</div>
		                                    </div>
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">City</div>
                                                    <div class="booking-value-info">{{ $waybill->meta->_customer_city }}</div>
                                                </div>
                                            </div>
		                                    <div class="col-md-6">
		                                       <div class="info-list-box">
		                                          	<div class="booking-title-info">Postal code</div>
		                                          	<div class="booking-value-info">{{ $waybill->meta->_customer_pincode }}</div>
		                                       </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                       <div class="info-list-box">
		                                          	<div class="booking-title-info">Mobile</div>
		                                          	<div class="booking-value-info">{{ $waybill->meta->_customer_phone }}</div>
		                                       </div>
		                                    </div>
	                                 	</div>
	                              	</div>
	                           	</div>
	                           	<div class="info-list-section">
	                              	<h2>Shipment Details</h2>
                                    <div class="info-list-inner">
    	                              	<div class="row">
                                            <div class="col-md-6">
                                                <div class="info-list-box">
                                                    <div class="booking-title-info">RG Refrence Number</div>
                                                    <div class="booking-value-info">{{ $waybill->id??"N/A" }}</div>
                                                </div>
                                            </div>
    	                                 	<div class="col-md-6">
    	                                    	<div class="info-list-box">
    	                                       		<div class="booking-title-info">Order Number</div>
    	                                       		<div class="booking-value-info">{{ $waybill->way_bill_number??"N/A" }}</div>
    	                                    	</div>
    	                                 	</div>
    	                                 	<div class="col-md-6">
    		                                    <div class="info-list-box">
    		                                       	<div class="booking-title-info">Number Of Packages </div>
    		                                       	<div class="booking-value-info">
    		                                       		@php $cnt = 0; @endphp
    		                                       		@forelse($waybill->packages as $package)                                                            
    		                                       		    @php $cnt += $package->package_count; @endphp
    		                                       		@empty
    		                                       		@endforelse
    		                                       		{{ $cnt }}
    		                                       	</div>
    		                                    </div>
    	                                 	</div>
    	                                 	<div class="col-md-6">
    		                                    <div class="info-list-box">
    		                                       	<div class="booking-title-info">RMA Number</div>
    		                                       	<div class="booking-value-info">{{ $waybill->meta->_rma_number??"N/A" }}</div>
    		                                    </div>
    	                                 	</div>
    	                                 	<div class="col-md-6">
    		                                    <div class="info-list-box">
    		                                       	<div class="booking-title-info">Remarks</div>
    		                                       	<div class="booking-value-info">{{ $waybill->meta->_remark??"N/A" }}</div>
    		                                    </div>
    	                                 	</div>
    	                                 	<div class="col-md-6">
    		                                    <div class="info-list-box">
    		                                       	<div class="booking-title-info">Return Option</div>
    		                                       	<div class="booking-value-info">{{ str_replace('_', ' ', $waybill->meta->_drop_off) ?? "N/A" }}</div>
    		                                    </div>
    	                                 	</div>
    	                              	</div>
                                    </div>
	                           	</div>	                           	
	                           	<div class="info-list-section">
	                           	    <h2>Item Details</h2>
                                    <div class="info-list-inner table-responsive">
    	                           	    <div class="row">
    	                           	        <div class="col-md-12">
    	                           	            <table id="client_user_list" class="table table-striped table-bordered table-sm">
    	                           	                <tr>
    	                           	                    <th>Item Bar Code</th>
    	                           	                    <th>Title</th>
    	                           	                    <th>Qty</th>
    	                           	                    <th>Color</th>
    	                           	                    <th>Size</th>
    	                           	                    <th>Dimension Unit</th>
                                                        <th>Pkg. Dimensions</th>    	                           	                    
    	                           	                    <th>Weight / Unit</th>
                                                        <th>Reason of Return</th>
    	                           	                    <th> Images &nbsp; &nbsp; &nbsp; </th>
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
    	                           	                        <td>
    	                           	                            <div class="row">
    	                           	                                {{-- @php dd(json_decode($package->file_data)); @endphp --}}
    	                           	                                @if(!empty($package->file_data))
    	                           	                                    @forelse(json_decode($package->file_data) as $image)
    	                           	                                        <div class="col-md-8">
    	                           	                                            <a href="{{ asset('public/'.$image) }}" target="_blank">
    	                           	                                                <img src="{{ asset('public/'.$image) }}" class="img-responsive img-thumbnail" width="168" height="100">
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
    	                           	            </table>
    	                           	        </div>
    	                           	    </div>
                                    </div>
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
