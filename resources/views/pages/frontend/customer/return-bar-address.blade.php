@forelse($address as $k => $add)
    @if($k > 0)
        @php
            continue;
        @endphp
    @endif
    <div class="col-lg-12 col-md-12 col-xs-12">
        <div class="form-group">
            <div class="address-info-card returnBar-al sub-inner-card">
                <div class="address-info-inner-card-1">                                             
                    <div class="address-info-check collapse">
                        <input type="radio" id="returnBarAddress{{ $k }}" name="bar_address" value="{{ json_encode($add) }}" checked="checked">
                        <label for="returnBarAddress{{ $k }}"></label>
                    </div>
                    <div class="address-info-text">
                        <h4>Your closest Return Bar is {{ $add['distance'] }} mi away. <u><a href="https://locations.happyreturns.com/" target="_blank" style="color: #05c;">See all locations ></a></u></h4>
                        <div class="address-h">
                            <p class="pull-left" style="font-size: 18px;">{{ $add['name'] }}</p>
                            <p class="pull-right">{{ $add['distance'] }} mi</p>
                        </div>

                        <h2 class="name">Address</h2>
                        <p class="phone">{{ $add['address']['address'] }}, {{ $add['address']['city'] }}, {{ $add['address']['state'] }} {{ $add['address']['zipcode'] }}, {{ $add['address']['phoneNumber'] }}</p>
                        <p>{{ $add['directions'] ?? '' }}</p>

                        <div class="type address-type">
                            <h3>Hours</h3>
                            <p class="address">{{ str_replace(',', ' | ', $add['hours']) }}</p>  
                        </div>

                        @if(isset($add['display']))
                            @forelse($add['display'] as $dis)
                                @if($dis['label'] != 'Promotions')
                                <div class="type address-type" style="margin-top: 10px;">
                                    <h3>{{ $dis['label'] }}</h3>
                                    <p class="address">{{ $dis['value'] }}</p>  
                                </div>
                                @endif
                            @empty
                            @endforelse
                        @endif
                    </div>  
                </div>
            </div>
        </div>
    </div>
@empty

@endforelse