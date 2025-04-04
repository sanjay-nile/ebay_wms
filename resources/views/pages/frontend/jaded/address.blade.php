<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label>Full Name</label>
			<input type="text" class="form-control" name="customer_name" value="{{ $order['delivery_name'] }}">
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>Phone Number</label>
			<input type="text" class="form-control" name="customer_phone" value="{{ $order['contact_phone'] }}">
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>Email Address</label>
			<input type="text" class="form-control" name="customer_mail" value="{{ $order['customer_email'] }}">
			<div class="input-subtext">We will send your return label to this email address.</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>County</label>
			<input type="text" class="form-control" name="customer_country" value="{{ $order['delivery_country'] }}">			
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>City</label>
			<input type="text" class="form-control" name="customer_city" value="{{ $order['delivery_town'] }}">
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>Post Code</label>
			<input type="text" class="form-control" name="customer_postcode" value="{{ $order['delivery_postcode'] }}">
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>Address Line 1</label>
			<input type="text" class="form-control" name="customer_address" value="{{ $order['delivery_address1'] }}">
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>Address Line 2</label>
			<input type="text" class="form-control" name="customer_address2" value="{{ $order['delivery_address2'] }}">
		</div>
	</div>
</div>