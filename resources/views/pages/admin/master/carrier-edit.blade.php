<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('admin.carrier.store') }}" method="post" class="form-horizontal" autocomplete="off">
                            @csrf
                            <input type="hidden" name="id" value="{{ $single_c->id }}">
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-md-2 col-form-label pt-0">Carrier</legend>
                                    <div class="col-md-9">
                                        <div class="form-row">
                                            <div class="form-group col-md-5">
                                                <input type="text" class="form-control" placeholder="Name..." name="name" value="{{ $single_c->name }}">
                                                @error('name')
                                                    <div class="error">The field is required</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-5">
                                                <input type="text" name="code" class="form-control" placeholder="Code..." value="{{ $single_c->code }}">
                                                @error('code')
                                                    <div class="error">The field is required</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="s" @if($single_c->status == 1) checked @endif>
                                                    <label class="form-check-label" for="s">Default</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-md-2 col-form-label pt-0">Carrier Product</legend>
                                    <div class="col-md-7" id="carrier-add">
                                        @forelse($single_c->product as $k => $cp)
                                            <div class="form-row cp-add-{{ $k }}">
                                                <input type="hidden" name="cp_id[]" value="{{ $cp->id }}">
                                                <div class="form-group col-md-5">
                                                    <input type="text" class="form-control" placeholder="Name..." name="cp_name[]" value="{{ $cp->name }}">
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <input type="text" name="cp_code[]" class="form-control" placeholder="Code..." value="{{ $cp->code }}">
                                                </div>
                                            </div>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-md-2 col-form-label pt-0">Carrier Service Code</legend>
                                    <div class="col-md-7" id="carrier-add">
                                        @forelse($single_c->service as $k => $csc)
                                            <div class="form-row csc-add-{{ $k }}">
                                                <input type="hidden" name="csc_id[]" value="{{ $csc->id }}">
                                                <div class="form-group col-md-5">
                                                    <input type="text" class="form-control" placeholder="Name..." name="csc_name[]" value="{{ $csc->name }}">
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <input type="text" name="csc_code[]" class="form-control" placeholder="Code..." value="{{ $csc->code }}">
                                                </div>
                                            </div>
                                        @empty
                                        @endforelse
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
                </div>
            </div>
        </div>
    </div>
</div>