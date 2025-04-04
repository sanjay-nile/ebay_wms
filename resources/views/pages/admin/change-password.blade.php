@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
		<div class="we-page-title">
			<div class="row">
				<div class="col-md-8 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Change Password</li>
					</ol>
				</div>
			</div>
		</div>
        <!-- Main content -->
        <div class="row">
			<div class="col-md-12">
				@include('includes/admin/notify')
			</div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
							<section class="list-your-service-section">
								<div class="list-your-service-content">
									<div class="container">
										<div class="list-your-service-form-box" style="width: 85%;">
                                            <div class="row">
                                                <div class="col-md-12"><h5 class="card-title">Change Password</h5></div>
                                            </div>
											<form action="{{ route('admin.change-password.update',Auth::user()) }}" method="post">
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
                                                            <input type="text" class="form-control" name="confirm_password" placeholder="Enter confirm password" value="{{ old('confirm_password') }}">
                                                            @if($errors->has('confirm_password'))
                                                            <span class="text-danger">{{ $errors->first('confirm_password') }}</span>
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
							<!-- Main Slider Close -->
						</div>
                    </div>
                    <!--  <div class="card-content collapse"></div> -->
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
@endsection
