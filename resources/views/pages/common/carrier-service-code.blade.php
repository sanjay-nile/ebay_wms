<div class="form-group service_code">
    <label for="">Carrier Service Code</label>
    <select name="service_code" id="service_code" class="form-control">
        <option value="">-- Select-- </option>
        @forelse($csc as $cp)
            <option value="{{ $cp->code }}">{{ $cp->name }}</option>
        @empty
        @endforelse
    </select>
    @error('service_code')
        <div class="error">The field is required</div>
    @enderror
</div>