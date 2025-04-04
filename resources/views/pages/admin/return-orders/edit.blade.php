@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style>
    .list-your-service-form-box .col-md-12{padding: 0px !important;}
    #create-waybill h5{font-size: 16px; padding: 2px 0;}
    .card-body .bg-light {background: #fef0f2 !important; border-bottom: 1px solid #ffdee3; border-radius: 3px; }
    .booking-info-box .info-list-section {padding: 20px; border: 1px solid #f4f5fa; margin-top: 2px; }
	.Information .info-list-section p {color: #b9b2b2; font-weight: 500;font-size: 13px; }
    .Information label {
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 0;
    }

    .create-form-section {
        border: 1px solid #f4f5fa;
        border-radius: 5px;
        margin-bottom: 20px;
        border-radius: 12px;
        background: #fff;
        padding:0px 18px 20px; 
    }

    .create-form-section h2 {
        font-size: 16px;
        font-weight: bold;
        padding: 10px 0px;
    }
    .create-form-body {
        padding: 10px;
    }

    .create-form-body .row{margin-left: -5px; margin-right: -5px;}
    .create-form-body .row .col-md-1,
    .create-form-body .row .col-md-2,
    .create-form-body .row .col-md-3,
    .create-form-body .row .col-md-4,
    .create-form-body .row .col-md-5,
    .create-form-body .row .col-md-6,
    .create-form-body .row .col-md-7,
    .create-form-body .row .col-md-8,
    .create-form-body .row .col-md-9,
    .create-form-body .row .col-md-10,
    .create-form-body .row .col-md-11,
    .create-form-body .row .col-md-12,

    .create-form-body .row .col-lg-1,
    .create-form-body .row .col-lg-2,
    .create-form-body .row .col-lg-3,
    .create-form-body .row .col-lg-4,
    .create-form-body .row .col-lg-5,
    .create-form-body .row .col-lg-6,
    .create-form-body .row .col-lg-7,
    .create-form-body .row .col-lg-8,
    .create-form-body .row .col-lg-9,
    .create-form-body .row .col-lg-10,
    .create-form-body .row .col-lg-11,
    .create-form-body .row .col-lg-12{padding-left:5px; padding-right:5px;}
    .navbar-light .navbar-text {
        color: #0e1035;
        font-weight: bold;
        font-size: 13px;
    }

    .non-editable {
        background: #fff;
        padding: 10px;
        border-radius: 2px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/update-waywill.js') }}"></script>
<script>	
    $(document).ready(function(){        
        //--------------------------------------------------------------------------------------------        
        $(document).on('change','#client_shipment_list',function(){
            if($(this).val()){
                let rate = $('option:selected', this).attr('rate');
                let carrier = $('option:selected', this).attr('carrier');
                let rate_div = `<div class="form-group">
                                <label for="">Rate</label>
                                <span class="form-control">${rate}</span>
                                <input type="hidden" name="rate" value="${rate}"/>
                            </div>`;
                let carrier_div = `<div class="form-group">
                                <label for="">Carrier</label>
                                <span class="form-control">${carrier}</span>
                            </div>`;
                $(".rate-id").html(rate_div);
                $(".carrier-div").html(carrier_div);
            }else{
                $(".rate-id").html('');
                $(".carrier-div").html('');
            }
            
        });
        //--------------------------------------------------------------------------------------------        
        $('body').on('keyup change','input,select',function(){
            let text = $(this).val();
            if(text.length>0){
                $(this).next('small').text('');
            }
        });
        
        function setRate(){
        	// Run code
        	let client_id = '{{ $waybill->client_id }}';
        	let waywill_id = '{{ $waybill->id }}';

        	$.ajax({
        	    type:'get',
        	    url :"{{ route('admin.shipment-list-by-client-id') }}",
        	    data : {id:client_id},
        	    dataType : 'json',
        	    success : function(data){
        	        $("#client_warehouse_list").replaceWith(data.warehouse);        	        
        	        $("#client_shipment_list").replaceWith(data.shipment);
        	        callRate();
        	    }
        	});

        	$.ajax({
                type:'get',
                url :"{{ route('admin.client-warehouse') }}",
                data : {client_id:client_id, waywill_id:waywill_id},
                dataType : 'json',
                success : function(data){
                    $("#client_warehouse_list").replaceWith(data.warehouse);
                }
            });
        }

        setRate();

        function callRate(){
        	let rate = $('#client_shipment_list option:selected').attr('rate');
        	let carrier = $('#client_shipment_list option:selected').attr('carrier');
        	let rate_div = `<div class="form-group">
        	                <label for="">Rate</label>
        	                <span class="form-control">${rate}</span>
        	                <input type="hidden" name="rate" value="${rate}"/>
        	            </div>`;
        	let carrier_div = `<div class="form-group">
        	                <label for="">Carrier</label>
        	                <span class="form-control">${carrier}</span>
        	            </div>`;
        	$(".rate-id").html(rate_div);
        	$(".carrier-div").html(carrier_div);
        }
    });
</script>
@endpush

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active">Edit New Return Orders</li>
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
                <div class="booking-info-box">
                    <div class="card-header">
                        @php 
                            $route_name = ($waybill->return_by == 'EQTOR_ADMIN')? 'reverse-logistic.new': 'all.return.orders';  
                        @endphp
                        <h4 class="card-title">
                            <a href="{{ route($route_name) }}" class="btn btn-outline-primary btn-sm">
                                <i class="la la-arrow-left"></i> Back
                            </a>
                        </h4>
                    </div>                    
                    <div class="card-content">
                    	<div class="card-body">
                            @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar')
                            <form method="post" id="create-waybill" action="{{ route('return-bar.order') }}">
                            @else
                            <form method="post" id="create-waybill" action="{{ route('admin-waybills.update') }}">
                            @endif                            
                                <input type="hidden" name="way_bill_id" value="{{ $waybill->id }}">
                                <input type="hidden" name="way_bill_number" value="{{ $waybill->way_bill_number }}">
                                <input type="hidden" id="return_url" value="{{ route($route_name) }}">
                                <input type="hidden" name="customer_code" value="00000">
                                {{-- customer --}}
                                <input type="hidden" name="customer_name" value="{{ $waybill->meta->_customer_name }}">
                                <input type="hidden" name="customer_email" value="{{ $waybill->meta->_customer_email }}">
                                <input type="hidden" name="customer_address" value="{{ $waybill->meta->_customer_address }}">
                                <input type="hidden" name="customer_country" value="{{ $waybill->meta->_customer_country }}">
                                <input type="hidden" name="customer_state" value="{{ $waybill->meta->_customer_state }}">
                                <input type="hidden" name="customer_city" value="{{ $waybill->meta->_customer_city }}">
                                <input type="hidden" name="customer_pincode" value="{{ $waybill->meta->_customer_pincode }}">
                                <input type="hidden" name="customer_phone" value="{{ $waybill->meta->_customer_phone }}">
                                {{-- <input type="hidden" name="service_code" value="PARTLOAD"> --}}
                                <input type="hidden" name="service_code" value="ECOMDOCUMENT">
                                <input type="hidden" name="client_code" value="REVERSEGEAR">
                                
                                {{-- customer detail --}}
                    			<div class="create-form-section">
    	                        	<nav class="">
    									<h2 class="navbar-text" href="#">Customer Details</h2>
    									<a class="Head-secondary pull-right" href="javascript:void(0);">
                                            Order Number / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}
                                        </a>
    								</nav>
                                    <div class="info-list-inner">
        								<div class="create-form-body">
        	                        		<div class="row">
        	                        			<div class="col-lg-3">
        											<label>Client Name</label>
        											<p class="non-editable">{{ $waybill->client->name }}</p>
        										</div>
        										<div class="col-lg-3">
                                                    <label>Name</label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_name }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Email</label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_email }}</p>
                                                </div>                                                
                                                <div class="col-lg-3">
                                                    <label>Mobile </label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_phone }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> Address</label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_address }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> Country </label>
                                                    <p class="non-editable">{{ get_country_name_by_id($waybill->meta->_customer_country) }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> State </label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_state }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> City </label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_city }}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label> Pincode </label>
                                                    <p class="non-editable">{{ $waybill->meta->_customer_pincode }}</p>
                                                </div>
        									</div>
        								</div>
                                    </div>
                                </div>

                                {{-- shipment detail --}}
    							<div class="create-form-section Shipment mt-1 @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar') collapse @endif">
                                    <nav class=>
    									<h2 class="navbar-text">Shipment Details</h2>
    								</nav>
                                    <div class="info-list-inner">
    									<div class="create-form-body">                                           
    		                        		<div class="row">
    		                        			<div class="col-lg-3">
    												<label>Way Bill Number</label>
    												<p class="non-editable">{{ $waybill->way_bill_number }}</p>
    											</div>
    											<div class="col-lg-3">
    												<label>Payment Mode *</label>
    												<p>
    													<select class="form-control" name="payment_mode">
                                                            <option value="">Select</option>
                                                            <option value="FOD">FOD</option>
                                                            <option value="PAID">PAID</option>
                                                            <option value="TBB">TBB</option>
                                                            <option value="FOC">FOC</option>  
                                                        </select>
    												</p>
    											</div>
    											<div class="col-lg-3">
    												<label> Cash On Pickup</label>
    												<p>
    													<select class="form-control" name="cash_on_pickup">
                                                            <option value="">Select</option>
                                                            <option value="Cash">Cash</option>
                                                            <option value="Cheque">Cheque</option>
                                                        </select>
    												</p>
    											</div>
    											<div class="col-lg-3">
    												<label>Amount </label>
    												<p><input type="text" class="form-control" name="amount" placeholder="Amount" value=""></p>
    											</div>
    										</div>
    										<div class="row mt-1">
    											<div class="col-lg-3">
    												<label> Number Of Packages</label>
    												<p class="non-editable">
                                                        @php $cnt = $wt = 0; @endphp
                                                        @forelse($waybill->packages as $package)
                                                            @php
                                                                $cnt += $package->package_count;
                                                                $wt += $package->weight;
                                                            @endphp
                                                        @empty
                                                        @endforelse
                                                        {{ $cnt }}
                                                        <input type="hidden" name="number_of_packages" value="{{ $cnt }}">
                                                    </p>
    											</div>
    											<div class="col-lg-3">
    												<label>Weight Unit Type</label>
    												<p>
    													<select class="form-control" name="unit_type">
                                                            <option value="GRAM">GRAM</option>
                                                            <option value="KILOGRAM">KILOGRAM</option>
                                                            <option value="TONNE">TONNE</option>
                                                            <option value="POUND" selected>POUND</option>
                                                        </select>
                                                    </p>
    											</div>
    											<div class="col-lg-3">
    												<label> Actual Weight *</label>
    												<p>
    													<input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="{{ $wt }}">
    												</p>
    											</div>
    											<div class="col-lg-3">
    												<label>Charged Weight *</label>
    												<p>
    													<input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="">
    												</p>
    											</div>
    										</div>
    										<div class="row mt-1">    											
    											<div class="col-lg-6">
    												<label>Description</label>
    												<p>
    													<textarea name="remark" placeholder="Enter Description" class="form-control" rows="2"></textarea>
    												</p>
    											</div>
    											<div class="col-lg-3">
    												<label>Warehouse *</label>
    												<p>
    													<select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                            <option value="">Select</option>
                                                                @forelse($warehouse_list as $warehouse)
                                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                                @empty
                                                                    <option value="">Warehouse not added yet</option>
                                                                @endforelse
                                                        </select>
                                                    </p>
    											</div>
    											<div class="col-lg-3">
    												<label>Shipment *</label>
    												<p>
    													<select name="shipment_id" id="client_shipment_list" class="form-control">
                                                            <option value="">Select</option>
                                                                @forelse($shipment_list as $shipment)
                                                                    <option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}">{{ $shipment->shipment_name }}</option>
                                                                @empty
                                                                    <option value="">Shipment not added yet</option>
                                                                @endforelse
                                                        </select>
                                                    </p>
    											</div>
                                                <div class="col-lg-3 rate-id"></div>
                                                <div class="col-lg-3 carrier-div"></div>    											
    										</div>
    									</div>
                                    </div>
								</div>
                                
                                {{-- pakage detail --}}
    							<div class="create-form-section Packages mt-1">
                            		<nav class="">
                            			<h2 class="navbar-text">Packages Details</h2>
                            		</nav>
                                    <div class="info-list-inner">
    								    <div class="create-form-body">
    		                        		<div class="create-form-list">
                                                @php
                                                    // dd($packge_list);
                                                @endphp
                                                @forelse($packge_list as $packge)
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Item Bar Code</label>
                                                                <input type="hidden" value="{{ $packge->id }}" name="package_arr[]">
                                                                <input type="hidden" class="form-control" name="charged__weight[]" value="1">
                                                                <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">

                                                                <input type="text" class="form-control" name="bar_code[]" placeholder="Enter Item Bar Code" value="{{ $packge->bar_code }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Description</label>
                                                                <input type="text" class="form-control" name="title[]" placeholder="Enter Product Description" value="{{ $packge->title }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Quantity</label>
                                                                <input type="text" class="form-control" name="package_count[]" placeholder="Quantity" value="{{ $packge->package_count }}" autocomplete="off"> 
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Color</label>
                                                                <input type="text" class="form-control" name="color[]" placeholder="Color" value="{{ $packge->color }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Size</label>
                                                                <input type="text" class="form-control" name="size[]" placeholder="Size" value="{{ $packge->size }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Dimension</label>
                                                                <select class="form-control valid" name="dimension[]" aria-invalid="false">
                                                                    <option value="CM" @if($packge->dimension == 'CM') selected @endif>CM</option>
                                                                    <option value="IN" @if($packge->dimension == 'IN') selected @endif>IN</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Lenght</label>
                                                                <input type="text" class="form-control" name="length[]" placeholder="Lenght" value="{{ $packge->length }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Width</label>
                                                                <input type="text" class="form-control" name="width[]" placeholder="Width" value="{{ $packge->width }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Height</label>
                                                                 <input type="text" class="form-control" name="height[]" placeholder="Height" value="{{ $packge->height }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Weight Unit</label>
                                                                <select class="form-control valid" name="weight_unit_type[]" aria-invalid="false">
                                                                    <option value="LBS" @if($packge->weight_unit_type == 'LBS') selected @endif>LBS</option>
                                                                    <option value="KGS" @if($packge->weight_unit_type == 'KGS') selected @endif>KGS</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Weight</label>
                                                                <input type="text" class="form-control" name="weight[]" placeholder="Weight" value="{{ $packge->weight }}">
                                                            </div>
                                                        </div>                                                
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Images &nbsp; &nbsp; &nbsp;</label>
                                                                <input type="hidden" name="product_image[]" value="{{ $packge->file_data }}">
                                                                <input type="hidden" name="image_url[]" value="{{ $packge->image_url }}">
                                                                @if(!empty($packge->file_data))
                                                                    @forelse(json_decode($packge->file_data) as $image)
                                                                        <div class="package-media col-lg-5">
                                                                            <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="100">
                                                                            </a>
                                                                        </div>
                                                                    @empty
                                                                        <p>N/A</p>
                                                                    @endforelse
                                                                @else
                                                                    <p>N/A</p>
                                                                @endif                                                            
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="row">
                                                        <div class="Package-mss">Package not created yet</div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<div class="create-form-btn">
                                    <button type="submit" class="btn-blue mt-1 save-waybill">Submit</button>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                            </form>
                    	</div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection