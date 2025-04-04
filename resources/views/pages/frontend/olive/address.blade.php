@forelse($address as $addr)    
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="customer_name" value="{!! $addr['first_name'] !!} {!! $addr['last_name'] !!}" id="customer_name">
            </div>
        </div>                                   
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="customer_email" value="{!! $addr['email'] !!}" id="customer_email">
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Phone</label>
                <input type="text" class="form-control" name="customer_phone" value="{!! $addr['phone'] !!}" id="customer_phone">
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Address</label>
                <input type="text" class="form-control" name="customer_address" value="{!! $addr['street_1'] !!} {!! $addr['street_2'] !!}" id="customer_address">
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Country</label>
                {{-- <input type="text" class="form-control" name="customer_country" value="{!! $addr['country_iso2'] !!}" id="customer_country"> --}}
                <select name="customer_country" id="customer_country" class="form-control" required="required">
                    <option value="">Choose your country</option>
                    @forelse($country as $cnt)
                        <option value="{{ $cnt->sortname }}" @if($cnt->sortname == $addr['country_iso2']) selected="selected" @endif>{{ $cnt->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">State</label>
                @php
                    $name = $addr['state'];
                    foreach ($state as $key => $st) {
                        if($st->name == $addr['state']){
                            $name = $st->shortname;
                        }
                    }
                @endphp
                <input type="text" class="form-control" name="customer_state" value="{!! $name !!}" id="customer_state">
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">City</label>
                <input type="text" class="form-control" name="customer_city" value="{!! $addr['city'] !!}" id="customer_city">
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="">Pincode</label>
                <input type="text" class="form-control" name="customer_pincode" value="{!! substr($addr['zip'], 0, 5) !!}" id="customer_pincode">
            </div>
        </div>
    </div>
@empty
    <div class="row">No Address added. Click on "Add Address" button to add the address.</div>
@endforelse