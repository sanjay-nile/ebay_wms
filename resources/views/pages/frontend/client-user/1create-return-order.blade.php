@include('pages.frontend.client-user.breadcrumb', ['title' => 'Create a New Return'])

@push('css')
<style type="text/css">
    #errorContainer{display: none;}
    .collapse{display: none;}
</style>
@endpush

@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/create-waywill.js') }}"></script>
<script>
    $(document).ready(function(){
        //-----------------------------------------------------------------
        $(document).on('change','#client_shipment_list',function(){
            if($(this).val()){
                let rate = $('option:selected', this).attr('rate');
                let carrier = $('option:selected', this).attr('carrier');
                let rate_div = `<div class="form-group">
                                <label for="">Rate</label>
                                <span class="form-control">${rate}</span>
                                <input type="hidden" name="rate" value="${rate}"/>
                            </div>`;
                let carrier_div = `<div class="form-group">
                                <label for="">Carrier</label>
                                <span class="form-control">${carrier}</span>
                            </div>`;
                $(".rate-id").html(rate_div);
                $(".carrier-div").html(carrier_div);
            }else{
                $(".rate-id").html('');
                $(".carrier-div").html('');
            } 
        });

        //------------------------------------------------------------------
        $('body').on('change','#customer_country',function(){
            let country_id = $('option:selected', this).attr('data-id');
            let client_id = $( "#client_id" ).val();
            if(client_id && country_id){
                $.ajax({
                    type:'get',
                    url :"{{ route('admin.client-warehouse') }}",
                    data : {client_id:client_id, country_id:country_id},
                    dataType : 'json',
                    success : function(data){
                        $("#client_warehouse_list").replaceWith(data.warehouse);
                    }
                });
            }else{
                $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
            }
        });

        // -------------------olive data--------------------------
        $('body').on('click','#fetch-product',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();
            let cde = $('#rma_code').val();

            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:"{{ route('client-user.fetch.product') }}",
                    type:"GET",
                    data : {id: id, email: mail, rma_code: cde},
                    dataType:"json",
                    async:true,
                    crossDomain:true,
                    beforeSend: function() {
                        $('#load').removeClass('collapse');
                    },
                    success:function(response){
                        // console.log(response);
                        if(response.status){
                            $("#errorContainer").hide();
                            $('#address-card').html(response.add_html);
                            $('#item-card').html(response.item_html);
                            $('#ftr-hide').addClass('collapse');

                            let country = $( "#customer_country option:selected" ).val();
                            if(country == 'US' || country == 'United States'){
                                $('#By_ReturnBar').removeAttr('disabled');
                            } else {
                                $('#By_ReturnBar').attr("disabled", true);
                                $('#By_ReturnBar').prop('checked', false);
                                $('#return-bar').html('');
                            }
                        } else{
                            $("#errorContainer").show();
                            $("#errorLabelContainer").html('<li>'+response.msg+'</li>');
                            $('#ftr-hide').removeClass('collapse');
                        }
                    },
                    complete: function() {
                        $('#load').addClass('collapse');
                    },
                });
            } else {
                $('#waywill_id').val();
                $("#errorContainer").show();
                $("#errorLabelContainer").html('<li>Order Email Id or Order Id is required.</li>');
            }
        });
        
        //  -------------------- return bar option -------------------
        $('input[name=drop_off]').change(function(){
            let v = $(this).val();
            let zip = $('#customer_pincode').val();
            if(v == 'By_ReturnBar'){
                $('#ship-div').addClass('collapse');
                if(zip){
                    $.ajax({
                        url:"{{ route('return-bar') }}",
                        type:"GET",
                        data : {zip:zip},
                        dataType:"json",
                        async:true,
                        crossDomain:true,
                        beforeSend: function() {
                            $('.spinner-border').removeClass('collapse');
                        },
                        success:function(response){
                            if(response.status){
                                $('#return-bar').html(response.html);
                            } else {
                                $('#return-bar').html(response.message);
                            }
                        },
                        complete: function() {
                            $('.spinner-border').addClass('collapse');
                            $('#rtn-location').removeClass('collapse');
                        },
                    });
                } else {
                    $(this).prop("checked", false);
                    alert('Please select first address');
                }
            } else {
                $('#rtn-location').addClass('collapse');
                $('#return-bar').html('');
                $('.img-itm').removeClass('item-image');
                $('.img-itm').removeAttr('style');
                $('#ship-div').removeClass('collapse');
            }
        });

        // ----------- item select -------------------------------
        $('body').on('click','.item-chk',function(){
            var checked = $(this).is(':checked');
            var val = $(this).val();
            let radioValue = $("input[name='drop_off']:checked").val();
            // $('#itm-rtn-'+val).addClass('itm-rtn');
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

        function setRate(){
            let rate = $('#client_shipment_list option:selected').attr('rate');
            let carrier = $('#client_shipment_list option:selected').attr('carrier');
            let rate_div = `<div class="form-group">
                            <label for="">Rate</label>
                            <span class="form-control">${rate}</span>
                            <input type="hidden" name="rate" value="${rate}"/>
                        </div>`;
            let carrier_div = `<div class="form-group">
                            <label for="">Carrier</label>
                            <span class="form-control">${carrier}</span>
                        </div>`;
            $(".rate-id").html(rate_div);
            $(".carrier-div").html(carrier_div);
        }

        //---------------------------------------------------------------
        $('body').on('keyup change','input,select',function(){
            let text = $(this).val();
            if(text.length>0){
                $(this).next('small').text('');
            }
        });

        setRate();

        $('body').on('click','.close',function(){
            $('#errorLabelContainer').html();
            $("#errorContainer").hide();
        });

        // -------------------------------------------------------------
        // $(document).on('change','#carrier',function(){
        //     // let id = $(this).val();
        //     $('#dly').show();
        //     var element = $(this).find('option:selected'); 
        //     var id = element.attr("name");
        //     $.ajax({
        //         type:'get',
        //         url : "{{ route('carrier.product') }}",
        //         data:{carrier_id:id},
        //         dataType : 'json',
        //         success : function(data){
        //             $('#dly').hide();
        //             $(".carrier_product").replaceWith(data.cp);
        //             $(".service_code").replaceWith(data.csc);
        //         }
        //     })
        // });


        // -------------------missguided data--------------------------
        $('body').on('click','#missguided-fetch-order',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();

            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:"{{ route('client-user.missguided.order') }}",
                    type:"GET",
                    data : {id: id, email: mail},
                    dataType:"json",
                    async:true,
                    crossDomain:true,
                    beforeSend: function() {
                        $('#load').removeClass('collapse');
                    },
                    success:function(response){
                        if(response.status){
                            $("#errorContainer").hide();
                            $('#address-card').html(response.add_html);
                            $('#item-card').html(response.item_html);
                            // $('#itm-div').removeClass('collapse');
                            $('#ftr-hide').addClass('collapse');
                        } else{
                            $("#errorContainer").show();
                            $("#errorLabelContainer").html('<li>'+response.msg+'</li>');
                            $('#ftr-hide').removeClass('collapse');
                        }
                    },
                    complete: function() {
                        $('#load').addClass('collapse');
                    },
                });
            } else {
                $('#waywill_id').val();
                $("#errorContainer").show();
                $("#errorLabelContainer").html('<li>Order Email Id or Order Id is required.</li>');
            }
        });

        $('input[name=shipment_label]').change(function(){
            let v = $(this).val();
            if(v == 'Yes'){
                $('#ship-div').removeClass('collapse');
                $('#rtn-div').removeClass('collapse');
                $('#itm-div').removeClass('collapse');
            } else {
                $('#ship-div').addClass('collapse');
                $('#rtn-div').addClass('collapse');
                $('#itm-div').addClass('collapse');
            }
        });
    });
</script>
@endpush


<!-- Main content -->
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="booking-info-box">
            <div class="card-content">
                <div class="box no-border" id="errorContainer">
                    <div class="box-tools">
                        <div class="col-md-12 alert alert-warning alert-dismissible">
                            <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                            <ul id="errorLabelContainer"></ul>
                        </div>
                    </div>
                </div>
                {{--  @if(!empty($owner) && in_array($owner->user_code, ['RG00000038','RG00000060']))
                    @include('pages.frontend.client-user.html.olive')
                @elseif (!empty($owner) && in_array($owner->user_code, ['RG00000063']))
                    @include('pages.frontend.client-user.html.missguided')
                @else
                    @include('pages.frontend.client-user.html.normal')
                @endif --}}

                @if ($client->client_type == '1')
                    @include('pages.frontend.client-user.html.olive')
                @elseif ($client->client_type == '2')
                    @include('pages.frontend.client-user.html.missguided')
                @else
                    @include('pages.frontend.client-user.html.normal')
                @endif
            </div>
        </div>
    </div>
</div>