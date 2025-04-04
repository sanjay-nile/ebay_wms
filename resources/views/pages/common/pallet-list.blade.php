<div class="form-group" id="exist_pallet_list">
    <select name="pallet_name" id="" class="form-control">
        @forelse($pallets as $pallet)
            <option value="{{ $pallet->pallet_id }}">{{ $pallet->pallet_id }}</option>
        @empty
            <option value="">No Existing pallet exists</option>
        @endforelse
    </select>
</div>