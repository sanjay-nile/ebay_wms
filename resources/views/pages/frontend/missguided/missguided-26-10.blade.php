@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('body').on('click','.next-li',function(e){
		    $('ul#progressbar li.active').next('li').find('a').trigger('click');

		    // $('ul#progressbar li.active').next('li').addClass('active');
		    let hrf = $('ul#progressbar li.active:last').find('a').attr('href');
		    if (hrf == '#tab2' || hrf == '#tab3' || hrf == '#tab4') {
		    	$('.order-item').show();
		    	$('.last-item').hide();
		    }
		    if (hrf == '#tab1' || hrf == '#tab5') {
		    	$('#nxt-btn-add').addClass('collapse');
		    }
		    if (hrf == '#tab4') {
		    	$('#nxt-btn-add').html('<div class="col-md-6"><div class="step-btn-group"><div class="step-btn-content"><button type="submit" class="btn btn-sm btn-next save-waybill">Submit</button></div></div></div>');
		    }
		    e.preventDefault();
		});

		$('ul#progressbar li').on('click', function(e) {
			let hrf = $(this).find('a').attr('href');
			$(this).addClass('active');
			$(this).prevAll('li').addClass('active');
			$(this).nextAll('li').removeClass('active');
			if (hrf == '#tab1' || hrf == '#tab5') {
	    		// $('.order-item').hide();
		    	// $('.last-item').hide();
		    	// $('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Next</a>');
		    	$('#nxt-btn-add').addClass('collapse');
		    	// $('.rtn_sumry').html(response.refund_html);
		    }
			// if (hrf == '#tab2' || hrf == '#tab3' || hrf == '#tab4') {
		 //    	$('.order-item').show();
		 //    	$('.last-item').hide();
		 //    	$('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Next</a>');
		 //    }
		    if (hrf == '#tab4') {
		    	// $('#nxt-btn').html('');
		    	$('#nxt-btn-add').html('<div class="col-md-6"><div class="step-btn-group"><div class="step-btn-content"><button type="submit" class="btn btn-sm btn-next save-waybill">Submit</button></div></div></div>');
		    }
		    // if (hrf == '#tab5') {
		    // 	$('.order-item').hide();
		    // 	$('.last-item').show();
		    // 	$('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Make another return</a>');
		    // }
		    e.preventDefault();
		});

		$('.back-tab').on('click', function(e) {
			let tb = $(this).attr('data-id');
			$('ul#progressbar li').find('a#'+tb).trigger('click');
		    e.preventDefault();
		});

		$('input[name=auth_off]').change(function(){
		    let v = $(this).val();
		    if(v == 'Yes'){
		        $('#yes_div').removeClass('collapse');
		        $('#no_div').addClass('collapse');
		    } else {
		        $('#yes_div').addClass('collapse');
		        $('#no_div').removeClass('collapse');
		    }
		});

		$('body').on('click','#fetch-product',function(){
		    let ordr_id = $('#order_no').val();
		    let mail_id = $('#email_id').val();
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
		                // console.log(response);
		                if(response.status){
		                    $('#item-summary').html(response.item_html);
		                    $('#customer-info').html(response.addres_html);
		                    $('.rtn_sumry').html(response.refund_html);
		                    $('ul#progressbar li.active').next('li').find('a').trigger('click');
		                    $('#nxt-btn-add').removeClass('collapse');
		                } else{
		                    // alert(response.msg);
		                    $('.order-msg').html(`<div class="alert alert-danger alert-dismissible">
		                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                        ${response.msg}    
		                    </div>`);
		                }
		            },
		            complete: function() {
		                $('#load').addClass('collapse');
		            },
		        });
		    } else {
		        // alert('Please Enter the Order Number and Email.');
		        $('.order-msg').html(`<div class="alert alert-danger alert-dismissible">
		            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		            It seems the details entered are incorrect. Please verify the details and re-enter them   
		        </div>`);
		    }
		});

		$('body').on('change','.rtn_qty',function(e){
		    let ke = $(this).attr('data-key');
		    let price = $(this).attr('data-price');
		    let vl = $(this).val();
		    if(vl == 0){
		    	$('.price_'+ke).html('0 EUR');
		    	$('.qty_'+ke).html('Qty:0');
		    	$('.itm_total').html('0 EUR');
		    } else {
		    	let it = vl * price;
		    	$('.price_'+ke).html(it+' EUR');
		    	$('.qty_'+ke).html('Qty:'+vl);

		    	let ship = 4;
		    	var total = 0;
		    	$('.rtn_qty  > option:selected').each(function() {
		    		total += $(this).val() * $(this).attr('item-price');
		    	    // console.log($(this).attr('item-price') + ' ' + $(this).val());
		    	});
		    	let all_tt = total - ship;
		    	$('.itm_total').html(total +' EUR');
		    	$('.ttl_price').html(all_tt +' EUR');
		    	$('.ship_chrg').html('- '+ship +' EUR');

		    	// console.log(total);
		    }		    
		    // console.log(price);
		    // console.log(vl);
		    e.preventDefault();
		});
	});

	// form submit..
	$(document).ready(function() {
	    $.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

	    //----------------------------------------------------------------------------------------------        
	    $("#create-waybill").on('submit',function(e){
	        e.preventDefault();
	        var formData = $(this);        
	        //return false;
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
	                    $(".save-waybill").html(`Submit`).attr('disabled',false);
	                    $('ul#progressbar li.active').next('li').find('a').trigger('click');
	                    return false;
	                }else{
	                    $('.error-msg').html(`<div class="alert alert-info alert-dismissible">
	                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	                        ${response.message}    
	                    </div>`);
	                    $(".save-waybill").html(`Submit`).attr('disabled',false);
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
	                    return false;
	                }else{
	                    $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
	                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	                        ${data.statusText}    
	                    </div>`);
	                    $(".save-waybill").html(`Submit`).attr('disabled',false);
	                    return false;
	                }
	            }
	        });
	    });
	});
</script>
@endpush

<div class="row">
	<div class="col-md-8">
		<div class="missguided-wrapper-content">
			<div class="missguided-list">
				<div class="tab-pane1 missguided-right-content" id="tabs">
					<div class="step-info">
						<ul id="progressbar" class="nav nav-tabs">
				            <li class="active">
				              	<a data-toggle="tab" href="#tab1"  aria-expanded="true" id="first">
				                	<span class="number">1</span>
				              	</a>
				            </li>
				            <li class="">
				              	<a href="#tab2" data-toggle="tab" aria-expanded="false" id="second">
				                	<span class="number">2</span>
				              	</a>
				            </li>
				            <li class="">
				              	<a href="#tab3" data-toggle="tab" aria-expanded="false" id="third">
				                	<span class="number">3</span>
				              	</a>
				            </li>
				            <li class="">
				              	<a href="#tab4" data-toggle="tab" aria-expanded="false" id="four">
				                	<span class="number">4</span>
				              	</a>
				            </li>
				            <!-- <li class="">
				              	<a href="#tab5" data-toggle="tab" aria-expanded="false" id="five">
				                	<span class="number">5</span>
				              	</a>
				            </li>  -->
				            <li class="">
				              	<a href="#tab6" data-toggle="tab" aria-expanded="false" id="Six">
				                	<span class="number">5</span>
				              	</a>
				            </li>  
				        </ul> 
					</div>
					<form method="post" id="create-waybill" action="{{ route('missguided.order.create') }}">
						<div class="step-content tab-content">
							{{-- step 1 --}}
							<div class="tab-pane active" id="tab1">
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
				              				</div>              				
				              				<div class="form-group">              					
				              					<label>Delivery postcode or email address</label>
				              					<input type="text" class="form-control" name="email" id="email_id" placeholder="e.g. 'M13 8RG' OR 'youremail@address.com' ">
				              					<span style="display: none;">Where can i find my order number?</span>
				              				</div>
				              				<div class="bl-msg">This must match the information used to place the order.</div>
				              				{{-- <div class="form-group">
				              					<button type="button" id="fetch-product" class="btn btn-sm btn-dark">Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
				              				</div> --}}              				
				              			</div>
				          			</div>
				          		</div>
				  				<div class="Summary-card-content">
				  					<div class="row">
				  		      			<div class="col-md-6">
				  							<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									{{-- <a class="btn-next next-li"  href="#">Next</a> --}}
				  									<button type="button" id="fetch-product" class="btn-next btn btn-sm btn-dark">Next <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      		</div>
				  		    	</div>
							</div>
							{{-- tab 2 start --}}
							<div class="tab-pane" id="tab2">
								<div class="missguided-tabs-info">
									{{-- <div class="missguided-left-content">
										<div class="missguided-media">
											<img src="{{ asset('public/images/missguided.jpg') }}">
										</div>
									</div> --}}
									<div class="missguided-right-content">
										<div class="missguided-scroll">				
							          		<div class="step-content-body">
							          			<div class="step-content-form">
							          				<h3>Please select the items which are in your parcel</h3>
							          				<p>If you have multiple parcels to return, create a seperate return label for each parcel. Pierced jewellery, grooming products, and underwear/ swimwear where the hygiene seal is removed are non-refundable. All of our face coverings are non-refundable. View our full <a href="https://www.missguided.eu/help#ui-id-1" target="_blank"><b>return policy</b></a>.</p>
							          				<br>
							          				<div id="item-summary"></div>          				
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
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="first">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      		</div>
				  		    	</div>
							</div>
							{{-- tab 3 start --}}
							<div class="tab-pane" id="tab3">
								<div class="missguided-tabs-info">									
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			<div class="step-content-form">
							          				{{-- <div class="order-item rtn_sumry" id="rtn_sumry_2"></div> --}}
							          				<h3>Confirm your personal information</h3>
							          				<div class="step-form" id="customer-info">
							          					<div class="row">
							          						<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Full Name</label>
								                  					<input type="text" class="form-control" name="customer_name" placeholder="John Doe">
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Phone Number</label>
								                  					<input type="text" class="form-control" name="customer_phone" placeholder="9988776655">
								                  				</div>
								                  			</div>	                  			
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Email Address</label>
								                  					<input type="text" class="form-control" name="customer_mail" placeholder="youremail@address.com">
							              							<div class="input-subtext">We will send your return label to this email address.</div>
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
																<div class="form-group">
																	<label>County</label>
																	<input type="text" class="form-control" name="customer_country" value="">			
																</div>
															</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>City</label>
								                  					<input type="text" class="form-control" name="customer_city" placeholder="Frankfurt am Main">
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Post Code</label>
								                  					<input type="text" class="form-control" name="customer_postcode" placeholder="60486">
								                  				</div>
								                  			</div>	                  			
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Address Line 1</label>
								                  					<input type="text" class="form-control" name="customer_address1" placeholder="Lise-Meitner-Str.2">
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					
								                  				</div>
								                  			</div>
								                  			<div class="col-md-6">
								                  				<div class="form-group">
								                  					<label>Address Line 2</label>
								                  					<input type="text" class="form-control" name="customer_address2" placeholder="">
								                  				</div>
								                  			</div>
								                  		</div>
							              			</div>
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
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="second">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      		</div>
				  		    	</div>
							</div>
							{{-- tab 4 start --}}
							<div class="tab-pane" id="tab4">
								<div class="row mt-1">
								    <div class="col-md-10 error-msg"></div>
								</div>
								<div class="missguided-tabs-info">
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			{{-- <div class="order-item rtn_sumry"></div> --}}								    			
							          			{{-- <div class="step-content-form">
							          				<h3>Choose Return Method</h3>
							          				<div class="step-form">
							          					<div class="row">
								                  			<div class="col-md-12">
								                  				<div class="PBCheckbox  step-Choose-option">
							                            			<input type="radio" id="By_Mail" name="drop_off" value="By_Mail/Courier">
							                                        <label for="By_Mail">
							                                            <p><b>By Mail/Courier</b></p>
							                                            <p class="color-grey">Create a label to drop off your return at nearest store location:</p>
							                                            <p>
							                                                <u>
							                                                	<a href="https://www.myhermes.de/paketshop/" target="_blank" style="color: #05c;">Nearest Store Location &gt;</a>
							                                                </u>
							                                            </p>
							                                        </label>
							                        			</div>
								                  			</div>
							          						<div class="col-md-12">
								                  				<div class="PBCheckbox  step-Choose-option">
							                            			<input type="radio" id="By_ReturnBar" name="drop_off" value="By_ReturnBar">
							                                        <label for="By_ReturnBar">
							                                            <p><b>By ReturnsBar Drop Off</b></p>
							                                            <p class="color-grey">Create a QR code to drop off your return at a Returns Bar Drop Off. No packaging, label or printer required.</p>
							                                            <p>
							                                                <u>
							                                                	<a href="https://www.happyreturns.com/" target="_blank" style="color: #05c;">How it works &gt;</a>
							                                                </u>
							                                            </p>
							                                        </label>
							                        			</div>
								                  			</div>
								                  			<div class="col-md-12 mt-2" style="display: none;">
								                  				<p>1. Here is tracking number: 172AF52364789</p>
								                  				<p>2. Email send confirming you request including label and drop off location</p>
								                  				<p>3. Any problem please Email : info@reversegear.net</p>
								                  				<p>Thankyou for shopping</p>
								                  			</div>
								                  		</div>
							              			</div>
							          			</div> --}}
							          			<div class="step-content-form">
						              				<h3>Carrier</h3>
					                  				<div class="step-Choose-option">
					                  					{{-- <input type="radio" id="card-select" name="hermes" value="hermes"> --}}
				                                        <label for="card-select">
				                                            <div class="carrier-card">
								              					<div class="card-image">
								              						<img src="{{ asset('public/images/hermes.jpeg') }}" height="100px;">
								              					</div>
								              					<div class="card-content">
								              						<h5>Hermes</h5>
									              					<ul>
									              					   <li>Max 20kg Max 40cm X 55cm x 53cm</li>
									              					   <li>Please note that Misguided will deduct &#128;4 shipping cost from your refund amount.</li>
									              					</ul>
								              					</div>
								              				</div>
				                                        </label>
				                        			</div>		              				
						              				<div class="return-content-list">
							              				<h3>Your nearest drop-off points are below. You can return your item to any of these drop off points.</h3>
							              				<div class="form-group">
							              					<label>Enter Address or postcode to find drop off points 
							              						<img src="{{ asset('public/images/location.png') }}" height="16px">
							              					</label>
							              					<input type="text" class="form-control" name="" placeholder="e.g. 'M13 8RG' ">
							              				</div>

							              				<!--Google map-->
														<div id="map-container-google-1" class="z-depth-1-half map-container" style="height: 500px">
														  	<iframe src="https://maps.google.com/maps?q=frankfurt&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0"
														    style="border:0" allowfullscreen></iframe>
														</div>
							              			</div>
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
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="third">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>				  		      			
				  		      		</div>
				  		    	</div>
							</div>
							{{-- tab 5 start --}}
							<!-- <div class="tab-pane" id="tab5">
								<div class="missguided-tabs-info">
									<div class="missguided-left-content">
										<div class="missguided-media">
											<img src="{{ asset('public/images/missguided.jpg') }}">
										</div>
									</div>
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			<div class="step-content-body">
							              			
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
				  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="four">Back</a>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      			<div class="col-md-6">
				  							<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<a class="btn-next next-li"  href="#">Submit</a>
				  								</div>
				  		      				</div>
				  		      			</div>
				  		      		</div>
				  		    	</div>
							</div> -->
							{{-- tab 6 start --}}
							<div class="tab-pane" id="tab6">
								<div class="row mt-1">
								    <div class="col-md-10 success-msg"></div>
								</div>
								<div class="missguided-tabs-info">
									{{-- <div class="missguided-left-content">
										<div class="missguided-media">
											<img src="{{ asset('public/images/missguided.jpg') }}">
										</div>
									</div> --}}
									<div class="missguided-right-content">
										<div class="missguided-scroll">		
							          		<div class="step-content-body">
							          			<div class="step-content-body">
							              			<div class="step-content-form ">
							              				<h3>Thanks for creating your return.</h3>
							              				<div class="mt-2 return-detail">
							              					<p>Print this label and attach it to your parcel.</p>
							              					<p>We've also sent an email from info@reversegear.net that contains your label. It might go to your junk or spam folder.</p>
							              					<p>If you have chosen a drop-off return method - you can take your parcel to any convenient drop off location. Return locations are detailed on your confirmation email.</p>
							                  			</div>
							              			</div>
							              			<div>
							              				<img src="{{ asset('public/images/img-1.jpg') }}" width="100%;">
							              			</div>
							              		</div>	
							          		</div>
							          	</div>
							        </div>
							    </div>
				  				<div class="Summary-card-content">
				  					<div class="row">
				  		  				<!-- <div class="col-md-6">
				  		          			<div class="step-btn-group">
				  		      					<div class="step-btn-content">
				  									<a class="btn-next pdf-print" href="javascript:void(0)" target="_blank">Save to Gallery</a>
				  								</div>
				  		      				</div>
				  		      			</div> -->
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
	</div> 
	<div class="col-md-4">
		<div class="order-item rtn_sumry" id="rtn_sumry"></div>
		<div class="Summary-card-content collapse" id="nxt-btn-add">
			<div class="row">  				
      			<div class="col-md-6">
					<div class="step-btn-group">
      					<div class="step-btn-content">
							<a class="btn-next next-li" href="#">Next</a>
						</div>
      				</div>
      			</div>
      		</div>
		</div>
	</div>
</div>



