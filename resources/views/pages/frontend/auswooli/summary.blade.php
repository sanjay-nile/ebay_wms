@if(is_array($order['line_items']) && count($order['line_items']) > 0)
	<div class="Summary-card-header">
		<div class="Summary-card-title mb-0 cu-title">Return Summary</div>
	</div>
	@php
		$total = 0;
	@endphp
	@forelse($order['line_items'] as $k => $item)
		<div class="Summary-card-body collapse dis-{{ $k }}">
			<div class="Summary-card-list">
				<div class="Summary-card-item">
					<div class="Summary-title name_{{ $k }}">{{ $item['name'] }}</div>
				</div>
				<div class="mb-3 d-flex">
					<div class="Summary-title qty_{{ $k }}">Qty: 0</div>
					<div class="Summary-value price_{{ $k }}">0 {{ $item['price_set']['shop_money']['currency_code'] }}</div>
				</div>
				<hr>				
			</div>
		</div>
		@php
			$total += $item['price'];
		@endphp
	@empty
	@endforelse
	<div class="Summary-card-footer">
		<div class="Summary-total-content mt-3">
			<div class="Summary-total-title">Total</div>
			<div class="Summary-total-value itm_total">0 USD</div>
		</div>
		{{-- <div class="Summary-total-content">
			<div class="Summary-total-title">Shipping Charges</div>
			<div class="Summary-total-value ship_chrg">0 USD</div>
		</div> --}}
		<div class="Summary-total-content">
			<div class="Summary-total-title">Return Charges</div>
			<div class="Summary-total-value rtn_chrg">0 USD</div>
		</div>
		<div class="Summary-total-content">
			<div class="Summary-total-title env_title"></div>
			<div class="Summary-total-value env_chrg"></div>
		</div>
		<div class="Summary-total-content">
			<div class="Summary-total-title">Return Total</div>
			<div class="Summary-total-value ttl_price">0 USD</div>
		</div>
		<input type="hidden" name="rtn_total" class="rtn_total" value="">
	</div>
	<div class="">
		<div class="Summary-card-footer">
    		<ul>
    			<li>We are happy to refund any items within 30 days of receipt. if 30 days have gone by since your delivery, unfortunately we cannot offer you a refund.</li>
    			<li class="mt-2">We no longer offer exchanges. any returned items will be refunded instead.</li>
    		</ul>
		</div>
	</div>
@endif