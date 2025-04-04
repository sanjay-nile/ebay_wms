@push('js')
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
		    if (hrf == '#tab5') {
		    	$('.order-item').hide();
		    	$('.last-item').show();
		    }
		    e.preventDefault();
		});

		$('ul#progressbar li').on('click', function(e) {
			let hrf = $(this).find('a').attr('href');
			$(this).addClass('active');
			$(this).prevAll('li').addClass('active');
			$(this).nextAll('li').removeClass('active');
			if (hrf == '#tab1') {
		    	$('.order-item').hide();
		    	$('.last-item').hide();
		    	$('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Next</a>');
		    }
			if (hrf == '#tab2' || hrf == '#tab3' || hrf == '#tab4') {
		    	$('.order-item').show();
		    	$('.last-item').hide();
		    	$('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Next</a>');
		    }
		    if (hrf == '#tab4') {
		    	$('#nxt-btn').html('');
		    }
		    if (hrf == '#tab5') {
		    	$('.order-item').hide();
		    	$('.last-item').show();
		    	$('#nxt-btn').html('<a class="btn-next next-li"  href="javascript:void(0)">Make another return</a>');
		    }
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
		    if(ordr_id != ''){
		        $.ajax({
		            url:"{{ route('missguided.fetch.order') }}",
		            type:"GET",
		            data : {ordr_id:ordr_id},
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
		                    $('#email_id').val(response.mail);
		                } else{
		                    alert(response.msg);
		                }
		            },
		            complete: function() {
		                $('#load').addClass('collapse');
		            },
		        });
		    } else {
		        alert('Please Enter the Order Number.');
		    }
		});
	});
</script>
@endpush

