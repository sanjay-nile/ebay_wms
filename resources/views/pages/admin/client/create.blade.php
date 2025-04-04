@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')

<script src="{{ asset('public/plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('public/plugins/js/jquery.validate.min.js') }}"></script>
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

    $('#delivery_date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    var increment = 2;
    var shipment_increment = 2;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //----------------------------------------------------------------------------------------------
    $('body').on('click','.add-more-shipment-type',function(){
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
                <button type="subbmit" class="btn btn-delete btn-danger delete-shipment" data-id="${shipment_increment}"><i class="la la-trash"></i></button>
            </td>
        </tr>`;
        shipment_increment++;
        $('#carrier-add').append(shipment);
    });

    //-----------------------------code by sanjay------------------------------------
    $('body').on('click','.add-more-charges',function(){
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
                <button type="subbmit" class="btn btn-delete btn-danger delete-charges" data-id="${shipment_increment}"><i class="la la-trash"></i></button>
            </td>
        </tr>`;
        shipment_increment++;
        $('#charges-add').append(shipment);
    });

    //----------------------------------------------------------------------------------------------
    $('body').on('click','.delete-shipment',function(){
        let id = $(this).data('id');
        $('.carrier-add-'+id).remove();

    });
    //----------------------------------------------------------------------------------------------
    $('body').on('click','.delete-warehouse',function(){
        let id = $(this).data('id');
        $('.add-'+id).remove();

    });

    //-------------------------------code by sanjay------------------------------------------
    $('body').on('click','.delete-charges',function(){
        let id = $(this).data('id');
        $('.charges-add-'+id).remove();

    });

    //----------------------------------------------------------------------------------------------        
    $('body').on('submit','#save-client',function(e){
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
                $(".save-client").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
            },
            success : function(res){
                if(res.status==201){
                    $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.message}    
                            </div>`);
                    $(".save-client").html(`Submit`).attr('disabled',false);
                    form[0].reset();
                    setTimeout(function(){
                        window.location.href = "{{ route('admin.client') }}"
                    },2000);
                    return false;
                }
                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        ${res.message}    
                    </div>`);
                $(".save-client").html(`Submit`).attr('disabled',false);
            },
            error:function(res){
                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        ${res.statusText}    
                    </div>`);
                $(".save-client").html(`Submit`).attr('disabled',false);
                return false;
            }
        })
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
    //------------------------------------------------------------------------------------------------------
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
						<li class="breadcrumb-item active">Add Client</li>
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
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12"><h5 class="card-title">Add Client Admin</h5></div>
                            </div>
                            <form action="{{ route('admin.client.store.new') }}" method="post" id="save-client">
                                @csrf
                                <input type="hidden" name="create_type" value="add_client">
                                @include('pages.admin.client.common.client',array('data'=>array()))

                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                    <div class="col-md-2">
                                        <button class="btn-red pull-right save-client" type="submit">Submit</button>
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