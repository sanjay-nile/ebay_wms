@if(is_array($order['item_shipped']) && count($order['item_shipped']) > 0)	
	@forelse($order['item_shipped'] as $k => $item)
		<?php
			$class = '';
			$sku = false;
			if(in_array($item['sku'], $bar_codes) && in_array($item['sku'], $cncel_bar_code)){				
				$sku = true;
			} elseif (in_array($item['sku'], $cncel_bar_code)) {
				# code...
				$sku = false;
			} elseif (in_array($item['sku'], $bar_codes)) {
				# code...
				$sku = true;
			}

			# quantity chek and sku..
			$cn_qn = $item['confirm_qty'];
			$qn = false;
			if(isset($qnty_arr[$item['sku']]) && $qnty_arr[$item['sku']] == $cn_qn){
				$qn = true;
			} elseif (isset($qnty_arr[$item['sku']]) && $qnty_arr[$item['sku']] != $cn_qn) {
				# code...
				$cn_qn = $cn_qn - $qnty_arr[$item['sku']];
			}

			if($sku && $qn){
				$class = 'disabledItem';
			} elseif ($cn_qn <= 0) {
				# code...
				$class = 'disabledItem';
			}
			// print_r($cn_qn);
			// print_r($cncel_bar_code);
		?>
		<div class="return-content-list item-2 {{ $class }}" id="{{ $k }}">
			<div class="item-info-card">
				<div class="item-info-text">
					<div class="product-text">{{ $item['name'] }}</div>
					<div class="product-price">£{{ $item['price'] }}</div>
					<div class="product-code">Item Code: {{ $item['sku'] }}</div>
					<div class="product-code">Size: {{ $item['size'] }}</div>
				</div>
				<div class="item-media-info">
					<img src="{{ asset('public/images/no-image.jpg') }}" width="120">
				</div>
			</div>
			<div class="step-form">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Quantity of return</label>
							<select class="form-control rtn_qty" name="rtn_qty[]" data-price="{{ $item['price'] }}" data-key="{{ $k }}">
								<option value="0" item-price="{{ $item['price'] }}" selected="selected">I'll keep these thanks</option>
								@for ($i = 1; $i <= $cn_qn; $i++)
							        <option value="{{ $i }}" item-price="{{ $item['price'] }}">{{ $i }}</option>
							    @endfor
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Reason for return</label>
							@if($cn_qn > 1)
								<select class="form-control rtn_reason" name="reason_of_return[{{ $k }}][]" data-key="{{ $k }}" id="single-reason-{{ $k }}">
									@foreach(reason_of_return() as $key => $rtn)
										<option value="{{ $key }}">{{ $rtn }}</option>
									@endforeach
								</select>
							@else
								<select class="form-control rtn_reason" name="reason_of_return[]" data-key="{{ $k }}" id="single-reason-{{ $k }}">
									@foreach(reason_of_return() as $key => $rtn)
										<option value="{{ $key }}">{{ $rtn }}</option>
									@endforeach
								</select>
							@endif							
							<div id="multi-reason-{{ $k }}"></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label>Comments</label>
							<textarea placeholder="Enter comments here" class="form-control cmt-box" type="text" name="remark[]" id="remark-{{ $k }}"></textarea>
							<input type="hidden" name="item_price[]" id="item_price_{{ $k }}" value="{{ $item['price'] }}">
							<input type="hidden" name="item_name[]" value="{{ $item['name'] }}">
							<input type="hidden" name="item_sku[]" value="{{ $item['sku'] }}">
							<input type="hidden" name="item_size[]" value="{{ $item['size'] }}">
							<input type="hidden" name="hs_code[]" value="{{ $item['hs_code'] }}">
						</div>
					</div>
					
				</div>
			</div>
		</div>
	@empty
	@endforelse
@endif