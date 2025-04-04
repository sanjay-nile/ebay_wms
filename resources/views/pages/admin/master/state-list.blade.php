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
                    {{-- <h3 class="content-header-title mb-0 d-inline-block">State</h3> --}}
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                                <li class="breadcrumb-item active">State List</li>
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
                        <form class="form-horizontal fiter-form ml-0">
                            <div class="row">                 
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="name" class="form-control" placeholder="Name" value="" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
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
                        <div class="card-body booking-info-box card-dashboard">
                            <div class="dt-buttons btn-group pull-right mr-1">
                                <button class="btn buttons-copy" data-toggle="modal" data-target="#addNew">
                                    <span><i class="la la-plus"></i> Add</span>
                                </button>
                            </div>
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults" data-page-length='100'>
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Name</th>
                                        <th>Short Name</th>
                                        <th>Country Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($state_list as $state)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $state->name }}</td>
                                            <td>{{ $state->shortname }}</td>
                                            <td>{{ $state->country->name }}</td>
                                            <td><span class="badge badge-{{ ($state->status==1)?'success':'danger' }}">{{ ($state->status==1)?"Active":"InActive" }}</span></td>
                                            <td>
                                                <button class="btn btn-view" data-id="{{ $state->id }}" data-status="{{ $state->status }}" data-name="{{ $state->name }}" data-country="{{ $state->country_id }}"><i class="la la-edit"></i></button>
                                                <a class="btn btn-delete btn-danger" onclick="return confirm('Are you sure you want to delete this Shipment?')" href="{{route('admin.state-list-delete', $state->id)}}"><i class="la la-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="col-md-12">
                                {{ $state_list->appends(Request::except('page'))->onEachSide(2)->links() }}
                            </div>
                        </div><!-- /.col -->
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

<!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" enctype="multipart/form-data" id="saveState" class="was-validated">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">Add State</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validName">Name</label>
                                <input type="text" class="form-control" id="validName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validSName">Short Name</label>
                                <input type="text" class="form-control" id="validSName" name="shortname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validCountry">Country Name</label>
                                <select name="country_id" id="validCountry" class="form-control">
                                    <option>-- Select --</option>
                                    @forelse($country_list as $con)
                                        <option value="{{$con->id }}">{{$con->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validStatus">Status</label>
                                <select name="status" id="validStatus" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="2">InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="msg"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="state_id" value="">
                    <button type="button" class="btn btn-outline-secondary btn-sm action" data-dismiss="modal">Close</button>
                    <button type="submit" name="save" id="save-shipment" class="btn btn-outline-primary btn-sm btn-action">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('body').on('click','.btn-view',function(){
        let id = $(this).data('id');
        let status = $(this).data('status');
        let name = $(this).data('name');
        let country = $(this).data('country');

        let obj = $("#saveState");
        obj.find('.modal-title').text('Edit Shipment');
        obj.find('#state_id').val(id);
        $("#validStatus").val(status);
        $("#validCountry").val(country);
        $('input[name="name"]',obj).val(name);
        
        $('#addNew').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });
    
    $('#addNew').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    $("#saveState").on("submit", function(e) {
        e.preventDefault();
        let self = $(this);
        let txt = self.text();
        let form = $("#saveState");
        let name = $('input[name="name"]',form).val();

        if (name.trim()!='') {
            $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', true);
            $(self).find('.modal-footer .btn-action').html('Save <i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url : "{{ route('admin.state-list.store') }}",
                method: "POST",
                data : form.serialize(),
                dataType : 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status==true){
                        $('#msg').html(`<div class="alert alert-success alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            ${response.msg}
                        </div>`);

                        setTimeout(function(){
                            window.onbeforeunload = null;
                            window.location.reload();
                        },1000);
                    }else{
                        $('#msg').html(`<div class="alert alert-danger alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            ${response.msg}
                        </div>`);
                        $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', false);
                        $(self).find('.modal-footer .btn-action').html('Upload');
                    }
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $('#msg').html('<p class="alert alert-danger">An error occurred. Please Try again later.</p>');
                    $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', false);
                    $(self).find('.modal-footer .btn-action').html('Save');
                }
            });
        } else {
            $('#msg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please enter name</div>');
        }
    });
});
</script>
@endpush