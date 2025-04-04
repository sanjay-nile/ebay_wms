<div class="info-list-section">
    <div class="row">
        <div class="col-md-6">
            <h5 class="card-title">Warehouse Address</h5>
        </div>
        <div class="col-md-6">
            <div class="dt-buttons btn-group pull-right">      
                <button class="btn btn-blue add-warehouse" type="button" data-client="{{ $client_id }}">
                    <span><i class="la la-plus"></i> Add</span>
                </button>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Warehouse Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="warehouse-add">
                        @php $i=1 @endphp
                        @forelse($warehouse_list as $wh)
                        <tr class="add-{{ $i }}">
                            <td>{{ $wh->name }}</td>
                            <td>{{ $wh->contact_person }}</td>
                            <td>{{ $wh->email }}</td>
                            <td>{{ $wh->phone }}</td>
                            <td>{!! $wh->address !!}, {!! $wh->city !!},{!!  $wh->state !!},{!! $wh->zip_code !!}, {!! get_country_name_by_id($wh->country_id) !!}</td>
                            <td>                                
                                <button class="btn btn-view edit-warehouse" data-id="{{ $wh->id }}" data-row="{{ $i }}" type="button"><i class="la la-edit"></i></button>
                                <a class="btn btn-delete btn-danger delete-warehouse" href="javascript:void(0)" data-row="{{ $i++ }}" data-id="{{ $wh->id }}"><i class="la la-trash"></i></a>
                            </td>
                        </tr>
                       @empty
                           
                       @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>