@forelse($product as $k => $item)
	@php
		$name = explode(',', $item['name']);
		$color = end($name);

		$size = '';
		$option = reset($item['product_options']);
		if (isset($option['display_name']) && $option['display_name'] == 'Size') {
			# code...
			$size = $option['display_value'];
		}

		# product image data...
		$img_url = \Config::get('constants.oliveUrl').'v3/catalog/products/'.$item['product_id'].'/images';
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_URL => $img_url,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		        "x-auth-client: ".Config::get('constants.authClient'),
		        "x-auth-token: ".Config::get('constants.authToken'),
		        "Content-Type: application/json",
		        "Accept: application/json"
		    ),
		));

		$img_response = curl_exec($curl);
		curl_close($curl);
		$images = json_decode($img_response, true);

		# for hs code...
		$hs_url = \Config::get('constants.oliveUrl').'v3/catalog/products/'.$item['product_id'];
		$hs_curl = curl_init();
		curl_setopt_array($hs_curl, array(
		    CURLOPT_URL => $hs_url,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		        "x-auth-client: ".Config::get('constants.authClient'),
		        "x-auth-token: ".Config::get('constants.authToken'),
		        "Content-Type: application/json",
		        "Accept: application/json"
		    ),
		));

		$hs_response = curl_exec($hs_curl);
		curl_close($hs_curl);
		$hs_code = json_decode($hs_response, true);
		// dd($hs_code);
	@endphp
	<div class="return-form-item add-{{ $k }}">		
		<div class="row">
			@if(isset($images['data']))
				@forelse($images['data'] as $img)
					@if($img['is_thumbnail'])
						<div class="col-md-2">
							{{-- <input type="checkbox" name="item-select[]" value="{{ $k }}" class="item-chk" style="margin: 27px 4px 0px 0px;" @if(count($product) == 1) checked @endif> --}}
							<input type="checkbox" name="item-select[]" value="{{ $k }}" class="item-chk" style="margin: 27px 4px 0px 0px;">
							<img src="{{ $img['url_thumbnail'] }}" class="img-responsive img-thumbnail" style="width: 60px;">
							<input type="hidden" name="image_of_item[]" value="{{ $img['url_thumbnail'] }}">
						</div>
					@endif
				@empty
				@endforelse
			@else
				<div class="col-md-2">
					<input type="checkbox" name="item-select[]" value="{{ $k }}" class="item-chk" style="margin: 27px 4px 0px 0px;" @if(count($product) == 1) checked @endif>
					<input type="hidden" name="image_of_item[]" value="">
					<img src="{{ asset('public/images/no-image.jpg') }}" class="img-responsive img-thumbnail" style="width: 60px;">
				</div>
			@endif
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Item Bar Code	</label>
					<input type="text" class="form-control item-barcode" name="bar_code[]" value="{{ $item['sku'] }}">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Item Name	</label>
					<input type="text" class="form-control item-title" name="title[]" value="{{ $item['name'] }}">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Quantity</label>
					<input type="text" class="form-control package_count_arr" name="package_count[]" value="{{ $item['quantity'] }}" autocomplete="off">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Reason for Return</label>
					<select class="form-control valid" name="item_return_reason[]" aria-invalid="false" id="itm-rtn-{{ $k }}">
						<option value="">-- Select a Reason--</option>
						@foreach(olive_reason_of_return() as $k => $v)
	                    	<option value="{{ $k }}">{{ $v }}</option>
	                    @endforeach
	                </select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Color</label>
					<input type="text" class="form-control clr_arr" name="color[]" placeholder="Color" value="{{ $color }}">
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="">Size</label>
					<input type="text" class="form-control sze_ar" name="size[]" placeholder="Size" value="{{ $size }}">
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Dimension</label>
					<select class="form-control valid" name="dimension[]" aria-invalid="false">
	                    <option value="IN">IN</option>
	                    <option value="CM">CM</option>
	                </select>
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Length</label>
					{{-- <input type="text" class="form-control" name="length[]" placeholder="Length" value="{{ $item['depth'] }}"> --}}
					<input type="text" class="form-control" name="length[]" placeholder="Length" value="10">
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Width</label>
					{{-- <input type="text" class="form-control" name="width[]" placeholder="Width" value="{{ $item['width'] }}"> --}}
					<input type="text" class="form-control" name="width[]" placeholder="Width" value="8">
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Height</label>
					{{-- <input type="text" class="form-control" name="height[]" placeholder="Height" value="{{ $item['height'] }}"> --}}
					<input type="text" class="form-control" name="height[]" placeholder="Height" value="3">
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Weight Unit</label>
					<select class="form-control valid" name="weight_unit_type[]" aria-invalid="false">
	                    <option value="LBS">LBS</option>
	                    <option value="KGS" selected="selected">KGS</option>
	                </select>
				</div>
			</div>
			<div class="col-md-2 collapse">
				<div class="form-group">
					<label for="">Weight</label>
					<input type="text" class="form-control" name="weight[]" placeholder="Weight" value="{{ $item['weight']/1000 }}">
				</div>
			</div>
			{{-- <div class="col-md-9">
				<div class="form-group">
					<label for="">Select Images (<span style="color: #B51F38;">* If you are returning via a Return Bar, please upload a photograph of the item you are returning</span>)</label>
					<input class="form-control img-itm" id="image-upload-{{ $k }}" type="file" name="item_images[{{ $k }}][]" accept="image/*" multiple>
				</div>
			</div> --}}
			@if(isset($hs_code['data']['product_tax_code']))
				<input type="hidden" class="form-control" name="hs_code[]" value="{{ $hs_code['data']['product_tax_code'] }}">
			@else
				<input type="hidden" class="form-control" name="hs_code[]" value="">
			@endif

			@if(isset($item['bin_picking_number']))
				<input type="hidden" class="form-control" name="country_of_origin[]" value="{{ $item['bin_picking_number'] }}">
			@else
				<input type="hidden" class="form-control" name="country_of_origin[]" value="">
			@endif

			<input type="hidden" class="form-control" name="charged__weight[]" value="1">
	        <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
	        <input type="hidden" class="form-control" name="price[]" value="{{ $item['base_total'] }}">
		</div>
	</div>
@empty
@endforelse