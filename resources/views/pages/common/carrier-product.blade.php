<div class="form-group carrier_product">
    <label for="">Carrier Product</label>
    <select name="carrier_product" id="carrier_product" class="form-control">
        <option value="">-- Select-- </option>
        @forelse($cp as $cps)
            <option value="{{ $cps->code }}">{{ $cps->name }}</option>
        @empty
        @endforelse
    </select>
    @error('carrier_product')
        <div class="error">The field is required</div>
    @enderror
</div>