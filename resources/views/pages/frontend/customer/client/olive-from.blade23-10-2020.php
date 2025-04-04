@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });        

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


        // -------------------olive data--------------------------
        $('body').on('click','#fetch-product',function(){
            let code = $('#client_code').val();
            let id = $('#way_bill_number').val();
            let ordr_id = $('#order_no').val();
            let is_data = $('#is_data').val();

            // let mail = '{{ Auth::user()->email }}';
            let mail = $('#customer_email').val();
            let cde = $('#rma_code').val();
            if(id != '' && mail != ''){
                $.ajax({
                    url:"{{ route('customer.fetch.product') }}",
                    type:"GET",
                    data : {code:code, id:id, email: mail, rma_code: cde},
                    dataType:"json",
                    async:true,
                    crossDomain:true,
                    beforeSend: function() {
                        $('#load').removeClass('collapse');
                    },
                    success:function(response){
                        // console.log(response);
                        if(response.status){
                            $('#no-add').remove();
                            if (is_data) {
                                $('#address-card').append(response.add_html);
                            } else {
                                $('#address-card').append(response.add_html);
                            }                            
                            $('#item-card').html(response.item_html);
                            $('#order_no').val(id);
                            $('#is_data').val('yes');
                        } else{
                            // alert(response.msg);
                            $("#errorContainer").show();
                            $("#errorLabelContainer").html('<li>'+response.msg+'</li>');
                        }
                    },
                    complete: function() {
                        $('#load').addClass('collapse');
                    },
                });
            } else {
                $("#errorContainer").show();
                $("#errorLabelContainer").html('<li>Please Enter the Order Number and Email.</li>');
            }
        });

        $('body').on('click','.item-chk',function(){
            var checked = $(this).is(':checked');
            var val = $(this).val();
            let radioValue = $("input[name='drop_off']:checked").val();
            
            if(checked){
                $('#itm-rtn-'+val).addClass('itm-rtn');
            } else {
                $('#itm-rtn-'+val).removeClass('itm-rtn');
            }

            if (checked && radioValue == 'By_ReturnBar') {
                $('#image-upload-'+val).addClass('item-image');
            } else {
                $('#image-upload-'+val).removeClass('item-image');
            }
        });

        $('body').on('click','#email_chk',function(){
            var checked = $(this).is(':checked');
            var eml = $('#email_id').val();
            
            if(checked){
                $('#customer_email').val(eml)
            } else {
                $('#customer_email').val('')
            }
        });

        $('body').on('click','.close',function(){
            $('#errorLabelContainer').html();
            $("#errorContainer").hide();
        });
        
        //----------------------------------------------------------------------------------------------        
        $("#create-waybill").validate({
            rules: {
                client_id: {required:true, maxlength: 50 },
                way_bill_number: {required:true, maxlength: 191 },
                reason_of_return: { required:true},
                address: { required:true},
                drop_off: { required:true},
                'item-select[]': { required: true, minlength: 1 }
            },
            messages: {
                client_id: {required: "Please Select Etailer Name."},
                way_bill_number: {required: "Please Enter Order Number."},
                reason_of_return: {required: "Please Select Reason For Return."},
                address: {required: "Please Select Customer Address."},
                drop_off: {required: "Please Select Return Option."},
                "item-select[]": "You must check at least 1 product checkbox",
            },                
            errorElement: "li",
            focusInvalid: false,
            onkeyup: false,
            focusCleanup: true,
            onfocusout: false,
            errorPlacement: function ( error, element ) {
                $("#errorContainer").show();
                error.appendTo($("#errorLabelContainer"));
            },
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                $('html, body').animate({
                    scrollTop: $('#errorLabelContainer').offset().top-150
                  }, 1000);
            },
            success: function() {
                return false;
            },
            submitHandler: function(form) {                
                let ror = ci = false;
                ror = check_reason('itm-rtn');
                ci = check_image('item-image');
                if(ror || ci){
                    console.log('is true');
                    return false;
                } else {
                    form.submit();
                    console.log('is false');
                }
            }
        });
    });

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
                <img src="{{ asset('images/Olive-Reverse-Gear-Banner.jpg') }}">
                {{-- <div class="trolly">
                    <img src="{{ asset('images/shopping-trolly.png') }}">
                </div> --}}
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
                    {{-- checkc order data --}}
                    <input type="hidden" id="order_no" value="">
                    <input type="hidden" id="is_data" value="">
                    <!-- default hidden fields -->
                    <input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
                    <input type="hidden" name="client_code" value="REVERSEGEAR">
                    <input type="hidden" name="customer_code" value="00000">
                    <input type="hidden" name="payment_mode" value="PAID">
                    <input type="hidden" name="actual_weight" value="1">
                    {{-- address details --}}
                    <input type="hidden" id="customer_name" name="customer_name" value="">
                    <input type="hidden" id="customer_phone" name="customer_phone" value="">
                    <input type="hidden" id="email_id" name="email_id" value="{{ Auth::user()->email }}">
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
                                                    <li class="address-info-inner-card" id="no-add">No Address added. Click on "Add Address" button to add the address.</li>
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
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="" class="checkbox-inline"><input type="checkbox" name="email_chk" id="email_chk" value="1"> Tick here if your Reverse Gear registered email is the same used for your Olive order, otherwise please enter the email used for your Olive order below</label>
                                                        </div>
                                                    </div>                                                   
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Email ID</label>
                                                            <input type="text" class="form-control" name="customer_email" value="{{old('email_id')}}" id="customer_email">
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Order Number <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Order Number" value="{{old('way_bill_number')}}" id="way_bill_number">
                                                            <input type="hidden" name="client_id" value="{{ $client_data->id }}" id="client_id_change">
                                                            <input type="hidden" name="client_code" value="{{ $client_data->user_code }}" id="client_code">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">RMA Number</label>
                                                            <input type="text" class="form-control" name="rma_number" id="rma_code" placeholder="Enter RMA Code" value="{{old('rma_number')}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12 collapse">
                                                        <div class="form-group">
                                                            <label for="">Reason For Return <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="reason_of_return">
                                                                <option value="">Blank</option>
                                                                <option value="WP">Wrong Product</option>
                                                                <option value="WS">Wrong Size</option>
                                                                <option value="WC">Wrong Color</option>
                                                                <option value="DP">Damage Product</option>
                                                                <option value="DONT_LIKE_IT">Don't Like It</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Remarks</label>
                                                            <textarea name="remark" placeholder="Enter Remarks" class="form-control" rows="5">{{old('remark')}}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <button type="button" id="fetch-product" class="btn btn-sm btn-red mt-2">Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
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

                    {{--  return bar address listing--}}
                    <div class="container" style="overflow: auto;">
                        <div class="row flex-row flex-nowrap" id="return-bar"></div>
                    </div>

                    {{-- item div --}}
                    <div class="info-list-section">
                        <h2>Item Details <small style="color: #000;">(* Please click on the checkbox to return one or more items.)</small></h2>
                        <div class="return-form-info">
                            <div class="return-form-info-content" id="item-card"></div>
                        </div>
                        <div class="return-form-footer row">
                            <div class="col-md-6 col-sm-6">
                                {{-- <button type="button" class="btn-red add-more-package">+ Add More</button> --}}
                            </div>
                            <div class="col-md-6 col-sm-6 text-right">
                                <button type="submit" class="btn-red pull-right save-waybill">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>