@forelse($address as $addr)
    <li class="address-info-inner-card">
        <div class="address-info-check">
            <input type="radio" id="{!! $addr['id'] !!}" name="address" value="{!! $addr['id'] !!}" data-name="{!! $addr['first_name'] !!} {!! $addr['last_name'] !!}" data-phone="{!! $addr['phone'] !!}" data-address="{!! $addr['street_1'] !!} {!! $addr['street_2'] !!}" data-country="{!! $addr['country_iso2'] !!}" data-state="{!! $addr['state'] !!}" data-city="{!! $addr['city'] !!}" data-zip="{!! $addr['zip'] !!}">
            <label for="{!! $addr['id'] !!}"></label>
        </div>
        <div class="address-info-text">
            <h2 class="name">{!! $addr['first_name'] !!} {!! $addr['last_name'] !!}</h2>
            <p class="phone">{!! $addr['phone'] !!}</p>
            <div class="type address-type">
                <h3>Office</h3>
                <p class="address">{!! $addr['street_1'] !!} {!! $addr['street_2'] !!}, {!! $addr['city'] !!} {!! $addr['state'] !!} , {!! $addr['country'] !!} ({!! $addr['country_iso2'] !!}), {!! $addr['zip'] !!}</p>  
            </div>
        </div>
    </li>
@empty
    <li class="address-info-inner-card">No Address added. Click on "Add Address" button to add the address.</li>
@endforelse