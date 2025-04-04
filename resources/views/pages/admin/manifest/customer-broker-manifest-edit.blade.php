@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
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

        $('input[name="declaration_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
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
                            <li class="breadcrumb-item active">Customs Broker -LCA</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <form action="{{ route('admin.generate.manifest') }}" method="post" id="product-form" class="form-horizontal fiter-form mb-0">
                @csrf
                <input type="hidden" name="pallet_id" id="pallet_id" value="{{ $pallet->id }}">
                <input type="hidden" name="manifest_type" id="manifest_type" value="custom_broker">
            </form>
        </div><!-- /.content-wrapper -->

        <div class="content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    @include('pages/errors-and-messages')
                    <div class="card booking-info-box">
                        <div class="card-content">
                            <div class="card-body">
                                <form method="post" action="{{ route('admin.cust.broker.update') }}" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="p_id" value="{{ $pallet->id }}">
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Declaration Date</label>
                                                <input type="text" class="form-control" name="declaration_date" value="{{ $pallet->meta->declaration_date ?? '' }}">
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Declaration Type</label>
                                                <div class="form-group">
                                                    <select name="declaration_type" id="" class="form-control">
                                                        <option value="">-- Select Declaration Type --</option>
                                                        <option value="Charity">Charity</option>
                                                    </select>
                                                </div>
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Additional Declaration Type</label>
                                                <div class="form-group">
                                                    <select name="addtion_declaration_type" id="" class="form-control">
                                                        <option value="">-- Select Additional Declaration Type --</option>
                                                        <option value="Charity">Charity</option>
                                                    </select>
                                                </div>
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Customs Procedure</label>
                                                <input type="text" class="form-control" name="customs_procedure" value="{{ $pallet->meta->customs_procedure ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Additional Procedure</label>
                                                <input type="text" class="form-control" name="additional_procedure"  value="{{ $pallet->meta->additional_procedure ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">INCO Term</label>
                                                <input type="text" class="form-control" name="inco_term" value="{{ $pallet->meta->inco_term ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Invoice Currency</label>
                                                <input type="text" class="form-control" name="invoice_currency" value="{{ $pallet->meta->invoice_currency ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">UNLO Code</label>
                                                <input type="text" class="form-control" name="unlo_code" value="{{ $pallet->meta->unlo_code ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Net Mass Kg</label>
                                                <input type="text" class="form-control" name="net_mass" value="{{ $pallet->meta->net_mass ?? '' }}">
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Gross Mass Kg</label>
                                                <input type="text" class="form-control" name="gross_mass" value="{{ $pallet->meta->gross_mass ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Number Packages</label>
                                                <input type="text" class="form-control" name="number_packages" value="{{ $pallet->meta->number_packages ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">LRN</label>
                                                <input type="text" class="form-control" name="lrn" value="{{ $pallet->meta->lrn ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Use Average Customs Value</label>
                                                <input type="text" class="form-control" name="avg_custom_value" value="{{ $pallet->meta->avg_custom_value ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Unique ID Number</label>
                                                <input type="text" class="form-control" name="unique_id_number" value="{{ $pallet->meta->unique_id_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Container ID Number</label>
                                                <input type="text" class="form-control" name="container_id_number" value="{{ $pallet->meta->container_id_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Seller Item Refrence </label>
                                                <input type="text" class="form-control" name="seller_item_ref" value="{{ $pallet->meta->seller_item_ref ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Internet Hypertext Link Item</label>
                                                <input type="text" class="form-control" name="internet_hypertext" value="{{ $pallet->meta->internet_hypertext ?? '' }}">
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Consignee</label>
                                                <input type="text" class="form-control" name="email_consignee" value="{{ $pallet->meta->email_consignee ?? '' }}">
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">ID Mother Package</label>
                                                <input type="text" class="form-control" name="id_mother_package" value="{{ $pallet->meta->id_mother_package ?? '' }}">
                                            </div>
                                       </div>
                                       <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Consignee Status</label>
                                                <input type="text" class="form-control" name="consignee_status" value="{{ $pallet->meta->consignee_status ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Method Payment </label>
                                                <input type="text" class="form-control" name="payment_method" value="{{ $pallet->meta->payment_method ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Postal Marking</label>
                                                <input type="text" class="form-control" name="postal_marketing" value="{{ $pallet->meta->postal_marketing ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button class="btn-red" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Submit</button>
                                        </div>
                                        <div class="col-md-10">
                                            <button type="button" class="btn btn-primary" id="frm-sbt">
                                                <i class="la la-upload"></i> Generate Custom Manifest
                                            </button>
                                        </div>
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