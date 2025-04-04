@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function(){
    // code by sanjay
    let cnt = ship = carrier = charges = '';
    $.ajax({
        type : 'get',
        url : "{{ route('admin.country-list') }}",            
        dataType : 'html',
        success : function(res){
            cnt = res;
        }
    });

    $.ajax({
        type : 'get',
        url : "{{ route('admin.carrier-list') }}",            
        dataType : 'html',
        success : function(res){
            carrier = res;
        }
    });

    $.ajax({
        type : 'get',
        url : "{{ route('admin.shipment-list') }}",            
        dataType : 'html',
        success : function(res){
            ship = res;
        }
    });

    $.ajax({
        type : 'get',
        url : "{{ route('admin.charges-list') }}",            
        dataType : 'html',
        success : function(res){
            charges = res;
        }
    });

    //------------------------------------------------------------------------------------------------------
    $(document).on('click','.add-warehouse',function(){
        let client = $(this).data('client');
        $.ajax({
            url : "{{ route('warehouse.create') }}",
            data : {'client':client},
            dataType : 'json',
            success : function(data){
                $('#defaultModal').html(data.html);
                $('#defaultModal').modal({
                    backdrop:'static',
                    keyboard:false,
                    show:true
                });

            }
        })
    });
    //------------------------------------------------------------------------------------------------------

    $("#saveWarehouse").on("submit", function(e) {
        e.preventDefault();
        let self = $(this);
        let txt = self.text();
        let form = $("#saveWarehouse");
        let name = $('input[name="name"]',form).val();

        if (name.trim()!='') {            
            $.ajax({
                url : "{{ route('warehouse.store') }}",
                method: "POST",
                data : form.serialize(),
                dataType : 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', true);
                    $(self).find('.modal-footer .btn-action').html('Save <i class="fa fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if(response.status==true){
                        $('#msg').html(`<div class="alert alert-success alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            ${response.msg}
                        </div>`);
                    }else{
                        $('#msg').html(`<div class="alert alert-danger alert-dismissible">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            ${response.msg}
                        </div>`);
                        $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', false);
                        $(self).find('.modal-footer .btn-action').html('Save');
                    }
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $('#msg').html('<p class="alert alert-danger">An error occurred. Please Try again later.</p>');
                    $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', false);
                    $(self).find('.modal-footer .btn-action').html('Save');
                },
                complete: function() {
                    $(self).find('.modal-footer .action, .modal-footer .btn-action, .close').prop('disabled', false);
                    $(self).find('.modal-footer .btn-action').html('Save');
                }
            });
        } else {
            $('#msg').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please enter warehouse name</div>');
        }
    });

    //------------------------------------------------------------------------------------------------------
    $(document).on('change','.country-list',function(){
        let id = $(this).val();
        $.ajax({
            type:'get',
            url : "{{ route('country.state') }}",
            data:{country_id:id},
            dataType : 'json',
            success : function(data){
                $(".state-list").replaceWith(data.html);
            }
        })
    });

    var increment = 2;
     

    //----------------------------------------------------------------------------------------------
    $('body').on('click','.add-more-shipment-type',function(){
        let shipment_increment = $("#carrier-add tr").length+1;

        let shipment = `<tr class="carrier-add-${shipment_increment}">
            <td>
                <select name="shipments[]" id="" class="form-control">${ship}
                </select>
            </td>
            <td>
                <input type="text" class="form-control " name="rates[]" placeholder="Rate" value="">
            </td>
            <td>
                <select name="curency[]" id="" class="form-control">
                    <option value="">Select</option>
                    @forelse(available_currency() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
            </td>
            <td>
                <select name="carriers[]" id="" class="form-control">${carrier}
                </select>
            </td>
            <td>
                <select name="ship_default[]" id="" class="form-control">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-delete btn-danger delete-shipment" data-id="" data-row="${shipment_increment}"><i class="la la-trash"></i></button>
            </td>
        </tr>`;
        shipment_increment++;
        $('#carrier-add').append(shipment);
    });

    //-----------------------------code by sanjay------------------------------------
    $('body').on('click','.add-more-charges',function(){
        let shipment_increment = $("#charges-add tr").length+1;

        let shipment = `<tr class="charges-add-${shipment_increment}">
            <td>
                <select name="otherCharges[]" id="" class="form-control">${charges}
                </select>
            </td>
            <td>
                <input type="text" class="form-control " name="otherRates[]" placeholder="Rate" value="">
            </td>
            <td>
                <select name="oc_curency[]" id="" class="form-control">
                    <option value="">Select</option>
                    @forelse(available_currency() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-delete btn-danger delete-charges" data-id="" data-row="${shipment_increment}"><i class="la la-trash"></i></button>
            </td>
        </tr>`;
        shipment_increment++;
        $('#charges-add').append(shipment); 
    });

    //----------------------------------------------------------------------------------------------
    $('body').on('click','.delete-shipment',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        if(row && confirm("Are you sure you want to delete this record")){
            if(id){
                let url = "{{ route('client-shipment-other-charges.delete',":id") }}";
                url = url.replace(":id",id);
                $.ajax({
                    type:'delete',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    success : function(res){
                        if(res.status==true){
                            $('.carrier-add-'+row).remove();
                            alert(res.msg);
                        }else{
                            alert(res.msg);
                        }
                    }
                })
                
            }else{
                $('.carrier-add-'+row).remove();
            }
        }
        

    });
    
    //-------------------------------code by sanjay------------------------------------------
    $('body').on('click','.delete-charges',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        if(row && confirm('Are you sure, you want to delete this record')){
            if(id){
                let url = "{{ route('client-shipment-other-charges.delete',":id") }}";
                url = url.replace(":id",id);
                $.ajax({
                    type:'delete',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    success : function(res){
                        if(res.status==true){
                            $('.charges-add-'+row).remove();
                            alert(res.msg);
                        }else{
                            alert(res.msg);
                        }
                    }
                })
            }else{
                $('.charges-add-'+row).remove();
            }
        }
        

    });

    //------------------------------------------------------------------------------------------------------
    $(document).on('click','.edit-warehouse',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        let url = "{{ route('warehouse.show',":id") }}";
        url = url.replace(":id",id);
        if(id){
            $.ajax({
                url : url,
                dataType:'json',
                data:{'row':row},
                success : function(res){
                    if(res.status==true){
                        $('#defaultModal').html(res.html);
                        $('#defaultModal').modal('show');
                    }else{
                        alert(res.msg);
                    }
                }
            })
        }
    });

    //-------------------------------------------------------------------------
    $('body').on('click','.delete-warehouse',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        var url = '{{ route("warehouse.delete", ":id") }}';
        url = url.replace(':id', id);
        if(id && confirm('Are you sure, you want to delete this warehouse')){
            $.ajax({
                type:'delete',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success : function(res){
                    if(res.status==true){
                        $('.add-'+row).remove();
                    }else{
                        alert(res.msg)
                    }
                }
            })
        }

    });

    //----------------------------------------------------------------------------------------------        
    $('body').on('submit','#edit-client',function(e){
        e.preventDefault();        
        let formData = new FormData();
        let TotalImages = $('#image-upload')[0].files.length;  //Total Images
        let images = $('#image-upload')[0];  

        for (let i = 0; i < TotalImages; i++) {
            formData.append('company_logo', images.files[i]);
        }
        formData.append('TotalImages', TotalImages);
        let form = $(this);
        $.each($(form).serializeArray(), function (i, field) {
            formData.append(field.name, field.value);
        });
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType : 'json',
            beforeSend : function(){
                $(".save-client").html(`Update <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
            },
            success : function(res){
                $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.message}    
                            </div>`);
                            $(".save-client").html(`Update`).attr('disabled',false);
                if(res.status==200){
                    setTimeout(function(){
                        
                        window.location.reload();
                    },2000);
                }
                return false;
            },
            error:function(res){
                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.statusText}    
                            </div>`);
                $(".save-client").html(`Update`).attr('disabled',false);
                return false;
            }
        })
    });
});
</script>

@endpush

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-12 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">View/Edit Client</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <div class="row">
            <div class="col-md-12">
                @include('includes/admin/notify')
            </div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ route('admin.client') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                        </h4>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12"><h5 class="card-title">Edit Client Admin</h5></div>
                            </div>

                            <form action="{{ route('admin.client.store.new') }}" method="post" id="edit-client">
                                @csrf
                                <input type="hidden" name="create_type" value="edit_client">
                                <input type="hidden" name="client_id" value="{{ $user->id }}">
                                <input type="hidden" name="company_name" value="{{ $user->name }}">

                                @include('pages.admin.client.common.client',array('data'=>$user))
                                
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                    <div class="col-md-2">
                                        <button class="btn-red pull-right save-client" type="submit">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- card-body Close -->
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection