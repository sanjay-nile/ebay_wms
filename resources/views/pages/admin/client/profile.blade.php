@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
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
                            <ul class="nav nav-tabs nav-tabs-list nav-underline">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#about-client" aria-controls="homeIcon11" aria-expanded="true">Client Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#BillingAddress" aria-controls="aboutIcon11" aria-expanded="false">Addresses</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#warehouses" aria-controls="aboutIcon11" aria-expanded="false">Warehouses</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#ShipmentTypes" aria-controls="aboutIcon11" aria-expanded="false">Commercials</a>
                                </li>
                            </ul>
                            
                            <form action="{{ route('admin.client.store.new') }}" method="post" id="edit-client">
                                @csrf
                                <input type="hidden" name="client_type" value="edit_client">
                                <input type="hidden" name="client_id" value="{{ $user->id }}">
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="about-client" aria-labelledby="about-client" aria-expanded="true">
                                        @include('pages.admin.client.common.client',array('data'=>$user))
                                    </div><!-- about-client Close -->

                                    <div role="tabpanel" class="tab-pane" id="BillingAddress" aria-labelledby="BillingAddress" aria-expanded="true">
                                         @include('pages.admin.client.common.address',array('data'=>$address_details))
                                    </div><!-- BillingAddress Close -->

                                    <div role="tabpanel" class="tab-pane" id="warehouses" aria-labelledby="about-customer" aria-expanded="true">
                                        @include('pages.admin.client.common.warehouses',array('warehouse_list'=>$warehouses,'country_list'=>$country_list,'client_id'=>$user->id ))
                                    </div><!-- warehouses Close -->

                                    <div role="tabpanel" class="tab-pane" id="ShipmentTypes" aria-labelledby="about-customer" aria-expanded="true">
                                        <div class="info-list-section">
                                            <div class="row">
                                                <div class="col-md-12"><h5 class="card-title">Shipment Types & Selling Rates</h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Shipment Type</th>
                                                                    <th>Rate</th>
                                                                    <th>Carrier</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="carrier-add">
                                                                @forelse($shipments as $shipment)
                                                                <tr class="carrier-add-1">
                                                                    <td>
                                                                        @forelse($shipping_list as $shipping)
                                                                            @if($shipment->shipping_type_id==$shipping->id)
                                                                                {{ $shipping->name }}
                                                                            @endif
                                                                        @empty
                                                                        @endforelse
                                                                    </td>
                                                                    <td>
                                                                        {{ html_entity_decode(get_currency_symbol($shipment->currency)) }} {{ $shipment->rate }}
                                                                    </td>
                                                                    <td>
                                                                        @forelse($carrier_list as $carrier)
                                                                            @if($shipment->carrier_id==$carrier->id)
                                                                                {{ $carrier->name }}
                                                                            @endif
                                                                        @empty
                                                                        @endforelse
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

                                        <!-- other charges -->
                                        <div class="info-list-section">
                                            <div class="row">
                                                <div class="col-md-12"><h5 class="card-title">Other Charges</h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Other Charges</th>
                                                                    <th>Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="charges-add">                                        
                                                                @forelse($charges as $charge)
                                                                <tr class="charges-add-1">
                                                                    <td>
                                                                        @forelse($charges_list as $list)
                                                                            @if($charge->other_charge_id==$list->id)
                                                                                {{ $list->name }}
                                                                            @endif
                                                                        @empty
                                                                        @endforelse
                                                                    </td>
                                                                    <td>
                                                                        {{ html_entity_decode(get_currency_symbol($charge->currency)) }} {{ $charge->rate }}
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
                                    </div><!-- ShipmentTypes Close -->

                                    <div class="row mt-1">
                                        <div class="col-md-10 error-msg"></div>
                                        <div class="col-md-2">
                                            <button class="btn-red pull-right save-client" type="submit">Update</button>
                                        </div>
                                    </div>
                                </div><!-- tab-content Close -->
                            </form>
                        </div> <!-- card-body Close -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
@endsection

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

    var increment = 2;
    var shipment_increment = 2;
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
                <select name="carriers[]" id="" class="form-control">${carrier}
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

    //----------------------------------------------------------------------------------------------        
    $('body').on('submit','#edit-client',function(e){
        e.preventDefault();
        let form = $(this); 
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : form.serialize(),
             headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType : 'json',
            beforeSend : function(){
                $(".save-client").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
            },
            success : function(res){
                $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.message}    
                            </div>`);
                $(".save-client").html(`Submit`).attr('disabled',false);
                setTimeout(function(){
                    window.location.href = "{{ route('client.profile') }}"
                },2000);
                return false;
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
});
</script>

@endpush
