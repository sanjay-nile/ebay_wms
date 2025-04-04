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
    $(document).ready(function(){        
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
                        $("#client_warehouse_list").replaceWith(data.warehouse);
                    }
                });
            }else{
                $("#client_warehouse_list").find('option').remove().end().append('<option value="">Select</option>');
            }
        });

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

        $("#frm-sbt").click(function () {
            // let cnt = $("#country option:selected").val();
            // if(cnt){
            //     $("#product-form").submit();
            // } else {
            //     alert('Select at least one country');
            // }
            $("#product-form").submit();
        });

        $('input[name="export_declaration_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $('input[name="rtn_import_entry_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});

        $('#btn-download-payroll').click(function(){
            var obj = $(this);
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Please Wait...');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let pallet_id = "{{ $pallet->pallet_id }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob',
                },
                type: 'POST',
                url: "{{ route('admin.download.excel') }}",
                data: {
                    pallet_id: pallet_id,
                },
                success: function(result, status, xhr) {
                    $(obj).prop('disabled', false);
                    $(obj).html('<i aria-hidden="true" class="fa fa-cog"></i> Download Excel');
                    if (result.flag) {
                        alert(result.msg);
                    } else {
                        var disposition = xhr.getResponseHeader('content-disposition');
                        var matches = /"([^"]*)"/.exec(disposition);
                        var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-import-uk.xlsx');

                        // The actual download
                        var blob = new Blob([result], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;

                        document.body.appendChild(link);

                        link.click();
                        document.body.removeChild(link);
                    }                
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="fa fa-eye"> Get Details</i>');
                }
            });
        });

        // manifest file download
        $('#manifes-frm-sbt').click(function(){
            var obj = $(this);
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Please Wait...');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let pallet_id = "{{ $pallet->id }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob',
                },
                type: 'POST',
                url: "{{ route('admin.generate.manifest') }}",
                data: {
                    pallet_id: pallet_id,
                    manifest_type: 'import_uk',
                },
                success: function(result, status, xhr) {
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="la la-upload"></i> Generate Custom Manifest');
                    if (result.flag) {
                        alert(result.msg);
                    } else {
                        var disposition = xhr.getResponseHeader('content-disposition');
                        var matches = /"([^"]*)"/.exec(disposition);
                        var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-import-uk.xlsx');

                        // The actual download
                        var blob = new Blob([result], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;

                        document.body.appendChild(link);

                        link.click();
                        document.body.removeChild(link);
                    }                
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('<i class="la la-upload"></i> Generate Custom Manifest');
                }
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
				<div class="col-md-8 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Import Back to Uk Manifest</li>
					</ol>
				</div>
			</div>
		</div>

        <form action="{{ route('admin.generate.manifest') }}" method="post" id="product-form" class="form-horizontal fiter-form mb-0">
            @csrf
            <input type="hidden" name="pallet_id" id="pallet_id" value="{{ $pallet->id }}">
            <input type="hidden" name="manifest_type" id="manifest_type" value="import_uk">
        </form>
    </div>

    <!-- Main content -->
    <div class="content-wrapper">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                @include('pages/errors-and-messages')
                <div class="card booking-info-box">
                    <div class="card-content">
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.import.uk.update') }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="p_id" value="{{ $pallet->id }}">
                                <div class="info-list-section">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">
                                                <a href="{{ route('admin.import.uk') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>

                                                <button type="button" id="btn-download-payroll" class="btn btn-outline-info btn-sm">
                                                    <i aria-hidden="true" class="fa fa-cog"></i> Download Excel
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Pallet id</label>
                                                <input type="text" class="form-control" name="pallet_id" placeholder="Enter pallet id" value="{{ $pallet->pallet_id }}">
                                                @error('pallet_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Client Name <span class="text-danger">*</span></label>
                                                <select name="client_id" id="client_id_change" class="form-control">
                                                    <option value="">-- Select--</option>
                                                    @forelse($client_list as $client)
                                                        <option value="{{ $client->id }}" @if($client->id == $pallet->client_id) selected @endif>{{ $client->name }}</option>
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
                                                        @forelse($warehouse_list as $warehouse)
                                                            <option value="{{ $warehouse->id }}" @if($warehouse->id == $pallet->warehouse_id) selected @endif>{{ $warehouse->name }}</option>
                                                        @empty
                                                            <option value="">Warehouse not added yet</option>
                                                        @endforelse
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Shipment Type</label>                                                
                                                {{-- <select name="shipping_type_id" id="client_shipment_list" class="form-control">
                                                    <option value="">-- Select --</option>
                                                        @forelse($shipment_list as $shipment)
                                                            <option value="{{ $shipment->shipping_type_id }}" rate="{{ $shipment->rate }}" carrier="{{ $shipment->carrier_name }}" @if($shipment->shipping_type_id == $pallet->shipping_type_id) selected @endif>{{ $shipment->shipment_name }}</option>
                                                        @empty
                                                            <option value="">Shipment not added yet</option>
                                                        @endforelse
                                                </select> --}}
                                                <select name="shipping_type_id" id="client_shipment_list" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="1" @if($pallet->shipping_type_id == 1) selected @endif rate="0" carrier="UPS Ground">Ocean</option>
                                                    <option value="3" @if($pallet->shipping_type_id == 3) selected @endif rate="0" carrier="UPS Ground">Air</option>
                                                    <option value="4" @if($pallet->shipping_type_id == 4) selected @endif rate="0" carrier="UPS Ground">Road</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tracking id</label>
                                                <input type="text" class="form-control" name="tracking_id" value="{{ $pallet->tracking_id }}">
                                                @error('tracking_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Freight Buy Rate</label>
                                                <input type="text" class="form-control" name="fright_charges" value="{{ $pallet->fright_charges }}">
                                                @error('fright_charges')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Custom Duty Paid</label>
                                                <input type="text" class="form-control" name="custom_duty" value="{{ $pallet->custom_duty }}">
                                                @error('custom_duty')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Custom Vat Paid</label>
                                                <input type="text" class="form-control" name="custom_vat" value="{{ $pallet->custom_vat }}">
                                                @error('custom_vat')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Export Vat Number</label>
                                                <input type="text" class="form-control" name="export_vat_number" value="{{ $pallet->export_vat_number }}">
                                                @error('export_vat_number')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Import Duty Paid</label>
                                                <input type="text" class="form-control" name="import_duty_paid" value="{{ $pallet->import_duty_paid }}">
                                                @error('import_duty_paid')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Import Vat Paid</label>
                                                <input type="text" class="form-control" name="import_vat_paid" value="{{ $pallet->import_vat_paid }}">
                                                @error('import_vat_paid')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Import Vat Number</label>
                                                <input type="text" class="form-control" name="import_vat_number" value="{{ $pallet->import_vat_number }}">
                                                @error('import_vat_number')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 rate-id">
                                            <div class="form-group">
                                                <label for="">Sell Rate</label>
                                                <input type="text" name="rate" class="form-control" value="{{ $pallet->rate }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3 carrier-div">
                                            <div class="form-group">
                                                <label for="">Carrier</label>
                                                <input type="text" name="carrier" class="form-control" value="{{ $pallet->carrier }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Weight of shipment</label>
                                                <input type="text" name="weight_of_shipment" class="form-control" value="{{ $pallet->weight_of_shipment }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Weight Unit Type</label>
                                                <select class="form-control" name="weight_unit_type">
                                                    <option value="Lbs" @if($pallet->weight_unit_type == 'Lbs') selected @endif>Lbs</option>
                                                    <option value="GRAM" @if($pallet->weight_unit_type == 'GRAM') selected @endif>GRAM</option>
                                                    <option value="KILOGRAM" @if($pallet->weight_unit_type == 'KILOGRAM') selected @endif>KILOGRAM</option>
                                                    <option value="TONNE" @if($pallet->weight_unit_type == 'TONNE') selected @endif>TONNE</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Return Type</label>
                                                <select class="form-control" name="return_type">
                                                    <option value="">-- Select --</option>
                                                    <option value="Charity" @if($pallet->return_type == 'Charity') selected @endif>Charity</option>
                                                    <option value="Discrepency" @if($pallet->return_type == 'Discrepency') selected @endif>Discrepency</option>
                                                    <option value="Restock" @if($pallet->return_type == 'Restock') selected @endif>Restock</option>
                                                    <option value="Resell" @if($pallet->return_type == 'Resell') selected @endif>Resell</option>
                                                    <option value="Return" @if($pallet->return_type == 'Return') selected @endif>Return</option>
                                                    <option value="Redirect" @if($pallet->return_type == 'Redirect') selected @endif>Redirect</option>
                                                    <option value="Recycle" @if($pallet->return_type == 'Recycle') selected @endif>Recycle</option>
                                                    <option value="Other" @if($pallet->return_type == 'Other') selected @endif>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">HAWB#</label>
                                                <input type="text" name="hawb_number" class="form-control" value="{{ $pallet->hawb_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">MAWB Number</label>
                                                <input type="text" name="mawb_number" class="form-control" value="{{ $pallet->mawb_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Manifest #</label>
                                                <input type="text" name="manifest_number" class="form-control" value="{{ $pallet->manifest_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Return Import Entry Number</label>
                                                <input type="text" name="rtn_import_entry_number" class="form-control" value="{{ $pallet->rtn_import_entry_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Return Import Entry Date</label>
                                                <input type="text" name="rtn_import_entry_date" class="form-control" value="{{ date('Y-m-d', strtotime($pallet->rtn_import_entry_date)) }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Export Declaration Number</label>
                                                <input type="text" name="export_declaration_number" class="form-control" value="{{ $pallet->export_declaration_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Export Declaration Date</label>
                                                <input type="text" name="export_declaration_date" class="form-control" value="{{ date('Y-m-d', strtotime($pallet->export_declaration_date)) }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Exchange Rate</label>
                                                <input type="text" name="exchange_rate" class="form-control" value="{{ $pallet->exchange_rate }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Flight Number</label>
                                                <input type="text" name="flight_number" class="form-control" value="{{ $pallet->flight_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Flight Date</label>
                                                <input type="text" name="flight_date" class="form-control" value="{{ date('Y-m-d', strtotime($pallet->flight_date)) }}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-blue">Submit</button>
                                        
                                            {{-- <button type="button" class="btn btn-red" id="frm-sbt">
                                                <i class="la la-upload"></i> Generate Custom Manifest
                                            </button> --}}
                                            <button type="button" class="btn btn-red" id="manifes-frm-sbt">
                                                <i class="la la-upload"></i> Generate Custom Manifest
                                            </button>
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
        </div>
    </div>

    <!-- /.content-wrapper -->
    <div class="container">
       	<div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="card table-card-section booking-info-box card-dashboard table-responsive">
                    @if ($pallet->pallet_type == 'Shipped')
                        @include('pages.admin.manifest.html.shipped')
                    @else
                        @include('pages.admin.manifest.html.process-close')
                    @endif
                </div>
            </div>
            @if(!empty($orders))
                <div class="col-md-12">{{ $orders->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
            @endif
       	</div>
    </div>
</div>

@endsection