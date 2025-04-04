@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {            
        // add more package
        var increment = 1;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click','.add-more-package',function(){
            let cl = '';
            let radioValue = $("input[name='drop_off']:checked").val();
            if(radioValue == 'By_ReturnBar'){
                cl = 'item-image';
            }

            let package = `<div class="return-form-item add-${increment}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Item Bar Code </label>
                            <input type="text" class="form-control item-barcode" name="bar_code[]" placeholder="Enter Item Bar Code" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Item Name </label>
                            <input type="text" class="form-control item-title" name="title[]" placeholder="Enter Product Description" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Quantity</label>
                            <input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Quantity" value="" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Reason for Return</label>
                            <select class="form-control valid itm-rtn" name="item_return_reason[]" aria-invalid="false">
                                <option value="">-- Select a Reason--</option>
                                <option value="Does Not Suit">Does Not Suit</option>
                                <option value="Too Small">Too Small</option>
                                <option value="Too Large">Too Large</option>
                                <option value="Dissatisfied With Quality">Dissatisfied With Quality</option>
                                <option value="Faulty">Faulty</option>
                                <option value="Received Incorrect Item">Received Incorrect Item</option>
                                <option value="Looks Different Than Expected">Looks Different Than Expected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Color</label>
                            <input type="text" class="form-control clr_arr" name="color[]" placeholder="Color" value="">
                        </div>
                    </div>                      
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Size</label>
                            <input type="text" class="form-control sze_ar" name="size[]" placeholder="Size" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Dimension</label>
                            <select class="form-control valid" name="dimension[]" aria-invalid="false">
                                <option value="CM">CM</option>
                                <option value="IN">IN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Length</label>
                            <input type="text" class="form-control" name="length[]" placeholder="Length" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Width</label>
                            <input type="text" class="form-control" name="width[]" placeholder="Width" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Height</label>
                            <input type="text" class="form-control" name="height[]" placeholder="Height" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Weight Unit</label>
                            <select class="form-control valid" name="weight_unit_type[]" aria-invalid="false">
                                <option value="LBS">LBS</option>
                                <option value="KGS">KGS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Weight</label>
                            <input type="text" class="form-control" name="weight[]" placeholder="Weight" value="">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="">Select Images (jpeg/png)  *Uploading the Image will expedite the Return Process</label>
                            <input class="form-control img-itm ${cl}" id="image-upload-${increment}" type="file" name="item_images[${increment}][]" accept="image/*" multiple>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="charged__weight[]" value="1">
                    <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
                    <div class="col-md-2">
                        <button type="subbmit" class="btn btn-sm btn-danger delete-package" data-id="${increment}"><i class="la la-trash"></i></button>
                    </div>
                </div>
            </div>`;
            increment++;
            $('.return-form-info-content').append(package);
        });

        $('body').on('click','.delete-package',function(){
            let id = $(this).data('id');
            $('.add-'+id).remove();
        });

        // $('input[name=address]').change(function(){
        $('body').on('change','input[name=address]',function(){
            let name = $(this).data('name');
            let phone = $(this).data('phone');
            let address = $(this).data('address');
            let country = $(this).data('country');
            let state = $(this).data('state');
            let city = $(this).data('city');
            let zip = $(this).data('zip');
            $('#customer_name').val(name);
            $('#customer_phone').val(phone);
            $('#customer_address').val(address);
            $('#customer_country').val(country);
            $('#customer_state').val(state);
            $('#customer_city').val(city);
            $('#customer_pincode').val(zip);

            $('#By_Mail').removeAttr('disabled');
            if(country == 'US' || country == 'United States'){
                $('#By_ReturnBar').removeAttr('disabled');
                let radioValue = $("input[name='drop_off']:checked").val();
                if (radioValue == 'By_ReturnBar') {
                    if(zip){
                        $.ajax({
                            url:"{{ route('return-bar') }}",
                            type:"GET",
                            data : {zip:zip},
                            dataType:"json",
                            async:true,
                            crossDomain:true,
                            beforeSend: function() {
                                // setting a timeout
                                $('.spinner-border').removeClass('collapse');
                            },
                            success:function(response){
                                // console.log(response);
                                if(response.status){
                                    $('#return-bar').html(response.html);
                                } else {
                                    $('#return-bar').html(response.message);
                                }
                            },
                            complete: function() {
                                $('.spinner-border').addClass('collapse');
                            },
                        });
                    } else {
                        $(this).prop("checked", false);
                        alert('Please select first address');
                    }
                }
            } else {
                $('#By_ReturnBar').attr("disabled", true);
                $('#By_ReturnBar').prop('checked', false);
                $('#return-bar').html('');
            }
        });

        //  return bar option...
        $('input[name=drop_off]').change(function(){
            let v = $(this).val();
            let zip = $('#customer_pincode').val();
            if(v == 'By_ReturnBar'){                
                $('.img-itm').addClass('item-image');
                if(zip){
                    $.ajax({
                        url:"{{ route('return-bar') }}",
                        type:"GET",
                        data : {zip:zip},
                        dataType:"json",
                        async:true,
                        crossDomain:true,
                        beforeSend: function() {
                            // setting a timeout
                            $('.spinner-border').removeClass('collapse');
                        },
                        success:function(response){
                            // console.log(response);
                            if(response.status){
                                $('#return-bar').html(response.html);
                            } else {
                                $('#return-bar').html(response.message);
                            }
                        },
                        complete: function() {
                            $('.spinner-border').addClass('collapse');
                        },
                    });
                } else {
                    $(this).prop("checked", false);
                    alert('Please select first address');
                }
            } else {
                $('#return-bar').html('');
                $('.img-itm').removeClass('item-image');
                $('.img-itm').removeAttr('style');
            }
        });

        //----------------------------------------------------------------------------------------------        
        $("#create-waybill").validate({
            rules: {
                client_id: {required:true, maxlength: 50 },
                way_bill_number: {required:true, maxlength: 191 },
                reason_of_return: { required:true},
                address: { required:true},
                drop_off: { required:true},
            },
            messages: {
                client_id: {required: "Please Select Etailer Name."},
                way_bill_number: {required: "Please Enter Order Number."},
                reason_of_return: {required: "Please Select Reason For Return."},
                address: {required: "Please Select Customer Address."},
                drop_off: {required: "Please Select Return Option."},
            },                
            errorElement: "li",
            focusInvalid: false,
            onkeyup: false,
            focusCleanup: true,
            onfocusout: false,
            errorPlacement: function ( error, element ) {
                // error.addClass( "text-danger" );
                // error.insertAfter( element );
                $("#errorContainer").show();
                error.appendTo($("#errorLabelContainer"));
            },
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top-10
                  }, 1000);
            },
            success: function() {
                return false;
            },
            submitHandler: function(form) {                
                let ror = ci = status = clr = sze = false;
                status = check_validation('item-barcode');
                status = check_validation('item-title');
                status = check_validation('package_count_arr');
                status = check_validation('length_arr');
                status = check_validation('width_arr');
                status = check_validation('height_arr');
                status = check_validation('weight_arr');
                ci = check_image('item-image');
                clr = check_validation('clr_arr');
                sze = check_validation('sze_ar');
                ror = check_reason('itm-rtn');
                if(status || ror || ci || clr || sze){
                    console.log(status);
                    return false;
                } else {
                    form.submit();
                    console.log(status);
                }
            }
        });

        $('body').on('click','.close',function(){
            $('#errorLabelContainer').html();
            $("#errorContainer").hide();
        });
    });

    function check_validation(cls){
        let status = false;
        $('.'+cls).each(function(i,v){
            let value = $(this).val();
            if(value!=0 && value!=''){
                $(this).removeAttr('style');
            }else{
                status = true;
                $(this).css('border-color','#ff0000');
            }
        });
        return status;
    }

    function check_image(cls){
        let status = false;
        $('.'+cls).each(function(i,v){
            let value = $(this).val();
            if(value!=0 && value!=''){
                $(this).removeAttr('style');
            }else{
                status = true;
                $(this).css('border-color','#ff0000');
            }
        });

        return status;
    }

    function check_reason(cls){
        let status = false;
        $('.'+cls).each(function(i,v){
            let value = $(this).val();
            if(value!=0 && value!=''){
                $(this).removeAttr('style');
            }else{
                status = true;
                $(this).css('border-color','#ff0000');
            }
        });

        return status;
    }
