@include('pages.frontend.client.breadcrumb', ['title' => 'Create a New Return'])

@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/create-waywill.js') }}"></script>
<script>
    $(document).ready(function(){
        // $('#refund_html').hide();
        $('.returnfullsection').hide();
        $('.downloadandprint').hide();
        $('.Eucountrycarrieritem').hide();
		$('.noneucountrycarrieritem').hide();
		$('.uscountrycarrieritem').hide();
		$('.carrierasda').hide();
		$('#servicecode').val('Postal Services');
		$('.carrierlabel2').on('click',function(e){
			var starting_rtn_total = $('.starting_rtn_total').val();
			var previoustotalprice = $('.ttl_price').html() ;
			var returntotal        = $('.rtn_total').val() ;
			var currency           = ' '+$('#currency').val();

			$('.rtn_total').val(starting_rtn_total);
			$('.ttl_price').html(starting_rtn_total + currency);
			$('.rtn_chrg').html("- 0" + currency);
			$('#servicecode').val('InPost');
		});

		$('#carrierlabel3').on('click',function(e){
			var starting_rtn_total = $('.starting_rtn_total').val();
			var previoustotalprice = $('.ttl_price').html() ;
			var returntotal        = $('.rtn_total').val() ;
			var currency           = ' '+$('#currency').val();

			$('.rtn_total').val(starting_rtn_total);
			$('.ttl_price').html(starting_rtn_total + currency);
			$('.rtn_chrg').html("- 0" + currency);
			$('#servicecode').val('Asda');
		});

		$('.carrieruslabel1').on('click',function(e){
			var starting_rtn_total = $('.starting_rtn_total').val();
			var previoustotalprice = $('.ttl_price').html() ;
			var returntotal        = $('.rtn_total').val() ;
			var currency           = ' '+$('#currency').val();
			var total = (parseInt(starting_rtn_total) - 25 );
			$('.rtn_chrg').html(- 25 + currency);
			$('.rtn_total').val(total);
			$('.ttl_price').html(total + currency);
			$('#servicecode').val('UPS');
		});

		$('.carrieruspslabel1').on('click',function(e){
			var starting_rtn_total = $('.starting_rtn_total').val();
			var previoustotalprice = $('.ttl_price').html() ;
			var returntotal        = $('.rtn_total').val() ;
			var currency           = ' '+$('#currency').val();
			var total = (parseInt(starting_rtn_total) - 15 );
			$('.rtn_chrg').html(- 15 + currency);
			$('.rtn_total').val(total);
			$('.ttl_price').html(total + currency);
			$('#servicecode').val('USPS');
		});



		// $('#carrierlabel3').on('click',function(e){
		// 	$('#servicecode').val('asda');
		// })

		$('.carrierlabel1eucountry').on('click',function(e){
			$('#servicecode').val('Postal Services');
		})
		$('.carrierlabel1').on('click',function(e){
			var starting_rtn_total = $('.starting_rtn_total').val();
			var previoustotalprice = $('.ttl_price').html() ;
			var returntotal        = $('.rtn_total').val() ;
			var currency           = ' '+$('#currency').val();
			var total = (parseInt(starting_rtn_total) - 3.95 );
			$('.rtn_chrg').html(- 3.95 + currency);
			$('.rtn_total').val(total);
			$('.ttl_price').html(total + currency);
			$('#servicecode').val('Postal Services');
		})
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
                    url:"{{ route('client.fetch.product') }}",
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
                // $('#ship-div').addClass('collapse');
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

        // On return quantity change...
		$('body').on('change','.rtn_qty',function(e){
		    let ke = $(this).attr('data-key');
		    let price = $(this).attr('data-price');
		    let vl = $(this).val();
		    let ship = $('#shipping_charges').val();
            let env = $('#env_amount').val();
            var total = 0;
			let rtn = $('#return_charges').val();
			
			let country = $('#customer_country').val();
            
			$('#country_code_return').val(country);
			// $('#return_charges').val(country);
			
			
		    let rtn_itm = $('#rtn-items').text();
		    let rtn_itm_pl = 0;
		    var curncy = ' '+$('#currency').val();
			console.log(price);
		    $('.rtn_qty  > option:selected').each(function() {
		    	var sl_op = $(this).val();
		    	rtn_itm_pl = +rtn_itm_pl + +sl_op;
		    });

		    if(rtn_itm_pl > 1){
		    	$('#chn-txt').html('items');
		    } else{
		    	$('#chn-txt').html('item');
		    }

		    if(vl == 0){
		    	let it = vl * price;
		    	$('.price_'+ke).html(it+curncy);
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

		    	let wav = $('#waiver').val();
		    	let all_tt = total - rtn;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(total +curncy);
		    		$('.ttl_price').html(all_tt +curncy);
		    		$('.ship_chrg').html('- '+ship +curncy);
		    		$('.rtn_chrg').html('- '+rtn +curncy);
		    		$('.rtn_total').val(total);
		    		$('.starting_rtn_total').val(total);
		    	} else {
                        $('.itm_total').html(total +curncy);
                        $('.ttl_price').html(total +curncy);
                        $('.ship_chrg').html('0' +curncy);
                        $('.rtn_chrg').html('0' +curncy);
                        $('.rtn_total').val(total);
                        $('.starting_rtn_total').val(total);
		    		

		    	}

		    	$('.rtn_sumry').addClass('collapse');
		        $('.Summary-card-content').addClass('collapse');
		    	$('.dis-'+ke).addClass('collapse');
		    	$('#rtn-items').html(rtn_itm_pl);
		    } else {
		    	let it = vl * price;
		    	$('.price_'+ke).html(it+curncy);
		    	$('.qty_'+ke).html('Qty:'+vl);
		    	
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

                shipmentwaiver = document.getElementById('shipmentwaiver');
                // Check if the element is selected/checked
                if(shipmentwaiver.checked) {
                    rtn = 0 ;
                }
                if(country == 'US')
                {
                    if(shipmentwaiver.checked) {
                        rtn = 0 ;
                    }
                    else{
                        rtn = 25 ;
                    }
                    
                }

		    	let wav = $('#waiver').val();
		    	let all_tt = total - rtn;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(total +curncy);
		    		$('.ttl_price').html(all_tt +curncy);
		    		$('.ship_chrg').html('- '+ship +curncy);
		    		$('.rtn_total').val(total);
		    		$('.starting_rtn_total').val(total);

		    		$('.rtn_chrg').html('- '+rtn +curncy);
		    	} else {
                        $('.itm_total').html(total +curncy);
                        $('.ttl_price').html(total +curncy);
                        $('.ship_chrg').html(ship +curncy);
                        $('.rtn_total').val(total);
                        $('.starting_rtn_total').val(total);

                        $('.rtn_chrg').html('- '+rtn +curncy);
		    		
		    	}

		    	$('#rtn-items').html(rtn_itm_pl);
		    	$('.rtn_sumry').removeClass('collapse');
		        $('.Summary-card-content').removeClass('collapse');
		        $('.dis-'+ke).removeClass('collapse');

		        // add more return of reason..
		        if (vl > 1) {
		        	$('#multi-reason-'+ke).html('');
		        	for (var i = 1; i < vl; i++) {
		        		var $button = $('#single-reason-'+ke).clone();
  						$('#multi-reason-'+ke).append($button);
		        	}
		        } else {
		        	$('#multi-reason-'+ke).html('');
		        }
		    }
		    e.preventDefault();
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

        // -------------------missguided data--------------------------
        $('body').on('click','#missguided-fetch-order',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();

            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:"{{ route('client.missguided.fetch.product') }}",
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

        // $('input[name=shipment_label]').change(function(){
        //     let v = $(this).val();
        //     if(v == 'Yes'){
        //         $('#ship-div').removeClass('collapse');
        //         $('#rtn-div').removeClass('collapse');
        //         $('#itm-div').removeClass('collapse');
        //     } else {
        //         $('#ship-div').addClass('collapse');
        //         $('#rtn-div').addClass('collapse');
        //         $('#itm-div').addClass('collapse');
        //     }
        // });

        // -------------------curated data--------------------------
        $('body').on('click','#curated-fetch-order',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();

            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:"{{ route('client.curated.fetch.order') }}",
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
                            // customer data field
                            if(response.countryEustatus == 1)
                            {
                                $('.Eucountrycarrieritem').show();
				                $('.noneucountrycarrieritem').hide();
                                // $('#iseucountryavailable').val('1');
                                $('.carrierlabel2').val('postal');
                                
                            }
                            else{
                                $('#servicecode').val('InPost');
                                // $('#iseucountryavailable').val('0');
                                $('.Eucountrycarrieritem').hide();
				                $('.noneucountrycarrieritem').show();

                            }
                            $("input[name=customer_name]").val(response.customer.customer_name);
                            $("input[name=customer_phone]").val(response.customer.customer_phone);
                            $("input[name=customer_email]").val(response.customer.customer_mail);
                            $("input[name=customer_city]").val(response.customer.customer_city);
                            $("input[name=customer_country]").val(response.customer.customer_country);
                            $("input[name=customer_state]").val(response.customer.customer_state);
                            $("input[name=customer_pincode]").val(response.customer.customer_postcode);
                            $("input[name=customer_address]").val(response.customer.customer_address);
                            $("input[name=actual_weight]").val(response.weight);
                            $("input[name=charged_weight]").val(response.weight);

                            $("#errorContainer").hide();
                            $('#item-card').html(response.item_html);
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

        // -------------------other client data--------------------------
        $('body').on('click','#client-fetch-order',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();
            let client_id = $('#client_id').val();
            let order_url = $('#fetch_order_url').val();
            let waiver = 0;
            waivercheckbox = document.getElementById('getwaiver');
            // Check if the element is selected/checked
            if(waivercheckbox.checked) {
                // Respond to the result
                // alert("Checkbox checked!");
                waiver = 1 ;
            }
            
            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:'{{route("other.client.order")}}',
                    type:"GET",
                    data : {id: id, email: mail, client_id: client_id,waiver:waiver},
                    dataType:"json",
                    async:true,
                    crossDomain:true,
                    beforeSend: function() {
                        $('#load').removeClass('collapse');
                    },
                    success:function(response){
                        if(response.status){
                            $('.returnfullsection').show();
                            $('#ship-div').removeClass('collapse');
                            if(response.countryEustatus == 1)
                            {
                                $('.Eucountrycarrieritem').show();
				                $('.noneucountrycarrieritem').hide();
                                // $('#iseucountryavailable').val('1');
                                $('.carrierlabel2').val('postal');
                                $('#servicecode').val('Postal Services');
                            }
                            else{
                                $('#servicecode').val('InPost');
                                // $('#iseucountryavailable').val('0');
                                $('.Eucountrycarrieritem').hide();
				                $('.noneucountrycarrieritem').show();

                            }
                            $('#refund_html').html(response.refund_html);
                            $("#errorContainer").hide();
                            $('#address-card').html(response.add_html);
                            $('#item-card').html(response.item_html);
                            $('#itm-div').removeClass('collapse');
                            $('#ftr-hide').addClass('collapse');
                            $('#currency').val(response.currency);
                            $('#return_charges').val(response.returncharges);
                            $('#total_price').val(response.total_price);
                            $('#curated_id').val(response.curated_id);
                            $('#currency').val(response.currency);
                            $('#origincountry').val(response.origincountry);
                            $("input[name=actual_weight]").val(response.weight);
                            $("input[name=charged_weight]").val(response.weight);
                            $("input[name=customer_name]").val(response.customer.customer_name);
                            $("input[name=customer_phone]").val(response.customer.customer_phone);
                            $("input[name=customer_email]").val(response.customer.customer_mail);
                            $("input[name=customer_order_email]").val(response.customer.customer_mail);
                            $("input[name=customer_address]").val(response.customer.customer_address);
                            $("input[name=customer_country]").val(response.customer.customer_country);
                            $("input[name=customer_state]").val(response.customer.customer_state);
                            $("input[name=customer_city]").val(response.customer.customer_city);
                            $("input[name=customer_pincode]").val(response.customer.customer_postcode);
                            $("input[name=customer_phone]").val(response.customer.customer_phone);
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


        // Client user 

        $('body').on('click','#clientuser-fetch-order',function(){
            let id = $('#order_id').val();
            let mail = $('#email_id').val();
            let client_id = $('#client_id').val();
            let order_url = $('#fetch_order_url').val();
            let waiver = 0;
            waivercheckbox = document.getElementById('getwaiver');
            // Check if the element is selected/checked
            if(waivercheckbox.checked) {
                waiver = 1 ;
            }
            
            if(id != '' && mail != ''){
                $('#waywill_id').val(id);
                $.ajax({
                    url:'{{route("other.clientuser.order")}}',
                    type:"GET",
                    data : {id: id, email: mail, client_id: client_id,waiver:waiver},
                    dataType:"json",
                    async:true,
                    crossDomain:true,
                    beforeSend: function() {
                        $('#load').removeClass('collapse');
                    },
                    success:function(response){
                        if(response.status){
                            $('.returnfullsection').show();
                            $('#ship-div').removeClass('collapse');
                            // console.log(response.customer.customer_country)
                            $("input[name=customer_country]").val(response.customer.customer_country);
                            var country = $('#customer_country').val() ;
                            // alert(country);
                            // if(response.countryEustatus == 1)
                            // {
                            //     $('.Eucountrycarrieritem').show();
				            //     $('.noneucountrycarrieritem').hide();
                            //     // $('#iseucountryavailable').val('1');
                            //     $('.carrierlabel2').val('postal');
                            //     $('#servicecode').val('Postal Services');
                            // }
                            // else{
                            //     $('#servicecode').val('InPost');
                            //     // $('#iseucountryavailable').val('0');
                            //     $('.Eucountrycarrieritem').hide();
				            //     $('.noneucountrycarrieritem').show();

                            // }
                            if(response.countryEustatus == 1)
                            {
                                $('.Eucountrycarrieritem').show();
                                $('.noneucountrycarrieritem').hide();
                                $('.uscountrycarrieritem').hide();
                                var country = $('#customer_country').val() ;
                                if(country == 'CA')
                                {
                                    $('#servicecode').val('CanadaPost');
                                }
                            }
                            else{
                                var country = $('#customer_country').val() ;
                                console.log(country);
                                if(country == 'US')
                                {
                                    $('.Eucountrycarrieritem').hide();
                                    $('.noneucountrycarrieritem').hide();
                                    $('.uscountrycarrieritem').show();
                                    var starting_rtn_total = $('.starting_rtn_total').val();
                                    var previoustotalprice = $('.ttl_price').html() ;
                                    var returntotal        = $('.rtn_total').val() ;
                                    var currency           = ' '+$('#currency').val();
                                    var total = (parseInt(starting_rtn_total) - 25 );
                                    $('.rtn_chrg').html(- 25 + currency);
                                    $('.rtn_total').val(total);
                                    $('.ttl_price').html(total + currency);
                                    $('#servicecode').val('UPS');
                                    $('.carrierlabel1').val('postal');
                                    $('.carrierlabel2').val('postal');
                                }
                                else{
                                    $('#servicecode').val('InPost');
                                    $('.carrierasda').show();
                                    $('.Eucountrycarrieritem').hide();
                                    $('.noneucountrycarrieritem').show();
                                    $('.uscountrycarrieritem').hide();
                                }
                                
                            }
                            $('#refund_html').html(response.refund_html);
                            $("#errorContainer").hide();
                            $('#address-card').html(response.add_html);
                            $('#item-card').html(response.item_html);
                            $('#itm-div').removeClass('collapse');
                            $('#ftr-hide').addClass('collapse');
                            $('#currency').val(response.currency);
                            $('#return_charges').val(response.returncharges);
                            $('#total_price').val(response.total_price);
                            $('#curated_id').val(response.curated_id);
                            $('#currency').val(response.currency);
                            $('#origincountry').val(response.origincountry);
                            $("input[name=actual_weight]").val(response.weight);
                            $("input[name=charged_weight]").val(response.weight);
                            $("input[name=customer_name]").val(response.customer.customer_name);
                            $("input[name=customer_phone]").val(response.customer.customer_phone);
                            $("input[name=customer_email]").val(response.customer.customer_mail);
                            $("input[name=customer_order_email]").val(response.customer.customer_mail);
                            $("input[name=customer_address]").val(response.customer.customer_address);
                            $("input[name=customer_country]").val(response.customer.customer_country);
                            $("input[name=customer_state]").val(response.customer.customer_state);
                            $("input[name=customer_city]").val(response.customer.customer_city);
                            $("input[name=customer_pincode]").val(response.customer.customer_postcode);
                            $("input[name=customer_phone]").val(response.customer.customer_phone);
                            $("input[name=customer_city]").val(response.customer.customer_postcode);
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
    });
</script>
<style type="text/css">
	#errorContainer{display: none;}
	.collapse{display: none;}
</style>
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
                @include('pages.frontend.client-user.html.other-client')
                {{-- 
                @if ($client->client_type == '1')
                    @include('pages.frontend.client.html.olive')
                @elseif ($client->client_type == '2')
                    @include('pages.frontend.client.html.missguided')
                @elseif ($client->client_type == '3')
                    @include('pages.frontend.client.html.normal')                    
                @elseif ($client->client_type == '4')
                    @include('pages.frontend.client.html.curated')
                @else
                    @include('pages.frontend.client-user.html.other-client')
                @endif
                --}}
            </div>
        </div>
    </div>
</div>