{{-- <div class="form-group state-list col-md-3">
  <label for="">State</label>
  <select name="state" id="" class="form-control form-control-sm">
  	<option value="">Select</option>
  	@forelse($state_list as $state)
  		<option value="{{ ($state->shortname)??$state->name }}" name="{{ $state->name }}">{{ $state->name }}</option>
  	@empty
  	<option value="">State not found</option>
  	@endforelse
  </select>
</div> --}}

<div class="form-group state-list">        
    <select name="state_id" id="" class="form-control form-control-sm">
        @forelse($state_list as $state)
          <option value="{{ ($state->shortname)??$state->name }}" name="{{ $state->name }}">{{ $state->name }}</option>
        @empty
        <option value="">State not found</option>
        @endforelse
    </select>
</div>