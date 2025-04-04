<form method="post" action="{{ route('shopify.shipping.code.store') }}" enctype="multipart/form-data">
	@csrf
	<input type="file" name="ship_code" value="">
	<button type="submit">Submit</button>
</form>