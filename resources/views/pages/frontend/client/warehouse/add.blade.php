<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.add.warehouse')}}" method="post" class="form-horizontal" autocomplete="off">
                            @csrf
                            <div class="row">
                                
                                <!-- <div class="form-group col-md-3">
                                    <input type="text" name="nickname" value="" class="form-control" placeholder="Warehouse Nick Name">
                                    @error('nickname')
                                        <div class="error">The field is required</div>
                                    @enderror
                                </div> -->
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" name="name" value="" class="form-control" placeholder="Warehouse Name">
                                        @error('name')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <input type="text" name="contact_person" value="" class="form-control" placeholder="Contact Person">
                                        @error('contact_person')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="email" value="" class="form-control" placeholder="Email">
                                        @error('email')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="phone" value="" class="form-control" placeholder="Phone">
                                        @error('phone')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="address" value="" class="form-control" placeholder="Address">
                                        @error('address')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control country-list form-control-sm" name="country">
                                            <option value="">-- Select Country -- </option>
                                            @forelse($country_list as $country)
                                                <option value="{{ $country->id }}" name="{{ $country->name }}">{{ $country->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group state-list">
                                        {{-- <input type="text" class="form-control" placeholder="State" name="state_id"> --}}
                                        <select name="state_id" id="" class="form-control form-control-sm">
                                            <option value="">-- Select State --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="City" name="city">
                                        @error('city')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Postal Code" name="zip_code">
                                        @error('zip_code')
                                            <div class="error">The field is required</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                {{-- <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="carrier_id">
                                                <option value="">-- Select Carrier -- </option>
                                                @forelse($carrier_list as $cc)
                                                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('carrier_id')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                </div> --}}
                                {{-- <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Client Code" name="client_code" value="">
                                            @error('client_code')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                </div> --}}
                                {{-- <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm select2 assigncountry" name="assign_to[]" multiple>
                                                <option value="">-- Assign Country -- </option>
                                                @forelse($country_list as $country)
                                                    <option value="{{ $country->sortname }}" name="{{ $country->name }}">{{ $country->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>   
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="form-group">
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