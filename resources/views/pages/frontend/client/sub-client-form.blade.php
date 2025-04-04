@include('pages.frontend.client.breadcrumb', ['title' => 'Add Sub Client'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush
@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
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
                if(res.status==201){
                    $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.message}    
                            </div>`);
                    $(".save-client").html(`Submit`).attr('disabled',false);
                    form[0].reset();
                    setTimeout(function(){
                        window.location.href = "{{ route('create.sub-client-list') }}"
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

<!-- Main content -->
<div class="row">
    <div class="col-xs-12 col-md-12 table-responsive">
        <div class="card booking-info-box">
            <div class="card-header">
                <h4 class="card-title">
                    <a href="{{ route('create.sub-client-list') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                </h4>                
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
                    <form action="{{ route('store.sub-client') }}" method="post" id="save-client" autocomplete="off">
                        @csrf
                        <input type="hidden" name="client_type" value="add_client">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="about-client" aria-labelledby="about-client" aria-expanded="true">
                                @include('pages.admin.client.common.client',array('data'=>array()))
                            </div><!-- about-client Close -->

                            <div role="tabpanel" class="tab-pane" id="BillingAddress" aria-labelledby="BillingAddress" aria-expanded="true">
                                @include('pages.admin.client.common.address',array('data'=>array()))
                            </div><!-- BillingAddress Close -->

                            <div role="tabpanel" class="tab-pane" id="warehouses" aria-labelledby="about-customer" aria-expanded="true">
                                @include('pages.admin.client.common.warehouses',array('warehouse_list'=>array(),'country_list'=>$country_list,'client_id'=>0))
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
                                                            <th>Currency</th>
                                                            <th>Carrier</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="carrier-add">
                                                        <tr class="carrier-add-1">
                                                            <td>
                                                                <select name="shipments[]"  class="form-control" >
                                                                    <option value="">Select</option>
                                                                    @forelse($shipping_list as $shipping)
                                                                        <option value="{{ $shipping->id }}">{{ $shipping->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="rates[]" placeholder="Rate" value="">
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
                                                                <select name="carriers[]"  class="form-control">
                                                                    <option value="">Select</option>
                                                                    @forelse($carrier_list as $carrier)
                                                                        <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>                                                             
                                                            <td>
                                                                <!-- <button type="button" class="btn btn-sm btn-danger delete-shipment" data-id="1"><i class="la la-trash"></i></button> -->
                                                            </td>
                                                        </tr>
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
                                                        <tr class="charges-add-1">
                                                            <td>
                                                                <select name="otherCharges[]"  class="form-control" >
                                                                    <option value="">Select</option>
                                                                    @forelse($charges_list as $charges)
                                                                        <option value="{{ $charges->id }}">{{ $charges->name }}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="otherRates[]" placeholder="Rate" value="">
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
                                                                <!-- <button type="button" class="btn btn-sm btn-danger delete-charges" data-id="1"><i class="la la-trash"></i></button> -->
                                                            </td>
                                                        </tr>
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
                                    <button class="btn btn-danger btn-red pull-right save-client" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
