@extends('layouts.admin.layout')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
		<div class="we-page-title">
			<div class="row">
				<div class="col-md-12 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Add Admin Rep</li>
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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ route('admin.sub-admin') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                        </h4>
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
                    <div class="card-body">
						<section class="list-your-service-section">
							<div class="list-your-service-content">
								<div class="list-your-service-form-box" style="width: 85%;">
                                    <div class="row">
                                        <div class="col-md-12"><h5 class="card-title">Add Admin Rep</h5></div>
                                    </div>
									<form action="{{ route('admin.sub-admin.store') }}" method="post">
                                        @csrf
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
                                        </div>
                                        <div class="row">
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
                                        </div>
                                        <div class="row">
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
                                                    <label for="">Department</label>
                                                    <input type="text" class="form-control" name="department" placeholder="Enter Your Department" value="{{ old('department') }}">
                                                    @if($errors->has('department'))
                                                    <span class="text-danger">{{ $errors->first('department') }}</span>
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
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn-red pull-right" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Submit</button>
                                            </div>
                                        </div>
									</form>
								</div>
							</div>
						</section>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
