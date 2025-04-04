@extends('layouts.admin.layout') 
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
				<div class="col-md-12 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                        <li class="breadcrumb-item active">View Client</li>
                    </ol>
               </div>
            </div>
        </div>
        <div class="row">
			<div class="col-xs-12 col-md-12 ">
				@include('includes/admin/notify')
                <div class="card">                    
                    <div class="card-content">                    		
                        <div class="card-body booking-info-box card-dashboard table-responsive">
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults table-sm">
                                <thead>
                                    <tr>
                                        <th>Client ID</th>
                                        <th>Client Code</th>
                                        <th>Name</th>
                                        <th>Email</th>                          
                                        <th>Phone no</th>
                                        <th>Status</th> 
                                        @if(Auth::user()->user_type_id==1)
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                	@php $i=1 @endphp
                                    @forelse($users as $user)
                                    	<tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->user_code?'success':'danger' }}">{{ $user->user_code??" N/A " }}</span>
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>
                                            @if(Auth::user()->user_type_id==1)
                                            <td>
                                                <a class="btn btn-view" href="{{ route('admin.client.edit', $user) }}" title="Edit"><i class="la la-edit"></i></a>
                                                <a class="btn btn-delete btn-danger" href="{{ route('admin.client.destory', $user->id) }}" title="Delete" onclick="return(confirm('Are you sure, You want to delete this client. If you delete this client then other relation will be deleted'))"><i class="la la-trash"></i></a>
                                            </td>   
                                            @endif
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
           {{--  <div class="col-xs-12 col-md-12 ">
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
                        <div class="card-body booking-info-box card-dashboard ">
                            <div class="Container">
                                <div class="row">
                                    <div class="col-md-12">
                                    <div class="table-responsive">                           
                                        <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                                            <thead>
                                                <tr>
                                                    <th>S no.</th>
                                                    <th>Company Name</th>
                                                    <th>Contact Person Name</th>                          
                                                    <th>Phone no</th>
                                                    <th>Email ID</th> 
                                                    <th>Assigned Customer Rep</th> 
                                                    <th>Total Reverse Orders</th> 
                                                    <th>Total Completed Orders</th> 
                                                    <th>Total Pending Orders</th> 
                                                    <th>Status</th>
                                                    <th>Action</th> 
                                                    @if(Auth::user()->user_type_id==1)
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i=1 @endphp
                                                @forelse($users as $user)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->phone }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>

                                                        @if(Auth::user()->user_type_id==1)
                                                        <td>
                                                            <a class="btn btn-view btn-primary" href="{{ route('client.edit', $user) }}" title="Edit"><i class="la la-edit"></i></a>
                                                            <a class="btn btn-delete btn-danger" href="{{ route('client.destory', $user->id) }}" title="Delete" onclick="return(confirm('Are you sure, You want to delete this client. If you delete this client then other relation will be deleted'))"><i class="la la-trash"></i></a>
                                                        </td>   
                                                        
                                                        @endif
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">{{ $users->links() }}</div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
	
@endsection