</script>
@endpush

{{-- banner section --}}
<section class="banner-sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <img src="{{ asset('images/Banner-reverseGear.jpg') }}">
                <div class="trolly">
                    <img src="{{ asset('images/shopping-trolly.png') }}">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="request-section return-request-sec">
    <div class="container">
        <div class="address-heading">
            <h4>Return Request</h4>
        </div>
        <div class="mt-1">
            <div class="box no-border" id="errorContainer">
                <div class="box-tools">
                    <div class="col-md-12 alert alert-warning alert-dismissible">
                        <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                        <ul id="errorLabelContainer"></ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form method="post" id="create-waybill" class="return-form" autocomplete="off" enctype="multipart/form-data" action="{{ route('customer.return-request.store') }}">
                    @csrf
                    <!-- default hidden fields -->
                    <input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
                    <input type="hidden" name="client_code" value="REVERSEGEAR">
                    <input type="hidden" name="customer_code" value="00000">
                    <input type="hidden" name="payment_mode" value="PAID">
                    <input type="hidden" name="actual_weight" value="1">
                    {{-- address details --}}
                    <input type="hidden" id="customer_name" name="customer_name" value="">
                    <input type="hidden" id="customer_phone" name="customer_phone" value="">
                    <input type="hidden" id="customer_email" name="customer_email" value="{{ Auth::user()->email }}">
                    <input type="hidden" id="customer_address" name="customer_address" value="">
                    <input type="hidden" id="customer_country" name="customer_country" value="">
                    <input type="hidden" id="customer_state" name="customer_state" value="">
                    <input type="hidden" id="customer_city" name="customer_city" value="">
                    <input type="hidden" id="customer_pincode" name="customer_pincode" value="">
                                        
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="request-list-section">
                                        <div class="add-address-card">
                                            <div class="add-address-inner">
                                                <h2>Customer Address</h2>
                                                <a class="btn-address" href="{{ route('customer.address') }}">+ Add Address</a>
                                            </div>
                                        </div>
                                        <div class="address-info-scroll">
                                            <ul class="address-info-card" id="address-card">
                                                @forelse($address as $addr)
                                                    <li class="address-info-inner-card">
                                                        <div class="address-info-check">
                                                            <input type="radio" id="{!! $addr->id !!}" name="address" value="{!! $addr->id !!}" data-type="{!! $addr->type !!}" data-name="{!! $addr->name !!}" data-phone="+{{ $addr->country->phonecode}}{!! $addr->phone !!}" data-address="{!! $addr->address_1 !!} {!! $addr->address_2 !!}" data-country="{!! $addr->country->sortname !!}" data-state="{!! $addr->state !!}" data-city="{!! $addr->city !!}" data-zip="{!! $addr->zip !!}">
                                                            <label for="{!! $addr->id !!}"></label>
                                                        </div>
                                                        <div class="address-info-text">
                                                            <h2 class="name">{!! $addr->name !!}</h2>
                                                            <p class="phone">+{{ $addr->country->phonecode}}{!! $addr->phone !!}</p>
                                                            <div class="type address-type">
                                                                <h3>{!! $addr->type !!}</h3>
                                                                <p class="address">{!! $addr->address_1 !!} {!! $addr->address_2 !!}, {!! $addr->city !!} {!! $addr->state !!} , {!! get_country_name_by_id($addr->country_id) !!}, {!! $addr->zip !!}</p>  
                                                            </div>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="address-info-inner-card">No Address added. Click on "Add Address" button to add the address.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end address div --}}

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="request-list-section">
                                        <div class="Shipment-card">
                                            <h2>Shipment Details</h2>
                                            <div class="address-info-scroll-1">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                                        <div class="form-group Etailer-info">
                                                            <label for="">Etailer Name</label>
                                                            <select name="client_id" id="client_id_change" class="form-control">
                                                                <option value="">-- Select Etailer --</option>
                                                                @forelse($client_list as $client)
                                                                    <option value="{{ $client->id }}" {{ (old("client_id") == $client->id ? "selected":"") }}>{{ $client->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                            <div class="client_id_change-media">
                                                                <img src="{{ asset('images/Picture1.jpg') }}" class="img-responsive" style="width: 45px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Order Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Order Number" value="{{old('way_bill_number')}}" id="way_bill_number">
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">RMA Number</label>
                                                            <input type="text" class="form-control" name="rma_number" placeholder="Enter RMA Number" value="{{old('rma_number')}}">
                                                        </div>
                                                    </div>      
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Remarks</label>
                                                            <textarea name="remark" placeholder="Enter Remarks" class="form-control" rows="5">{{old('remark')}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end shipment div --}}                                
                    </div>

                    @if(count($address) > 0)
                        <div class="container">
                            <div class="form-group mt-2">
                                <label for="">Select Return Option</label>
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
                                            <input type="radio" id="By_Mail" name="drop_off" value="By_Mail/Courier" disabled="disabled">
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
                    @endif

                    {{--  return bar address listing--}}
                    <div class="container" style="overflow: auto;">
                        <div class="row flex-row flex-nowrap" id="return-bar"></div>
                    </div>

                    {{-- item div --}}
                    <div class="info-list-section">
                        <h2>Item Details</h2>
                        <div class="return-form-info">
                            <div class="return-form-info-content" id="item-card">
                                <div class="return-form-item add-0">
                                	<div class="row">
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Item Bar Code	</label>
                                				<input type="text" class="form-control item-barcode" name="bar_code[]" placeholder="Enter Item Bar Code" value="{{old('bar_code[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Item Name	</label>
                                				<input type="text" class="form-control item-title" name="title[]" placeholder="Enter Product Description" value="{{old('title[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Quantity</label>
                                				<input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Quantity" value="{{old('package_count[]')}}" autocomplete="off">
                                			</div>
                                		</div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Reason for Return</label>
                                                <select class="form-control valid itm-rtn" name="item_return_reason[]" aria-invalid="false">
                                                    <option value="">-- Select a Reason--</option>
                                                    <option value="Does Not Suit">Does Not Suit</option>
                                                    <option value="Too Small">Too Small</option>
                                                    <option value="Too Large">Too Large</option>
                                                    <option value="Dissatisfied With Quality">Dissatisfied With Quality</option>
                                                    <option value="Faulty">Faulty</option>
                                                    <option value="Received Incorrect Item">Received Incorrect Item</option>
                                                    <option value="Looks Different Than Expected">Looks Different Than Expected</option>
                                                </select>
                                            </div>
                                        </div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Color</label>
                                				<input type="text" class="form-control clr_arr" name="color[]" placeholder="Color" value="{{old('color[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Size</label>
                                				<input type="text" class="form-control sze_ar" name="size[]" placeholder="Size" value="{{old('size[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Dimension</label>
                                				<select class="form-control valid" name="dimension[]" aria-invalid="false">
                                                    <option value="CM">CM</option>
                                                    <option value="IN">IN</option>
                                                </select>
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Length</label>
                                				<input type="text" class="form-control" name="length[]" placeholder="Length" value="{{old('length[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Width</label>
                                				<input type="text" class="form-control" name="width[]" placeholder="Width" value="{{old('width[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Height</label>
                                				<input type="text" class="form-control" name="height[]" placeholder="Height" value="{{old('height[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Weight Unit</label>
                                				<select class="form-control valid" name="weight_unit_type[]" aria-invalid="false">
                                                    <option value="LBS">LBS</option>
                                                    <option value="KGS">KGS</option>
                                                </select>
                                			</div>
                                		</div>
                                		<div class="col-md-2">
                                			<div class="form-group">
                                				<label for="">Weight</label>
                                				<input type="text" class="form-control" name="weight[]" placeholder="Weight" value="{{old('weight[]')}}">
                                			</div>
                                		</div>
                                		<div class="col-md-7">
                                			<div class="form-group">
                                				<label for="">Select Images (<span style="color: #B51F38;">* If you are returning via a Return Bar, please upload a photograph of the item you are returning</span>)</label>
                                				<input class="form-control img-itm" id="image-upload-0" type="file" name="item_images[0][]" accept="image/*" multiple>
                                			</div>
                                		</div>
                                		<input type="hidden" class="form-control" name="charged__weight[]" value="1">
                                        <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
                                	</div>
                                </div>
                            </div>
                        </div>
                        <div class="return-form-footer row">
                            <div class="col-md-6 col-sm-6">
                                <button type="button" class="btn-red add-more-package">+ Add More</button>
                            </div>
                            <div class="col-md-6 col-sm-6 text-right">
                                <button type="button" class="btn-red pull-right save-waybill">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>