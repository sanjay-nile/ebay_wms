@push('css')
<link href="{{ asset('admin/css/new-admin-app.css') }}" rel="stylesheet">
<style type="text/css">
    .booking-profile-info {
    width: 40%;
    margin: 0 auto;
}
</style>
@endpush

<section class="tips-section booking-profile-info ">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">                
                <div class="card-content collapse show">
                    <div class="card-body booking-info-box card-dashboard">
                        <form method="post" action="{!! route('customer.change.password') !!}">
                            @csrf
                            <h2>Changed Password</h2>                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Old Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Old Password" name="old_password" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword2">Confirm Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Confirm Password" name="password_confirmation" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{!! Auth::user()->id !!}">
                                        <button type="submit" class="btn btn-red">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</section>