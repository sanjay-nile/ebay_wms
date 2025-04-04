@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
				<div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
					<h3 class="content-header-title mb-0 d-inline-block">View Client User</h3>
					<div class="row breadcrumbs-top d-inline-block">
					  <div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li> 
							<li class="breadcrumb-item active">View Client User</li>
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
                                        <th>Client</th>
                                        <th>Status</th> 
                                        @if(Auth::user()->user_type_id==1 || Auth::user()->user_type_id==3)
                                            <th>Action</th>
                                        @endif
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
                                            <td>{{ $user->owner_name??"N/A" }}</td>
                                            <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>
                                            @if(Auth::user()->user_type_id==1 || Auth::user()->user_type_id==3)
                                            <td>
                                                <a class="btn btn-view btn-primary" href="{{ route('client-user.edit', $user) }}" title="Edit"><i class="la la-edit"></i></a>
                                                <a class="btn btn-delete btn-danger" href="{{ route('client-user.destory', $user->id) }}" title="Delete"><i class="la la-trash" onclick="return(confirm('Are you sure, You want to delete this record, if you delete this record then other relation will be deleted'))" ></i></a>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
							<div class="col-md-12">{{  $users->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection