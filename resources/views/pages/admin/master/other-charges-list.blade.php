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
                    <h3 class="content-header-title mb-0 d-inline-block">Other Charges</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                                <li class="breadcrumb-item active">Other Charges List</li>
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
                            <div class="dt-buttons btn-group pull-right mr-1">
                                <button class="btn buttons-copy" data-toggle="modal" data-target="#addNew">
                                    <span><i class="la la-plus"></i> Add</span>
                                </button>
                            </div>
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($other_charges_list as $other_charges)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $other_charges->name }}</td>
                                            <td><span class="badge badge-{{ ($other_charges->status==1)?'success':'danger' }}">{{ ($other_charges->status==1)?"Active":"InActive" }}</span></td>
                                            <td>
                                                <button class="btn btn-view" data-id="{{ $other_charges->id }}" data-status="{{ $other_charges->status }}" data-name="{{ $other_charges->name }}"><i class="la la-edit"></i></button>
                                                <a class="btn btn-delete btn-danger" onclick="return confirm('Are you sure you want to delete this Shipment?')" href="{{route('admin.other-charges-delete', $other_charges->id)}}"><i class="la la-trash"></i></a>
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

<!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" enctype="multipart/form-data" id="saveOtherCharges" class="was-validated">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">Add Other Charges</h4>
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
                    <input type="hidden" name="id" id="other_charges_id" value="">
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

        let obj = $("#saveOtherCharges");
        obj.find('.modal-title').text('Edit Other Charges');
        obj.find('#other_charges_id').val(id);
        $("#validStatus").val(status);
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

    $("#saveOtherCharges").on("submit", function(e) {
        e.preventDefault();
        let self = $(this);
        let txt = self.text();
        let form = $("#saveOtherCharges");
        let name = $('input[name="name"]',form).val();

        if (name.trim()!='') {
            $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', true);
            $(self).find('.modal-footer .btn-action').html('Save <i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url : "{{ route('admin.other-charges.store') }}",
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
