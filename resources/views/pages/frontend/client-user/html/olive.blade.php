<form method="post" id="create-waybill" action="{{ route('client-user.return-order.store') }}" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <input type="hidden" name="client_id" id="client_id" value="{{ ($client)?$client->owner_id:'' }}">
    <input type="hidden" name="client_code" value="REVERSEGEAR">
    <input type="hidden" name="customer_code" value="00000">
    <input type="hidden" name="service_code" value="ECOMDOCUMENT">
    
    <div class="info-list-section reverse-create-form">
        <h2>Order Details</h2>
        <div class="info-list-inner">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Order Id <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="order_id" value="" id="order_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Order Email Id<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="email_id" value="" id="email_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">RMA Code<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="rma_number" value="" id="rma_code">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for=""></label>
                        <button type="button" id="fetch-product" class="btn btn-red btn-fetch">Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-list-section reverse-create-form">
        <h2>Select Return Option</h2>
        <div class="info-list-inner">
            <div class="row">
                <ul class="radioBtn">
                    <li class="row">
                        <div class="col-lg-4 address-info-check" id="rtnBar">
                            <input type="radio" id="By_ReturnBar" name="drop_off" value="By_ReturnBar" disabled="disabled">
                            <label for="By_ReturnBar">
                                <p>By Return Bar™ <img src="{{ asset('images/hr_circle_logo_400.png') }}" height="25" width="25"></p>
                                <p class="color-grey">Create a QR code to drop off your return at a Return Bar. No packaging, label or printer required.</p>
                                <p>
                                    <u><a href="https://www.happyreturns.com/" target="_blank" style="color: #05c;">How it works ></a></u>
                                </p>
                            </label>
                        </div>
                        <div class="col-lg-4 address-info-check">
                            <input type="radio" id="By_Mail" name="drop_off" value="By_Mail/Courier">
                            <label for="By_Mail">
                                <p>By Mail/Courier</p>
                                <p class="color-grey">Create a UPS label to print and attach to your returning parcel.</p>
                            </label>
                        </div>
                        <div class="address-info-check spinner-border text-info collapse" role="status">
                            <span class="spinner-grow spinner-grow-sm">Loading...</span>
                        </div>
                    </li>  
                </ul>
            </div>
        </div>
    </div>

    <div class="info-list-section reverse-create-form collapse" id="rtn-location">
        <div class="">
            <div class="row" id="return-bar"></div>
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
                        <label for="">Country</label>
                        <select name="customer_country" id="customer_country" class="form-control">
                            <option value="">-- Select --</option>
                            @forelse(get_country_list() as $country)
                                <option value="{{ $country->sortname }}" data-id="{{ $country->id }}">{{ $country->name }}</option>
                            @empty
                            @endforelse
                        </select>                                                
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">State</label>
                        <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="{{ old('customer_state') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">City</label>
                        <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="{{ old('customer_city') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Pincode</label>
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

    {{-- new section --}}
    <div class="info-list-section reverse-create-form" id="ship-div">
        <h2>Shipment Details</h2>
        <div class="info-list-inner">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Way Bill Number</label>
                        <input type="text" class="form-control" name="way_bill_number" id="waywill_id" value="{{ generateUniqueWaybillNumber() }}">
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
                {{-- <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Carrier  <span id="dly" style="color: red; display: none;">Loading...</span></label>
                        <select name="carrier" id="carrier" class="form-control">
                            <option value="">-- Select-- </option>
                            @forelse($shipment_list as $cp)
                                <option value="{{ $cp->code }}" name="{{ $cp->carrier_id }}">{{ $cp->carrier_name }}</option>
                            @empty
                            <option value="">Carrier not found</option>
                            @endforelse
                        </select>
                        @error('carrier')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group carrier_product">
                        <label for="">Carrier Product</label>
                        <select name="carrier_product" id="carrier_product" class="form-control">
                            <option value="">-- Select-- </option>
                        </select>
                        @error('carrier_product')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group service_code">
                        <label for="">Carrier Service Code</label>
                        <select name="service_code" id="service_code" class="form-control">
                            <option value="">-- Select-- </option>
                        </select>
                        @error('service_code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                </div> --}}
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
                        <label for="">Weight Unit Type</label>
                        <select class="form-control" name="unit_type">
                            <option value="GRAM">GRAM</option>
                            <option value="KILOGRAM">KILOGRAM</option>
                            <option value="TONNE">TONNE</option>
                            <option value="POUND" selected>POUND</option>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Remark</label>
                        <textarea name="remark" placeholder="Enter Description" class="form-control" rows="2">{{ old('remark') }}</textarea>
                    </div>
                </div>                                
                <div class="col-md-3 rate-id"></div>
                <div class="col-md-3 carrier-div"></div>
            </div>
        </div>
    </div>

    {{-- package section --}}
    <div class="info-list-section">
        <h2>Item Details <small style="color: #000;">(* Please click on the checkbox to return one or more items.)</small></h2>
        <div class="package-item-info">
            <div class="package-item-inner" id="item-card"></div>                            
        </div>
        <div class="package-item-btn">
            <button type="submit" class="btn save-waybill">Submit</button>
        </div>
    </div>                        
    <div class="row mt-1">
        <div class="col-md-10 error-msg"></div>
    </div>
</form>