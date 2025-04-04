@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // -------------------olive data--------------------------
        $('body').on('click','#fetch-product',function(){
            let id = $('#way_bill_number').val();
            let mail = $('#email_id').val();
            let cde = $('#rma_code').val();
            let cl_id = $('#client_id_change').val();

            if(cl_id != ''){
                if(id != '' && mail != ''){
                    $.ajax({
                        url:"{{ route('olive.fetch.order') }}",
                        type:"GET",
                        data : {id:id, email: mail, rma_code: cde},
                        dataType:"json",
                        async:true,
                        crossDomain:true,
                        beforeSend: function() {
                            $('#load').removeClass('collapse');
                        },
                        success:function(response){
                            if(response.status){
                                $('#address-content').html(response.add_html);
                                $('#item-card').html(response.item_html);
                                $('#item-div').removeClass('collapse');
                            } else{
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
            } else {
                $("#errorContainer").show();
                $("#errorLabelContainer").html('<li>Please Enter the Correct Order Number and Email.</li>');
            }            
        });

        $('body').on('click','.item-chk',function(){
            var checked = $(this).is(':checked');
            var val = $(this).val();
            let radioValue = $("input[name='drop_off']:checked").val();
            var qty = $('#slected_qty').text();
            var b = 1;
            
            if(checked){
                $('#itm-rtn-'+val).addClass('itm-rtn');
                var c = (+qty) + (+b);
                $('#slected_qty').text(c);
            } else {
                $('#itm-rtn-'+val).removeClass('itm-rtn');
                var c = qty - b;
                $('#slected_qty').text(c);
            }

            if (checked && radioValue == 'By_ReturnBar') {
                $('#image-upload-'+val).addClass('item-image');
            } else {
                $('#image-upload-'+val).removeClass('item-image');
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
                customer_email: { required:true},
                customer_name: { required:true},
                customer_phone: { required:true},
                customer_address: { required:true},
                customer_country: { required:true},
                customer_state: { required:true},
                customer_city: { required:true},
                customer_pincode: { required:true},
                drop_off: { required:true},
                'item-select[]': { required: true, minlength: 1 }
            },
            messages: {
                client_id: {required: "Please Select Etailer Name."},
                way_bill_number: {required: "Please Enter Order Number."},
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
                $('html, body').animate({scrollTop: $('#errorLabelContainer').offset().top-150}, 1000);
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
                    /*$('.save-waybill').prop('disabled', true);
                    $('.save-waybill').html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
                    form.submit();
                    console.log('is false');*/
                    var checked = $('#flexCheckDefault').is(':checked');
                    if (checked) {
                        $('.save-waybill').prop('disabled', true);
                        $('.save-waybill').html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
                        form.submit();
                    } else{
                        alert('Please confirm how may items you are returning.');
                    }
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
                <form method="post" id="create-waybill" class="return-form" autocomplete="off" enctype="multipart/form-data" action="{{ route('olive.order.create') }}">
                    @csrf
                    <!-- default hidden fields -->
                    <input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
                    <input type="hidden" name="client_code" value="REVERSEGEAR">
                    <input type="hidden" name="customer_code" value="00000">
                    <input type="hidden" name="payment_mode" value="PAID">
                    <input type="hidden" name="actual_weight" value="1">
                    <input type="hidden" name="charged_weight" value="1">
                    <input type="hidden" name="client_id" value="@if($client){!! $client->id !!}@endif" id="client_id_change">

                    <div class="row">
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
                                                            <label for="">Email ID <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="email_id" value="{{old('email_id')}}" id="email_id">
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
                                                            <input type="text" class="form-control" name="rma_number" id="rma_code" placeholder="Enter RMA Code" value="{{old('rma_number')}}">
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

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="request-list-section">
                                        <div class="Shipment-card">
                                            <h2>Customer Address</h2>
                                            <div class="address-info-scroll-1" id="address-content">
                                                <div class="row">
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Name</label>
                                                            <input type="text" class="form-control" name="customer_name" value="" id="customer_name">
                                                        </div>
                                                    </div>                                   
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Email</label>
                                                            <input type="text" class="form-control" name="customer_email" value="" id="customer_email">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Phone</label>
                                                            <input type="text" class="form-control" name="customer_phone" value="" id="customer_phone">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Address</label>
                                                            <input type="text" class="form-control" name="customer_address" value="" id="customer_address">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Country</label>
                                                            <select name="customer_country" id="customer_country" class="form-control" required="required">
                                                                <option value="">Choose your country</option>
                                                                @forelse($country as $cnt)
                                                                    <option value="{{ $cnt->sortname }}">{{ $cnt->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">State</label>
                                                            <input type="text" class="form-control" name="customer_state" value="" id="customer_state">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">City</label>
                                                            <input type="text" class="form-control" name="customer_city" value="" id="customer_city">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="">Pincode</label>
                                                            <input type="text" class="form-control" name="customer_pincode" value="" id="customer_pincode">
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
                            <label for="">Your Return Option</label>
                            <ul class="radioBtn">
                                <li class="row">                                    
                                    <div class="col-lg-4 address-info-check">
                                        <input type="radio" id="By_Mail" name="drop_off" value="By_Mail/Courier" checked="checked">
                                        <label for="By_Mail">
                                            <p>By Mail/Courier</p>
                                            <p class="color-grey">Create a UPS label to print and attach to your returning parcel.</p>
                                        </label>
                                    </div>
                                </li>  
                            </ul>
                        </div>
                    </div>

                    {{-- item div --}}
                    <div class="info-list-section collapse" id="item-div">
                        <h2>Item Details <small style="color: #000;">(* Please click on the checkbox to return one or more items.)</small></h2>
                        <div class="return-form-info">
                            <div class="return-form-info-content" id="item-card"></div>
                        </div>
                        <div class="return-form-footer row">
                            <div class="col-md-6 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">I confirm that I am returning <span id="slected_qty">0</span> items in one package.</label>
                                </div>
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