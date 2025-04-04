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
                        var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-custom-broker.xlsx');

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
                                                <a href="{{ route('admin.cust.broker') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="la la-arrow-left"></i> Back
                                                </a>

                                                <button type="button" id="btn-download-payroll" class="btn btn-outline-info btn-sm">
                                                    <i aria-hidden="true" class="fa fa-cog"></i> Download Excel
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Declaration Date</div>
                                                <div class="booking-value-info">
                                                    {{ ($pallet->meta->declaration_date) ? date('Y-m-d', strtotime($pallet->meta->declaration_date)) : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Declaration Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->declaration_type ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Additional Declaration Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->addtion_declaration_type ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Customs Procedure</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->customs_procedure ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Additional Procedure</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->additional_procedure ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">INCO Term</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->inco_term ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Invoice Currency</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->invoice_currency ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">UNLO Code</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->unlo_code ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Net Mass Kg</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->net_mass ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Gross Mass Kg</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->gross_mass ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Number Packages</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->number_packages ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">LRN</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->lrn ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Use Average Customs Value</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->avg_custom_value ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Unique ID Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->unique_id_number ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Container ID Number</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->container_id_number ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Seller Item Refrence</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->seller_item_ref ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Internet Hypertext Link Item</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->internet_hypertext ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Email Consignee</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->email_consignee ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">ID Mother Package</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->id_mother_package ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Consignee Status</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->consignee_status ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Method Payment</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->payment_method ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Postal Marking</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->postal_marketing ?? 'N/A' }}
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