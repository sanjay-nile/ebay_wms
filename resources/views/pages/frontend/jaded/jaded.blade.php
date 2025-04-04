@push('js')
<script src="{{ asset('public/plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
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
		
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$(".changeLang").change(function(){
			var url = "{{ route('changeLang') }}";
			window.location.href = url + "?lang="+ $(this).val();
		});

		$(".nav-tabs a[data-toggle=tab]").on("click", function(e) {
		  	if ($(this).hasClass("disabled")) {
		    	e.preventDefault();
		    	return false;
		  	}
		});

		$('ul#progressbar li').on('click', function(e) {
			let hrf = $(this).find('a').attr('href');
			$(this).addClass('active');
			$(this).prevAll('li').addClass('active');
			$(this).nextAll('li').removeClass('active');
			$("html, body").animate({ scrollTop: 0 }, "slow");
		    e.preventDefault();
		});

		$('body').on('click','.next-li',function(e){
		    $('ul#progressbar li.active').next('li').find('a').trigger('click');
		    let hrf = $('ul#progressbar li.active:last').find('a').attr('href');
		    $("html, body").animate({ scrollTop: 0 }, "slow");
		    e.preventDefault();
		});

		// fetch order data form the Db...
		$('body').on('click','#fetch-product',function(){
		    let ordr_id = $('#order_no').val();
		    let mail_id = $('#email_id').val();
		    let cl_id = $('#client_id_change').val();
		    if(ordr_id != '' && mail_id == ''){
		    	$('#mail-msg').html('Enter a valid email address');
		    	return false;
		    }

		    if(ordr_id == '' && mail_id != ''){
		    	$('#ord-msg').html('Order number is required');
		    	return false;
		    }

		    if(cl_id != ''){
		    	if(ordr_id != '' && mail_id != ''){
		    	    $.ajax({
		    	        url:"{{ route('jaded.fetch.order') }}",
		    	        type:"GET",
		    	        data : {ordr_id:ordr_id, mail_id: mail_id},
		    	        dataType:"json",
		    	        async:true,
		    	        crossDomain:true,
		    	        beforeSend: function() {
		    	            $('#load').removeClass('collapse');
		    	        },
		    	        success:function(response){
		    	            if(response.status){
								console.log(response);
								if(response.countryEustatus == 1)
								{
									$('#iseucountryavailable').val('1');
									$('.carrierlabel2').val('postal');
								}
								else{
									$('#servicecode').val('InPost');
									$('#iseucountryavailable').val('0');
								}
								$('#origincountry').val(response.origincountry);
								$('#lineitemsorigincountry').val(response.origincountry);
								$('#return_charges').val(response.returncharges);
								$('#customer_state').val(response.customer.customer_state);
		    	                $('#item-summary').html(response.item_html);
		    	                // $('#customer-info').html(response.addres_html);
		    	                $('#total_price').val(response.total_price);
                                $('#curated_id').val(response.curated_id);
                                $('#currency').val(response.currency);

                                $("input[name=actual_weight]").val(response.weight);
                                $("input[name=charged_weight]").val(response.weight);

		    	                // customer data field
		    	                $("input[name=customer_name]").val(response.customer.customer_name);
		    	                $("input[name=customer_phone]").val(response.customer.customer_phone);
		    	                $("input[name=customer_mail]").val(response.customer.customer_mail);
		    	                $("input[name=customer_order_email]").val(response.customer.customer_mail);
		    	                // $("input[name=customer_country]").val(response.customer.customer_country);

		    	                $('select[name^="customer_country"]').val(response.customer.customer_country).attr("selected","selected");
		    	                var cntName = $('#customer_country option:selected').val();


		    	                // country validation..
		    	                if (typeof cntName == "undefined" || cntName == false || cntName == '') {
		    	                	$('.address-msg').html(`<div class="alert alert-danger alert-dismissible">
		    	                	    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>We are currently unable to create your return from this site. Please go to our <a href="https://www.missguided.co.uk/returns" target="_blank" style="color: #000;"><b>UK site </b></a> to generate a return label.
		    	                	</div>`);
		    	                }

		    	                $("input[name=customer_city]").val(response.customer.customer_city);
		    	                $("input[name=customer_postcode]").val(response.customer.customer_postcode);
		    	                $("input[name=customer_address]").val(response.customer.customer_address);
		    	                $("input[name=customer_address2]").val(response.customer.customer_address2);

		    	                // $("#location_url").attr("href", response.customer.location_url);
		    	                
		    	                $('.rtn_sumry').addClass('collapse');
		    	                $('.Summary-card-content').addClass('collapse');
		    	                $('.rtn_sumry').html(response.refund_html);
		    	                // $('.nav-tabs a[data-toggle=tab]').removeClass('disabled');
		    	                $('#second').removeClass('disabled');
		    	                $('ul#progressbar li.active').next('li').find('a').trigger('click');
		    	            } else{
		    	                $('.order-msg').html(`<div class="alert alert-danger alert-dismissible">
		    	                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		    	                    ${response.msg}
		    	                </div>`);
		    	                $("html, body").animate({ scrollTop: 0 }, "slow");
		    	            }
		    	        },
		    	        complete: function() {
		    	            $('#load').addClass('collapse');
		    	        },
		    	    });
		    	} else {
		    	    $('.order-msg').html(`<div class="alert alert-danger alert-dismissible">
		    	        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		    	        It seems the details entered are incorrect. Please verify the details and re-enter them   
		    	    </div>`);
		    	    $("html, body").animate({ scrollTop: 0 }, "slow");
		    	}
		    } else {
	    		$('.order-msg').html(`<div class="alert alert-danger alert-dismissible">
	    	        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	    	        It seems the details entered are incorrect. Please verify the details and re-enter them   
	    	    </div>`);
	    	    $("html, body").animate({ scrollTop: 0 }, "slow");
		    }		    
		});

		// forward to third step...
		$('body').on('click','#step2-valid',function(e){
		    let valid = false;
		    $('.cmt-box').each(function() {
		        var hasRequired = $(this).attr('required');
		        var txtval = $(this).val();
		        $(this).removeAttr('style');
	            if (typeof hasRequired !== "undefined" && hasRequired !== false && txtval == '') {
	                valid = true
	                $(this).css('border-color','#ff0000');
	            }
		    });

		    var selectIsValid = false;
		    var totalQty = 0;
            $('.rtn_qty').each(function(){
                if($(this).val() == 0 && selectIsValid == false) {
                    selectIsValid = true;
                }
                totalQty = totalQty + parseInt($(this).val());
            });

            if(selectIsValid && totalQty == 0) {
            	$('.item-msg').html(`<div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please select the quantity.
                </div>`);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            } else if (valid){
		    	$('.item-msg').html(`<div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Any comments?
                </div>`);
                $("html, body").animate({ scrollTop: 0 }, "slow");
		    } else {
		    	$('#third').removeClass('disabled');
		    	$('ul#progressbar li.active').next('li').find('a').trigger('click');
		    }

		    e.preventDefault();
		});

		// forward to four step...
		$('body').on('click','#step3-valid',function(e){
			var checkeucountry = $('#iseucountryavailable').val();
			
		    let valid = false;
		    let msg = 'Please fill all the mandatory (*) fields.';
		    var email = $('#customer_mail').val();
		    var phn = $('#customer_phone').val();
		    $('.valid-field').each(function() {
		        var hasRequired = $(this).attr('required');
		        var txtval = $(this).val();
		        var attr = $(this).attr('data-error');
		        $(this).next('span').html('');
	            if (typeof hasRequired !== "undefined" && hasRequired !== false && txtval == '') {
	                valid = true;
	                // msg = attr;
		    	    $(this).next('span').html(attr);
	            }
		    });

			if(typeof phn == "undefined" || phn == false || phn == '')
			{
				$("html, body").animate({ scrollTop: 0 }, "slow");
				return false;
			}

		    if(IsEmail(email)==false){
                $('.address-msg').html(`<div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please enter the email address that you would like your return label/QR code to be emailed to.
                </div>`);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }

            var cntName = $('#customer_country option:selected').val();
            // country validation..
            if (typeof cntName == "undefined" || cntName == false || cntName == '') {
            	$('.address-msg').html(`<div class="alert alert-danger alert-dismissible">
            	    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> We are currently unable to create your return from this site. Please go to our <a href="https://www.missguided.co.uk/returns" target="_blank" style="color: #000;"><b>UK site </b></a> to generate a return label.
            	</div>`);
            	return false;
            }

            var first_three = phn.substr(0, 4);
            var n = phn.search("0000");
            // if (n > -1) {
            //     $('.address-msg').html(`<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>${phn} is an invalid number.</div>`);
            //     $("html, body").animate({ scrollTop: 0 }, "slow");
            //     return false;
            // }

		    if (valid){
		    	// $('.address-msg').html(`<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>${msg}</div>`);
				
		    } else {
		    	$('#four').removeClass('disabled');
		    	$('ul#progressbar li.active').next('li').find('a').trigger('click');
		    }
			if(checkeucountry == 1)
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
						$('.carrierasda').show();
						$('.Eucountrycarrieritem').hide();
						$('.noneucountrycarrieritem').show();
						$('.uscountrycarrieritem').hide();
					}
					
				}
		    e.preventDefault();
		});

		// mail validation...
		function IsEmail(email) {
	        var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	        if(!regex.test(email)) {
	           return false;
	        }else{
	           return true;
	        }
	    }	    

		// Back to previous tab...
		$('.back-tab').on('click', function(e) {
			let tb = $(this).attr('data-id');
			$('ul#progressbar li').find('a#'+tb).trigger('click');
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$('.Eucountrycarrieritem').hide();
			$('.noneucountrycarrieritem').hide();
			$('uscountrycarrieritem').hide();
		    e.preventDefault();
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

		// On return reason select...
		$('body').on('change','.rtn_reason',function(e){
		    let ke = $(this).attr('data-key');
		    let vl = $(this).val();
		    if(vl == '10'){
		    	$("#remark-"+ke).attr("required", true);
		    	$("#other-"+ke).attr("required", true);
		    	$("#otherBox-"+ke).removeClass("collapse");
		    } else {
		    	$("#remark-"+ke).attr("required", false);
		    	$("#other-"+ke).attr("required", false);
		    	$("#otherBox-"+ke).addClass("collapse");
		    }
		    e.preventDefault();
		});

		// checkbox validation..
		$('#step2_confirm').click(function(){
            if($(this).is(":checked")){
                $('#step2-valid').addClass('btn-next-lewin');
                $('#step2-valid').removeClass('collapse');
            }else if($(this).is(":not(:checked)")){
            	$('#step2-valid').removeClass('btn-next-lewin');
                $('#step2-valid').addClass('collapse');
            }
        });

		// submit for create waywill...
		$("#create-waybill").on('submit',function(e){
		    e.preventDefault();
		    var formData = $(this);        
		    $.ajax({
		        type : 'post',
		        url : formData.attr('action'),
		        data : formData.serialize(),
		        dataType: 'json',
		        beforeSend : function(){
		            $(".save-waybill").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
		        },
		        success : function(response){
		        	console.log(response);
					
		            if(response.status==201){
						$('.Eucountrycarrieritem').hide();
						$('.noneucountrycarrieritem').hide();
						$('.uscountrycarrieritem').hide();
						if(response.postaltype == 'QRCode')
						{
							$('.printedlabel').hide();
							$('.qrcodelabel').show();
						}
						else{
							$('.printedlabel').show();
							$('.qrcodelabel').hide();
						}
		                $('.success-msg').html(`<div class="alert alert-success alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".pdf-print").attr("href", response.url);
		                // $(".pdf-print").attr("href", response.pdf_url);
		                $(".save-waybill").html(`Submit`);
		                $('#five').removeClass('disabled');
		                $('ul#progressbar li.active').next('li').find('a').trigger('click');
		                return false;
						
		            }

		            if(response.status==202){
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',true);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }

		            if(response.status==203){
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }

		            if(response.status==200){
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',false);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }
		        },
		        error : function(data){
		            if(data.status==422){
		            	let li_htm = '';
		                $.each(data.responseJSON.errors,function(k,v){
		                    const $input = formData.find(`input[name=${k}],select[name=${k}]`);                
		                    if($input.next('small').length){
		                        $input.next('small').html(v); 
		                    }else{
		                        $input.after(`<small class='text-danger'>${v}</small>`); 
		                    }
		                    li_htm += `<li>${v}</li>`;
		                });
		                
		                // ${Object.values(data.responseJSON.errors)[0]}

		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    <ul>${li_htm}</ul>
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',false);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }else{
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${data.statusText}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',false);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }
		        }
		    });
		});
	});
</script>

<style type="text/css">
	.form-group .control-label:after {
		content: "*";
	    color: red;
	    font-size: 20px;
	    top: 5px;
	    position: absolute;
	    margin-left: 3px;	  
	}
</style>
@endpush


<div class="row">
	<div class="col-md-3">
        @include('pages.frontend.jaded.left-sidebar')
    </div>

	<div class="col-md-9">
		<form method="post" id="create-waybill" action="{{ route('jaded.order.create') }}" class="myform">			
			<input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
			<input type="hidden" name="client_code" value="REVERSEGEAR">
			<input type="hidden" name="customer_code" value="00000">
			<input type="hidden" name="customer_state" value="" id="customer_state">
			<input type="hidden" name="payment_mode" id="payment_mode" value="TBB">
			<input type="hidden" name="amount" id="total_price" value="0">
			<input type="hidden" name="currency" id="currency" value="USD">
			<input type="hidden" name="actual_weight" value="1">
			<input type="hidden" name="charged_weight" value="1">
			<input type="hidden" name="shipping_charges" value="25" id="shipping_charges">
			<input type="hidden" name="countrycode" value="" id="country_code_return">
			<input type="hidden" name="return_charges" value="0" id="return_charges">
			<input type="hidden" name="env_amount" value="1.40" id="env_amount">
			<input type="hidden" name="curated_id" value="" id="curated_id">
			<input type="hidden" name="origincountry" value="" id="origincountry">
			<input type="hidden" name="lineitemsorigincountry" value="" id="lineitemsorigincountry">
			<input type="hidden" name="client_id" value="@if($client){!! $client->id !!}@endif" id="client_id_change">

			<div class="rg-step-content">
				<span class="bg-shape"></span>
				<div class="rg-step-content-body">
					<div class="step-content tab-content">
						{{-- step 1 --}}
						<div class="tab-pane active" id="tab1">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
						          			<div class="step-content-form">
						          				<div class="order-msg"></div>
						          				<h3>{{googleTranslate('Return an item in a few easy steps')}}</h3>
						          				<p>{{googleTranslate('Start by providing some information about your purchase so we can locate your order')}}</p>
						          				<div class="step-form step-60 ">
						              				<div class="form-group">
						              					<label>{{googleTranslate('Order Number')}} </label>
						              					<input type="text" class="form-control" name="order_no" id="order_no" placeholder="{{googleTranslate('Enter your order number here')}}">
						              					<span class="text-danger" id="ord-msg"></span>
						              				</div>
						              				<div class="form-group m-0">
						              					<label>{{googleTranslate('Email Address')}}</label>
						              					<div class="bl-msg-lewin">{{googleTranslate('This must match the information used to place the order.')}} </div>
						              					<input type="text" class="form-control" name="email" id="email_id" placeholder="{{googleTranslate('Enter your email address here')}}">
						              					<span class="text-danger" id="mail-msg"></span>
						              					<span style="display: none;">{{googleTranslate('Where can i find my order number?')}}</span>
						              				</div>
						              			</div>
						          			</div>
							  		    </div>
					  		    	</div>
								</div>
								<div class="step-content-btn">
									<div class="row">
										<div class="col">
				  							<div class="step-btn-group">
				  								<!-- <button type="button" id="fetch-product" class="btn-next-lewin">Next <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button> -->
				  		      				</div>
				  		      			</div>
				  		      			<div class="col">
				  							<div class="step-btn-group text-right">
				  								<button type="button" id="fetch-product" class="btn-next-lewin">{{googleTranslate('Next')}}<i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
				  		      				</div>
				  		      			</div>
				  		      		</div>
								</div>
							</div>
						</div>

						{{-- tab 2 start --}}
						<div class="tab-pane" id="tab2">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
											<div class="item-msg"> {{-- bl-msg --}}</div>
											<div class="missguided-tabs-info">
												<div class="missguided-right-content">
								          			<div class="step-content-form">
								          				<h3>{{googleTranslate('Please select the items which are in your package')}}</h3>
							          					<!-- <p style="margin-bottom: 1.1rem;">{{googleTranslate('To be eligible for a return, Your item must be unworn with the original tags attached, in the original packaging and all swimwear must be returned with the hygiene sticker attached.</p>
							          					<p>If our customer care team feel any of these point are not met, it is at their discretion on whether the item is suitable for return. Should a refund be refused, your item will be returned to you.</p>
							          					<p>Items marked as non-refundable including but not limited to, beauty products, hair care or earrings cannot be returned once packaging has been opened due to hygiene reasons.</p>
							          					<p>Gift cards are also not eligible for refund.')}}</p> -->
							          					<p>{{googleTranslate('For more information view your')}} <a href="https://jadedldn.com/a/returns" target="_blank"><b>{{googleTranslate('return terms')}}</b></a>.</p>
								          				<div id="item-summary"></div>

								          				<!-- <div class="check-text-info">
								          					<h3 class="mb-2">Return Charges </h3>
								          					<p class="cnf-text mb-0">EU (Germany, France, Italy, Spain, Netherlands, Denmark, Austria, Belgium, Luxembourg) - <b>€7 </b></p>
								          					<p class="cnf-text  mb-0">EU (rest: Bulgaria, Hungary, Cyprus, Finland, Greece, Hungary) - <b>€15</b></p>
								          					<p class="cnf-text  mb-0">UK (Free for InPost and AsdaToYou, rest tbc)</p>
								          					<p class="cnf-text ">Australia: <b>AUD 35</b></p>
								          				</div> -->
								          				<div class="check-text-info">
								          					<p class="cnf-text"><b>{{googleTranslate('You have chosen')}} <span id="rtn-items">0</span> <span id="chn-txt">{{googleTranslate('item')}}</span> {{googleTranslate('to return, please check the box below to confirm your selection submit my return.')}} </b></p>
								          					<!-- <p class="cnf-text"><b>{{googleTranslate('You have chosen')}} <span id="rtn-items">0</span> <span id="chn-txt">{{googleTranslate('item')}}</span> {{googleTranslate('to return, please check the box below to confirm your selection. I would like to submit my return.')}} </b></p> -->
								          					<div class="">
								          						<div class="itscheckbox">
								          							<input type="checkbox" name="step2_confirm" id="step2_confirm">
								          							<label for="step2_confirm">{{googleTranslate('Confirmed - please proceed with submitting my return.')}}</label>
								          						</div>
								          					</div>
								          				</div>
								          			</div>
									          	</div>
								          	</div>
							  		    </div>
						  		    	<div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>
						  		    	</div>
							  		</div>
							  	</div>
							  	{{-- button --}}
		  	  		    		<div class="step-content-btn">
		  	  		    			<div class="row mob-btn-change">
		  	  		    				<div class="col">
		  	  		          				<div class="step-btn-group">
		  		  								<a class="btn-next-lewin back-tab" href="javascript:void(0)" data-id="first">{{googleTranslate('Back')}}</a>
		  		  		      				</div>
		  	  		          			</div>
		  	  		          			<div class="col">
		  	  		    					<div class="step-btn-group text-right">
		  	  		    						<a class="collapse" id="step2-valid" href="javascript:void(0)">{{googleTranslate('Next')}}</a>
		  	  		          				</div>
		  	  		          			</div>
		  	  		          		</div>
		  	  		    		</div>
							</div>
						</div>

						{{-- tab 3 start --}}
						<div class="tab-pane" id="tab3">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
											<div class="address-msg"> {{-- bl-msg --}}</div>
						          			<div class="step-content-form">
						          				<h3>{{googleTranslate('Confirm your personal information')}}</h3>
						          				<div class="step-form" id="customer-info">
						          					<div class="row">
						          						<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="control-label">{{googleTranslate('Full Name')}}</label>
							                  					<input type="text" class="form-control valid-field" name="customer_name" placeholder="{{googleTranslate('Enter Full Name')}}" required="required" data-error="{{googleTranslate('Full name is required')}}">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
              				                  			<div class="col-md-12">
              				                  				<div class="form-group">
              				                  					<label class="control-label">{{googleTranslate('Email Address')}}</label>
              				                  					<input type="email" class="form-control valid-field" name="customer_mail" id="customer_mail" placeholder="youremail@address.com" required="required" data-error="{{googleTranslate('Email is Required')}}">
              				                  					<input type="hidden" name="customer_order_email" id="customer_order_email" value="">
              				                  					<span class="text-danger"></span>
              			              							<div class="input-subtext">{{googleTranslate('We will send your return label to this email address.')}}</div>
              				                  				</div>
              				                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="control-label">{{googleTranslate('Phone Number')}}</label>
							                  					<input type="text" class="form-control valid-field" name="customer_phone" id="customer_phone" placeholder="{{googleTranslate('Enter Phone Number')}}" data-error="{{googleTranslate('Phone Number is Required')}}">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-5">
															<div class="form-group">
																<label class="control-label">{{googleTranslate('Country')}}</label>
																{{-- <input type="text" class="form-control valid-field" name="customer_country" value="" required="required" placeholder="{{googleTranslate('Enter County')}}" data-error="{{googleTranslate('County is required')}}"> --}}
																<select name="customer_country" id="customer_country" class="form-control valid-field" required="required" data-error="{{googleTranslate('County is required')}}">
																	<option value="">{{googleTranslate('Choose your country')}}</option>
																	@forelse($country as $cnt)
																		<option value="{{ $cnt->sortname }}">{{ $cnt->name }}</option>
																	@empty
																	@endforelse
																</select>
																<span class="text-danger"></span>
															</div>
														</div>
							                  			<div class="col-md-4">
							                  				<div class="form-group">
							                  					<label class="control-label">{{googleTranslate('City')}}</label>
							                  					<input type="text" class="form-control valid-field" name="customer_city" placeholder="{{googleTranslate('Enter City')}}" required="required" data-error="{{googleTranslate('City is required')}}">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-3">
							                  				<div class="form-group">
							                  					<label class="control-label">{{googleTranslate('Post Code')}}</label>
							                  					<input type="text" class="form-control valid-field" name="customer_postcode" placeholder="{{googleTranslate('Enter Post Code')}}" required="required" data-error="{{googleTranslate('Post Code is required')}}">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="control-label">{{googleTranslate('Address Line 1')}}</label>
							                  					<input type="text" class="form-control valid-field" name="customer_address" placeholder="{{googleTranslate('Enter Address Line 1')}}" required="required" data-error="{{googleTranslate('Address line 1 is required')}}">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label>{{googleTranslate('Address Line 2')}}</label>
							                  					<input type="text" class="form-control" name="customer_address2" placeholder="{{googleTranslate('Enter Address Line 2')}}">
							                  				</div>
							                  			</div>
							                  		</div>
						              			</div>
						          			</div>
							  		    </div>
							  		    <div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>
						  		    	</div>
							  		</div>
					  			</div>
					  			{{-- button --}}
  			  		    		<div class="step-content-btn">
  			  		    			<div class="row">
  			  		    				<div class="col">
  				  		          			<div class="step-btn-group">
  				  								<a class="btn-next-lewin back-tab" href="javascript:void(0)" data-id="second">{{googleTranslate('Back')}}</a>
  				  		      				</div>
  				  		      			</div>
  			  		          			<div class="col">
  			  		    					<div class="step-btn-group text-right">
  			  		    						<a class="btn-next-lewin" id="step3-valid" href="javascript:void(0)">{{googleTranslate('Next')}}</a>
  			  		          				</div>
  			  		          			</div>
  			  		          		</div>
  			  		    		</div>
					  		</div>
						</div>


						<input type="hidden" name="" id="iseucountryavailable">

						{{-- tab 4 start --}}
						<div class="tab-pane Eucountrycarrieritem" id="tab4">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
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
		      					              				<table class="table table-responsive">
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
						  		    	<div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>
						  		    	</div>
						  		    </div>
						  		</div>

		  				  		<div class="step-content-btn">
		  							<div class="row">
		  								<div class="col">
		  		  							<div class="step-btn-group">
												<button class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</button>
		  		  								<!-- <a class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</a> -->
		  		  		      				</div>
		  		  		      			</div>
		  		  		      			<div class="col">
		  		  							<div class="step-btn-group text-right">
		  		  								<button type="submit" class="btn btn-sm btn-next-lewin save-waybill">{{googleTranslate('Submit')}}</button>
		  		  		      				</div>
		  		  		      			</div>
		  		  		      		</div>
		  						</div>
						  	</div>
						</div>

						<input type="hidden" name="servicecode" value="" id="servicecode">

						<div class="tab-pane noneucountrycarrieritem" id="tab4">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
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
		      					              							<th style="text-align: center;width:35%;">{{googleTranslate('Service')}} </th>
		      					              							<th style="width:15%;">{{googleTranslate('Max Weight/')}} <br>{{googleTranslate('Dimensions (LxWxH)')}}</th>
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
		      					              								<p>41cm x</p>
		      					              								<p>38cm x 64cm</p>
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
		      					              								<p>90cm x</p>
		      					              								<p>60 cm x 60cm</p>
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
		      					              							
		      					              							<th style="text-align: center;width: 35%;">{{googleTranslate('Service')}}</th>
		      					              							<th style="width: 15%;">{{googleTranslate('Max Weight/')}}<br>{{googleTranslate('Dimensions')}}<br>{{googleTranslate('(LxWxH)')}}</th>
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
		      					              								<p>61cm x</p>
		      					              								<p>46cm x 46cm</p>
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
						  		    	<div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>
						  		    	</div>
						  		    </div>
						  		</div>

		  				  		<div class="step-content-btn">
		  							<div class="row">
		  								<div class="col">
		  		  							<div class="step-btn-group">
												<button class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</button>
		  		  								<!-- <a class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</a> -->
		  		  		      				</div>
		  		  		      			</div>
		  		  		      			<div class="col">
		  		  							<div class="step-btn-group text-right">
		  		  								<button type="submit" class="btn btn-sm btn-next-lewin save-waybill">{{googleTranslate('Submit')}}</button>
		  		  		      				</div>
		  		  		      			</div>
		  		  		      		</div>
		  						</div>
						  	</div>
						</div>


						<div class="tab-pane uscountrycarrieritem" id="tab4">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
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
		      					              							
		      					              							<th style="text-align: center;width: 35%;">{{googleTranslate('Service')}}</th>
		      					              							<th style="width: 15%;">{{googleTranslate('Max Weight/')}}<br>{{googleTranslate('Dimensions')}}<br>{{googleTranslate('(LxWxH)')}}</th>
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
		      					              								<p>61cm x</p>
		      					              								<p>46cm x 46cm</p>
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
		      					              								<p>61cm x</p>
		      					              								<p>46cm x 46cm</p>
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
						  		    	<div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>
						  		    	</div>
						  		    </div>
						  		</div>

		  				  		<div class="step-content-btn">
		  							<div class="row">
		  								<div class="col">
		  		  							<div class="step-btn-group">
												<button class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</button>
		  		  								<!-- <a class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">{{googleTranslate('Back')}}</a> -->
		  		  		      				</div>
		  		  		      			</div>
		  		  		      			<div class="col">
		  		  							<div class="step-btn-group text-right">
		  		  								<button type="submit" class="btn btn-sm btn-next-lewin save-waybill">{{googleTranslate('Submit')}}</button>
		  		  		      				</div>
		  		  		      			</div>
		  		  		      		</div>
		  						</div>
						  	</div>
						</div>


						{{-- tab 5 start --}}
						<div class="tab-pane qrcodelabel" id="tab5">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row mt-1">
									    <div class="col-md-10 success-msg"></div>
									</div>
									<div class="step-content-form">
			              				<h3>{{googleTranslate('All done – Thank you for creating your return.')}}</h3>
			              				<div class="mt-2 return-detail">
			              					<p>{{googleTranslate('You can either download your QR Code by clicking the button at the bottom of this page, or you can open it from the confirmation email that you will receive from info@shipcycle.com')}} </p>
			              					<p>{{googleTranslate('The email confirmation also includes a link to find your local Drop-off location.')}} </p>
			              					<p><b>{{googleTranslate('If you have not received the email in your inbox, please check your junk or spam folder.')}}</b> </p>
			                  			</div>
			              			</div>
								</div>
			              		<div class="step-content-btn">
									<div class="row">
										<div class="col-md-12 text-center">
				  							<div class="step-btn-group">
				  								<a class="btn-next-lewin pdf-print" href="javascript:void(0)" target="_blank">{{googleTranslate('Download QR Code')}}</a>
				  		      				</div>
				  		      			</div>
				  		      		</div>
								</div>
							</div>
						</div>

						<div class="tab-pane printedlabel" id="tab5">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row mt-1">
									    <div class="col-md-10 success-msg"></div>
									</div>
									<div class="step-content-form">
			              				<h3>{{googleTranslate('All done – Thank you for creating your return.')}}</h3>
			              				<div class="mt-2 return-detail">
			              					<p>{{googleTranslate('You can either print your return label by clicking the button at the bottom of this page, or you can open it from the confirmation email that you will receive from info@shipcycle.com')}} </p>
			              					<p>{{googleTranslate('An email has also been sent to you from info@shipcycle.com containing the shipping label and a list of the return carrier locations.')}} </p>
			              					<p><b>{{googleTranslate('If you have not received the email in your inbox, please check your junk or spam folder.')}}</b> </p>
			                  			</div>
			              			</div>
								</div>
			              		<div class="step-content-btn">
									<div class="row">
										<div class="col-md-12 text-center">
				  							<div class="step-btn-group">
				  								<a class="btn-next-lewin pdf-print" href="javascript:void(0)" target="_blank">{{googleTranslate('Download and Print')}}</a>
				  		      				</div>
				  		      			</div>
				  		      		</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</form>	
	</div>
</div>