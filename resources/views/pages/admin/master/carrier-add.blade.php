<div class="ml-2">
<form action="{{ route('admin.carrier.store') }}" method="post" class="form-horizontal" autocomplete="off">
    @csrf
    <fieldset class="form-group">
        <div class="row">
            <legend class="col-md-2 col-form-label pt-0">Carrier</legend>
            <div class="col-md-9">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" placeholder="Name..." name="name">
                        @error('name')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="code" value="" class="form-control" placeholder="Code...">
                        @error('code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="unit_type" value="" class="form-control" placeholder="Weight unit type...">
                        @error('code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="client_code" value="" class="form-control" placeholder="Client Code ...">
                        @error('client_code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="s">
                            <label class="form-check-label" for="s">Default</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
    <div class="row">
        <legend class="col-md-2 col-form-label pt-0">Select Country</legend>
        <div class="form-group col-md-8">
            <select class="form-control form-control-sm select2" name="selectcountry[]" id="assignwarehouse" multiple>
                <option value="">-- Select Country -- </option>
                @forelse($country_list as $country)
                    <option value="{{ $country->sortname }}" name="{{ $country->name }}">{{ $country->name }}</option>
                @empty
                @endforelse
            </select>
            @error('selectcountry')
                <div class="error">The field is required</div>
            @enderror
        </div>
    </div>
</fieldset>
    <fieldset class="form-group">
        <div class="row">
            <legend class="col-md-2 col-form-label pt-0">Carrier Product</legend>
            <div class="col-md-7" id="carrier-add">
                <div class="form-row cp-add-1">
                    <div class="form-group col-md-5">
                        <input type="text" class="form-control" placeholder="Name..." name="cp_name[]">
                        @error('cp_name')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5">
                        <input type="text" name="cp_code[]" class="form-control" placeholder="Code...">
                        @error('cp_code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-blue add-cp"> 
                    <i class="fa fa-plus"> Add More</i>
                </button>
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group">
        <div class="row">
            <legend class="col-md-2 col-form-label pt-0">Carrier Service Code</legend>
            <div class="col-md-7" id="service-add">
                <div class="form-row csc-add-1">
                    <div class="form-group col-md-5">
                        <input type="text" class="form-control" placeholder="Name..." name="csc_name[]">
                        @error('csc_name')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5">
                        <input type="text" name="csc_code[]" class="form-control" placeholder="Code...">
                        @error('csc_code')
                            <div class="error">The field is required</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-blue add-csc"> 
                    <i class="fa fa-plus"> Add More</i>
                </button>
            </div>
        </div>
    </fieldset>
    <div class="form-row align-items-center">
        <div class="form-group col-md-1">
            <button type="submit" class="btn btn-red save-client">Save</button>
        </div>
    </div>
</form>
</div>
