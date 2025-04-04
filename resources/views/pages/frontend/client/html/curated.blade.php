<form method="post" id="create-waybill" action="{{ route('client.curated.order.store') }}" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <input type="hidden" name="client_id" id="client_id" value="{{ Auth::id() }}">
    <input type="hidden" name="client_code" value="REVERSEGEAR">
    <input type="hidden" name="customer_code" value="00000">
    <input type="hidden" name="service_code" value="ECOMDOCUMENT">
    
    <div class="info-list-section reverse-create-form">
        <h2>Order Details</h2>
        <div class="info-list-inner">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Order Id <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="order_id" value="" id="order_id">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Delivery postcode or Email Address<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="email_id" value="" id="email_id">
                    </div>
                </div>                
                <div class="col-md-4">
                    <div class="form-group mt-4">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-red" style="margin-top: 2px;" id="curated-fetch-order">
                            Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-list-section reverse-create-form">
        <h2>Customer Details</h2>
        <div class="info-list-inner" id="address-card">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Enter Name" value="{{ old('customer_name') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_email" placeholder="Enter Email" value="{{ old('customer_email') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_address" placeholder="Enter Address" value="{{ old('customer_address') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Country <span class="text-danger">*</span></label>
                        <input type="text" name="customer_country" class="form-control" value="" placeholder="Enter Country">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="{{ old('customer_state') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="{{ old('customer_city') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Pincode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_pincode" placeholder="Enter Pincode" value="{{ old('customer_pincode') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_phone" placeholder="Enter Phone" value="{{ old('customer_phone') }}">
                    </div>
                </div>   
            </div>
        </div>
    </div>    

    <div class="info-list-section reverse-create-form">
        <h2>Select Waiver</h2>
        <div class="info-list-inner">
            <div class="radioBtn">
                <ul class=" row">
                    <li class="col-md-4">
                        <div class="address-info-check" id="rtnBar">
                            <input type="radio" id="By_RPT" name="waiver" value="Return_Policy_Timeline">
                            <label for="By_RPT">
                                <p>Return Policy Timeline</p>                                
                            </label>
                        </div>
                    </li>
                    <li class="col-md-4">
                        <div class="address-info-check">
                            <input type="radio" id="By_WRF" name="waiver" value="Waiving_of_Shipping_Cost">
                            <label for="By_WRF">
                                <p>Waiver of shipping cost</p>
                            </label>
                        </div>
                    </li>
                    <li class="col-md-4">
                        <div class="address-info-check">
                            <input type="radio" id="Both" name="waiver" value="Both">
                            <label for="Both">
                                <p>Waiver of both Timeline and shipping cost</p>
                            </label>
                        </div>
                    </li>  
                </ul>
            </div>
        </div>
    </div>

    <div class="info-list-section reverse-create-form">
        <h2>Shipment Label</h2>
        <div class="info-list-inner">
            <div class="row">
                <div class="col-lg-4">
                    <label><p>Do you want generate the shipment label?</p></label>
                </div>
                <div class="col-lg-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="shipment_label" id="shipment_label_y" value="Yes">
                        <label class="form-check-label" for="shipment_label_y">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="shipment_label" id="shipment_label_n" value="No" checked="checked">
                        <label class="form-check-label" for="shipment_label_n">No</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-list-section reverse-create-form collapse" id="rtn-div">
        <h2>Your Return Option</h2>
        <div class="info-list-inner">
            <div class="row">
                <ul class="radioBtn">
                    <li class="row">                        
                        <div class="col-lg-12 address-info-check">
                            <input type="radio" id="By_Mail" name="drop_off" value="By_Courier" checked="checked">
                            <label for="By_Mail">
                                <p>By Courier</p>
                                <!-- <p class="color-grey">Download carrier label or QR Code and drop off label PDF</p> -->
                            </label>
                        </div>
                    </li>  
                </ul>
            </div>
        </div>
    </div>

    {{-- shipment detail --}}
    <div class="info-list-section reverse-create-form collapse" id="ship-div">
        <h2>Shipment Details</h2>
        <div class="info-list-inner">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Order Id</label>
                        <input type="text" class="form-control" name="way_bill_number" id="waywill_id" value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Warehouse</label>
                        <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                            <option value="">Select</option>
                            @forelse($warehouse_list as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @empty
                                <option value="">Warehouse not added yet</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-md-3 collapse">
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
                        <label for="">Weight Unit Type</label>
                        <select class="form-control" name="unit_type">
                            <option value="KGS" selected>KGS</option>
                            <option value="LBS">LBS</option>
                        </select>                                                           
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Actual Weight <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="{{ old('actual_weight') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Charged Weight <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="{{ old('charged_weight') }}">
                    </div>
                </div>                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Shipment</label>
                        <select name="shipment_id" id="client_shipment_list" class="form-control">
                            <option value="">Select</option>
                            @forelse($shipment_list as $shipment)
                                <option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}" carrier_id="{{ $shipment->carrier_id }}" @if($shipment->is_default) selected @endif>{{ $shipment->shipment_name }}</option>
                            @empty
                                <option value="">Shipment not added yet</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-md-3 carrier-div"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Remark</label>
                        <textarea name="remark" placeholder="Enter Description" class="form-control" rows="2">{{ old('remark') }}</textarea>
                    </div>
                </div>
                <div class="col-md-3 rate-id collapse"></div>
            </div>
        </div>
    </div>

    {{-- package section --}}
    <div class="info-list-section collapse" id ="itm-div">
        <h2>Item Details <small style="color: #000;">(* Please click on the checkbox to return one or more items.)</small></h2>
        <div class="package-item-info">
            <div class="package-item-inner" id="item-card"></div>                            
        </div>        
    </div>
    
    <div class="package-item-btn text-right">
        <button type="submit" class="btn save-waybill">Submit</button>
    </div>
    <div class="row mt-1">
        <div class="col-md-10 error-msg"></div>
    </div>
</form>