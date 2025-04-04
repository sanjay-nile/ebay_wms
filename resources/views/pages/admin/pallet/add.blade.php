@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('input[name="date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        //--------------------------------------------------------------------------------------------
        $('body').on('change','#client_id_change',function(){
            let client_id = $(this).val();
            if(client_id){
                $.ajax({
                    type:'get',
                    url :"{{ route('admin.shipment-list-by-client-id') }}",
                    data : {id:client_id},
                    dataType : 'json',
                    success : function(data){
                        $("#client_shipment_list").replaceWith(data.shipment);
                        $("#client_warehouse_list").replaceWith(data.warehouse);
                        $(".rate-id").html('');
                        $(".carrier-div").html('');
                        // setRate();
                    }
                });
            }else{
                $('#client_shipment_list').find('option').remove().end().append('<option value="">Select</option>');
                $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
                $(".rate-id").html('');
                $(".carrier-div").html('');
            }
        });

        //--------------------------------------------------------------------------------------------
        $(document).on('change','#client_shipment_list',function(){
            if($(this).val()){
                let rate = $('option:selected', this).attr('rate');
                let carrier = $('option:selected', this).attr('carrier');
                let rate_div = `<div class="form-group">
                                <label for="">Rate</label>
                                <span class="form-control">${rate}</span>
                                <input type="hidden" name="rate" value="${rate}"/>
                            </div>`;
                let carrier_div = `<div class="form-group">
                                <label for="">Carrier</label>
                                <span class="form-control">${carrier}</span>
                                <input type="hidden" name="carrier" value="${carrier}"/>
                            </div>`;
                $(".rate-id").html(rate_div);
                $(".carrier-div").html(carrier_div);
            }else{
                $(".rate-id").html('');
                $(".carrier-div").html('');
            }
        });

        $(document).on('click','#process-btn',function(){
            $.each($(".chk-orders:checked"), function(){
                let id = $(this).val();
                let waywill = $(this).attr('data-waywill');
                let pkg_id = $(this).attr('data-pkg-id');
                let sku = $(this).attr('data-sku');
                let traking = $(this).attr('data-tracking');
                let client = $(this).attr('data-client');
                let customer = $(this).attr('data-customer');
                let package = `<tr class="add-${pkg_id}">
                    <td>${waywill}</td>
                    <td>${sku}</td>
                    <td>${client}</td>
                    <td>${customer}</td>
                    <td>${traking}</td>
                    <td>Success</td>
                    <td>
                        <input type="hidden" name="pallet_orders[]" value="${pkg_id}">
                        <button type="button" class="btn btn-danger" onclick="delete_row(${pkg_id});">Delete</button>
                    </td>
                </tr>`;
                $('.parcel-list').append(package);
                // $('#p-name').removeClass('collapse');
                $('.parcel-tf').removeClass('collapse');
                $(window).scrollTop($('#myTable').offset().top);
            });
        });

        //--------------------------------------------------------------------------------------------
        function setRate(){
            let rate = $('#client_shipment_list option:selected').attr('rate');
            let carrier = $('#client_shipment_list option:selected').attr('carrier');
            let rate_div = `<div class="form-group">
                            <label for="">Rate</label>
                            <span class="form-control">${rate}</span>
                            <input type="hidden" name="rate" value="${rate}"/>
                        </div>`;
            let carrier_div = `<div class="form-group">
                            <label for="">Carrier</label>
                            <span class="form-control">${carrier}</span>
                            <input type="hidden" name="carrier" value="${carrier}"/>
                        </div>`;
            $(".rate-id").html(rate_div);
            $(".carrier-div").html(carrier_div);
        }

        //--------------------------------------------------------------------------------------------
        $('body').on('keyup change','input,select',function(){
            let text = $(this).val();
            if(text.length>0){
                $(this).next('small').text('');
            }
        });

        $("#select-all").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });

    //--------------------------------------------------------------------------------------------
    function add_orders(){
        var p_id = $('#barcode').val();
        if(p_id){
            $.ajax({
                type: 'get',
                url: "{{ route('admin.add.orders') }}",
                data: {barcode:p_id},
                dataType : 'json',
                success: function (response) {
                    if(response.htm){
                        $('.parcel-list').append(response.htm);
                        // $('#p-name').removeClass('collapse');
                        $('.parcel-tf').removeClass('collapse');
                    } else {
                        alert('Order is not proccessed yet.');
                    }
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        } else {
            alert('Please enter a barcode.');
        }
    }

    function add_to_old(){
        $('#pl_id').html('<input type="text" name="pallet_name" value="" placeholder="Enter Existing Pallet Id" class="form-control">');
        $('#sb_btn').html('<button type="submit" class="btn btn-info float-right btn-sm">Add to Existing Pallet</button>');
        $('#ex_btn').addClass('collapse');
    }

    //--------------------------------------------------------------------------------------------
    function delete_row(id){
        $('.add-'+id).remove();
        var rowCount = $('#myTable >tbody >tr').length;
        if (rowCount == 0) {
            $('#p-name').addClass('collapse');
            $('.parcel-tf').addClass('collapse');
        }
    }    
</script>
@endpush

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">Add Pallet</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <div class="row">
            <div class="col-xs-12 col-md-12 ">
                @include('pages/errors-and-messages')
                <div class="card booking-info-box">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="alert alert-info"><i class="la la-list"></i> Pallet Create</p>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Order Number / Tracking Id  <span class="danger">*</span></label>
                                        <input type="text" name="barcode" class="form-control" value="" autocomplete="off" id="barcode"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-success btn-sm" onclick="add_orders()" style="margin-top: 25px;">
                                        <i class="la la-edit"></i> Add parcels
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <form method="post" action="{{ route('admin.pallet.by-orders') }}" class="row form-horizontal">
                                        @csrf
                                        <div class="col-md-4" id="p-name">
                                            <label>Pallet ID </label>
                                            <div class="form-group" id="pl_id">
                                                <input type="text" name="pname" value="{{ generateUniquePalletName() }}" class="form-control" readonly="readonly">
                                                <input type="hidden" name="pallet_name" value="{{ generateUniquePalletName() }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Return Type</label>
                                            <div class="form-group">
                                                <select name="return_type" id="" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="Charity">Charity</option>
                                                    <option value="Discrepency">Discrepency</option>
                                                    <option value="Restock">Restock</option>
                                                    <option value="Resell">Resell</option>
                                                    <option value="Return">Return</option>
                                                    <option value="Redirect">Redirect</option>
                                                    <option value="Recycle">Recycle</option>
                                                    <option value="Other">Other</option>
                                                    <option value="Short Shipment">Short Shipment</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-sm" id="myTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Order No</th>
                                                            <th>Item SKU</th>
                                                            <th>Client Name</th>
                                                            <th>Customer Name</th>
                                                            <th>Tracking ID</th>
                                                            <th>Status</th>
                                                            <th>&nbsp;</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="parcel-list"></tbody>
                                                    <tfoot class="parcel-tf collapse">
                                                        <tr>
                                                            <td colspan="1" id="sb_btn">
                                                                <button type="submit" class="btn btn-info float-right btn-sm">Create a New Pallet</button>
                                                            </td>
                                                            {{-- <td colspan="1">
                                                                <button type="submit" class="btn btn-success float-right btn-sm">
                                                                    Reassign to New Pallet
                                                                </button>
                                                            </td> --}}
                                                            <td colspan="1" id="ex_btn">
                                                                <button type="button" class="btn btn-danger float-right btn-sm" onclick="add_to_old()">
                                                                    Add to Existing Pallet
                                                                </button>
                                                            </td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- fillter --}}
                <div class="card booking-info-box">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal fiter-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="alert alert-info"><i class="la la-search"></i> Filters</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="date" class="form-control" value="{{ app('request')->input('date') }}" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Client</label>
                                        <div class="form-group">
                                            <select name="client" id="" class="form-control">
                                                <option value="">Select Client</option>
                                                @forelse($client_list as $client)
                                                <option value="{{ $client->id }}" {{ (app('request')->input('client')==$client->id)?"selected":'' }}>{{ $client->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <!-- <a href="{{ route('admin.pallet.add') }}" class="btn btn-default reset float-right">
                                            <i class="la la-refresh"></i> Discard Filter
                                        </a> -->
                                        <button type="submit" class="btn btn-primary btn-sm" id="search-btn">
                                            <i class="la la-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.pallet.add') }}" class="btn btn-danger reset btn-sm">
                                            <i class="la la-refresh"></i> Reset
                                        </a>                                    
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if(!empty($lists))
            <div class="row">
                <div class="col-xs-12 col-md-12 ">
                    <div class="card booking-info-box">
                        <div class="card-content collapse show">
                            <div class="text-right">
                                <button type="button" id="process-btn" class="btn btn-success btn-sm">Add parcels to pallet</button>
                            </div>
                            <div class="card-body booking-info-box card-dashboard table-responsive">
                                <form action="" method="post" id="process-save">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}"> 
                                    <table id="client_user_list" class="table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input name="select_all" value="1" id="select-all" type="checkbox" />
                                                </th>
                                                <th>RG Order No.</th>
                                                <th>Order No.</th>
                                                <th>Client Name</th>
                                                <th>Customer Name</th>
                                                <th>Return Request Date</th>
                                                <th>Sku #</th>
                                                <th>Carrier</th>
                                                <th>Shipment Type</th>
                                                <th>Tracking ID</th>
                                                <th>Package Weight</th>
                                                <th>Package Dimensions</th>
                                                <th>Shipping Rate</th>
                                                <th>Return Option</th>
                                                <th>Process Status</th>
                                                {{-- <th>Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=1 @endphp
                                            @forelse($lists as $row)
                                                @forelse($row->processed_item as $pakage)                                                    
                                                    <tr>
                                                        <td>
                                                            <input name="order_ids[]" value="{{ $row->id }}" data-pkg-id="{{ $pakage->id }}" data-sku="{{ $pakage->bar_code }}" data-waywill="{{ $row->way_bill_number }}" data-tracking="{{ $row->tracking_id }}" data-client="{{ $row->client->name }}" data-customer="{{ $row->meta->_customer_name }}" type="checkbox" class="chk-orders" />
                                                        </td>
                                                        <td>{{ $row->id }}</td>
                                                        <td>{{ $row->way_bill_number }}</td>
                                                        <td>{{ $row->client->name ?? 'N/A' }}</td>
                                                        <td>{{ $row->meta->_customer_name }}</td>
                                                        <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                                        <td>{{ $pakage->bar_code }}</td>
                                                        <td>{{ $row->shippingPolicy->carrier->name ?? 'N/A' }}</td>
                                                        <td>{{ $row->shippingPolicy->shippingType->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <?php
                                                                $track_id = 'Not Generated';
                                                                if($row->meta->_order_tracking_id){
                                                                    $track = json_decode($row->meta->_order_tracking_id);
                                                                    foreach($track as $t){
                                                                        if (empty($t->carrierWaybillNumber)) {
                                                                            continue;
                                                                        }
                                                                        $track_id = $t->carrierWaybillNumber;
                                                                    }
                                                                }
                                                            ?>

                                                            {{ $track_id }}
                                                        </td>
                                                        <td>{{ $pakage->weight }}</td>
                                                        <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
                                                        <td>
                                                            {!! ($row->meta->_currency) ? get_currency_symbol($row->meta->_currency): get_currency_symbol('USD') !!} {{ (is_numeric($row->meta->_rate)) ? $row->meta->_rate : 0}}
                                                        </td>
                                                        <td>
                                                            @if($row->hasMeta('_drop_off') && $row->meta->_drop_off == 'By_ReturnBar')
                                                                By Return Bar™
                                                            @else
                                                                {{ str_replace('_', ' ', $row->meta->_drop_off) ?? "N/A" }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $row->process_status }}
                                                        </td>
                                                        {{-- <td>
                                                            <a class="btn btn-view btn-primary" href="{{ route('reverse-logistic.show',$row) }}" title="Edit"><i class="la la-eye"></i></a>
                                                        </td> --}}
                                                    </tr>
                                                @empty
                                                @endforelse
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </form>
                                <div class="col-md-12">
                                    @if($lists)
                                        {{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}
                                    @endif
                                </div>
                            </div><!-- /.col -->
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- <div class="row">
            <div class="col-md-12">
                @include('pages/errors-and-messages')
            </div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-content">
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.pallet.save') }}" autocomplete="off">
                                @csrf
                                <div class="info-list-section">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Pallet id</label>
                                                <input type="text" class="form-control" name="pallet_id" placeholder="Enter pallet id" value="">
                                                @error('pallet_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Client Name <span class="text-danger">*</span></label>
                                                <select name="client_id" id="client_id_change" class="form-control">
                                                    <option value="">Select</option>
                                                    @forelse($client_list as $client)
                                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @error('client_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Warehouse</label>
                                                <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                    <option value="">-- Select --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Shipment</label>
                                                <select name="shipping_type_id" id="client_shipment_list" class="form-control">
                                                    <option value="">-- Select --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 rate-id"></div>
                                        <div class="col-md-3 carrier-div"></div>
                                        <div class="col-md-3 fade">
                                            <div class="form-group">
                                                <label for="">Tracking id</label>
                                                <input type="hidden" class="form-control" name="tracking_id" value="">
                                                @error('tracking_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 fade">
                                            <div class="form-group">
                                                <label for="">Fright Charges</label>
                                                <input type="hidden" class="form-control" name="fright_charges" value="">
                                                @error('fright_charges')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 fade">
                                            <div class="form-group">
                                                <label for="">Custom Duty</label>
                                                <input type="hidden" class="form-control" name="custom_duty" value="">
                                                @error('custom_duty')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn-blue btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-10 error-msg"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div><!-- /.content-wrapper -->
</div>
<style type="text/css">
.btn-danger {color: #FFF; background-color: #FF4961; border-color: #FF4961; font-size: 11px; text-transform: uppercase; font-weight: bold; margin-left: 10px;}
.btn-success {
    color: #ffffff;}

</style>

@endsection