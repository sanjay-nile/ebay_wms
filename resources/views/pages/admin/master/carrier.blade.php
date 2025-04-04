@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script>
$(document).ready(function() {
    var increment = 2;
    $('body').on('click','.add-cp',function(){
        let shipment = `<div class="form-row cp-add-${increment}">
                <div class="form-group col-md-5">
                    <input type="text" class="form-control" placeholder="Name..." name="cp_name[]">                    
                </div>
                <div class="form-group col-md-5">
                    <input type="text" name="cp_code[]" class="form-control" placeholder="Code...">
                </div>
                <div class="form-group col-md-2">
                    <button type="subbmit" class="btn btn-sm btn-red delete-carrier" data-id="${increment}"><i class="fa fa-trash"></i></button>
                </div>
            </div>`;
        increment++;
        $('#carrier-add').append(shipment);
    });

    $('body').on('click','.add-csc',function(){
        let shipment = `<div class="form-row csc-add-${increment}">
                <div class="form-group col-md-5">
                    <input type="text" class="form-control" placeholder="Name..." name="csc_name[]">                    
                </div>
                <div class="form-group col-md-5">
                    <input type="text" name="csc_code[]" class="form-control" placeholder="Code...">
                </div>
                <div class="form-group col-md-2">
                    <button type="subbmit" class="btn btn-sm btn-red delete-service" data-id="${increment}"><i class="fa fa-trash"></i></button>
                </div>
            </div>`;
        increment++;
        $('#service-add').append(shipment);
    });

    $('body').on('click','.delete-carrier',function(){
        let id = $(this).data('id');
        $('.cp-add-'+id).remove();
    });

    $('body').on('click','.delete-service',function(){
        let id = $(this).data('id');
        $('.csc-add-'+id).remove();
    });

    $('body').on('click','.btn-view',function(){
        let id = $(this).data('id');
        let status = $(this).data('status');
        let name = $(this).data('name');

        let obj = $("#saveCarrier");
        obj.find('.modal-title').text('Edit Carrier');
        obj.find('#carrier_id').val(id);
        $("#validStatus").val(status);
        $('input[name="name"]',obj).val(name);
        $('#addNew').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });    
});
</script>
@endpush

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
				<div class="content-header-left col-md-12 col-lg-12 mb-2 breadcrumb-new">
					<h3 class="content-header-title mb-0 d-inline-block">Carrier</h3>
					<div class="row breadcrumbs-top d-inline-block">
					  <div class="breadcrumb-wrapper col-md-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li> 
							<li class="breadcrumb-item active">Carrier List</li>
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
                    <div class="card-content">
                        <div class="card-body booking-info-box">
                            @if(isset($single_c) && !empty($single_c))
                                @include('pages.admin.master.carrier-edit')
                            @else
                                @include('pages.admin.master.carrier-add')
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body booking-info-box card-dashboard">
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Carrier Name</th>
                                        <th>Code</th>
                                        <th>Carrier Product</th>
                                        <th>Carrier Service Code</th>
                                        <th>Status</th> 
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($carrier_list as $carrier)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $carrier->name }}</td>
                                            <td>{{ $carrier->code }}</td>
                                            <td class="ws">{{ $carrier->product_list }}</td>
                                            <td class="ws">{{ $carrier->service_list }}</td>
                                            <td><span class="badge badge-{{ ($carrier->status==1)?'success':'danger' }}">{{ ($carrier->status==1)?"Active":"InActive" }}</span></td>
                                            <td>
                                                <a class="btn btn-delete btn-success" href="{{route('admin.carrier', $carrier->id)}}"><i class="la la-edit"></i></a>
                                                <a class="btn btn-delete btn-danger" onclick="return confirm('Are you sure you want to delete this Carrier?')" href="{{route('admin.carrier-delete', $carrier->id)}}"><i class="la la-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
							<div class="col-md-12"></div>
                        </div><!-- /.col -->
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('public/plugins/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/css/select2override.css') }}">
@endpush


@push('scripts')
    <script src="{{ asset('public/plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#assignwarehouse').select2({
              placeholder: 'Select Country',
              allowClear: true
            });
        })
    </script>
@endpush