<div class="missguided-list">
	<div class="missguided-left-content">
		<div class="missguided-media">
			<img src="{{ asset('public/images/missguided.jpg') }}">
		</div>
	</div>
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
	            <li class="">
	              	<a href="#tab5" data-toggle="tab" aria-expanded="false" id="five">
	                	<span class="number">5</span>
	              	</a>
	            </li> 
	            <li class="">
	              	<a href="#tab6" data-toggle="tab" aria-expanded="false" id="Six">
	                	<span class="number">6</span>
	              	</a>
	            </li>  
	        </ul>
		</div>
		<div class="step-content tab-content">
			<div class="tab-pane active" id="tab1">
          		<div class="step-content-body">
          			<div class="step-content-form">
          				<h3>Return an item in a few easy steps</h3>
          				<p>Start by providing some information about your purchase so we can locate your order</p>
          				<div class="step-form step-60 ">
              				<div class="form-group">
              					<div class="bl-msg">
              						Due to current restrictions, we have extended our return policy from 14 days to 28 days from your delivery date
              					</div>
              					<div class="bl-msg">This must match the information used to place the order.</div>
              					<label>Order Number</label>
              					<input type="text" class="form-control" name="order_no" id="order_no" placeholder="e.g 'XXXXXXXXX'">
              					{{-- <div class="msg-text tooltip">Where can i find my order number?
              						<span class="tooltiptext">You can find your 9 digit order number starting with ‘1’ on your order confirmation email or delivery note</span>
              					</div> --}}
              				</div>
              				<div class="form-group">
              					<button type="button" id="fetch-product" class="btn btn-sm btn-dark">Fetch Order Detail <i class="fa fa-spinner fa-spin fa-1x fa-fw collapse" id="load"></i></button>
              				</div>
              				<div class="form-group">              					
              					<label>Delivery postcode or email address</label>
              					<input type="text" class="form-control" name="email" id="email_id" placeholder="e.g. 'M13 8RG' OR 'youremail@address.com' ">
              					<span style="display: none;">Where can i find my order number?</span>
              				</div>
              				{{-- <div class="bl-msg">
              					Your order has exceeded our return policy timeframe. View our <a href="https://www.missguided.eu/help#ui-id-1" target="_blank"><b>return policy</b></a> for more details.
              				</div> --}}
              				{{-- <div class="form-group">
              					<div class="row checkboxes ">
	              					<p class="col-md-12">Do you have a Authorization code from our Customer Service Representative team</p>
	              					<div class=" input-yes col-md-2">
		              					<input type="radio" id="check-Yes" name="auth_off" value="Yes">
		              					<label>Yes</label>
		              				</div>
		              				<div class="input-no col-md-2">
		              					<input type="radio" id="check-No" name="auth_off" value="No">
		              					<label>No</label>
		              				</div>
		              			</div>
              				</div> --}}
		          			<div class="form-group collapse" id="yes_div">
	          					<label>Authorization Code</label>
	          					<input type="text" class="form-control" name="" placeholder="Enter Unique Authorization Code">
	          				</div>
	          				<div class="form-group collapse" id="no_div">
	          					<label>Since Your Return request exceeds the Return Policy period of 36 days. Please get in touch with our Customer Service Representative.</label>
	          				</div>
              			</div>
          			</div>
          		</div>
  				<div class="Summary-card-content">
  					<div class="row">
  		      			<div class="col-md-6">
  							<div class="step-btn-group">
  		      					<div class="step-btn-content">
  									<a class="btn-next next-li"  href="#">Next</a>
  								</div>
  		      				</div>
  		      			</div>
  		      		</div>
  		    	</div>
			</div>
			{{-- tab 2 start --}}
			<div class="tab-pane" id="tab2">
          		<div class="step-content-body">
          			<div class="step-content-form">
          				<h3>Please select the items which are in your parcel</h3>
          				<p>If you have multiple parcels to return, create a seperate return label for each parcel. Pierced jewellery, grooming products, and underwear/ swimwear where the hygiene seal is removed are non-refundable. All of our face coverings are non-refundable. View our full <a href="https://www.missguided.eu/help#ui-id-1" target="_blank"><b>return policy</b></a>.<br>
          				{{-- <u>
          					<a href="https://www.missguided.eu/help#help-returns-container" target="_blank" style="color: #05c;">View our full return policy &gt;</a>
          				</u> --}}
          				</p>
          				<div id="item-summary">
	          				<div class="return-content-list">
	              				<div class="item-info">
	              					<div class="product-text">Pink Puppy love dog Sweater-L</div>
	              					<div class="product-price">20.00 USD</div>
	              					<div class="product-code">Item Code: 123345</div>
	              				</div>
	              				<div class="step-form">
	              					<div class="row">
	              						<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Quantity of return</label>
			                  					<select class="form-control">
			                  						<option>I'll keep these thanks</option>
			                  						<option>1 item</option>
			                  						<option>2 item</option>
			                  						<option>3 item</option>
			                  					</select>
			                  				</div>
			                  			</div>
			                  			<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Reason for return</label>
			                  					<select class="form-control">
			                  						<option>Changed my mind</option>
			                  						<option>Doesn’t suit me</option>
			                  						<option>Incorrect item received</option>
			                  						<option>Not like picture</option>
			                  						<option>Fit – Too big/ Too long</option>
			                  						<option>Fit – Too small/ Too short</option>
			                  						<option>Faulty</option>
			                  						<option>Poor value/ Poor quality</option>
			                  						<option>Bought more than one for style/colour/size</option>
			                  						<option>Fabric – I don’t like it</option>
			                  					</select>
			                  				</div>
			                  			</div>
			                  			<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Comments</label>
			                  					<textarea placeholder="comments" class="form-control" type="text"></textarea>  
			                  				</div>
			                  			</div>
			                  		</div>
	                  			</div>
	                  		</div>
	                  		<div class="return-content-list item-2">
	              				<div class="item-info">
	              					<div class="product-text">Pink Puppy love dog Sweater-M</div>
	              					<div class="product-price">30.00 USD</div>
	              					<div class="product-code">Item Code: 98765</div>
	              				</div>
	              				<div class="step-form">
	              					<div class="row">
	              						<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Quantity of return</label>
			                  					<select class="form-control">
			                  						<option>I’ll keep these thanks</option>
			                  						<option>1 item</option>
			                  						<option>2 item</option>
			                  						<option>3 item</option>
			                  					</select>
			                  				</div>
			                  			</div>
			                  			<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Reason for return</label>
			                  					<select class="form-control">
			                  						<option>Changed my mind</option>
			                  						<option>Doesn’t suit me</option>
			                  						<option>Incorrect item received</option>
			                  						<option>Not like picture</option>
			                  						<option>Fit – Too big/ Too long</option>
			                  						<option>Fit – Too small/ Too short</option>
			                  						<option>Faulty</option>
			                  						<option>Poor value/ Poor quality</option>
			                  						<option>Bought more than one for style/colour/size</option>
			                  						<option>Fabric – I don’t like it</option>
			                  					</select>
			                  				</div>
			                  			</div>
			                  			<div class="col-md-12">
			                  				<div class="form-group">
			                  					<label>Comments</label>
			                  					<textarea placeholder="comments" class="form-control" type="text"></textarea>  
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
  									<a class="btn-next back-tab" href="javascript:void(0)" data-id="first">Back</a>
  								</div>
  		      				</div>
  		      			</div>
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
			{{-- tab 3 start --}}
			<div class="tab-pane" id="tab3">
          		<div class="step-content-body">
          			<div class="step-content-form">
          				<h3>Confirm your personal information</h3>
          				<div class="step-form" id="customer-info">
          					<div class="row">
          						<div class="col-md-6">
	                  				<div class="form-group">
	                  					<label>Full Name</label>
	                  					<input type="text" class="form-control" name="full_name" placeholder="John Doe">
	                  				</div>
	                  			</div>
	                  			<div class="col-md-6">
	                  				<div class="form-group">
	                  					<label>Phone Number</label>
	                  					<input type="text" class="form-control" name="phone_number" placeholder="9988776655">
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
	                  					<select class="form-control">
	                  						<option>Frankfurt</option>
	                  					</select>
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
  				<div class="Summary-card-content">
  					<div class="row">
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
  									<a class="btn-next next-li"  href="#">Next</a>
  								</div>
  		      				</div>
  		      			</div>
  		      		</div>
  		    	</div>
			</div>
			{{-- tab 4 start --}}
			<div class="tab-pane" id="tab4">
          		<div class="step-content-body">
          			<div class="order-item">
			    		<div class="Summary-card-header">
			    			<div class="Summary-card-title">Return Summary</div>
			    		</div>
			    		<div class="Summary-card-body">
			    			<div class="Summary-card-list">
			    				<div class="Summary-card-item">
			    					<div class="Summary-title">Pink Puppy love dog Sweater-L</div>
			    					<div class="Summary-value">20.00 USD</div>
			    				</div>
			    				<div class="">
			    					<div class="Summary-title">Qty:1</div>
			    				</div>
			    				<hr>
			    				<div class="Summary-card-item">
			    					<div class="Summary-title">Pink Puppy love dog Sweater-M</div>
			    					<div class="Summary-value">30.00 USD</div>
			    				</div>
			    				<div class="">
			    					<div class="Summary-title">Qty:1</div>
			    				</div>
			    			</div>
			    		</div>	    		
	    				<hr>
			    		<div class="Summary-card-footer">
			    			<div class="Summary-total-content">
			        			<div class="Summary-total-title">Return total</div>
			        			<div class="Summary-total-value">50.00 USD</div>
			        		</div>
			        		<ul>
				    			<li>Your actual refund amount will take into account any discount applied to your order</li>
				    			<li>Please note that Misguided will deduct €4 shipping cost from your refund amount</li>
				    		</ul>
			    		</div>
	    			</div>			    	
          			<div class="step-content-form">
          				<h3>Choose Return Method</h3>
          				<div class="step-form">
          					<div class="row">
	                  			<div class="col-md-12">
	                  				<div class="PBCheckbox  step-Choose-option">
                            			<input type="radio" id="By_Mail" name="drop_off" value="By_Mail/Courier" checked>
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
          						<!-- <div class="col-md-12">
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
	                  			</div> -->
	                  			<div class="col-md-12 mt-2" style="display: none;">
	                  				<p>1. Here is tracking number: 172AF52364789</p>
	                  				<p>2. Email send confirming you request including label and drop off location</p>
	                  				<p>3. Any problem please Email : info@reversegear.net</p>
	                  				<p>Thankyou for shopping</p>
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
  		      			<div class="col-md-6">
  							<div class="step-btn-group">
  		      					<div class="step-btn-content">
  									<a class="btn-next next-li"  href="#">Next</a>
  								</div>
  		      				</div>
  		      			</div>
  		      		</div>
  		    	</div>
			</div>
			{{-- tab 5 start --}}
			<div class="tab-pane" id="tab5">
          		<div class="step-content-body">
          			<div class="step-content-body">
              			<div class="step-content-form">
              				<h3>Select Carrier</h3>
              				<div class="carrier-card">
              					<div class="card-image">
              						<img src="http://dev-returns.reversegear.net/public/images/hermes.jpeg" height="70px;">
              					</div>
              					<div class="card-content">
              						<h5>Hermes</h5>
              					<ul>
              					   <li>Max 20kg Max 40cm X 55cm x 53cm</li>
              					   <li>Please note that Misguided will deduct &#128;4 shipping cost from your refund amount.</li>
              					</ul>
              					</div>
              					<div class="select-check-btn ">
              						<input type="radio" id="card-select" name="drop_off" 
              						value="">
              					</div>
              				</div>
              				<div class="return-content-list">
	              				<h3>Your nearest drop-off points are below. You can return your item to any of these drop off points.</h3>
	              				<div class="form-group">
	              					<label>Enter Address or postcode to find drop off points 
	              						<img src="http://dev-returns.reversegear.net/public/images/location.png"
	              						 height="16px">
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
			</div>
			{{-- tab 6 start --}}
			<div class="tab-pane" id="tab6">
          		<div class="step-content-body">
          			<div class="step-content-body">
              			<div class="step-content-form ">
              				<h3>Thanks for creating your return.</h3>
              				<div class="col-md-12 mt-2 return-content-list return-detail">
              					<p>Print this label and attach it to your parcel.</p>
              					<p>We've also sent an email from info@reversegear.net that contains your label. It might go to your junk or spam folder.</p>
              					<p>If you have chosen a drop-off return method - you can take your parcel to any convenient drop off location. Return locations are detailed on your confirmation email.</p>
              					{{-- <h6>To return your item follow these 4 simple steps:</h6>
                  				<p>1. Take your parcel to your most convenient drop-off point</p>
                  				<p>2. Show the label on your mobile at the in-store terminal (or show it to a member of staff to scan)
                  				</p>
                  				<p>3. Print a label in-store</p>
                  				<p>4. We will confirm when the package is received at the store and at our hub and checked and refund is issued</p> --}}
                  			</div>
              			</div>
              			<div>
              				<img src="http://dev-returns.reversegear.net/public/images/img-1.jpg" width="100%;">
              			</div>
              		</div>	
          		</div>
  				<div class="Summary-card-content">
  					<div class="row">
  		  				<div class="col-md-6">
  		          			<div class="step-btn-group">
  		      					<div class="step-btn-content">
  									<a class="btn-next" href="javascript:void(0)">Save to Gallery</a>
  								</div>
  		      				</div>
  		      			</div>
  		      			<div class="col-md-6">
  							<div class="step-btn-group">
  		      					<div class="step-btn-content">
									<a class="btn-next" href="javascript:void(0)"> Download and Print</a>  									
  								</div>
  		      				</div>
  		      			</div>
  		      		</div>
  		    	</div>
			</div>
		</div>
	</div>
</div>

