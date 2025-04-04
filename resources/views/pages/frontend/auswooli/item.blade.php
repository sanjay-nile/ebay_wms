@if(is_array($order['line_items']) && count($order['line_items']) > 0)	
	@forelse($order['line_items'] as $k => $item)
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
			$cn_qn = $item['quantity'];
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

			//$item_sku = str_replace('/', '', $item['sku']);
			$item_sku = $item['sku'];
		?>
		<div class="return-content-list item-2 {{ $class }}" id="{{ $k }}">
			<div class="item-info">
				<div class="product-text">{{ $item['name'] }}</div>
				<div class="product-price">{{ $item['price'] }} EUR</div>
				<div class="product-code">Item Code: {{ $item_sku }}</div>
				<div class="product-code">Size: {{ $item['variant_title'] }}</div>
			</div>
			<div class="step-form cu-frm">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Quantity of return</label>
							<select class="form-control rtn_qty" name="rtn_qty[]" data-price="{{ $item['price'] }}" data-key="{{ $k }}">
								<option value="0" item-price="{{ $item['price'] }}">Not returning </option>
								@for ($i = 1; $i <= $cn_qn; $i++)
							        <option value="{{ $i }}" item-price="{{ $item['price'] }}">{{ $i }}</option>
							    @endfor
							</select>
							<input type="hidden" name="total_qty[]" value="{{ $item['quantity'] }}">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Reason for return</label>
							<select class="form-control rtn_reason" name="reason_of_return[]" data-key="{{ $k }}" id="single-reason-{{ $k }}">
								@foreach(curated_reason_of_return() as $key => $rtn)
									<option value="{{ $key }}">{{ $rtn }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Item Images</label>
							<input type="file" name="item_image[]" class="form-control cmt-box" data-key="{{ $k }}" id="single-image-{{ $k }}">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label id="re-label-{{ $k }}">Comments</label>
							<textarea placeholder="Enter comments here" class="form-control cmt-box" type="text" name="remark[]" id="remark-{{ $k }}"></textarea>
							<input type="hidden" name="item_price[]" id="item_price_{{ $k }}" value="{{ $item['price'] }}">
							<input type="hidden" name="item_name[]" value="{{ $item['name'] }}">
							<input type="hidden" name="item_sku[]" value="{{ $item_sku }}">
							<input type="hidden" name="item_size[]" value="{{ $item['variant_title'] }}">
							<input type="hidden" name="hs_code[]" value="">
							<input type="hidden" name="weight[]" value="{{ $weight }}">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<input type="checkbox" name="env_price[]" class="env-box" data-key="{{ $k }}" id="env-box-{{ $k }}">
							Please only tick this box if you would like to withdraw your environmental pledge. Should you wish to keep your pledge, your contribution will still be traceable and you will be helping world-leading organisations to create positive impact on sustainability.
						</div>
					</div>
				</div>
			</div>
		</div>
	@empty
	@endforelse
@endif