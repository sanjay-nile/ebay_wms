@include('pages.frontend.client.breadcrumb', ['title' => 'List Client User'])

<style type="text/css">
.info-list-section{background: #fff;
    border-radius: 12px;
    margin-bottom: 10px;
}
</style>
<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <form class="form-horizontal ml-1" id="filter-frm">
            <div class="card client-card">
                <div class="card-header">
                    <h2>Search</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <input type="text" name="name" class="form-control" placeholder="By Name" value="{{ app('request')->input('name') }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <input type="text" name="email" class="form-control" placeholder="By Email ID" value="{{ app('request')->input('email') }}" />
                            </div>
                        </div>                        
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-cyan btn-sm" id="search-btn"><i class="fa fa-search"></i> Search</button>
                            <a href="{{ route('client-user-list') }}" class="btn-refresh reset"><i class="la la-refresh"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <div class="info-list-section ">
            <div class="card-content">
                <div class="card-body table-responsive booking-info-box card-dashboard">
                    <table id="client_user_list" class="table table-striped table-hover nowrap avn-defaults table-sm">
                        <thead>
                            <tr>
                                <th>S no.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone no</th>
                                <th>Client</th>
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
                                <td>{{ $user->owner_name??"N/A" }}</td>
                                <td>{!! $user->status==1?"<span class='badge badge-success'> Active </span>":"<span class='badge badge-danger'> InActive </span>" !!}</td>
                                <td>
                                    <a class="btn btn-view btn-primary" href="{{ route('edit.client-user', $user) }}" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-delete btn-danger" href="{{ route('destory.client-user', $user->id) }}" title="Delete">
                                        <i class="fa fa-trash" onclick="return(confirm('Are you sure, You want to delete this record, if you delete this record then other relation will be deleted'))" ></i>
                                    </a>
                                </td>
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