<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('admin.add.warehouse') }}" method="post" class="form-horizontal" autocomplete="off">
                            @csrf
                            <input type="hidden" name="wh_id" value="{{ $single_wh->id }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" name="name" value="{{ $single_wh->name }}" class="form-control" placeholder="Warehouse Name">
                                        @error('name')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" name="contact_person" value="{{ $single_wh->contact_person }}" class="form-control" placeholder="Contact Person">
                                        @error('contact_person')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                    <input type="text" name="email" value="{{ $single_wh->email }}" class="form-control" placeholder="Email">
                                    @error('email')
                                        <div class="error">The field is required</div>
                                    @enderror
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                    <input type="text" name="phone" value="{{ $single_wh->phone }}" class="form-control" placeholder="Phone">
                                    @error('phone')
                                        <div class="error">The field is required</div>
                                    @enderror
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                    <input type="text" name="address" value="{{ $single_wh->address }}" class="form-control" placeholder="Address">
                                    @error('address')
                                        <div class="error">The field is required</div>
                                    @enderror
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                    <select class="form-control country-list form-control-sm" name="country">
                                        <option value="">-- Select Country -- </option>
                                        @forelse($country_list as $country)
                                            <option value="{{ $country->id }}" name="{{ $country->name }}" {{ ($country->id==$single_wh->country_id)?'selected':'' }}>{{ $country->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group state-list">
                                    @if(isset($state_list) && $state_list)
                                        <select class="form-control form-control-sm" name="state_id">
                                            <option value="">-- Select State --</option>
                                            @forelse($state_list as $state)
                                                <option value="{{ $state->id }}" name="{{ $state->name }}" {{ ($state->id==$single_wh->state_id)?'selected':'' }} >{{ $state->name }}</option>
                                            @empty
                                            @endforelse
                                         </select>
                                    @else
                                        <select name="state_id" id="" class="form-control form-control-sm">
                                            <option value="">-- Select State --</option>
                                        </select>
                                    @endif                                    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" placeholder="City" name="city" value="{{ $single_wh->city }}">
                                        @error('city')
                                            <div class="error">The field is required</div>
                                        @enderror 
                                    </div>  
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" class="form-control" placeholder="Postal Code" name="zip_code" value="{{ $single_wh->zip_code }}">
                                        @error('zip_code')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- <div class="form-group col-md-9">
                                    <select class="form-control form-control-sm select2 assigncountry" name="assign_to[]" multiple>
                                        <option value="">-- Assign Country -- </option>
                                        @forelse($country_list as $country)
                                            <?php  $isSelected = in_array($country->sortname,$assignedcountry) ? "selected='selected'" : ""; ?>
                                            <option {{ $isSelected }} value="{{ $country->sortname }}" name="{{ $country->name }}">{{ $country->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div> -->
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <button type="submit" class="btn btn-red save-client">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>