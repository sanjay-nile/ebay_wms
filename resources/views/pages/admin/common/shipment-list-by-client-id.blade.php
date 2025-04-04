<select name="shipment_id" id="client_shipment_list" class="form-control">
	<option value="">Select</option>
	@forelse($shipment_list as $shipment)
		<option value="{{ $shipment->id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}" @if($shipment->is_default) selected @endif>{{ $shipment->shipment_name }}</option>
	@empty
	<option value="">Client not added shipment yet</option>
	@endforelse
</select>