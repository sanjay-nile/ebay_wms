@include('pages.frontend.client.breadcrumb', ['title' => 'List Sub Client'])

<div class="row">
    <div class="col-xs-12 col-md-12 ">
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
            <div class="card-content">                            
                <div class="card-body">  
                    <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults table-sm">
                        <thead>
                            <tr>
                                <th>S no.</th>
                                <th>Sub Client Code</th>
                                <th>Name</th>
                                <th>Email</th>                          
                                <th>Phone no</th>
                                <th>Customer Rep</th>
                                <th>Status</th> 
                                @if(Auth::user()->user_type_id==3)
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1 @endphp
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td><span class="badge badge-{{ $user->user_code?'success':'danger' }}">{{ $user->user_code??" N/A " }}</span></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->owner_name??"N/A" }}</td>
                                    <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>
                                    @if(Auth::user()->user_type_id==3)
                                    <td>
                                        <a class="btn btn-view" href="{{ route('edit.sub-client', $user) }}" title="Edit"><i class="fa fa-eye"></i></a>
                        
                                        <a class="btn btn-delete btn-danger" href="{{ route('delete.sub-client', $user->id) }}" title="Delete" onclick="return(confirm('Are you sure, You want to delete this client. If you delete this client then other relation will be deleted'))"><i class="fa fa-trash"></i></a>
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
</div>
