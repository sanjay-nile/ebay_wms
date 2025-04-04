@include('pages.frontend.client.breadcrumb', ['title' => 'Change Password'])

<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="card booking-info-box">            
            <div class="card-content">
                <div class="card-header">
                    <h5 class="card-title">Change Password</h5>
                </div>
                <div class="card-body">                    
                    <form action="{{ route('client.update-password',Auth::user()) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Enter password" value="{{ old('password') }}">
                                    @if($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Enter confirm password" value="{{ old('confirm_password') }}">
                                    @if($errors->has('confirm_password'))
                                        <span class="text-danger">{{ $errors->first('confirm_password') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-red btn-danger pull-left" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>