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
					<h3 class="content-header-title mb-0 d-inline-block">Client Orders</h3>
					<div class="row breadcrumbs-top d-inline-block">
					  <div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
							<li class="breadcrumb-item active">Client Order List</li>
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
                        <form class="form-horizontal fiter-form ml-1">
                            <div class="row">
                                
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="name" class="form-control"  placeholder="Client Name" value="{{ app('request')->input('name') }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="from" class="form-control" id="from" placeholder="From Date" value="{{ app('request')->input('from') }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        
                                        <input type="text" name="to" id="to" class="form-control" placeholder="To Date" value="{{ app('request')->input('to') }}" autocomplete="off" />
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
                                    <a href="{{ route('client-order') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>
                                </div>
                            </div>
                        </form>
						<a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                        
                    </div>

                    <div class="card-content collapse show">  
                        <button class="list-right-btn">Create Invoice</button>
                        <div class="card-body booking-info-box card-dashboard">                        	                        	
                            <table id="client_order_list" class="table table-striped table-bordered nowrap avn-defaults">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Way Bill No</th>
                                        <th>Date</th>                          
                                        <th>Total Package</th>
                                        <th>Total Weight</th>
                                        <th>Buy Rate</th>
                                        <th>Sell Rate</th>
                                        <th>Total Amount</th>
                                        <th>Status</th> 
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	<tr>
                                        <td>1</td>
                                        <td>{{ rand() }}</td>
                                        <td>08/23/2019</td>
                                        <td>2</td>
                                        <td>4</td>
                                        <td>$20</td>
                                        <td>$30</td>
                                        <td>$180</td>
                                        <td>Delivered</td>
                                        <td><a class="btn btn-view btn-primary" href="#" title="View"><i class="la la-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>{{ rand() }}</td>
                                        <td>08/24/2019</td>
                                        <td>2</td>
                                        <td>4</td>
                                        <td>$20</td>
                                        <td>$30</td>
                                        <td>$200</td>
                                        <td>In Transit</td>
                                        <td><a class="btn btn-view btn-primary" href="#" title="View"><i class="la la-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>{{ rand() }}</td>
                                        <td>08/25/2019</td>
                                        <td>2</td>
                                        <td>4</td>
                                        <td>$20</td>
                                        <td>$30</td>
                                        <td>$250</td>
                                        <td>Others</td>
                                        <td><a class="btn btn-view btn-primary" href="#" title="View"><i class="la la-eye"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
							<div class="col-md-12">{{-- pagination --}}</div>
                        </div>
                        <!-- /.col -->
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

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
	
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatable/css/datatables.min.css') }}">
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('plugins/datatable/js/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
   $('#from').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
   $('#to').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    var defaults= {
        dom: 'Bfrtip', buttons: [ {
            extend:'copy', attr: {
                id: 'allan'
            }
            , text: '<i class="la la-copy"></i> Copy', exportOptions: {
                columns: ':not(:last-child)'
            }
        }
        , {
            extend:'excel', text: '<i class="la la-file-excel-o"></i> Excel', exportOptions: {
                columns: ':not(:last-child)'
            }
        }
        , {
            extend:'print', text: '<i class="la la-print"></i> Print', exportOptions: {
                columns: ':not(:last-child)'
            }
        }
        , 'colvis'], 'aoColumnDefs': [ {
            'bSortable': false, 'aTargets': [-1]/* 1st one, start by the right */
        }
        ], exportOptions: {
            columns: [1, 2, 3, 4]
        }
        , "searching": false, "ordering": true, "bPaginate": false, "bInfo": false
    }
    ;
    $('.avn-defaults').dataTable($.extend(true, {}
    , defaults, {}
    ));
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary');
   
});
</script>
@endpush
