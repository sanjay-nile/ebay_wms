@extends('layouts.admin.layout')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
				<div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
					<h3 class="content-header-title mb-0 d-inline-block">View Customers</h3>
					<div class="row breadcrumbs-top d-inline-block">
					  <div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
							<li class="breadcrumb-item active">View Customers</li>
						</ol>
					  </div>
					</div>
				</div>
            </div>
        </div>
        <div class="row">
			<div class="col-xs-12 col-md-12 ">
				@include('includes/admin/notify')
                <div class="card">
                    <div class="card-header avn-card-header">
						<a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-content collapse show">                    		
                        <div class="card-body booking-info-box card-dashboard">
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Name</th>
                                        <th>Email</th>                                        
                                        <th>Phone no</th>
                                        <th>Status</th> 
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php $i=1 @endphp
                                    @forelse($users as $user)
                                    	<tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>
                                            <td><a class="btn btn-view btn-primary" href="{{ route('individual-user.edit', $user) }}" title="Edit"><i class="la la-edit"></i></a></td>   
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
							<div class="col-md-12">{{ $users->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
	
@endsection
