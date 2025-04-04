@include('pages.frontend.client.breadcrumb', ['title' => 'Add Client User'])

<style type="text/css">
    .booking-info-box .info-list-section {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 10px;
}
</style>
<div class="row">
    <div class="col-xs-12 col-md-12 table-responsive">
        <div class="booking-info-box">
            <div class="card-content">                
                <div class="">
                    <section class="list-your-service-section">
                        <div class="list-your-service-content">
                            <div class="container">
                                <div class="list-your-service-form-box">                                    
                                    <form action="{{ route('store.client-user') }}" method="post">
                                        @csrf
                                        <div class="info-list-section">
                                            <div class="">
                                                <h2 class="card-title">Add Client User</h2>
                                            </div>
                                            <div class="info-list-inner">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">First Name</label>
                                                        <input type="text" class="form-control" name="first_name" placeholder="Enter first name" value="{{ old('first_name') }}">
                                                        @if($errors->has('first_name'))
                                                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Last Name</label>
                                                        <input type="text" class="form-control" name="last_name" placeholder="Enter last name" value="{{ old('last_name') }}">
                                                        @if($errors->has('last_name'))
                                                        <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Email</label>
                                                        <input type="text" class="form-control" name="email" placeholder="Enter email" value="{{ old('email') }}">
                                                        @if($errors->has('email'))
                                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Phone</label>
                                                        <input type="text" class="form-control" name="phone" placeholder="Enter phone" value="{{ old('phone') }}">
                                                        @if($errors->has('phone'))
                                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Address</label>
                                                        <input type="text" class="form-control" name="address" placeholder="Enter address" value="{{ old('address') }}">
                                                        @if($errors->has('address'))
                                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Status</label>
                                                        <select name="status" class="form-control">
                                                            <option value="">Select</option>
                                                            <option value="1" @if(old('status')==1) {{ 'selected' }} @endif>Active</option>
                                                            <option value="2" @if(old('status')==2) {{ 'selected' }} @endif>Inactive</option>
                                                        </select>
                                                        @if($errors->has('status'))
                                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">User Permissions</label> <br>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="Yes" name="waiver">
                                                            <label class="form-check-label">Waiver Privileges</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="Yes" name="create_return">
                                                            <label class="form-check-label" for="inlineCheckbox2">Create Return</label>
                                                        </div>
                                                        <!-- <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="Yes" name="view_reports">
                                                            <label class="form-check-label">View Reports</label>
                                                        </div> -->
                                                    </div>
                                                </div>
                                                <div class="col-md-6 collapse">
                                                    <div class="form-group">
                                                        <label for="">Client Type</label>
                                                        {{-- <select name="client_type" class="form-control">
                                                            <option value="">Select Client Type</option>
                                                            @foreach(getClientType() as $type => $v)
                                                                <option value="{!! $type !!}">{!! $v !!}</option>
                                                            @endforeach
                                                        </select> --}}
                                                        <input type="hidden" name="client_type" value="{{ Auth::user()->client_type }}">
                                                        @if($errors->has('client_type'))
                                                            <span class="text-danger">{{ $errors->first('client_type') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button class="btn-red pull-right" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Submit</button>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section><!-- Main Slider Close -->
                </div>
            </div>
        </div>
    </div>
</div>