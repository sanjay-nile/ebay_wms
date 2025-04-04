<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_name" placeholder="Enter Name" value="{{ $order['delivery_name'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Email <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_email" placeholder="Enter Email" value="{{ $order['customer_email'] }}">
            <input type="hidden" name="customer_order_email" value="{{ $order['customer_email'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_address" placeholder="Enter Address" value="{{ $order['delivery_address1'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Country</label>
            <input type="text" class="form-control" name="customer_country" value="{{ $order['delivery_country_code'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">State</label>
            <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="{{ $order['delivery_town'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">City</label>
            <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="{{ $order['delivery_town'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Pincode</label>
            <input type="text" class="form-control" name="customer_pincode" placeholder="Enter Pincode" value="{{ $order['delivery_postcode'] }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="">Phone <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="customer_phone" placeholder="Enter Phone" value="{{ $order['contact_phone'] }}">
        </div>
    </div>   
</div>