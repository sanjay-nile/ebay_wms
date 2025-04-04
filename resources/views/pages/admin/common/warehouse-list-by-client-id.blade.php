<select name="warehouse_id" id="client_warehouse_list" class="form-control">
	{{-- <option value="">Select</option> --}}
	@forelse($warehouse_list as $warehouse)
		<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
	@empty
		<option value="discrepency">Discrepency Warehouse</option>
	@endforelse
</select>