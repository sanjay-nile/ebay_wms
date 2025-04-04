@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
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
		    	        url:"{{ route('motel-rocks.fetch.order') }}",
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
		    	                $('#item-summary').html(response.item_html);
		    	                // $('#customer-info').html(response.addres_html);
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
		    	                $('#itm-qty').html(response.item_count);
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
		    e.preventDefault();
		});		

		// On return quantity change...
		$('body').on('change','.rtn_qty',function(e){
		    let ke = $(this).attr('data-key');
		    let price = $(this).attr('data-price');
		    let vl = $(this).val();
		    var cntName = $('#customer_country option:selected').val();
		    var curncy = '£';
		    let rtn_itm = $('#rtn-items').text();
		    let rtn_itm_pl = 0;
		    $('.rtn_qty  > option:selected').each(function() {
		    	var sl_op = $(this).val();
		    	rtn_itm_pl = +rtn_itm_pl + +sl_op;
		    });

		    if(rtn_itm_pl > 1){
		    	$('#chn-txt').html('items');
		    } else {
		    	$('#chn-txt').html('item');
		    }

		    if(vl == 0){
		    	let it = vl * price;
		    	$('.price_'+ke).html(curncy + it);
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	let ship = 0;
		    	var total = 0;
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

		    	let wav = $('#waiver').val();
		    	let all_tt = total - ship;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(curncy + total);
		    		$('.ttl_price').html(curncy + all_tt);
		    		$('.ship_chrg').html('- '+curncy +ship);
		    		$('.rtn_total').val(total);
		    	} else {
		    		$('.itm_total').html(curncy + total);
		    		$('.ttl_price').html(curncy + total);
		    		$('.ship_chrg').html(curncy + '0');
		    		$('.rtn_total').val(total);
		    	}
		    	$('.dis-'+ke).addClass('collapse');
		    	$('#rtn-items').html(rtn_itm_pl);
		    } else {
		    	let it = vl * price;
		    	$('.price_'+ke).html(curncy + it);
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	let ship = 0;
		    	var total = 0;
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

		    	let wav = $('#waiver').val();
		    	let all_tt = total - ship;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(curncy + total);
		    		$('.ttl_price').html(curncy + all_tt);
		    		$('.ship_chrg').html('- ' + curncy +ship);
		    		$('.rtn_total').val(total);
		    	} else {
		    		$('.itm_total').html(curncy + total);
		    		$('.ttl_price').html(curncy + total);
		    		$('.ship_chrg').html(curncy + '0');
		    		$('.rtn_total').val(total);
		    	}

		    	$('#rtn-items').html(rtn_itm_pl);
		    	$('.rtn_sumry').removeClass('collapse');
		        $('.Summary-card-content').removeClass('collapse');
		        $('.dis-'+ke).removeClass('collapse');

		        // add more return of reason..
		        /*if (vl > 1) {
		        	$('#multi-reason-'+ke).html('');
		        	for (var i = 1; i < vl; i++) {
		        		var $button = $('#single-reason-'+ke).clone();
  						$('#multi-reason-'+ke).append($button);
		        	}
		        } else {
		        	$('#multi-reason-'+ke).html('');
		        }*/
		    }
		    e.preventDefault();
		});

		// On return reason select...
		$('body').on('change','.rtn_reason',function(e){
		    let ke = $(this).attr('data-key');
		    let vl = $(this).val();
		    if(vl == '7'){
		    	$("#remark-"+ke).attr("required", true);
		    } else {
		    	$("#remark-"+ke).attr("required", false);
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
        @include('pages.frontend.motel-rocks.left-sidebar')
    </div>

	<div class="col-md-9">
		<form method="post" id="create-waybill" action="{{ route('motel-rocks.order.create') }}" class="myform">
			<input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
			<input type="hidden" name="client_code" value="REVERSEGEAR">
			<input type="hidden" name="customer_code" value="00000">
			<input type="hidden" name="payment_mode" value="PAID">
			<input type="hidden" name="actual_weight" value="1">
			<input type="hidden" name="charged_weight" value="1">
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
						          				<h3>Return an item in a few easy steps</h3>
						          				<p>Start by providing some information about your purchase so we can locate your order</p>
						          				<div class="step-form step-60 ">
						              				<div class="form-group">						              					
						              					<label>Order Number</label>
						              					<input type="text" class="form-control" name="order_no" id="order_no" placeholder="Enter your order number here">
						              					<span class="text-danger" id="ord-msg"></span>
						              				</div>              				
						              				<div class="form-group m-0">              					
						              					<label>Delivery postcode or email address</label>
						              					<div class="bl-msg-lewin">This must match the information used to place the order.</div>
						              					<input type="text" class="form-control" name="email" id="email_id" placeholder="Enter your postcode or email address here">
						              					<span class="text-danger" id="mail-msg"></span>
						              					<span style="display: none;">Where can i find my order number?</span>
						              				</div>
						              			</div>
						          			</div>				          			
							  		    </div>
					  		    	</div>
								</div>
								<div class="step-content-btn">
									<div class="row">
										<div class="col-md-6">
				  							<div class="step-btn-group">
				  								<!-- <button type="button" id="fetch-product" class="btn-next-lewin">Next <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button> -->
				  		      				</div>
				  		      			</div>
				  		      			<div class="col-md-6">
				  							<div class="step-btn-group text-right">
				  								<button type="button" id="fetch-product" class="btn-next-lewin">Next <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
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
								          				<h3>Your order contains <span id="itm-qty">X</span> products - now please select items you would like to return.</h3>
								          				<p>If you have multiple orders to return, create a separate return label for each <b>ORDER</b>, otherwise your refund could be delayed.</p>
								          				<p class="bl-text"><b>ATTENTION!</b></p>
								          				<ul>
								          					<li>Please note Motel Rocks accepts only items with original tags attached and in resalable condition to qualify for refund.</li>
								          					<li>They must not be worn or have marks or make up on, If you receive your item in this state or it is damaged, please <a target="_blank" href="https://www.motelrocks.com/pages/contact-us"><b>contact</b></a> Customer Service immediately.</li>
								          					<li>Jewellery, Nail Polish & Cosmetics or Face Masks are not accepted for return, unless deemed faulty. </li>
								          					<li>Swimwear / Underwear must have original hygiene strip in place and tights be in original packaging.</li>
								          					<li>Shoes should be only tried indoors and do NOT show any signs of wear.</li>
								          					<li>Motel Rocks return policy is within 14 days from receiving date.</li>
								          					<!-- <li>Products must be logged on Motel Rocks return portal within 14 days from receiving date.</li>
								          					<li>Christmas extension time - any orders placed after 1st Nov 21 are eligible for refund until 10th jan 22.</li> -->
								          				</ul>
								          				
								          				<p class="sm-text">For more information, please visit Motel Rocks <a target="_blank" href="https://motelrocks.zendesk.com/hc/en-us/sections/200516221-Returns-Information ">return help page</a>, <a target="_blank" href="https://motelrocks.zendesk.com/hc/en-us/sections/200990812-Frequently-Asked-Questions">FAQ</a>, simply fill in <a target="_blank" href="https://www.motelrocks.com/pages/contact-us">contact form</a> or send an email to <a href="mailto:help@motelrocks.com">help@motelrocks.com</a></p>
								          				<!-- <br> -->
								          				<div id="item-summary"></div> 
								          				<div class="check-text-info">
								          					<p class="cnf-text"><b>You have chosen <span id="rtn-items">0</span> <span id="chn-txt">item</span> to send back, please click in the box below to confirm your selection.</b></p>

								          					<div class="">
								          						<div class="itscheckbox">
								          							<input type="checkbox" name="step2_confirm" id="step2_confirm">
								          							<label for="step2_confirm">Confirmed - please proceed with submitting my return.</label>
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
		  	  		    				<div class="col-md-6">
		  	  		          				<div class="step-btn-group">
		  		  								<a class="btn-next-lewin back-tab" href="javascript:void(0)" data-id="first">Back</a>
		  		  		      				</div>
		  	  		          			</div>  				
		  	  		          			<div class="col-md-6">
		  	  		    					<div class="step-btn-group text-right">
		  	  		    						<a class="collapse" id="step2-valid" href="javascript:void(0)">Next</a>
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
						          				<h3>Confirm your personal information</h3>
						          				<div class="step-form" id="customer-info">
						          					<div class="row">
						          						<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="control-label">Full Name</label>
							                  					<input type="text" class="form-control valid-field" name="customer_name" placeholder="Enter Full Name" required="required" data-error="Full name is required">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
              				                  			<div class="col-md-12">
              				                  				<div class="form-group">
              				                  					<label class="control-label">Email Address</label>
              				                  					<input type="email" class="form-control valid-field" name="customer_mail" id="customer_mail" placeholder="youremail@address.com" required="required" data-error="Email is Required">
              				                  					<input type="hidden" name="customer_order_email" id="customer_order_email" value="">
              				                  					<span class="text-danger"></span>
              			              							<div class="input-subtext">We will send your return label to this email address.</div>
              				                  				</div>
              				                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="">Phone Number</label>
							                  					<input type="text" class="form-control" name="customer_phone" id="customer_phone" placeholder="Enter Phone Number">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>							                  			
							                  			<div class="col-md-5">
															<div class="form-group">
																<label class="control-label">Country</label>
																{{-- <input type="text" class="form-control valid-field" name="customer_country" value="" required="required" placeholder="Enter County" data-error="County is required"> --}}
																<select name="customer_country" id="customer_country" class="form-control valid-field" required="required" data-error="County is required">
																	<option value="">Choose your country</option>
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
							                  					<label class="control-label">City</label>
							                  					<input type="text" class="form-control valid-field" name="customer_city" placeholder="Enter City" required="required" data-error="City is required">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-3">
							                  				<div class="form-group">
							                  					<label class="control-label">Post Code</label>
							                  					<input type="text" class="form-control valid-field" name="customer_postcode" placeholder="Enter Post Code" required="required" data-error="Post Code is required">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label class="control-label">Address Line 1</label>
							                  					<input type="text" class="form-control valid-field" name="customer_address" placeholder="Enter Address Line 1" required="required" data-error="Address line 1 is required">
							                  					<span class="text-danger"></span>
							                  				</div>
							                  			</div>
							                  			<div class="col-md-12">
							                  				<div class="form-group">
							                  					<label>Address Line 2</label>
							                  					<input type="text" class="form-control" name="customer_address2" placeholder="Enter Address Line 2">
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
  			  		    				<div class="col-md-6">
  				  		          			<div class="step-btn-group">
  				  								<a class="btn-next-lewin back-tab" href="javascript:void(0)" data-id="second">Back</a>
  				  		      				</div>
  				  		      			</div>
  			  		          			<div class="col-md-6">
  			  		    					<div class="step-btn-group text-right">
  			  		    						<a class="btn-next-lewin" id="step3-valid" href="javascript:void(0)">Next</a>
  			  		          				</div>
  			  		          			</div>
  			  		          		</div>
  			  		    		</div>
					  		</div>
						</div>

						{{-- tab 4 start --}}
						<div class="tab-pane" id="tab4">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row">
										<div class="col-md-8">
											<div class="row mt-1">
											    <div class="col-md-10 error-msg"></div>
											</div>
						          			<div class="step-content-form">
					              				<h3>Carrier</h3>
      				              				<div class="carrier-list">
      				              					<div class="carrier-item">
      						              				<div class="PBCheckbox">
      						              					<input type="radio" name="hermes" value="HERMES" checked="checked">
      						              					<label for="card-select">
      						              						<div class="carrier-text">
      						              							<h2>Carrier 1</h2>
      						              							<p>Max 10kg Max 40cm x 55cm x 53cm</p>
      						              						</div>
      						              					</label>
      						              				</div>
      						              			</div>
      					              			</div>		              				
					              				<div class="return-content-list">
						              				<h3 class="m-0 font-weight-normal">You can drop your parcel off at any Post Office.</h3>
						              				<div class="form-group col-md-5 m-0"></div>
						              			</div>
					              			</div>
						  		    	</div>
						  		    	<div class="col-md-4">
						  		    		<div class="order-item rtn_sumry"></div>						  		    		
						  		    	</div>
						  		    </div>
						  		</div>

		  				  		<div class="step-content-btn">
		  							<div class="row">
		  								<div class="col-md-6">
		  		  							<div class="step-btn-group">
		  		  								<a class="btn btn-next-lewin back-tab" href="javascript:void(0)" data-id="third">Back</a>
		  		  		      				</div>
		  		  		      			</div>
		  		  		      			<div class="col-md-6">
		  		  							<div class="step-btn-group text-right">
		  		  								<button type="submit" class="btn btn-sm btn-next-lewin save-waybill">Submit</button>
		  		  		      				</div>
		  		  		      			</div>
		  		  		      		</div>
		  						</div>
						  	</div>
						</div>

						{{-- tab 5 start --}}
						<div class="tab-pane" id="tab5">
							<div class="step-content-form-box">
								<div class="step-content-body">
									<div class="row mt-1">
									    <div class="col-md-10 success-msg"></div>
									</div>
									<div class="step-content-form">
										<div class="COVID-note">
											Due to the COVID-19 pandemic and warehouse restrictions, please note there might be delays with processing your refund.
										</div>
			              				<h3 class="text-uppercase">All done – Thank you for creating your return.</h3>
			              				<div class="mt-2 return-detail">
                                        	<p>You can then either print your return label at home or take it to the selected carrier drop off point to print and send your shipment back to Motel Rocks.</p>
			              					<p>An email has also been sent to you from <a href="mailto:info@reversegear.net">info@reversegear.net</a> containing the shipping label and a list of the return carrier locations.</p>
                                           	<p><b>If you have not received the email in your inbox, please check your junk or spam folder.</b> </p>
                                           	<p>Please allow 5 -10 working days from the date your parcel is delivered to Motel Rocks warehouse for the return to be completed and a refund sent back to your original method of payment.</p>
                                           	<p>You will be notified when your refund has been processed, not when it is delivered back to Motel Rocks warehouse - if your return falls outside of this time, please <a target="_blank" href="https://motelrocks.zendesk.com/hc/en-us/requests/new">submit request</a> to Customer Service or email <a href="mailto:help@motelrocks.com">help@motelrocks.com</a></p>
			                  			</div>
			              			</div>
								</div>
			              		<div class="step-content-btn">
									<div class="row">
										<div class="col-md-12 text-center">
				  							<div class="step-btn-group">
				  								<a class="btn-next-lewin pdf-print" href="javascript:void(0)" target="_blank">Download and Print</a>
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