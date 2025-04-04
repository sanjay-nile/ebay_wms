@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/create-waywill.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#delivery_date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $(".nav-tabs a").click(function(e){
            e.preventDefault();
            $(this).tab('show');
        });       
        
        //--------------------------------------------------------------------------------------------        
        $('body').on('change','#client_id_change',function(){
            let client_id = $(this).val();
            if(client_id){
                $.ajax({
                    type:'get',
                    url :"{{ route('admin.shipment-list-by-client-id') }}",
                    data : {id:client_id},
                    dataType : 'json',
                    success : function(data){
                        $("#client_shipment_list").replaceWith(data.shipment);
                        $("#client_warehouse_list").replaceWith(data.warehouse);
                        $(".rate-id").html('');
                        $(".carrier-div").html('');
                        setRate();
                    }
                });
            }else{
                $('#client_shipment_list').find('option').remove().end().append('<option value="">Select</option>');
                $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
                $(".rate-id").html('');
                $(".carrier-div").html('');
            }
        });

        //--------------------------------------------------------------------------------------------        
        $('body').on('change','#customer_country',function(){
            let country_id = $('option:selected', this).attr('data-id');
            let client_id = $( "#client_id_change option:selected" ).val();
            if(client_id && country_id){
                $.ajax({
                    type:'get',
                    url :"{{ route('admin.client-warehouse') }}",
                    data : {client_id:client_id, country_id:country_id},
                    dataType : 'json',
                    success : function(data){
                        $("#client_warehouse_list").replaceWith(data.warehouse);
                    }
                });
            }else{
                $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
            }
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

        function setRate(){
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

        //--------------------------------------------------------------------------------------------        
        $('body').on('keyup change','input,select',function(){
            let text = $(this).val();
            if(text.length>0){
                $(this).next('small').text('');
            }
        });
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
						<li class="breadcrumb-item active">Create Return Order</li>
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
                            <a href="{{ route('reverse-logistic') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                            <button class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#bulkUpload">
                                <i class="la la-cloud-upload"></i> Bulk Upload
                            </button>
                            <a href="{{ asset('public/admin/waybill.xlsx') }}" class="btn btn-outline-danger btn-sm"><i class="la la-download"></i> Download Sample File</a>
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
                            <form method="post" id="create-waybill" action="{{ route('admin-waybills.store') }}" enctype="multipart/form-data">
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane  active" id="about-customer" aria-labelledby="about-customer" aria-expanded="true">
                                        <div class="info-list-section">
                                            <div class="row">
                                                @if(Auth::user()->user_type_id==1 || Auth::user()->user_type_id==2)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Client Name <span class="text-danger">*</span></label>
                                                        <select name="client_id" id="client_id_change" class="form-control">
                                                            <option value="">Select</option>
                                                            @forelse($client_list as $client)
                                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                @else
                                                    @if(Auth::user()->user_type_id==3)
                                                        <input type="hidden" name="client_id" value="{{ Auth::id() }}">
                                                    @else
                                                        <input type="hidden" name="client_id" value="{{ ($client)?$client->owner_id:'' }}">
                                                    @endif
                                                @endif
                                                <input type="hidden" name="client_code" value="REVERSEGEAR">
                                                <input type="hidden" name="customer_code" value="00000">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="customer_name" placeholder="Enter Name" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Email <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="customer_email" placeholder="Enter Email" value="">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Address <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="customer_address" placeholder="Enter Address" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Country</label>
                                                        <select name="customer_country" id="customer_country" class="form-control">
                                                            <option value="">Select</option>
                                                            @forelse($country_list as $country)
                                                                <option value="{{ $country->sortname }}" data-id="{{ $country->id }}">{{ $country->name }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">State</label>
                                                        <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="DL">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">City</label>
                                                        <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="DELHI">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Pincode</label>
                                                        <input type="text" class="form-control" name="customer_pincode" placeholder="Enter Pincode" value="">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Phone <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="customer_phone" placeholder="Enter Phone" value="">
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
                                                        <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Way Bill Number" value="{{ generateUniqueWaybillNumber() }}">
                                                    </div>
                                                </div>
                                                
                                                {{-- <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Delivery Date</label>
                                                        <input type="text" class="form-control" name="delivery_date" placeholder="Enter Delivery Date" value="" id="delivery_date">
                                                    </div>
                                                </div> --}}
                                                
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
                                                    {{-- </div>
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
                                                        <label for="">Number Of Packages <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="number_of_packages" placeholder="Enter Number Of Packages" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Weight Unit Type</label>
                                                        <select class="form-control" name="weight_unit_type">
                                                            <option value="POUND" selected>POUND</option>
                                                            <option value="GRAM">GRAM</option>
                                                            <option value="KILOGRAM">KILOGRAM</option>
                                                            <option value="TONNE">TONNE</option>
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
                                                        <textarea name="description" placeholder="Enter Description" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Select Images <small>(can attach more than one):</small></label>
                                                        <input class="form-control" id="image-upload" type="file" name="images[]" accept="image/*" multiple>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Warehouse</label>
                                                        <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                            <option value="">Select</option>
                                                            @if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=2)
                                                                @forelse($warehouse_list as $warehouse)
                                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                                @empty
                                                                    <option value="">Warehouse not added yet</option>
                                                                @endforelse
                                                            @endif
                                                        </select>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Shipment</label>
                                                        <select name="shipment_id" id="client_shipment_list" class="form-control">
                                                            <option value="">Select</option>
                                                            @if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=2)
                                                                @forelse($shipment_list as $shipment)
                                                                    <option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}">{{ $shipment->shipment_name }}</option>
                                                                @empty
                                                                    <option value="">Shipment not added yet</option>
                                                                @endforelse
                                                            @endif
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
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="add-1">
                                                                    <td>
                                                                        <input type="text" class="form-control" name="bar_code[]" placeholder="Enter Bar Code" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="title[]" placeholder="Enter title" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Enter Package Count" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control length_arr" name="length[]" placeholder="Enter Length" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control width_arr" name="width[]" placeholder="Enter width" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control height_arr" name="height[]" placeholder="Enter height" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control weight_arr" name="weight[]" placeholder="Enter weight" value="">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control charged__weight_arr" name="charged__weight[]" placeholder="Enter Charged Weight" value="">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-delete btn-danger delete-package" data-id="1"><i class="la la-trash"></i></button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="10">
                                                                         <button type="button" class="btn-blue btn-sm pull-right add-more-package">Add More</button>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
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

@include('pages.common.order-upload', ['url' => route('admin-waybills.bulk-upload')])

@endsection