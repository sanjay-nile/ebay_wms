@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .nav.nav-tabs.nav-underline {
        border-bottom: 1px solid #ffdfe4 !important;
        margin-bottom: 26px !important;
        background: #fff1f3;
    }
    .align-col .row .col-md-2, .col-md-1{padding: 0px 5px;}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
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
                            <li class="breadcrumb-item active">Manifest Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.content-wrapper -->

        <div class="content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="card booking-info-box">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="info-list-section">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">
                                                <a href="{{ route('admin.import.uk') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="la la-arrow-left"></i> Back
                                                </a>

                                                <button type="button" id="btn-download-payroll" class="btn btn-outline-info btn-sm">
                                                    <i aria-hidden="true" class="fa fa-cog"></i> Download Excel
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Pallet Id</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->pallet_id }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Client Name</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->client->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Warehouse Name</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->warehouse->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Shipment Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->shipmentType->name ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Sell Rate</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->rate}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Carrier</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->carrier }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Tracking ID</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->tracking_id }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Freight Charges</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->fright_charges }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Custom Duty Paid</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->custom_duty }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Custom Vat Paid</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->custom_vat }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Export Vat Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->export_vat_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Import Duty Paid</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->import_duty_paid }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Import Vat Paid</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->import_vat_paid }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Import Vat Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->import_vat_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Weight of shipment</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->weight_of_shipment }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Weight Unit Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->weight_unit_type }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">HAWB#</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->hawb_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">MAWB Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->mawb_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Return Import Entry Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->rtn_import_entry_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Return Import Entry Date</div>
                                                <div class="booking-value-info">
                                                    {{ date('Y-m-d', strtotime($pallet->rtn_import_entry_date)) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Export Declaration Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->export_declaration_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Export Declaration Date</div>
                                                <div class="booking-value-info">
                                                    {{ date('Y-m-d', strtotime($pallet->export_declaration_date)) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Exchange Rate</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->exchange_rate }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Flight Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->flight_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Flight Date</div>
                                                <div class="booking-value-info">
                                                    {{ date('Y-m-d', strtotime($pallet->flight_date)) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Manifest #</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->manifest_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Return Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->return_type }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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