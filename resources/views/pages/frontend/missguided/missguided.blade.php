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
		    	        url:"{{ route('missguided.fetch.order') }}",
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
		    if(vl == 0){
		    	let it = vl * price;
		    	$('.price_'+ke).html(it+' EUR');
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	let ship = 4;
		    	var total = 0;
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

		    	let wav = $('#waiver').val();
		    	let all_tt = total - ship;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(total +' EUR');
		    		$('.ttl_price').html(all_tt +' EUR');
		    		$('.ship_chrg').html('- '+ship +' EUR');
		    		$('.rtn_total').val(total);
		    	} else {
		    		$('.itm_total').html(total +' EUR');
		    		$('.ttl_price').html(total +' EUR');
		    		$('.ship_chrg').html('0' +' EUR');
		    		$('.rtn_total').val(total);
		    	}
		    	$('.dis-'+ke).addClass('collapse');
		    } else {
		    	let it = vl * price;
		    	$('.price_'+ke).html(it+' EUR');
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	let ship = 4;
		    	var total = 0;
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	});

		    	let wav = $('#waiver').val();
		    	let all_tt = total - ship;
		    	if(wav == 'Return_Policy_Timeline' || wav == '' || wav == null){
		    		$('.itm_total').html(total +' EUR');
		    		$('.ttl_price').html(all_tt +' EUR');
		    		$('.ship_chrg').html('- '+ship +' EUR');
		    		$('.rtn_total').val(total);
		    	} else {
		    		$('.itm_total').html(total +' EUR');
		    		$('.ttl_price').html(total +' EUR');
		    		$('.ship_chrg').html('0' +' EUR');
		    		$('.rtn_total').val(total);
		    	}

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
		    if(vl == '7'){
		    	$("#remark-"+ke).attr("required", true);
		    } else {
		    	$("#remark-"+ke).attr("required", false);
		    }
		    e.preventDefault();
		});

		var ajaxObject = null;
		// submit for create waywill...
		$("#create-waybill").on('submit',function(e){
		    e.preventDefault();
		    var formData = $(this);
		    var dataquery = 'true';
		    $.ajax({
		        type : 'post',
		        url : formData.attr('action'),
		        data : formData.serialize(),
		        dataType: 'json',
		        timeout: 120000,
		        beforeSend : function(){
		            $(".save-waybill").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
		            $('#infoModal').modal('show');
		        },
		        success : function(response){
		        	console.log(response);
		            if(response.status==201){
		            	$('#infoModal').modal('hide');
		                $('.success-msg').html(`<div class="alert alert-success alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".pdf-print").attr("href", response.url);
		                $(".save-waybill").html(`Submit`);
		                $('#five').removeClass('disabled');
		                $('ul#progressbar li.active').next('li').find('a').trigger('click');
		                return false;
		            }

		            if(response.status==202){
		            	$('#infoModal').modal('hide');
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',true);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }

		            if(response.status==203){
		            	$('#infoModal').modal('hide');
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }

		            if(response.status==200){
		            	$('#infoModal').modal('hide');
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${response.message}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',false);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }
		        },
		        error : function(data, status, xhr){
		        	console.log(status);
		        	console.log(xhr);
		        	if(status==="timeout") {
		        		$('#infoModal').modal('hide');
		        	    $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		        	        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		        	        This return method is currently unavailable. Please try again later. If you are still experiencing issues please <a href="https://www.missguided.eu/help" target="_blank" style="color: #000;"> get in touch.</a>
		        	    </div>`);
		        	    $(".save-waybill").html(`Submit`).attr('disabled',true);
		        	    $("html, body").animate({ scrollTop: 0 }, "slow");
		        	    return false;
			        }

			        if (status == "error") {
			        	$('#infoModal').modal('hide');
                        //for Page Not Found Invalid URL
                        if (xhr == "Not Found") {
                            $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                This return method is currently unavailable. Please try again later. If you are still experiencing issues please <a href="https://www.missguided.eu/help" target="_blank" style="color: #000;"> get in touch.</a>
                            </div>`);
                            $(".save-waybill").html(`Submit`).attr('disabled',true);
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                            return false;
                        }

                        //for server error which is from controller
                        if (xhr == "Internal Server Error") {
                         	$('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                         	    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                         	    This return method is currently unavailable. Please try again later. If you are still experiencing issues please <a href="https://www.missguided.eu/help" target="_blank" style="color: #000;"> get in touch.</a>
                         	</div>`);
                         	$(".save-waybill").html(`Submit`).attr('disabled',true);
                         	$("html, body").animate({ scrollTop: 0 }, "slow");
                         	return false;   
                        }
                    }

		            if(data.status==422){
		            	$('#infoModal').modal('hide');
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
		            	$('#infoModal').modal('hide');
		                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
		                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                    ${data.statusText}    
		                </div>`);
		                $(".save-waybill").html(`Submit`).attr('disabled',false);
		                $("html, body").animate({ scrollTop: 0 }, "slow");
		                return false;
		            }
		        },
		        complete: function(data) {
		        	$('#infoModal').modal('hide');
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

<div class="missguided-wrapper-content">
	<div class="missguided-list">
		<div class="tab-pane1 missguided-right-content" id="tabs">
			<div class="row">
				<div class="col-md-8">
					<div class="step-info">
						<ul id="progressbar" class="nav nav-tabs">
				            <li class="active">
				              	<a data-toggle="tab" href="#tab1"  aria-expanded="true" id="first" class="">
				                	<span class="number">1</span>
				              	</a>
				            </li>
				            <li class="nav-item">
				              	<a href="#tab2" data-toggle="tab" aria-expanded="false" id="second" class="disabled">
				                	<span class="number">2</span>
				              	</a>
				            </li>
				            <li class="nav-item">
				              	<a href="#tab3" data-toggle="tab" aria-expanded="false" id="third" class="disabled">
				                	<span class="number">3</span>
				              	</a>
				            </li>
				            <li class="nav-item">
				              	<a href="#tab4" data-toggle="tab" aria-expanded="false" id="four" class="disabled">
				                	<span class="number">4</span>
				              	</a>
				            </li>
				            <li class="nav-item">
				              	<a href="#tab5" data-toggle="tab" aria-expanded="false" id="five" class="disabled">
				                	<span class="number">5</span>
				              	</a>
				            </li>  
				        </ul> 
					</div>
				</div>
			</div>
			<form method="post" id="create-waybill" action="{{ route('missguided.order.create') }}" class="myform">
				<input type="hidden" class="form-control valid" name="service_code" value="ECOMDOCUMENT">
				<input type="hidden" name="client_code" value="REVERSEGEAR">
				<input type="hidden" name="customer_code" value="00000">
				<input type="hidden" name="payment_mode" value="PAID">
				<input type="hidden" name="actual_weight" value="1">
				<input type="hidden" name="charged_weight" value="1">
				<input type="hidden" name="client_id" value="@if($client){!! $client->id !!}@endif" id="client_id_change">

				<div class="step-content tab-content">
					{{-- step 1 --}}
					<div class="tab-pane active" id="tab1">
						<div class="row">
							<div class="col-md-8">
				          		<div class="step-content-body">
				          			<div class="step-content-form">
				          				<div class="order-msg"> {{-- bl-msg --}}</div>
				          				<h3>Return an item in a few easy steps</h3>
				          				<p>Start by providing some information about your purchase so we can locate your order</p>
				          				<div class="step-form step-60 ">
				              				<div class="form-group">
				              					<div class="bl-msg">
				              						Due to current restrictions, we have extended our return policy from 14 days to 28 days from your delivery date
				              					</div>				              					
				              					<label>Order Number</label>
				              					<input type="text" class="form-control" name="order_no" id="order_no" placeholder="e.g 'XXXXXXXXX'">
				              					<span class="text-danger" id="ord-msg"></span>
				              				</div>              				
				              				<div class="form-group m-0">              					
				              					<label>Delivery postcode or email address</label>
				              					<div class="bl-msg">This must match the information used to place the order.</div>
				              					<input type="text" class="form-control" name="email" id="email_id" placeholder="e.g. 'M13 8RG' OR 'youremail@address.com' ">
				              					<span class="text-danger" id="mail-msg"></span>
				              					<span style="display: none;">Where can i find my order number?</span>
				              				</div>
				              			</div>
				          			</div>
				          		</div>
				  				<div class="Summary-card-content">
				  					<div class="row">
				  		      			<div class="col-md-6">
				  							<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<button type="button" id="fetch-product" class="btn-next btn btn-sm btn-dark">Next <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      		</div>
				  		    	</div>
				  		    </div>
		  		    	</div>
					</div>
					{{-- tab 2 start --}}
					<div class="tab-pane" id="tab2">
						<div class="row">
							<div class="col-md-8">
								<div class="item-msg"> {{-- bl-msg --}}</div>
								<div class="missguided-tabs-info">
									<div class="missguided-right-content">
										<div class="missguided-scroll">				
							          		<div class="step-content-body">
							          			<div class="step-content-form">
							          				<h3>Please select the items which are in your parcel</h3>
							          				<p style="margin-bottom: 1.1rem;">If you have multiple parcels to return, create a seperate return label for each parcel. Pierced jewellery, grooming products, and underwear/ swimwear where the hygiene seal is removed are non-refundable. All of our face coverings are non-refundable. View our full <a href="https://www.missguided.eu/help#ui-id-1" target="_blank"><b>return policy</b></a>.</p>
							          				<!-- <br> -->
							          				<div id="item-summary"></div>          				
							          			</div>
							          		</div>
							          	</div>
						          	</div>
					          	</div>
				  				{{-- <div class="Summary-card-content">
				  					<div class="row">
				  		      		</div>
				  		    	</div> --}}
				  		    </div>
			  		    	<div class="col-md-4">
			  		    		<div class="order-item rtn_sumry"></div>
			  		    		<div class="Summary-card-content">
			  		    			<div class="row mob-btn-change">
			  		    				<div class="col-md-6">
			  		          				<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="first">Back</a>
				  								</div>
				  		      				</div>
			  		          			</div>  				
			  		          			<div class="col-md-6">
			  		    					<div class="step-btn-group">
			  		          					<div class="step-btn-content">
			  		    							<a class="btn-next" id="step2-valid" href="javascript:void(0)">Next</a>
			  		    						</div>
			  		          				</div>
			  		          			</div>
			  		          		</div>
			  		    		</div>
			  		    	</div>
				  		</div>
					</div>
					{{-- tab 3 start --}}
					<div class="tab-pane" id="tab3">
						<div class="row">
							<div class="col-md-8">
								<div class="address-msg"> {{-- bl-msg --}}</div>
								<div class="missguided-tabs-info">
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			<div class="step-content-form">
							          				<h3>Confirm your personal information</h3>
							          				<div class="step-form" id="customer-info">
							          					<div class="row">
							          						<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="control-label">Full Name</label>
								                  					<input type="text" class="form-control valid-field" name="customer_name" placeholder="Enter Full Name" required="required" data-error="Full name is required">
								                  					<span class="text-danger"></span>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="">Phone Number</label>
								                  					<input type="text" class="form-control" name="customer_phone" id="customer_phone" placeholder="Enter Phone Number">
								                  					<span class="text-danger"></span>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="control-label">Email Address</label>
								                  					<input type="email" class="form-control valid-field" name="customer_mail" id="customer_mail" placeholder="youremail@address.com" required="required" data-error="Email is Required">
								                  					<input type="hidden" name="customer_order_email" id="customer_order_email" value="">
								                  					<span class="text-danger"></span>
							              							<div class="input-subtext">We will send your return label to this email address.</div>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
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
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="control-label">City</label>
								                  					<input type="text" class="form-control valid-field" name="customer_city" placeholder="Enter City" required="required" data-error="City is required">
								                  					<span class="text-danger"></span>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="control-label">Post Code</label>
								                  					<input type="text" class="form-control valid-field" name="customer_postcode" placeholder="Enter Post Code" required="required" data-error="Post Code is required">
								                  					<span class="text-danger"></span>
								                  				</div>
								                  			</div>	                  			
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label class="control-label">Address Line 1</label>
								                  					<input type="text" class="form-control valid-field" name="customer_address" placeholder="Enter Address Line 1" required="required" data-error="Address line 1 is required">
								                  					<span class="text-danger"></span>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Address Line 2</label>
								                  					<input type="text" class="form-control" name="customer_address2" placeholder="Enter Address Line 2">
								                  				</div>
								                  			</div>
								                  		</div>
							              			</div>
							          			</div>
							          		</div>
							          	</div>
							        </div>
							    </div>
							    <!-- <div class="alert alert-info">All the fields marked with Red Asterix are mandatory. However you can edit the field in case you want to change your email address or make a return from the address different than the address given above.</div> -->
				  				{{-- <div class="Summary-card-content">
				  					<div class="row">
				  		      		</div>
				  		    	</div> --}}
				  		    </div>
				  		    <div class="col-md-4">
			  		    		<div class="order-item rtn_sumry"></div>
			  		    		<div class="Summary-card-content">
			  		    			<div class="row mob-btn-change">
			  		    				<div class="col-md-6">
				  		          			<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="second">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>
			  		          			<div class="col-md-6">
			  		    					<div class="step-btn-group">
			  		          					<div class="step-btn-content">
			  		    							<a class="btn-next" id="step3-valid" href="javascript:void(0)">Next</a>
			  		    						</div>
			  		          				</div>
			  		          			</div>
			  		          		</div>
			  		    		</div>
			  		    	</div>
				  		</div>
					</div>
					{{-- tab 4 start --}}
					<div class="tab-pane" id="tab4">
						<div class="row">
							<div class="col-md-8">
								<div class="row mt-1">
								    <div class="col-md-10 error-msg"></div>
								</div>
								<div class="missguided-tabs-info">
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			<div class="step-content-form">
						              				<h3>Carrier</h3>
					                  				<div class="step-Choose-option">
				                                        <label for="card-select" class="m-0">
				                                            <div class="carrier-card">
								              					<div class="card-image">
								              						<div class="radio-container">
																	  <input type="radio" name="hermes" value="HERMES" checked="checked">
																	  <span class="checkmark"></span>
																	</div>
								              						{{-- <img src="{{ asset('public/images/hermes.jpeg') }}" height="100px;"> --}}
								              					</div>
								              					<div class="card-content">
								              						{{-- <h5>Hermes</h5> --}}
								              						<h5>Postal Service</h5>
									              					<ul>
									              					   <li>Max 10kg Max 40cm x 55cm x 53cm</li>
									              					   <li>Please note that Missguided will deduct &#128;4 shipping cost from your refund amount.</li>
									              					</ul>
								              					</div>
								              				</div>
				                                        </label>
				                        			</div>		              				
						              				<div class="return-content-list">
							              				{{-- <h3 class="m-0 font-weight-normal">Find your nearest <a class="btn-next pdf-print" href="https://international.myhermes.co.uk/help-centre/parcels/question/general/where-is-my-nearest-drop-off-point" target="_blank" id="location_url"> parcel drop-off location</a></h3> --}}
							              				<h3 class="m-0 font-weight-normal">You can drop your parcel off at any Post Office.</h3>
							              				<div class="form-group col-md-5 m-0">              					  							
							              					{{-- <label>Enter Address or postcode to find drop off points 
							              						<img src="{{ asset('public/images/location.png') }}" height="16px">
							              					</label>
							              					<input type="text" class="form-control" name="" placeholder="e.g. 'M13 8RG' "> --}}
							              				</div>

							              				<!--Google map-->
														{{-- <div id="map-container-google-1" class="z-depth-1-half map-container" style="height: 500px">
														  	<iframe src="https://maps.google.com/maps?q=frankfurt&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0"
														    style="border:0" allowfullscreen></iframe>
														</div> --}}
							              			</div>
						              			</div>
							          		</div>
							          	</div>
							        </div>
							    </div>
				  				{{-- <div class="Summary-card-content">
				  					<div class="row">
				  		      		</div>
				  		    	</div> --}}
			  		    	</div>
			  		    	<div class="col-md-4">
			  		    		<div class="order-item rtn_sumry"></div>
			  		    		<div class="Summary-card-content">
			  		    			<div class="row mob-btn-change">
			  		    				<div class="col-md-6">
				  		          			<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<a class="btn btn-next back-tab" href="javascript:void(0)" data-id="third">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>
			  		          			<div class="col-md-6">
			  		    					<div class="step-btn-group mob-mt-1">
			  		          					<div class="step-btn-content">
			  		    							<button type="submit" class="btn btn-sm btn-next save-waybill">Submit</button>
			  		    						</div>
			  		          				</div>
			  		          			</div>
			  		          		</div>
			  		    		</div>

			  		    		<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  		    		  	<div class="modal-dialog modal-dialog-centered" role="document">
			  		    		    	<div class="modal-content">
			  		    		      		<!-- <div class="modal-header">
			  		    		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  		    		          			<span aria-hidden="true">&times;</span>
			  		    		        		</button>
			  		    		      		</div> -->
			  		    		      		<div class="modal-body step-content-form">
			  		    		        		<span style="color: #444;font-size: 13px;font-weight: 600;">Your request for Label Generation is in progress. Please donot hit the Refresh or Back Button till the request is completed.</span>
			  		    		        		<p><img src="{{ asset('public/images/screen-loader.svg') }}" style="width:100%;height: 80px;"></p>
			  		    		      		</div>
			  		    		    	</div>
			  		    		  	</div>
			  		    		</div>
			  		    	</div>
			  		    </div>
					</div>					
					{{-- tab 5 start --}}
					<div class="tab-pane" id="tab5">
						<div class="row mt-1">
						    <div class="col-md-10 success-msg"></div>
						</div>
						<div class="missguided-tabs-info">
							<div class="missguided-right-content">
								<div class="missguided-scroll">		
					          		<div class="step-content-body">
					          			<div class="">
					              			<div class="step-content-form">
					              				<h3>Thanks for creating your return.</h3>
					              				<div class="mt-2 return-detail">
					              					<p>Print this label and attach it to your parcel.</p>
					              					<p>We've also sent an email from info@reversegear.net that contains your label. It might go to your junk or spam folder.</p>
					              					<p>If you have chosen a drop-off return method - you can take your parcel to any convenient drop off location. Return locations are detailed on your confirmation email.</p>
					                  			</div>
					              			</div>
					              			{{-- <div class="mt-1">
					              				<img src="{{ asset('public/images/img-1.jpg') }}" width="100%;">
					              			</div> --}}
					              		</div>	
					          		</div>
					          	</div>
					        </div>
					    </div>
		  				<div class="Summary-card-content">
		  					<div class="row">
		  		      			<div class="col-md-6">
		  							<div class="step-btn-group">
		  		      					<div class="step-btn-content">
											<a class="btn-next pdf-print" href="javascript:void(0)" target="_blank">Download and Print</a>
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
</div>