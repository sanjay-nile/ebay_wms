@if(is_array($order['item_shipped']) && count($order['item_shipped']) > 0)
	<span class="bg-shape"></span>
	<div class="Summary-card-header">
		<div class="Summary-card-title mb-0">Return Summary</div>
		<input type="hidden" name="waiver" id="waiver" value="{{ $order['waiver'] }}">
	</div>
	@php
		$total = 0;
	@endphp
	@forelse($order['item_shipped'] as $k => $item)
		<div class="Summary-card-body collapse dis-{{ $k }}">
			<div class="Summary-card-list">
				<div class="Summary-card-item">
					<div class="Summary-title name_{{ $k }}">{{ $item['name'] }}</div>
				</div>
				<div class="mb-3 d-flex">
					<div class="Summary-title qty_{{ $k }}">Qty: 0</div>
					<div class="Summary-value price_{{ $k }}">£0</div>
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
			<div class="Summary-total-value itm_total">£0</div>
		</div>
		<div class="Summary-total-content">
			<div class="Summary-total-title">Shipping Charges</div>
			<div class="Summary-total-value ship_chrg">£0</div>
		</div>
		<div class="Summary-total-content">
			<div class="Summary-total-title">Return Total</div>
			<div class="Summary-total-value ttl_price">£0</div>
		</div>
		<input type="hidden" name="rtn_total" class="rtn_total" value="">
	</div>
	<div class="">
		<div class="Summary-card-footer">
    		<ul>
    			<li>Your actual refund amount will take into account any discount applied to your order.</li>
    			<li>Return policy is 14 days post delivery.</li>
    			<li>FREE UK Returns, QR code, print at store OR print at home - NOT collect from home.</li>
    		</ul>
		</div>
	</div>
@endif