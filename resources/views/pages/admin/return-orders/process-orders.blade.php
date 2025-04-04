@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});

    // $(".fiter-form").submit(function() {
    //     $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
    //     return true;
    // });

    $('body').on('change','#client_id_change',function(){
        let client_id = $(this).val();
        if(client_id){
            $.ajax({
                type:'get',
                url :"{{ route('admin.shipment-list-by-client-id') }}",
                data : {id:client_id},
                dataType : 'json',
                success : function(data){
                    $("#client_warehouse_list").replaceWith(data.warehouse);
                }
            });
        }else{
            $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
        }
    });
    
    let explt =`<div class="col-md-4"><label>Existing Pallet</label>
        <div class="form-group">
            <select name="pallet_name" id="" class="form-control">
                <option value="">Select Existing Pallet</option>
                @forelse($pallets as $pallet)
                    <option value="{{ $pallet->pallet_id }}">{{ $pallet->pallet_id }}</option>
                @empty
                @endforelse
            </select>
        </div></div><div class="col-md-3">
            <button type="button" id="add-to-old-pallet" class="btn add-to-pallet mt-2">Submit</button>
        </div>`;

    let crplt =`<div class="col-md-3"><label>Pallet ID </label>
        <div class="form-group">
            <input type="text" name="pallet_name" value="{{ generateUniquePalletName() }}" class="form-control" readonly="readonly">
        </div></div>
        <div class="col-md-3"><label>Client </label>
        <div class="form-group">
            <select name="client_id" id="client_id_change" class="form-control" required>
                <option value="">-- Select--</option>
                @forelse($client_list as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @empty
                @endforelse
            </select>
        </div></div>
        <div class="col-md-3"><label>From Warehouse </label>
        <div class="form-group">
            <select name="fr_warehouse_id" id="to_client_warehouse_list" class="form-control">
                <option value="">-- Select --</option>
                    @forelse($warehouse_list as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @empty
                        <option value="">Warehouse not added yet</option>
                    @endforelse
            </select>
        </div></div>
        <div class="col-md-3"><label>To Warehouse </label>
        <div class="form-group">
            <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                <option value="">-- Select --</option>
                    @forelse($warehouse_list as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @empty
                        <option value="">Warehouse not added yet</option>
                    @endforelse
            </select>
        </div></div>
        <div class="col-md-3 collapse">
            <div class="form-group">
                <label for="">Pallet Status</label>
                <select class="form-control" name="pallet_type">
                    <option value="InProcess">InProcess</option>
                    <option value="Closed">Closed</option>
                    <option value="Shipped">Shipped</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label>Pallet Type</label>
            <div class="form-group">
                <select name="return_type" id="" class="form-control">
                    <option value="">-- Select --</option>
                    <option value="Charity">Charity</option>
                    <option value="Discrepency">Discrepency</option>
                    <option value="Restock">Restock</option>
                    <option value="Resell">Resell</option>
                    <option value="Return" selected>Return</option>
                    <option value="Redirect">Redirect</option>
                    <option value="Recycle">Recycle</option>
                    <option value="Other">Other</option>
                    <option value="Short Shipment">Short Shipment</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <button type="button" id="add-to-pallet" class="btn add-to-pallet mt-2">Submit</button>
        </div>`;

    $(document).on('click',"#add-to-pallet", function(){
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            var vl = $('select#client_id_change option:selected').val();
            var fr_wh = $('select#to_client_warehouse_list option:selected').val();
            var to_wh = $('select#client_warehouse_list option:selected').val();

            if (vl == '' || vl == null) {
                alert('Client is required');
                return false;
            } else if (fr_wh == '' || fr_wh == null){
                alert('From warehouse is required');
                return false;
            } else if (to_wh == '' || to_wh == null){
                alert('To warehouse is required');
                return false;
            } else {
                $("#pallet-save").submit();
            }
            // $(".pallet-div").toggle();
        }
    });

    $(document).on('click',"#add-to-old-pallet", function(){
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#pallet-save").submit();
            // $(".pallet-div").toggle();
        }
    });

    $("#existing-pallet").click(function () {
        $("#ex-pallet").html(explt);
    });

    $("#create-pallet").click(function () {
        $("#ex-pallet").html(crplt);
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $("#manifest-form").on("submit", function(e) {
        e.preventDefault();
        var obj = $(this);
        if ($('#import_file').val().length > 0) {
            $(obj).find('#manifest-search-btn').attr("disabled", true);
            $(obj).find('#manifest-search-btn').html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url: '{{ route('import.return.orders') }}',
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $(obj).find('#manifest-search-btn').attr("disabled", false);
                    $(obj).find('#manifest-search-btn').html('Import Excel File');
                    if (response.status == 'error') {
                        alert(response.type);
                    }

                    if (response.status == 'success') {
                        alert(response.type);
                        // setTimeout(function() {
                        //     window.location.href = window.location.href;
                        // }, 100);
                    }
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $('#error').html('<p class="alert alert-danger">An error occurred. Please Try again.</p>');
                    $(obj).find('.modal-body .upload-btn-action, .close').prop('disabled', false);
                    $(obj).find('.admin-action-loader').remove();
                }
            });
        } else {
            alert('Please Choose xlsx/CSV File');
        }
    });
});
</script>
@endpush

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                                <li class="breadcrumb-item active">Warehouse Inventory</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12 ">
                @include('pages/errors-and-messages')

                <div class="card">
                    <div class="card-header avn-card-header">
                        <form class="form-horizontal fiter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Client</label>
                                    <div class="form-group">
                                        <select name="client" id="" class="form-control">
                                            <option value="">Select Client</option>
                                            @forelse($clients as $client)
                                            <option value="{{ $client->id }}" {{ (app('request')->input('client')==$client->id)?"selected":'' }}>{{ $client->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Order Number / Tracking Id <span class="danger">*</span></label>
                                        <input type="text" name="barcode" class="form-control" value="{{ app('request')->input('barcode') }}" autocomplete="off" />
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label>QR Code <span class="danger">*</span></label>
                                        <input type="text" name="qr_code" class="form-control" value="{{ app('request')->input('qr_code') }}" autocomplete="off" />
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="text" name="start" class="form-control" value="{{ app('request')->input('start') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="text" name="end" class="form-control" value="{{ app('request')->input('end') }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-cyan" id="search-btn">
                                        <i class="la la-search"></i> Search
                                    </button>
                                    <a href="{{ (\Request::route()->getName() == 'process.list')?route('process.list') : route('process.list') }}" class="btn btn-refresh reset"><i class="la la-refresh"></i> Reset</a>
                                </div> 
                            </div>
                        </form>
                    </div>
                    <div class="card-header avn-card-header">
                        <form class="form-horizontal fiter-form" method="post" enctype="multipart/form-data" id="manifest-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Upload Manifest File (Excel)</label>
                                                <input type="file" name="import_file" id="import_file" class="form-control" value="" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-cyan" id="manifest-search-btn">
                                                Import Excel File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Upload B2C Invoice File (Excel)</label>
                                                <input type="file" name="import_b2c_file" class="form-control" value="" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    @if($lists)
                        <div class="alert alert-success mt-1">
                            Showing {{ $lists->firstItem() }} to {{ $lists->lastItem() }} of Total {{ $lists->total() }} Orders</strong>
                        </div>
                    @endif
                    <div class="card-content">
                        <div class="pallet-div row mb-2 px-1 mt-2">
                            <div class="col-md-2">
                                <button type="button" id="existing-pallet" class="btn existing-pallet">Add to existing Pallet </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="create-pallet" class="btn create-pallet">Create New Pallet</button>
                            </div>
                        </div>
                        <div class="card-body booking-info-box card-dashboard table-responsive">
                            <form action="{{ route('admin.add.pallet.orders') }}" method="post" id="pallet-save">
                                @csrf
                                <div class="row ml-1" id="ex-pallet"></div>
                                <table id="client_user_list" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th><input name="select_all" value="1" id="select-all" type="checkbox" /></th>
                                            <th>S no.</th>
                                            <th>Action</th>
                                            <th>SC Order No.</th>
                                            <th>Order No.</th>
                                            <th>Client Name</th>
                                            <th>Customer Name</th>
                                            <th>Return Request Date</th>
                                            <th>Sku #</th>
                                            <th>Tracking ID</th>
                                            <th>Supplier Code</th>
                                            <th>Package Weight</th>
                                            <th>Package Dimensions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i= $j = 1 @endphp
                                        @forelse($lists as $row)
                                            @forelse($row->packages as  $pakage)
                                                @php
                                                    // if($pakage->process_status != 'Processed' && empty($pakage->pallet_id)){
                                                    //     continue;
                                                    // }
                                                    $meta = getMetaKeyValye($row);
                                                    $track_id = $row->tracking_id;
                                                    if(empty($track_id) && isset($meta['_generate_waywill_status'])){
                                                        $tracking_data = json_decode($meta['_generate_waywill_status']);
                                                        if($tracking_data){
                                                            $track_id = $tracking_data->carrierWaybill ?? 'N/A';
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input name="pallet_orders[]" value="{{ $pakage->id }}" type="checkbox" class="selectone" />
                                                    </td>
                                                    <td>{{ $i++ }}</td>
                                                    <td>
                                                        <a class="btn btn-view btn-primary" href="{{ route('process.order.view',$row->id) }}" title="View" target="_blank">
                                                            <i class="la la-eye"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ str_replace("#","",$row->way_bill_number) }}</td>
                                                    <td>{{ $row->client->name ?? 'N/A' }}</td>
                                                    <td>{{ $meta['_customer_name'] ?? '' }}</td>
                                                    {{-- <td>{{ getMetaValue($pakage->rg_id, '_customer_name') }}</td> --}}
                                                    <td>{{ date('d/m/Y',strtotime($pakage->created_at)) }}</td>
                                                    <td>{{ $pakage->bar_code }}</td>
                                                    <td>
                                                        {{ $track_id }}
                                                    </td>
                                                    <td>{{ $meta['_order_suppliercode'] ?? '' }}</td>
                                                    {{-- <td>{{ getMetaValue($pakage->rg_id, '_order_suppliercode') }}</td> --}}
                                                    <td>{{ $pakage->weight }}</td>
                                                    <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
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
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>
@endsection
