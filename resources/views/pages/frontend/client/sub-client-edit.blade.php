
@include('pages.frontend.client.breadcrumb', ['title' => 'Edit Sub Client'])
<!-- Main content -->
<div class="row">
    <div class="col-md-12">
        @include('includes/admin/notify')
    </div>
    <div class="col-xs-12 col-md-12 table-responsive">
        <div class="card booking-info-box">
            <div class="card-header">
                <h4 class="card-title">
                    <a href="{{ route('create.sub-client-list') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
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
                            <a class="nav-link" data-toggle="tab" href="#ShipmentTypes" aria-controls="aboutIcon11" aria-expanded="false">Commercial</a>
                        </li>
                    </ul>
                    
                    <form action="{{ route('store.sub-client') }}" method="post" id="edit-client">
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
                            @php $j=1; $k=1; @endphp
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
                                                            <th>Currency</th>
                                                            <th>Carrier</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="carrier-add">
                                                        @forelse($shipments as $shipment)
                                                        <tr class="carrier-add-{{ $j }}">
                                                            <td>
                                                                <select name="shipments[]"  class="form-control" >
                                                                    <option value="">Select</option>
                                                                    @forelse($shipping_list as $shipping)
                                                                        <option value="{{ $shipping->id }}" @if($shipment->shipping_type_id==$shipping->id) selected @endif>{{ $shipping->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="shipment_id[]" value="{{ $shipment->id }}">
                                                                <input type="text" class="form-control" name="rates[]" placeholder="Rate" value="{{ $shipment->rate }}">
                                                            </td>
                                                            <td>
                                                                <select name="curency[]"  class="form-control">
                                                                    <option value="">Select</option>
                                                                    @forelse($currency_list as $key => $value)
                                                                        <option @if($shipment->currency==$key) selected @endif value="{{ $key }}">{{ $value }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="carriers[]"  class="form-control">
                                                                    <option value="">Select</option>
                                                                    @forelse($carrier_list as $carrier)
                                                                        <option value="{{ $carrier->id }}" @if($shipment->carrier_id==$carrier->id) selected @endif>{{ $carrier->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>                                                             
                                                            <td>
                                                                <button type="button" class="btn btn-delete btn-danger delete-shipment" data-id="{{ $shipment->id }}" data-row="{{ $j++ }}"><i class="la la-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9"></div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn-blue btn-sm pull-right mt-1 mb-2 add-more-shipment-type">Add More</button>
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
                                                            <th>Currency</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="charges-add">                                        
                                                        @forelse($charges as $charge)
                                                        <tr class="charges-add-{{ $k }}">
                                                            <td>
                                                                <select name="otherCharges[]"  class="form-control" >
                                                                    <option value="">Select</option>
                                                                    @forelse($charges_list as $list)
                                                                        <option value="{{ $list->id }}" {{ ($charge->other_charge_id==$list->id)?'selected':"" }}>{{ $list->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="charges_id[]" value="{{ $charge->id }}">
                                                                <input type="text" class="form-control" name="otherRates[]" placeholder="Rate" value="{{ $charge->rate }}">
                                                            </td>
                                                            <td>
                                                                <select name="oc_curency[]"  class="form-control">
                                                                    <option value="">Select</option>
                                                                    @forelse($currency_list as $key => $value)
                                                                        <option {{ ($charge->currency==$key)?'selected':"" }} value="{{ $key }}">{{ $value }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-delete btn-danger delete-charges" data-id="{{ $charge->id }}" data-row={{ $k++ }}><i class="la la-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9"></div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn-blue btn-sm pull-right mt-1 mb-2 add-more-charges">Add More</button>
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
</div><!-- /.content -->

@push('js')
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
                <select name="shipments[]"  class="form-control">${ship}
                </select>
            </td>
            <td>
                <input type="text" class="form-control " name="rates[]" placeholder="Rate" value="">
            </td>
            <td>
                <select name="curency[]"  class="form-control">
                    <option value="">Select</option>
                    @forelse(available_currency() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
            </td>
            <td>
                <select name="carriers[]"  class="form-control">${carrier}
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-delete btn-danger delete-shipment" data- data-row="${shipment_increment}"><i class="la la-trash"></i></button>
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
                <select name="otherCharges[]"  class="form-control">${charges}
                </select>
            </td>
            <td>
                <input type="text" class="form-control " name="otherRates[]" placeholder="Rate" value="">
            </td>
            <td>
                <select name="oc_curency[]"  class="form-control">
                    <option value="">Select</option>
                    @forelse(available_currency() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @empty
                    @endforelse
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-delete btn-danger delete-charges" data- data-row="${shipment_increment}"><i class="la la-trash"></i></button>
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
