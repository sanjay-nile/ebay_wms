@include('pages.frontend.client-user.breadcrumb', ['title' => 'Edit Profile'])

<div class="row">
    <div class="col-xs-12 col-md-12 table-responsive">
        <div class="card booking-info-box">            
            <div class="card-content">
                <div class="card-body">
                    <section class="list-your-service-section">
                        <div class="list-your-service-content">
                            <div class="container">
                                <div class="list-your-service-form-box" style="width: 85%;">
                                    <div class="row">
                                        <div class="col-md-12"><h5 class="card-title">Edit Profile</h5></div>
                                    </div>
                                    <form action="{{ route('client-user.profile.update',Auth::user()) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Name</label>
                                                    <input type="text" class="form-control" name="name" placeholder="Enter Name" value="{{ $user->name }}">
                                                    @if($errors->has('name'))
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Email</label>
                                                    <span class="form-control" readonly >{{ $user->email }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Phone</label>
                                                    <input type="text" class="form-control" name="phone" placeholder="Phone" value="{{ $user->phone }}">
                                                    @if($errors->has('phone'))
                                                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                                                    @endif
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Address</label>
                                                    <input type="text" class="form-control" name="address" placeholder="Address" value="{{ $user->address }}">
                                                    @if($errors->has('address'))
                                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn-red pull-right" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>