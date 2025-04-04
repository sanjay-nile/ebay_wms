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
            /*let cnt = $("#country option:selected").val();
            if(cnt){
                $("#product-form").submit();
            } else {
                alert('Select at least one country');
            }*/
            $("#product-form").submit();
        });

        $('input[name="entry_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
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
                        var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-vat-return.xlsx');

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
                            <li class="breadcrumb-item active">Vat Return Manifest</li>
                        </ol>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.generate.manifest') }}" method="post" id="product-form" class="form-horizontal fiter-form mb-0">
                @csrf
                <input type="hidden" name="pallet_id" id="pallet_id" value="{{ $pallet->id }}">
                <input type="hidden" name="manifest_type" id="manifest_type" value="vat_return">
            </form>
        </div><!-- /.content-wrapper -->

        <div class="content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    @include('pages/errors-and-messages')
                    <div class="card booking-info-box">
                        <div class="card-content">
                            <div class="card-body">
                                <form method="post" action="{{ route('admin.vat.return.update') }}" autocomplete="off">
                                    @csrf
                                    <input type="hidden" name="p_id" value="{{ $pallet->id }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">
                                                <a href="{{ route('admin.vat.return') }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="la la-arrow-left"></i> Back
                                                </a>

                                                <button type="button" id="btn-download-payroll" class="btn btn-outline-info btn-sm">
                                                    <i aria-hidden="true" class="fa fa-cog"></i> Download Excel
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Package Number</label>
                                                <input type="text" class="form-control" name="pakage_number" value="{{ $pallet->meta->pakage_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Order Number</label>
                                                <input type="text" class="form-control" name="order_number" value="{{ $pallet->meta->order_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Customer Name</label>
                                                <input type="text" class="form-control" name="customer_name" value="{{ $pallet->meta->customer_name ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Consignee Address</label>
                                                <input type="text" class="form-control" name="consignee_address" value="{{ $pallet->meta->consignee_address ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">SKU#'s</label>
                                                <input type="text" class="form-control" name="sku" value="{{ $pallet->meta->sku ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Item Description</label>
                                                <input type="text" class="form-control" name="item_description" value="{{ $pallet->meta->item_description ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 collapse">
                                            <div class="form-group">
                                                <label for="">Country of Origin</label>
                                                <input type="text" class="form-control" name="country_of_origin" value="{{ $pallet->meta->country_of_origin ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">AWB Number</label>
                                                <input type="text" class="form-control" name="awb_number" value="{{ $pallet->meta->awb_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Pallet Number</label>
                                                <input type="text" class="form-control" name="pallet_id" value="{{ $pallet->pallet_id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">RG to Complete Entry Number</label>
                                                <input type="text" class="form-control" name="rg_entry_number" value="{{ $pallet->meta->rg_entry_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Entry Date</label>
                                                <input type="text" class="form-control" name="entry_date" value="{{ $pallet->meta->entry_date ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Rate of Exchange</label>
                                                <input type="text" class="form-control" name="rate_of_exchange" value="{{ $pallet->meta->rate_of_exchange ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button class="btn-red" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Submit</button>
                                        
                                            <button type="button" class="btn-blue" id="frm-sbt">
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