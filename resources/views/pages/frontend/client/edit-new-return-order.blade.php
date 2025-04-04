@include('pages.frontend.client.breadcrumb', ['title' => 'Edit Return Order'])

@push('css')
<style>
    .list-your-service-form-box .col-md-12{padding: 0px !important;}
    #create-waybill h5{font-size: 16px; padding: 2px 0;}
    .card-body .bg-light {background: #fef0f2 !important; border-bottom: 1px solid #ffdee3; border-radius: 3px; }
    .booking-info-box .info-list-section {padding: 20px; border: 1px solid #f4f5fa; margin-top: 2px; background: #fff; border-radius: 12px;}
    .info-list-section .row p {color: #3b4781; font-weight: 500; }
    .info-list-section .row .non-editable{background: #fff; padding: 5px; border-radius: 2px; }
    .Head-secondary{position: absolute; right: 23px; top: 26px; font-size: 15px;}
    .info-list-inner{padding: 20px 10px 20px !important;}
    .address-type h4{font-size: 13px; font-weight: bold; margin: 0; padding: 0;} 
    .img-thumbnail {padding: .25rem; background-color: #fff; border: 1px solid #dee2e6; border-radius: .25rem; width: 60px; height: auto; }
</style>
@endpush

@push('js')
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
        // Run code...
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

    // setRate();

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

    callRate();
});
</script>
@endpush

<div class="row">
    <div class="col-xs-12 table-responsive">
        <div class="booking-info-box">
            <div class="">
                <h4 class="card-title">
                    <a href="{{ route('client.return.orders') }}" class="btn btn-back">
                        <i class="la la-arrow-left"></i> Back
                    </a>
                </h4>
            </div>
            <div class="card-content">
                <div class="">
                    @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar')
                    <form method="post" id="create-waybill" action="{{ route('client.return-bar.order') }}" autocomplete="off">
                    @else
                        @if($client->client_type == '2')
                            <form method="post" id="create-waybill" action="{{ route('client.missguided.order.update') }}">
                        @elseif($client->client_type == '4')
                            <form method="post" id="create-waybill" action="{{ route('client.curated.order.update') }}">
                        @else
                            <form method="post" id="create-waybill" action="{{ route('client-waybills.update') }}">
                        @endif
                    @endif

                        <input type="hidden" id="return_url" value="{{ route('client.return.orders') }}">
                        <input type="hidden" name="way_bill_id" value="{{ $waybill->id }}">
                        <input type="hidden" name="way_bill_number" value="{{ $waybill->way_bill_number }}">
                        <input type="hidden" name="client_code" value="REVERSEGEAR">
                        <input type="hidden" name="customer_code" value="00000">
                        {{-- <input type="hidden" name="customer_name" value="{{ $waybill->meta->_customer_name }}">
                        <input type="hidden" name="customer_email" value="{{ $waybill->meta->_customer_email }}">
                        <input type="hidden" name="customer_address" value="{{ $waybill->meta->_customer_address }}">
                        <input type="hidden" name="customer_country" value="{{ $waybill->meta->_customer_country }}">
                        <input type="hidden" name="customer_state" value="{{ $waybill->meta->_customer_state }}">
                        <input type="hidden" name="customer_city" value="{{ $waybill->meta->_customer_city }}">
                        <input type="hidden" name="customer_pincode" value="{{ $waybill->meta->_customer_pincode }}">
                        <input type="hidden" name="customer_phone" value="{{ $waybill->meta->_customer_phone }}"> --}}
                        <input type="hidden" name="service_code" value="ECOMDOCUMENT">

                        <div class="Information">
                            {{-- customer detail --}}
                            <div class="info-list-section" style="position: relative;">
                                <nav class="">
                                    <h2>Customer Details</h2>
                                    <a class="Head-secondary" href="javascript:void(0);">
                                        Order Number / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}
                                    </a>
                                </nav>
                                <div class="info-list-inner">
                                    <div class="row">
                                        {{-- <div class="col-lg-3">
                                            <label>Client Name</label>
                                            <p class="non-editable">{{ $waybill->client->name }}</p>
                                        </div> --}}
                                        <div class="col-lg-3">
                                            <label>Customer Name</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_name }}</p> --}}
                                            <input type="text" class="form-control" name="customer_name" value="{{ $waybill->meta->_customer_name }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Email</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_email }}</p> --}}
                                            <input type="text" class="form-control" name="customer_email" value="{{ $waybill->meta->_customer_email }}">
                                        </div>                                    
                                        <div class="col-lg-3">
                                            <label>Mobile </label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_phone }}</p> --}}
                                            <input type="text" class="form-control" name="customer_phone" value="{{ $waybill->meta->_customer_phone }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Address</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_address }}</p> --}}
                                            <input type="text" class="form-control" name="customer_address" value="{{ $waybill->meta->_customer_address }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Country</label>
                                            {{-- <p class="non-editable">{{ get_country_name_by_id($waybill->meta->_customer_country) }}</p> --}}
                                            <input type="text" class="form-control" name="customer_country" value="{{ $waybill->meta->_customer_country }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>State</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_state }}</p> --}}
                                            <input type="text" class="form-control" name="customer_state" value="{{ $waybill->meta->_customer_state }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>City</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_city }}</p> --}}
                                            <input type="text" class="form-control" name="customer_city" value="{{ $waybill->meta->_customer_city }}">
                                        </div>
                                        <div class="col-lg-3">
                                            <label>PinCode</label>
                                            {{-- <p class="non-editable">{{ $waybill->meta->_customer_pincode }}</p> --}}
                                            <input type="text" class="form-control" name="customer_pincode" value="{{ $waybill->meta->_customer_pincode }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- shipment detail --}}
                            <div class="info-list-section Shipment mt-4 @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar') collapse @endif">
                                <nav class="">
                                    <h2>Shipment Details</h2>
                                </nav>
                                <div class="info-list-inner">                                           
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label>Order Number</label>
                                            <p class="non-editable">{{ $waybill->way_bill_number }}</p>
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Payment Mode *</label>
                                            <p>
                                                <select class="form-control" name="payment_mode">
                                                    <option value="">Select</option>
                                                    <option value="FOD">FOD</option>
                                                    <option value="PAID">PAID</option>
                                                    <option value="TBB" selected>TBB</option>
                                                    <option value="FOC">FOC</option>  
                                                </select>
                                            </p>
                                        </div>
                                        <div class="col-lg-3 collapse">
                                            <label> Cash On Pickup</label>
                                            <p>
                                                <select class="form-control" name="cash_on_pickup">
                                                    <option value="">Select</option>
                                                    <option value="Cash" selected="selected">Cash</option>
                                                    <option value="Cheque">Cheque</option>
                                                </select>
                                            </p>
                                        </div>
                                        <div class="col-lg-3 collapse">
                                            <label>Amount </label>
                                            <p><input type="text" class="form-control" name="amount" placeholder="Amount" value="0"></p>
                                        </div>
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
                                                    <option value="POUND" @if($waybill->meta->_unit_type == 'LBS') selected @endif>LBS</option>
                                                    <option value="KILOGRAM" @if($waybill->meta->_unit_type == 'KGS') selected @endif>KGS</option>
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
                                                <input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="{{ $wt }}">
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
                                                    <option value="">-- Select -- </option>
                                                    @forelse($shipment_list as $shipment)
                                                        <option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}" @if($shipment->is_default) selected @endif>{{ $shipment->shipment_name }}</option>
                                                    @empty
                                                    <option value="">Shipment not added yet</option>
                                                    @endforelse
                                                </select>
                                            </p>
                                        </div>
                                        {{-- <div class="col-lg-3 rate-id"></div> --}}
                                        <div class="col-lg-3 carrier-div"></div>
                                        <div class="col-lg-6">
                                            <label>Description</label>
                                            <p>
                                                <textarea name="description" placeholder="Enter Description" class="form-control" rows="2">{{ $waybill->meta->_remark ?? '' }}</textarea>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- package detail --}}
                            <div class="info-list-section Packages mt-4">
                                <nav class="">
                                    <h2>Packages Details</h2>
                                </nav>
                                <div class="info-list-card">
                                    <div class="package-item-info">
                                        <div class="package-item-inner">
                                            <div class="info-list-inner">
                                                <div class="row">
                                                    @forelse($packge_list as $packge)
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Item Bar Code</label>
                                                                <input type="hidden" value="{{ $packge->id }}" name="package_arr[]">
                                                                <input type="hidden" class="form-control" name="charged__weight[]" value="1">
                                                                <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
                                                                <input type="hidden" class="form-control" name="hs_code[]" value="{{ $packge->hs_code }}">
                                                                <input type="hidden" class="form-control" name="item_return_reason[]" value="{{ $packge->return_reason }}">

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
                                                                <label>Dimension Unit</label>
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
                                                                        <div class="image-content">
                                                                            <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="70">
                                                                            </a>
                                                                        </div>
                                                                    @empty
                                                                        <p>N/A</p>
                                                                    @endforelse
                                                                @else
                                                                    @if(!empty($package->image_url))
                                                                        @if(str_contains($package->image_url, 'uploads/'))
                                                                            <div class="col-md-7">
                                                                               <a href="{{ asset('public/'.$package->image_url) }}" target="_blank">
                                                                                   <img src="{{ asset('public/'.$package->image_url) }}" class="img-thumbnail" width="168" height="100">
                                                                               </a>
                                                                            </div>
                                                                        @else
                                                                            <div class="col-md-7">
                                                                               <a href="{{ $package->image_url }}" target="_blank">
                                                                                   <img src="{{ $package->image_url }}" class="img-thumbnail" width="168" height="100">
                                                                               </a>
                                                                            </div>
                                                                        @endif
                                                                   @else
                                                                       <p class="col-md-6">N/A</p>
                                                                   @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-md-12">
                                                            <div class="mss-text"> Package not created yet</div>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-info-card mt-4 text-right">
                                    <button type="submit" class="btn save-waybill">Generate Label</button>
                                </div>
                                <div class="error-info-card mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.content -->