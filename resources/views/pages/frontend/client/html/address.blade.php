@forelse($address as $addr)   
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer_name" value="{!! $addr['first_name'] !!} {!! $addr['last_name'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Email <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer_email" value="{!! $addr['email'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer_address" value="{!! $addr['street_1'] !!} {!! $addr['street_2'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Country</label>
                <select name="customer_country" id="customer_country" class="form-control">
                    <option value="">Select</option>
                    @forelse(get_country_list() as $country)
                        <option value="{{ $country->sortname }}" data-id="{{ $country->id }}" @if($addr['country'] == $country->name) selected @endif>{{ $country->name }}</option>
                    @empty
                    @endforelse
                </select>                                                
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">State</label>
                <input type="text" class="form-control" name="customer_state" value="{!! $addr['state'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">City</label>
                <input type="text" class="form-control" name="customer_city" value="{!! $addr['city'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Pincode</label>
                <input type="text" class="form-control" name="customer_pincode" id="customer_pincode" value="{!! $addr['zip'] !!}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer_phone" value="{!! $addr['phone'] !!}">
            </div>
        </div>   
    </div>
@empty
    <div class="row">
        <div class="col-md-12">No Address added. Click on "Add Address" button to add the address.</div>
    </div>
@endforelse