<div class="row mt-1">
    <div class="col-md-10 error-msg"></div>
</div>
<div class="row mt-1">
    <div class="col-md-10 success-msg"></div>
    <div class="col-md-2 downloadandprint">
        <a class="btn-next-lewin pdf-print" href="javascript:void(0)" target="_blank">{{googleTranslate('Download and Print')}}</a>
    </div>
</div>

<form method="post" id="create-waybill" action="{{route('other.client.ordercreate')}}" class="myform">
    @csrf
    <input type="hidden" name="client_id" id="client_id" value="{{ Auth::id() }}">
    <input type="hidden" name="client_code" value="REVERSEGEAR">
    <input type="hidden" name="customer_code" value="00000">
    <input type="hidden" name="service_code" value="ECOMDOCUMENT">
    <input type="hidden" name="amount" id="total_price" value="0">
    <input type="hidden" name="currency" id="currency" value="USD">
    <input type="hidden" name="return_charges" value="0" id="return_charges">
    <input type="hidden" name="payment_mode" id="payment_mode" value="TBB">
    <input type="hidden" name="actual_weight" value="1">
    <input type="hidden" name="charged_weight" value="1">
    <input type="hidden" name="shipping_charges" value="25" id="shipping_charges">
    <input type="hidden" name="countrycode" value="" id="country_code_return">
    <input type="hidden" name="env_amount" value="1.40" id="env_amount">
    <input type="hidden" name="curated_id" value="" id="curated_id">
    <input type="hidden" name="lineitemsorigincountry" value="" id="origincountry">
    <input type="hidden" name="customer_order_email" id="customer_order_email" value="">
    
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
                        <label for="">Email Address<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="email_id" value="" id="email_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group timelinewaiver">
                        <input type="checkbox" class="" name="getwaiver" id="getwaiver">
                        <label for="">Return Policy Timeline Waiver </label>
                        
                    </div>
                </div>    
                               
                <div class="col-md-3">
                    <div class="form-group mt-4">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-red" style="margin-top: 2px;" id="client-fetch-order">
                            Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div>
    <div class="info-list-section reverse-create-form">
        <h2>Customer Details</h2>
        <div class="info-list-inner" id="address-card">
            <div class="row">
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Enter Name" value="{{ old('customer_name') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" class="form-control" name="customer_email" placeholder="Enter Email" value="{{ old('customer_email') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Address</label>
                        <input type="text" class="form-control" name="customer_address" placeholder="Enter Address" value="{{ old('customer_address') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Country</label>
                        <input type="text" name="customer_country" class="form-control" value="" id="customer_country">
                        {{-- <select name="customer_country" id="customer_country" class="form-control">
                            <option value="">-- Select --</option>
                            @forelse(get_country_list() as $country)
                                <option value="{{ $country->sortname }}" data-id="{{ $country->id }}">{{ $country->name }}</option>
                            @empty
                            @endforelse
                        </select> --}}                                                
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
                        <label for="">Phone</label>
                        <input type="text" class="form-control" name="customer_phone" placeholder="Enter Phone" value="{{ old('customer_phone') }}">
                    </div>
                </div> 
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="checkbox" class="" name="shipmentwaiver" id="shipmentwaiver">
                        <label for="">Shipment Waiver </label>
                    </div>
                </div>  
            </div>
        </div>
    </div>    

    <!-- <div class="info-list-section reverse-create-form">
        <h2>Select Waiver</h2>
        <div class="info-list-inner">
            <div class="radioBtn">
                <ul class="row">
                    <li class="col-md-4">
                        <div class="address-info-check" id="rtnBarr">
                            <input type="checkbox" id="By_RPTT" name="waiver">
                            <label for="By_RPTT">
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
    </div> -->

    <!-- <div class="info-list-section reverse-create-form">
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
    </div> -->

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

    
<div class="returnfullsection">
    {{-- package section --}}
    <div class="row">
        <div class="col-md-8">
            <div class="info-list-section collapse" id ="itm-div">
                <h2>Item Details <small style="color: #000;">(* Please select atleast one item.)</small></h2>
                <div class="itemerror">

                </div>
                <div class="package-item-info">
                    <div class="package-item-inner" id="item-card"></div>                            
                </div>        
            </div>
        </div>
        <div class="col-md-4">
            <div class="order-item rtn_sumry" id="refund_html">

            </div>
        </div>
    </div>
    

    {{-- shipment detail --}}
    <div class="info-list-section reverse-create-form collapse" id="ship-div">
        <h2>Shipment Details</h2>
        <div class="info-list-inner">

            <div class="tab-pane Eucountrycarrieritem">
                <div class="step-content-form-box">
                    <div class="step-content-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                                <div class="step-content-form">
                                    <h3>{{googleTranslate('Select available return option')}}</h3>
                                      <!-- <h3>{{googleTranslate('Postal')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <div class="PBCheckbox">
                                                    <input id="card-select" type="radio" name="carrier" value="postal">
                                                    <label for="card-select">
                                                        <div class="carrier-text">
                                                            <h2>{{googleTranslate('Postal')}}</h2>
                                                            <p>{{googleTranslate('Max 10kg Max 40cm x 55cm x 53cm')}}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div> -->

                                        <div class="carrier-list">
                                            

                                            <div class="carrier-item">
                                                <!-- <div class="PBCheckbox">
                                                    <input id="Printed label required" type="radio" name="carrier" value="postal">
                                                    <label for="Printed label required">
                                                        <div class="carrier-text">
                                                            <h2>Printed label required</h2>
                                                        </div>
                                                    </label>
                                                </div> -->

                                                <div class="carrier-table">
                                                    <h4>{{googleTranslate('Printed label required')}}</h4>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                
                                                                <th style="text-align: left;width: 25%;">{{googleTranslate('Service')}}</th>
                                                                <th style="width: 25%;">{{googleTranslate('Max Weight/')}} {{googleTranslate('Dimensions')}} {{googleTranslate('(LxWxH)')}}</th>
                                                                <th style="text-align: center;width: 20%;">{{googleTranslate('Printer Required')}}</th>
                                                                <!-- <th style="text-align: center; width: 10%;">{{googleTranslate('Price')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Drop off Locations')}}</th> -->
                                                                <th style="text-align: center; width: 20%;">{{googleTranslate('Tracking available')}}</th>
                                                                <th style="text-align: center; width: 10%;"></th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <tr>
                                                                
                                                                <td>
                                                                    Postal Return <br>Drop off Location
                                                                </td>
                                                                
                                                                <td>
                                                                    <p class="">10kg <br>60cm x 40cm x 40cm</p>
                                                                    <p></p>
                                                                    <p></p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <img src="{{ asset('public/label/Printer.jpg') }}">
                                                                  </div>
                                                                </td>
                                                                <!-- <td style="text-align: center;vertical-align: middle;">0.00 EUR</td> -->
                                                                <!-- <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://www.royalmail.com/services-near-you#/" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td> -->
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking" target="_blank"target="_blank"> <img src="{{ asset('public/label/tracking.jpg') }}"></a>
                                                                  </div>
                                                                </td>


                                                                <td>
                                                                    <div class="PBradio1Eucountry">
                                                                        <input id="carrierlabel1eucountry" class="carrierlabel1eucountry" type="radio" name="EUPostaltype" value="postal" checked="checked">
                                                                        <label for="carrierlabel1eucountry"></label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        {{-- <div class="navbar-brand logo">
                                        <img src="{{ asset('public/label/royal-mail-logo.png') }}" height="80">
                                    </div> --}}
                                      <!-- <div class="return-content-list">
                                          <h3 class="m-0 font-weight-normal">{{googleTranslate('You can drop your parcel off at any Post Office.')}}</h3>
                                          <div class="form-group col-md-5 m-0"></div>
                                      </div>
-->

                                      

                                        


                                  </div>
                                  {{-- <div class="step-content-form">
                                      <h3>{{googleTranslate('Courier')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <div class="PBCheckbox">
                                                    <input id="card-select1" type="radio" name="carrier" value="courier" checked="checked">
                                                    <label for="card-select1">
                                                        <div class="carrier-text">
                                                            <h2>{{googleTranslate('Courier')}}</h2>
                                                            <p>{{googleTranslate('Max 10kg Max 40cm x 55cm x 53cm')}}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="navbar-brand logo">
                                            <img src="{{ asset('public/label/DPD_logo.png') }}" height="60">
                                        </div>
                                      <div class="return-content-list">
                                          <h3 class="m-0 font-weight-normal">{{googleTranslate('You can drop your parcel off at any Post Office.')}}</h3>
                                          <div class="form-group col-md-5 m-0"></div>
                                      </div>
                                  </div> --}}
                              </div>
                              
                          </div>
                      </div>

                        
                  </div>
            </div>

            <input type="hidden" name="servicecode" value="" id="servicecode">

            <div class="tab-pane noneucountrycarrieritem">
                <div class="step-content-form-box">
                    <div class="step-content-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                                <div class="step-content-form">
                                    <h3>{{googleTranslate('Select available return option')}}</h3>
                                      <!-- <h3>{{googleTranslate('Postal')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <div class="PBCheckbox">
                                                    <input id="card-select" type="radio" name="carrier" value="postal">
                                                    <label for="card-select">
                                                        <div class="carrier-text">
                                                            <h2>{{googleTranslate('Postal')}}</h2>
                                                            <p>{{googleTranslate('Max 10kg Max 40cm x 55cm x 53cm')}}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div> -->

                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <!-- <div class="PBCheckbox">
                                                    <input id="Qr code label-no printer needed" type="radio" name="carrier" value="postal">
                                                    <label for="Qr code label-no printer needed">
                                                        <div class="carrier-text">
                                                            <h2>Qr code label-no printer needed</h2>
                                                        </div>
                                                    </label>
                                                </div> -->

                                                <div class="carrier-table">
                                                    <h4>{{googleTranslate('QR Code label – no printer needed')}}</h4>
                                                    <table class="table table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align: center;width:25%;">{{googleTranslate('Service')}} </th>
                                                                <th style="width:25%;">{{googleTranslate('Max Weight/')}} {{googleTranslate('Dimensions (LxWxH)')}}</th>
                                                                <th style="text-align: center;width:10%;">{{googleTranslate('Device needed')}} </th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Price')}}</th>
                                                                <th style="text-align: center; width:10%;">{{googleTranslate('Drop off Locations')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Tracking available')}}</th>

                                                                <th style="text-align: center; width:5%;"></th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <tr>
                                                                
                                                                <td>
                                                                    <div class="carrier-label-card">
                                                                        <div class="carrier-label-card-image">
                                                                            <img src="{{ asset('public/label/InPost_Logo_yellow.png') }}">
                                                                        </div>
                                                                        <div class="carrier-label-card-text">24/7 InPost Lockers:<br> Drop off <br> No Printer Required <br> 1-3 Days</div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <p class="">15kg</p>
                                                                    <p>41cm x 38cm x 64cm</p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                <div class="carrier-table-icon">
                                                                  <img src="{{ asset('public/label/QR_CODE.jpg') }}">
                                                              </div>
                                                                </td>
                                                                <td style="text-align: center;vertical-align: middle;">0.00 GBP</td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                        <a href="https://inpost.co.uk/lockers/?utm_source=Shipcycle&utm_medium=confirmation_page&utm_campaign=instant_returns" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking"> 
                                                                          <img src="{{ asset('public/label/tracking.jpg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>
                                                                <td>
                                                                    <div class="PBradio1">
                                                                        <input id="carrierlabel2"  class="carrierlabel2" type="radio" name="Postaltype" value="QRCode" checked="checked">
                                                                        <label for="carrierlabel2"></label>
                                                                    </div>
                                                                </td>

                                                            </tr>

                                                            <tr class="carrierasda">
                                                                
                                                                <td>
                                                                    <div class="carrier-label-card">
                                                                        <div class="carrier-label-card-image">
                                                                            <img src="{{ asset('public/label/Asda_toyou_Logo_-_High_Res.png') }}">
                                                                        </div>
                                                                        <div class="carrier-label-card-text">Asda toyou locations:<br> Drop off<br> No Printer Required </div>
                                                                    </div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <p class="">25kgs</p>
                                                                    <p>90cm x 60 cm x 60cm</p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                <div class="carrier-table-icon">
                                                                  <img src="{{ asset('public/label/QR_CODE.jpg') }}">
                                                              </div>
                                                                </td>
                                                                <td style="text-align: center;vertical-align: middle;">0.00 GBP</td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                        <a href="https://www.toyou.co.uk/locations" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>

                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking" target="_blank"target="_blank"> <img src="{{ asset('public/label/tracking.jpg') }}"></a>
                                                                  </div>
                                                                </td>

                                                                <td>
                                                                    <div class="PBradio1">
                                                                        <input id="carrierlabel3" type="radio" name="Postaltype" value="QRCode">
                                                                        <label for="carrierlabel3"></label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="carrier-item">
                                                <!-- <div class="PBCheckbox">
                                                    <input id="Printed label required" type="radio" name="carrier" value="postal">
                                                    <label for="Printed label required">
                                                        <div class="carrier-text">
                                                            <h2>Printed label required</h2>
                                                        </div>
                                                    </label>
                                                </div> -->

                                                <div class="carrier-table">
                                                    <h4>{{googleTranslate('Printed label required')}}</h4>
                                                    <table class="table table-responsive">
                                                        <thead>
                                                            <tr>
                                                                
                                                                <th style="text-align: center;width: 25%;">{{googleTranslate('Service')}}</th>
                                                                <th style="width: 25%;">{{googleTranslate('Max Weight/')}} {{googleTranslate('Dimensions')}}<br>{{googleTranslate('(LxWxH)')}}</th>
                                                                <th style="text-align: center;width: 10%;">{{googleTranslate('Printer Required')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Price')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Drop off Locations')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Tracking available')}}</th>
                                                                <th style="text-align: center; width: 5%;"></th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <tr>
                                                                
                                                                <td>
                                                                    <div class="carrier-label-card">
                                                                        <div class="carrier-label-card-image">
                                                                            <img src="{{ asset('public/label/royal-mail-logo.png') }}">
                                                                        </div>
                                                                        <div class="carrier-label-card-text">Royal Mail <br>Drop off Location</div>
                                                                    </div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <p class="">20kg</p>
                                                                    <p>61cm x 46cm x 46cm</p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <img src="{{ asset('public/label/Printer.jpg') }}">
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;vertical-align: middle;">3.95 GBP</td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://www.royalmail.com/services-near-you#/" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking" target="_blank"target="_blank"> <img src="{{ asset('public/label/tracking.jpg') }}"></a>
                                                                  </div>
                                                                </td>


                                                                <td>
                                                                    <div class="PBradio1">
                                                                        <input id="carrierlabel1" class="carrierlabel1" type="radio" name="Postaltype" value="postal" >
                                                                        <label for="carrierlabel1"></label>
                                                                    </div>
                                                                </td>
                                                            
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        {{-- <div class="navbar-brand logo">
                                        <img src="{{ asset('public/label/royal-mail-logo.png') }}" height="80">
                                    </div> --}}
                                      <!-- <div class="return-content-list">
                                          <h3 class="m-0 font-weight-normal">{{googleTranslate('You can drop your parcel off at any Post Office.')}}</h3>
                                          <div class="form-group col-md-5 m-0"></div>
                                      </div>
-->

                                      

                                        


                                  </div>
                                  {{-- <div class="step-content-form">
                                      <h3>{{googleTranslate('Courier')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <div class="PBCheckbox">
                                                    <input id="card-select1" type="radio" name="carrier" value="courier" checked="checked">
                                                    <label for="card-select1">
                                                        <div class="carrier-text">
                                                            <h2>{{googleTranslate('Courier')}}</h2>
                                                            <p>{{googleTranslate('Max 10kg Max 40cm x 55cm x 53cm')}}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="navbar-brand logo">
                                            <img src="{{ asset('public/label/DPD_logo.png') }}" height="60">
                                        </div>
                                      <div class="return-content-list">
                                          <h3 class="m-0 font-weight-normal">{{googleTranslate('You can drop your parcel off at any Post Office.')}}</h3>
                                          <div class="form-group col-md-5 m-0"></div>
                                      </div>
                                  </div> --}}
                              </div>
                              
                          </div>
                      </div>
                  </div>
            </div>


            <div class="tab-pane uscountrycarrieritem">
                <div class="step-content-form-box">
                    <div class="step-content-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                                <div class="step-content-form">
                                    <h3>{{googleTranslate('Select available return option')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">

                                                <div class="carrier-table">
                                                    <h4>{{googleTranslate('Printed label required')}}</h4>
                                                    <table class="table table-responsive">
                                                        <thead>
                                                            <tr>
                                                                
                                                                <th style="text-align: center;width: 25%;">{{googleTranslate('Service')}}</th>
                                                                <th style="width: 25%;">{{googleTranslate('Max Weight/')}} {{googleTranslate('Dimensions')}} {{googleTranslate('(LxWxH)')}}</th>
                                                                <th style="text-align: center;width: 10%;">{{googleTranslate('Printer Required')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Price')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Drop off Locations')}}</th>
                                                                <th style="text-align: center; width: 10%;">{{googleTranslate('Tracking available')}}</th>
                                                                <th style="text-align: center; width: 5%;"></th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="carrier-label-card carrier-label-card-image-us">
                                                                        <div class="carrier-label-card-image">
                                                                            <img src="{{ asset('public/label/UPS_logo.jpg') }}">
                                                                        </div>
                                                                        <div class="carrier-label-card-text">UPS <br>Drop off Location</div>
                                                                    </div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <p class="">20kg</p>
                                                                    <p>61cm x 46cm x 46cm</p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <img src="{{ asset('public/label/Printer.jpg') }}">
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;vertical-align: middle;">25 USD</td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://www.theupsstore.com/tools/find-a-store" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking" target="_blank"target="_blank"> <img src="{{ asset('public/label/tracking.jpg') }}"></a>
                                                                  </div>
                                                                </td>


                                                                <td>
                                                                    <div class="PBradio1">
                                                                        <input id="carrieruslabel1" class="carrieruslabel1" type="radio" name="USPostaltype" value="postal" checked>
                                                                        <label for="carrieruslabel1"></label>
                                                                    </div>
                                                                </td>
                                                            
                                                            </tr>
                                                        <tr>
                                                                <td>
                                                                    <div class="carrier-label-card carrier-label-card-image-us">
                                                                        <div class="carrier-label-card-image">
                                                                            <img src="{{ asset('public/label/usps_logo.jpg') }}">
                                                                        </div>
                                                                        <div class="carrier-label-card-text">USPS <br>Drop off Location</div>
                                                                    </div>
                                                                </td>
                                                                
                                                                <td>
                                                                    <p class="">20kg</p>
                                                                    <p>61cm x 46cm x 46cm</p>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <img src="{{ asset('public/label/Printer.jpg') }}">
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;vertical-align: middle;">15 USD</td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://tools.usps.com/find-location.htm" target="_blank">
                                                                          <img src="{{ asset('public/label/google-map-icon.svg') }}">
                                                                      </a>
                                                                  </div>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <div class="carrier-table-icon">
                                                                      <a href="https://returns.shipcycle.ecomglobalsystems.com/jaded/tracking" target="_blank"target="_blank"> <img src="{{ asset('public/label/tracking.jpg') }}"></a>
                                                                  </div>
                                                                </td>


                                                                <td>
                                                                    <div class="PBradio1">
                                                                        <input id="carrieruspslabel1" class="carrieruspslabel1" type="radio" name="USPostaltype" value="postal" >
                                                                        <label for="carrieruspslabel1"></label>
                                                                    </div>
                                                                </td>
                                                            
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        {{-- <div class="navbar-brand logo">
                                        <img src="{{ asset('public/label/royal-mail-logo.png') }}" height="80">
                                    </div> --}}
                                      


                                  </div>
                                  {{-- <div class="step-content-form">
                                      <h3>{{googleTranslate('Courier')}}</h3>
                                        <div class="carrier-list">
                                            <div class="carrier-item">
                                                <div class="PBCheckbox">
                                                    <input id="card-select1" type="radio" name="carrier" value="courier" checked="checked">
                                                    <label for="card-select1">
                                                        <div class="carrier-text">
                                                            <h2>{{googleTranslate('Courier')}}</h2>
                                                            <p>{{googleTranslate('Max 10kg Max 40cm x 55cm x 53cm')}}</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="navbar-brand logo">
                                            <img src="{{ asset('public/label/DPD_logo.png') }}" height="60">
                                        </div>
                                      <div class="return-content-list">
                                          <h3 class="m-0 font-weight-normal">{{googleTranslate('You can drop your parcel off at any Post Office.')}}</h3>
                                          <div class="form-group col-md-5 m-0"></div>
                                      </div>
                                  </div> --}}
                              </div>
                              
                          </div>
                      </div>
                  </div>
            </div>
        </div>
    </div>
</div>
    
    <div class="package-item-btn">
        <button type="submit" class="btn save-waybill">Submit</button>
    </div>
    </div>

    
</form>