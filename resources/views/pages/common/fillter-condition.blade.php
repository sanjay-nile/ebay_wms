<div class="form-group condition-list">
    <label for="">Condition</label>
    <select name="condition_code" class="form-control">
        <option value="">-- Select --</option>
        @forelse($cat_list as $cat)
            <option value="{{ $cat }}">{{ $cat }}</option>
        @empty
        @endforelse
    </select>
</div>