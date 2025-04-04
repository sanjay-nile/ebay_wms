@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@php
// dd($waybill);
@endphp

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<style>
    .list-your-service-form-box .col-md-12{padding: 0px !important;}
    #create-waybill h5{font-size: 16px; padding: 2px 0;}
</style>
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/update-waywill.js') }}"></script>
<script>	
    $(document).ready(function(){
        $('#delivery_date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $(".nav-tabs a").click(function(e){
            e.preventDefault();
            $(this).tab('show');
        });

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
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
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
                <div class="card booking-info-box">
                    <div class="card-header">
                        @php 
                            $route_name = ($waybill->return_by == 'EQTOR_ADMIN')? 'reverse-logistic.new': 'new-reverse-logistic';  
                        @endphp
                        <h4 class="card-title">
                            <a href="{{ route($route_name) }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                        </h4>
                        <!-- <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div> -->
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-list nav-underline">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#about-customer" aria-controls="homeIcon11" aria-expanded="true">Customer</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#Shipment" aria-controls="aboutIcon11" aria-expanded="false">Shipment</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#PackageDetails" aria-controls="aboutIcon11" aria-expanded="true">Package Details</a>
                                </li>
                            </ul>
                            <form method="post" id="create-waybill" action="{{ route('admin-waybills.update') }}">
                                <input type="hidden" name="way_bill_id" value="{{ $waybill->id }}">
                                <input type="hidden" name="way_bill_number" value="{{ $waybill->way_bill_number }}">
                                <input type="hidden" id="return_url" value="{{ route($route_name) }}">
	                            <div class="tab-content">
	                                <div role="tabpanel" class="tab-pane  active" id="about-customer" aria-labelledby="about-customer" aria-expanded="true">
	                                    <div class="info-list-section">
	                                        <div class="row">
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Client Name</label>
	                                                    <input type="text" class="form-control" disabled="true" value="{{ $waybill->client->name}}">
	                                                </div>
	                                            </div>

	                                            <input type="hidden" name="customer_code" value="00000">
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Name</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_name }}">
	                                                    <input type="hidden" name="customer_name" value="{{ $waybill->meta->_customer_name }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Email</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_email }}">
	                                                    <input type="hidden" name="customer_email" value="{{ $waybill->meta->_customer_email }}">
	                                                </div>
	                                            </div>
	                                            
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Address</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_address }}">
	                                                    <input type="hidden" name="customer_address" value="{{ $waybill->meta->_customer_address }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Country</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_country }}">
	                                                    <input type="hidden" name="customer_country" value="{{ $waybill->meta->_customer_country }}">
	                                                    
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">State</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_state }}">
	                                                    <input type="hidden" name="customer_state" value="{{ $waybill->meta->_customer_state }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">City</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_city }}">
	                                                    <input type="hidden" name="customer_city" value="{{ $waybill->meta->_customer_city }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Pincode</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_pincode }}">
	                                                    <input type="hidden" name="customer_pincode" value="{{ $waybill->meta->_customer_pincode }}">
	                                                </div>
	                                            </div>
	                                            
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Phone</label>
	                                                    <input type="text" class="form-control" disabled="" value="{{ $waybill->meta->_customer_phone }}">
	                                                    <input type="hidden" name="customer_phone" value="{{ $waybill->meta->_customer_phone }}">
	                                                </div>
	                                            </div>
	                                            
	                                        </div>
	                                    </div>
	                                </div> {{-- end about-customer --}}
	                                <div role="tabpanel" class="tab-pane fade" id="Shipment" aria-labelledby="about-customer" aria-expanded="true">
	                                    <div class="info-list-section">
	                                        <div class="row">
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Way Bill Number</label>
	                                                   <input type="text" class="form-control" disabled="" value="{{ $waybill->way_bill_number }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Payment Mode <span class="text-danger">*</span></label>
	                                                    <select class="form-control" name="payment_mode">
	                                                        <option value="">Select</option>
	                                                        <option value="FOD">FOD</option>
	                                                        <option value="PAID">PAID</option>
	                                                        <option value="TBB">TBB</option>
	                                                        <option value="FOC">FOC</option>  
	                                                    </select>
	                                                </div>
	                                            </div>
	                                            {{-- <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Service Code</label> --}}
	                                                    <input type="hidden" name="service_code" value="ECOMDOCUMENT">
	                                               {{--  </div>
	                                            </div> --}}
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Cash On Pickup</label>
	                                                    <select class="form-control" name="cash_on_pickup">
	                                                        <option value="">Select</option>
	                                                        <option value="Cash">Cash</option>
	                                                        <option value="Cheque">Cheque</option>
	                                                    </select>
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Amount</label>
	                                                    <input type="text" class="form-control" name="amount" placeholder="Amount" value="">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Number Of Packages</label>
	                                                   <input type="text" class="form-control number_of_packages" disabled="" value="{{ $waybill->meta->_number_of_packages }}">
	                                                   <input type="hidden" name="number_of_packages" value="{{ $waybill->meta->_number_of_packages }}">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Weight Unit Type</label>
	                                                    <select class="form-control" name="weight_unit_type">
	                                                        <option value="GRAM">GRAM</option>
	                                                        <option value="KILOGRAM" selected>KILOGRAM</option>
	                                                        <option value="TONNE">TONNE</option>
	                                                        <option value="POUND">POUND</option>
	                                                    </select>                                                           
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Actual Weight <span class="text-danger">*</span></label>
	                                                    <input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Charged Weight <span class="text-danger">*</span></label>
	                                                    <input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="">
	                                                </div>
	                                            </div>
	                                            <div class="col-md-6">
	                                                <div class="form-group">
	                                                    <label for="">Description</label>
	                                                    <textarea name="description" placeholder="Enter Description" class="form-control" rows="1"></textarea>
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Warehouse <span class="text-danger">*</span></label>
	                                                    <select name="warehouse" id="client_warehouse_list" class="form-control">
	                                                        <option value="">Select</option>
	                                                            @forelse($warehouse_list as $warehouse)
	                                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
	                                                            @empty
	                                                                <option value="">Warehouse not added yet</option>
	                                                            @endforelse
	                                                    </select>
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3">
	                                                <div class="form-group">
	                                                    <label for="">Shipment <span class="text-danger">*</span></label>
	                                                    <select name="shipment" id="client_shipment_list" class="form-control">
	                                                        <option value="">Select</option>
	                                                            @forelse($shipment_list as $shipment)
	                                                                <option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}">{{ $shipment->shipment_name }}</option>
	                                                            @empty
	                                                                <option value="">Shipment not added yet</option>
	                                                            @endforelse
	                                                    </select>
	                                                </div>
	                                            </div>
	                                            <div class="col-md-3 rate-id">
	                                                
	                                            </div>
	                                            <div class="col-md-3 carrier-div">
	                                                
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div> {{-- end Shipment --}}
	                                <div role="tabpanel" class="tab-pane fade" id="PackageDetails" aria-labelledby="about-customer" aria-expanded="true">
	                                    <div class="info-list-section">
	                                        <div class="row">
	                                            <div class="col-md-12">
	                                                <div class="table-responsive">
	                                                    <table class="table table-bordered">
	                                                         <thead>
	                                                            <tr>
	                                                                <th>Bar Code</th>
	                                                                <th>Title</th>
	                                                                <th>Package Count</th>
	                                                                <th>Length (In)</th>
	                                                                <th>Width (In)</th>
	                                                                <th>Height (In)</th>
	                                                                <th>Weight (Kg)</th>
	                                                                <th>Charged Weight (Kg)</th>
                                                                    <th>Custom Price</th>
	                                                            </tr>
	                                                        </thead>
	                                                        <tbody>
	                                                            @forelse($packge_list as $packge)
	                                                            <tr class="add-1">
	                                                                <td>
	                                                                    <input type="text" class="form-control"  value="{{ $packge->bar_code }}" disabled="">
	                                                                    <input type="hidden" value="{{ $packge->id }}" name="package_arr[]">
	                                                                    <input type="hidden" value="{{ $packge->package_count }}" name="package_count[]">
	                                                                    <input type="hidden" value="{{ $packge->bar_code }}" name="bar_code[]">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control" value="{{ $packge->title }}" disabled="">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control package_count_arr"  value="{{ $packge->package_count }}" disabled="">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control length_arr" name="length[]" placeholder="Enter Length" value="{{ $packge->length }}">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control width_arr" name="width[]" placeholder="Enter width" value="{{ $packge->width }}">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control height_arr" name="height[]" placeholder="Enter height" value="{{ $packge->height }}">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control weight_arr" name="weight[]" placeholder="Enter weight" value="{{ $packge->weight }}">
	                                                                </td>
	                                                                <td>
	                                                                    <input type="text" class="form-control charged__weight_arr" name="charged__weight[]" placeholder="Enter Charged Weight" value="{{ $packge->charged_weight }}">
	                                                                </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="custom_price[]" placeholder="Enter Custom Price" value="{{ $packge->custom_price }}">
                                                                    </td>
	                                                            </tr>
	                                                            @empty
	                                                                <tr>
	                                                                    <td colspan="7"> Package not created yet</td>
	                                                                </tr>
	                                                            @endforelse
	                                                        </tbody>
	                                                    </table>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="row">
	                                            <div class="col-md-9"></div>
	                                            <div class="col-md-3"><button type="submit" class="btn-blue btn-sm pull-right mt-1 save-waybill">Submit</button></div>
	                                        </div>
	                                    </div>
	                                </div>{{-- end PackageDetails --}}
	                                <div class="row mt-1">
	                                    <div class="col-md-10 error-msg"></div>
	                                </div>
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