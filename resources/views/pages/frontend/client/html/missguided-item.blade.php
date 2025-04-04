@if(is_array($order['item_shipped']) && count($order['item_shipped']) > 0)
	@forelse($order['item_shipped'] as $k => $item)
		<div class="package-item-list return-form-item add-{{ $k }}">
			<div class="info-list-inner">		
				<div class="row">					
					<div class="col-md-0">
						<input type="checkbox" name="item-select[]" value="{{ $k }}" class="item-chk" style="margin: 27px 4px 0px 0px;" @if(count($order['item_shipped']) == 1) checked @endif>
						<input type="hidden" name="image_of_item[]" value="">
						{{-- <img src="{{ asset('public/images/no-image.jpg') }}" class="img-responsive img-thumbnail" style="width: 60px;"> --}}
					</div>
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
							<input type="text" class="form-control package_count_arr" name="package_count[]" value="{{ $item['ordered_qty'] }}" autocomplete="off">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="">Reason for Return</label>
							<select class="form-control valid" name="item_return_reason[]" aria-invalid="false" id="itm-rtn-{{ $k }}">
								<option value="">-- Select a Reason--</option>
			                    @foreach(reason_of_return() as $key => $rtn)
			                    	<option value="{{ $key }}">{{ $rtn }}</option>
			                    @endforeach
			                </select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="">Color</label>
							<input type="text" class="form-control clr_arr" name="color[]" placeholder="Color" value="N/A">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="">Size</label>
							<input type="text" class="form-control sze_ar" name="size[]" placeholder="Size" value="{{ $item['size'] }}">
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
							<input type="text" class="form-control" name="length[]" placeholder="Length" value="1">
						</div>
					</div>
					<div class="col-md-2 collapse">
						<div class="form-group">
							<label for="">Width</label>
							<input type="text" class="form-control" name="width[]" placeholder="Width" value="1">
						</div>
					</div>
					<div class="col-md-2 collapse">
						<div class="form-group">
							<label for="">Height</label>
							<input type="text" class="form-control" name="height[]" placeholder="Height" value="1">
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
							<input type="text" class="form-control" name="weight[]" placeholder="Weight" value="1">
						</div>
					</div>
					{{-- <div class="col-md-7">
						<div class="form-group">
							<label for="">Select Images (jpeg/png) *Uploading the Image will expedite the Return Process</label>
							<input class="form-control img-itm" id="image-upload-{{ $k }}" type="file" name="item_images[{{ $k }}][]" accept="image/*" multiple>
						</div>
					</div> --}}					
					<input type="hidden" class="form-control" name="hs_code[]" value="{{ $item['hs_code'] }}">
					<input type="hidden" class="form-control" name="charged__weight[]" value="1">
			        <input type="hidden" class="form-control" name="selected_package[]" value="DOCUMENT">
			        <input type="hidden" class="form-control" name="price[]" value="{{ $item['price'] }}">
				</div>
			</div>
		</div>
	@empty
	@endforelse
@endif