@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<style type="text/css">
.create-form-section {
    border: 1px solid #f4f5fa;
    border-radius: 5px;
    margin-bottom: 20px;
    border-radius: 12px;
    background: #fff;
    padding:20px 30px 30px; 
}
.create-form-section h2 {
    font-size: 16px;
    font-weight: bold;
    padding: 10px;
}
.create-form-body{
    padding: 10px;
}

.package-item-list {
    border: 1px solid #eee;
    padding: 10px;
    margin-bottom: 15px;
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
.create-form-body .row .col-md-12{padding-left:5px; padding-right:5px;}

.add-more-package {background: #0e1035; font-size: 12px; padding: 8px 10px; color: #fff; }
.save-waybill{background: #b41e37; font-size: 12px; padding: 8px 10px; color: #fff; }
.save-waybill:hover{color: #fff; }
.create-form-body .form-group label{
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 0;
}
.info-list-inner {
    width: 100%;
    margin-top: 0;
    padding: 20px 4px 20px;
    border-radius: 4px;
    background-image: linear-gradient(#bbbdbf, #e7e9e9);
}
</style>
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/create-waywill.js') }}"></script>
<script>
    $(document).ready(function(){
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
                <div class="booking-info-box">
                    <div class="">
                        <h4 class="card-title">
                            <a href="{{ route('reverse-logistic') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                            <button class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#bulkUpload">
                                <i class="la la-cloud-upload"></i> Bulk Upload
                            </button>
                            <a href="{{ asset('public/admin/waybill.xlsx') }}" class="btn btn-outline-danger btn-sm"><i class="la la-download"></i> Download Sample File</a>
                        </h4>                       
                    </div>
                    <div class="card-content">
                        <div class="card-body">							
                            <form method="post" id="create-waybill" action="{{ route('admin-waybills.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="client_code" value="REVERSEGEAR">
                                <input type="hidden" name="customer_code" value="00000">
                                <input type="hidden" name="service_code" value="ECOMDOCUMENT">
                                {{-- customer detail --}}
                                <div class="create-form-section">
                                    <h2>Customer Details</h2>
                                    <div class="info-list-inner">
                                        <div class="create-form-body">
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
                                                        <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">City</label>
                                                        <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="">
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
                                    </div>
                                </div>
                                {{-- shipping detail --}}
                                <div class="create-form-section">
                                    <h2>Shipping Details</h2>
                                    <div class="info-list-inner">
                                        <div class="create-form-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Way Bill Number</label>
                                                        <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Way Bill Number" value="{{ generateUniqueWaybillNumber() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Payment Mode <span class="text-danger">*</span></label>
                                                        <select class="form-control" name="payment_mode">
                                                            <option value="">Select</option>
                                                            <option value="FOD">FOD</option>
                                                            <option value="PAID">PAID</option>
                                                            <option value="TBB" selected>TBB</option>
                                                            <option value="FOC">FOC</option>  
                                                        </select>
                                                    </div>
                                                </div>
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
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Weight Unit Type</label>
                                                        <select class="form-control" name="unit_type">
                                                            <option value="POUND" selected>POUND</option>
                                                            <option value="GRAM">GRAM</option>
                                                            <option value="KILOGRAM">KILOGRAM</option>
                                                            <option value="TONNE">TONNE</option>
                                                        </select>                                                           
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Actual Weight <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
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
                                                <div class="col-md-3 rate-id"></div>
                                                <div class="col-md-3 carrier-div"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- package section --}}
                                <div class="create-form-section">
                                    <h2>Item Details</h2>
                                    <div class="info-list-inner">
                                        <div class="create-form-body">
                                            <div class="create-form-info">
                                                <div class="package-item-inner">
                                                    <div class="">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Item Bar Code</label>
                                                                    <input type="text" class="form-control item-barcode" name="bar_code[]" placeholder="Enter Item Bar Code" value="{{old('bar_code[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Item Name</label>
                                                                    <input type="text" class="form-control item-title" name="title[]" placeholder="Enter Product Description" value="{{old('title[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Quantity</label>
                                                                    <input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Quantity" value="{{old('package_count[]')}}" autocomplete="off">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Color</label>
                                                                    <input type="text" class="form-control" name="color[]" placeholder="Color" value="{{old('color[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Size</label>
                                                                    <input type="text" class="form-control" name="size[]" placeholder="Size" value="{{old('size[]')}}">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Dimension</label>
                                                                    <select class="form-control valid" name="dimension[]" aria-invalid="false">
                                                                        <option value="CM">CM</option>
                                                                        <option value="IN">IN</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Lenght</label>
                                                                    <input type="text" class="form-control" name="length[]" placeholder="Lenght" value="{{old('length[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Width</label>
                                                                    <input type="text" class="form-control" name="width[]" placeholder="Width" value="{{old('width[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Height</label>
                                                                    <input type="text" class="form-control" name="height[]" placeholder="Height" value="{{old('height[]')}}">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Weight Unit</label>
                                                                    <select class="form-control valid" name="weight_unit_type[]" aria-invalid="false">
                                                                        <option value="LBS">LBS</option>
                                                                        <option value="KGS">KGS</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Weight</label>
                                                                    <input type="text" class="form-control" name="weight[]" placeholder="Weight" value="{{old('weight[]')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Images (jpeg/png)</label>
                                                                    <input class="form-control item-image" id="image-upload-0" type="file" name="item_images[0][]" accept="image/*" multiple>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="form-control" name="charged__weight[]" value="1">
                                                            <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="create-form-info-footer">
                                                    <button type="button" class="btn add-more-package">+ Add More</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="create-form-btn">
                                    <button type="submit" class="btn mt-1 save-waybill">Submit</button>
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

@include('pages.common.order-upload', ['url' => route('admin-waybills.bulk-upload')])

@endsection