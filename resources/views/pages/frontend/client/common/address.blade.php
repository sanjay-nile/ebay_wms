<div class="info-list-section">
    <div class="info-list-inner">
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Billing Address</h5>
        </div> 
        <div class="col-md-6">
            <div class="form-group">
                <label for="">First Name</label>
                <input type="text" class="form-control" name="billing_first_name" placeholder="Enter First Name" value="{{ ($data)? $data['billing_first_name']:'' }}">
                @if($errors->has('billing_first_name'))
                <span class="text-danger">{{ $errors->first('billing_first_name') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Last Name</label>
                <input type="text" class="form-control" name="billing_last_name" placeholder="Enter Last Name" value="{{ ($data)?$data['billing_last_name']:'' }}">
                @if($errors->has('billing_last_name'))
                <span class="text-danger">{{ $errors->first('billing_last_name') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Email ID</label>
                <input type="text" class="form-control" name="billing_email" placeholder="Enter Email Address" value="{{ ($data)?$data['billing_email']:'' }}">
                @if($errors->has('billing_email'))
                <span class="text-danger">{{ $errors->first('billing_email') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Company Name</label>
                <input type="text" class="form-control" name="billing_company_name" placeholder="Enter Company Name" value="{{ ($data)?$data['billing_company_name']:'' }}">
                @if($errors->has('billing_company_name'))
                <span class="text-danger">{{ $errors->first('billing_company_name') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> Address 1</label>
                <input type="text" class="form-control" name="billing_address_1" placeholder="Enter Address 1" value="{{ ($data)?$data['billing_address_1']:'' }}">
                @if($errors->has('billing_address_1'))
                <span class="text-danger">{{ $errors->first('billing_address_1') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> Address 2</label>
                <input type="text" class="form-control" name="billing_address_2" placeholder="Enter Address 2" value="{{ ($data)?$data['billing_address_2']:'' }}">
                @if($errors->has('billing_address_2'))
                <span class="text-danger">{{ $errors->first('billing_address_2') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Country</label>
                <select name="billing_country" class="form-control">
                    <option value="">Select</option>
                    @forelse($country_list as $country)
                        <option value="{{ $country->id }}" {{ ($data && $country->id==$data['billing_country'])?"selected":'' }}>{{ $country->name }}</option>
                    @empty
                        <option value="">Please add country</option>
                    @endforelse
                </select>
                @if($errors->has('billing_country'))
                <span class="text-danger">{{ $errors->first('billing_country') }}</span>
                @endif
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="">State</label>
                <input type="text" class="form-control" placeholder="State" name="billing_state" value="{{ ($data)?$data['billing_state']:'' }}">
                @if($errors->has('billing_state'))
                <span class="text-danger">{{ $errors->first('billing_state') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> City</label>
                <input type="text" class="form-control" name="billing_city" placeholder="Enter City" value="{{ ($data)?$data['billing_city']:'' }}">
                @if($errors->has('billing_city'))
                <span class="text-danger">{{ $errors->first('billing_city') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> Postal Code</label>
                <input type="text" class="form-control" name="billing_postal_code" placeholder="Postal Code" value="{{ ($data)?$data['billing_postal_code']:'' }}">
                @if($errors->has('billing_postal_code'))
                <span class="text-danger">{{ $errors->first('billing_postal_code') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-6">
            <div class="form-group">
                <label for=""> Phone No.</label>
                <input type="text" class="form-control" name="billing_phone" placeholder="Enter Phone" value="{{ ($data)?$data['billing_phone']:'' }}">
                @if($errors->has('billing_phone'))
                <span class="text-danger">{{ $errors->first('billing_phone') }}</span>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>