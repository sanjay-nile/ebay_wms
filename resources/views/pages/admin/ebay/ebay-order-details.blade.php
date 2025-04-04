@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')
<?php
    $sales_id = 'None';
    if (isset($order_data_by_id['_sales_order_status']) && !empty($order_data_by_id['_sales_order_status'])) {
        $invoice = json_decode($order_data_by_id['_sales_order_status']);
        if (isset($invoice->salesInvoiceNumber)) {
            $sales_id = $invoice->salesInvoiceNumber;
        }
    }

    $track_id = $label_url = '';
    $trackingD = '';
    if($order_data_by_id['_ebay_order_status'] == 'Returned'){
        $trackingD = $order_data_by_id['_return_order_tracking_id'] ?? '';
        if(!empty($trackingD)){
            $track = json_decode($trackingD);
            if(isset($track->carrierWaybill)){
                $track_id = $track->carrierWaybill;
                foreach($track->labelDetailList as $t){
                    $label_url = $t->artifactUrl ?? '';
                }
            } else{
                foreach($track as $t){
                    $track_id = $t->carrierWaybillNumber ?? 'N/A';
                    $label_url = $t->carrierWaybillURL ?? '';
                }
            }

            $track_id = $track->carrierWaybill ?? '';
            foreach($track->labelDetailList as $t){
                $label_url = $t->artifactUrl ?? '';
            }
        }
    }else{
        $trackingD = $order_data_by_id['_order_tracking_id'] ?? '';
        if(!empty($trackingD)){
            $track = json_decode($trackingD);
            foreach($track as $t){
                $track_id = $t->carrierWaybillNumber ?? '';
                $label_url = $t->carrierWaybillURL ?? '';                
            }
        } else {
            $tracking_detail = $order_data_by_id['_generate_waybill_status'] ?? NULL; 
            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;            
            if($tracking_data){
                if (!empty($tracking_data->carrierWaybill)) {
                    $track_id = $tracking_data->carrierWaybill;
                }
            }
        }

        if(empty($label_url)){
            $tracking_detail = $order_data_by_id['_generate_waybill_status'] ?? NULL; 
            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;            
            if($tracking_data){
                foreach($tracking_data->labelDetailList as $t){            
                    if(isset($t->artifactUrl) && !empty($t->artifactUrl)){
                        $label_url = $t->artifactUrl;
                    }
                }
            }
        }
    }
?>

@push('js')
<script type="text/javascript">
$(document).ready(function() {

    $('#refund_mod').on('change', function() {
        if(this.value == 'Paypal'){
            $('#bank-div').hide();
        }else{
            $('#bank-div').show();
        }
    });

    $('#track-detail').click(function(){
        var obj = $(this);
        $(obj).prop('disabled', true);
        $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
        $.ajax({
            url: $('#hf_base_url').val() + '/get-tracking/{!! $track_id !!}',
            method: "get",
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // console.log(response);
                $(obj).prop('disabled', false);
                $(obj).html('<i class="fa fa-eye"> Get Details</i>');
                $('#track-data').html(response);
                $('#myModal').modal({show:true});
            },
            error : function(jqXHR, textStatus, errorThrown){
                $(obj).prop('disabled', false);
                $(obj).html('<i class="fa fa-eye"> Get Details</i>');
            }
        });
    });

    $('#track-id-update').click(function(){
        var track_id = $('#track_id').val();
        var obj = $(this);
        $(obj).prop('disabled', true);
        $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
        $.ajax({
            url: "",
            method: "post",
            data: {
                track_id: track_id,
                label: '{!! $label_url !!}',
                id: {!! $order_data_by_id['_order_id'] !!}
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {                    
                $(obj).prop('disabled', false);
                $(obj).html('Send');
                var dt = JSON.parse(response);
                if(dt.status){
                    alert(dt.msg);
                    // location.reload();
                }else{
                    alert(dt.msg);
                }
            },
            error : function(jqXHR, textStatus, errorThrown){
                $(obj).prop('disabled', false);
                $(obj).html('Send');
                alert('Something wrong');
            }
        });
    });
});
</script>
@endpush

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active"> eBay Order Details</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-1 mr-5">
                        <a class="btn btn-blue" href="{{ url()->previous() }}">
                            <i class="fa fa-hand-o-left" aria-hidden="true"></i> {!! trans('admin.back_button') !!}
                        </a>
                    </div>
                    <div class="col-sm-1 mr-5">
                        <a class="btn btn-red" href="{{ route('admin.order_invoice', $order_data_by_id['_order_id']) }}" target="_blank">
                            <i class="fa fa-print" aria-hidden="true"></i> {!! trans('admin.print_invoice_label') !!}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-section">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ trans('admin.order_details') }}</h5>
                                <hr>
                                <p>
                                    <strong>{{ trans('admin.order') }} #:</strong>
                                    {!! $order_data_by_id['_order_id'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.order_date') }}:</strong>
                                    {!! $order_data_by_id['_order_date'] !!}
                                </p>
                                <p>
                                    <strong>Ebay Order Id:</strong>
                                    {!! $order_data_by_id['order_number'] !!}
                                </p>
                                <p>
                                    <strong>Creation Date:</strong>
                                    {!! $order_data_by_id['sale_date'] !!}
                                </p>
                                <p>
                                    <strong>Sales Invoice Number:</strong>
                                    {!! $sales_id !!}
                                </p>
                                @if(in_array($order_data_by_id['_ebay_order_status'], ['Returned', 'P_Returned']))
                                    <p>
                                        <strong>Return Id:</strong>
                                        {!! $order_data_by_id['_ebay_return_order_id'] ?? 'N/A' !!}
                                    </p>
                                    <p>
                                        <strong>Return Date:</strong>
                                        {!! $order_data_by_id['_return_order_date'] ?? date("y-m-d H:i:s", strtotime('now')) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ trans('admin.shipping_address') }}</h5>
                                <hr>
                                <p>
                                    {!! $order_data_by_id['ship_to_name'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.phone') }}:</strong>
                                    {!! $order_data_by_id['ship_to_phone'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.address_1') }}:</strong>
                                    {!! $order_data_by_id['ship_to_address_1'] !!}
                                </p>
                                @if($order_data_by_id['ship_to_address_2'] != 'None')
                                    <p>
                                        <strong>{{ trans('admin.address_2') }}:</strong>
                                        {!! $order_data_by_id['ship_to_address_2'] !!}
                                    </p>
                                @endif
                                <p>
                                    <strong>{{ trans('admin.city') }}:</strong>
                                    {!! $order_data_by_id['ship_to_city'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.state') }}:</strong>
                                    {!!$order_data_by_id['ship_to_state'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.postCode') }}:</strong>
                                    {!! $order_data_by_id['ship_to_zip'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.country') }}:</strong>
                                    {!! $order_data_by_id['ship_to_country'] !!}
                                </p>
                                <p>
                                    <strong>{{ trans('admin.email') }}:</strong>
                                    {!! $order_data_by_id['ship_to_email'] !!}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Buyer Details</h5>
                                <hr>
                                <p>
                                    <strong>User Id:</strong>
                                    {!! $order_data_by_id['buyer_username'] !!}
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="btn">
                                    {{-- live new or return --}}
                                    @if (in_array($order_data_by_id['_ebay_order_status'], ['Returned', 'Completed', 'Pending']) && !$order_data_by_id['_ebay_return_order_status'])
                                        <a href="{{ route('admin.cancel-ebay-orders', $order_data_by_id['_order_id']) }}" onclick="return confirm('Are you sure you want to cancel order?')" class="btn btn-red">Cancel</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- logix waywill  push data --}}
        <div class="card card-section collapse @if($errors->any()) show @endif" id="logixErpPush">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post" class="form-horizontal" autocomplete="off">
                                    @if(in_array($order_data_by_id['_ebay_order_status'], ['Returned']) && $order_data_by_id['_ebay_return_order_status'] == '' && count(json_decode($order_data_by_id['items'])) > 1)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-condensed admin-data-table admin-data-list table-sm">
                                                <tr>
                                                    <th></th>
                                                    <th class="image">
                                                        Image
                                                    </th>
                                                    <th class="description">
                                                        Item Title
                                                    </th>
                                                    <th class="description">
                                                        Sku
                                                    </th>
                                                </tr>
                                                @foreach(json_decode($order_data_by_id['items']) as $item)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" value="{{ $item->sku }}" name="sku[]" class="rt_class">
                                                        </td>
                                                        <td class="order_product">
                                                            
                                                        </td>
                                                        <td>
                                                            {!! $item->item_title !!}
                                                        </td>
                                                        <td>
                                                            {!! $item->sku !!}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                        <input type="hidden" name="return_order_type" value="p_return">
                                    @endif

                                    <div class="form-row align-items-center">
                                        @csrf
                                        <input name="order_id" value="{{ $order_data_by_id['_order_id'] }}" type="hidden" />
                                        <input type="hidden" name="order_type" value="return_order">
                                        {{-- <input type="hidden" name="service_code" value="ECOMDOCUMENT"> --}}
                                        <input type="hidden" name="customer_code" value="00000">
                                        <input type="hidden" name="consignee_code" value="00000">

                                        <div class="form-group col-md-3">
                                            <label for="">Document Type</label>
                                            <select class="form-control form-control-sm" name="document_type">
                                                <option value="">-- Select-- </option>
                                                <option value="Non Document">Non Document</option>
                                                <option value="DOCUMENT">Document</option>
                                            </select>
                                            @error('document_type')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Carrier</label>
                                            <select name="carrier" id="carrier" class="form-control form-control-sm">
                                                <option value="">-- Select-- </option>
                                                @forelse($carrier as $cp)
                                                    <option value="{{ $cp->code }}" name="{{ $cp->id }}">{{ $cp->name }}</option>
                                                @empty
                                                <option value="">Carrier not found</option>
                                                @endforelse
                                            </select>
                                            @error('carrier')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 carrier_product">
                                            <label for="">Carrier Product</label>
                                            <select name="carrier_product" id="carrier_product" class="form-control form-control-sm">
                                                <option value="">-- Select-- </option>
                                            </select>
                                            @error('carrier_product')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 service_code">
                                            <label for="">Carrier Service Code</label>
                                            <select name="service_code" id="service_code" class="form-control form-control-sm">
                                                <option value="">-- Select-- </option>
                                            </select>
                                            @error('service_code')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Payment Mode <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm" name="payment_mode">
                                                <option value="">-- Select-- </option>
                                                <option value="FOD">FOD</option>
                                                <option value="PAID">PAID</option>
                                                <option value="TBB" selected>TBB</option>
                                                <option value="FOC">FOC</option>  
                                            </select>
                                            @error('payment_mode')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Number Of Packages</label>
                                            <input type="text" class="form-control" value="" name="number_of_packages">
                                            @error('number_of_packages')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Weight Unit Type</label>
                                            <select class="form-control form-control-sm" name="weight_unit_type">
                                                <option value="GRAM">GRAM</option>
                                                <option value="KILOGRAM">KILOGRAM</option>
                                                <option value="TONNE">TONNE</option>
                                                <option value="POUND" selected>POUND</option>
                                            </select>
                                            @error('weight_unit_type')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Actual Weight <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="">
                                            @error('actual_weight')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Charged Weight <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="">
                                            @error('charged_weight')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Warehouse <span class="text-danger">*</span></label>
                                            <select name="warehouse_id" id="client_warehouse_list" class="form-control form-control-sm">
                                                <option value="">-- Select-- </option>
                                                @forelse($warehouse as $wh)
                                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                @empty
                                                <option value="">Warehouse not found</option>
                                                @endforelse
                                            </select>
                                            @error('warehouse_id')
                                                <div class="error">The field is required</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-red btn-sm mt-4">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-info card-section">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Payment Details</h5>
                                <hr>
                                <p>
                                    <strong>Order Payment Method:</strong>
                                    {!! $order_data_by_id['payment_method'] !!}
                                </p>
                                <p>
                                    <strong>Shipping Services:</strong>
                                    {!! $order_data_by_id['shipping_service'] !!}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Other Details</h5>
                                <hr>
                                <p>
                                    <strong>Legacy Order Id:</strong>
                                    @if($order_data_by_id['_ebay_legacy_order_id']) {!! $order_data_by_id['_ebay_legacy_order_id'] !!} @endif
                                </p>
                                <p>
                                    <strong>Seller Id:</strong>
                                    @if($order_data_by_id['_ebay_order_seller_id']) {!! $order_data_by_id['_ebay_order_seller_id'] !!} @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Tracking Details</h5>
                                <hr>
                                <p>
                                    <strong>Tracking Id:</strong>
                                    @if($track_id) {{ $track_id }} @else None @endif
                                </p>
                                <p>
                                    @if($track_id)
                                        <button class="btn btn-sm btn-warning" id="track-detail" type="button">
                                            <i class="fa fa-eye">
                                                Get Details
                                            </i>
                                        </button>
                                    @endif
                                    @if($label_url)
                                        <a class="btn btn-sm btn-info" href="{!! $label_url !!}" target="_blank">
                                            <i class="fa fa-eye">
                                                Label
                                            </i>
                                        </a>
                                    @endif
                                </p>                        
                            </div>
                            <div class="box-footer">
                                <p>
                                    @if(!$track_id)
                                        <input type="text" name="track_id" id="track_id" value="" class="form-control" placeholder="Tracking Id">
                                        <button class="btn btn-red mt-1" id="track-id-update" type="button">
                                            Update Tracking ID
                                        </button>
                                    @else
                                        <input type="hidden" name="track_id" id="track_id" value="{{ $track_id }}" class="form-control">
                                    @endif
                                    @if(!isset($order_data_by_id['_order_tracking_id']))
                                        {{-- <button class="btn btn-red mt-1" id="track-id-update" type="button">
                                            Update Fullfilment
                                        </button> --}}
                                    @endif
                                </p>
                            </div>
                            <!-- The Modal -->
                            <div aria-hidden="true" aria-labelledby="updater" class="modal fade" id="myModal" role="dialog" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content track-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h4 class="modal-title">
                                                Tracking Details
                                            </h4>
                                            <button class="close" data-dismiss="modal" type="button">×</button>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <h6>
                                                Tracking ID:
                                                <b>{{ $track_id }}</b>
                                            </h6>
                                            <div id="track-data"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>{{ trans('admin.ordered_items') }}</h5>
                        <div class="table-responsive order_info">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr class="order_menu">
                                        <td class="image">
                                            {{ trans('admin.item') }}
                                        </td>
                                        <td class="description">
                                            Item Content
                                        </td>
                                        <td class="description">
                                            {{ trans('admin.description') }}
                                        </td>
                                        <td class="price">
                                            {{ trans('admin.price') }}
                                        </td>
                                        <td class="quantity">
                                            {{ trans('admin.quantity') }}
                                        </td>
                                        <td class="total">
                                            {{ trans('admin.totals') }}
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(json_decode($order_data_by_id['items']) as $item)
                                        <?php
                                            $tr_class = '';
                                            if(in_array($order_data_by_id['_ebay_order_status'], ['P_Returned'])){
                                                $skus = json_decode($order_data_by_id['_ebay_return_order_sku']);
                                                if(in_array($item->sku, $skus)){
                                                    $tr_class = 'table-active';
                                                }
                                            }
                                        ?>                           
                                        <tr class="{{ $tr_class }}">
                                            <td class="order_product">
                                                
                                            </td>
                                            <td>
                                                <p>
                                                    <strong>Line Item Id:</strong>
                                                    {!! $item->item_number !!}
                                                </p>
                                                <p>
                                                    <strong>Variation Id:</strong>
                                                    {!! $item->legacyVariationId !!}
                                                </p>                                    
                                            </td>
                                            <td class="order_description">
                                                <h6>{!! $item->item_title !!}</h6>
                                                <p>
                                                    <strong>SKU:</strong>
                                                    {!! $item->sku !!}
                                                </p>
                                                <p>
                                                    {!! $item->variation_details !!}
                                                </p>
                                            </td>
                                            <td class="order_price">
                                                <p>
                                                    {!! $item->price !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </p>
                                            </td>
                                            <td class="order_quantity">
                                                <p>
                                                    {!! $item->quantity !!}
                                                </p>
                                            </td>
                                            <td class="order_line_total">
                                                <p>
                                                    {!! $item->price !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="order-total" colspan="6">
                                            <p>
                                                <strong>{{ trans('admin.tax') }}</strong>
                                                <span>
                                                    {!! $order_data_by_id['seller_collected_tax'] !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </span>
                                            </p>
                                            <p>
                                                <strong>{{ trans('admin.shipping_cost') }}</strong>
                                                <span>
                                                    {!! $order_data_by_id['shipping_and_handling'] !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </span>
                                            </p>
                                            <p>
                                                <strong>{{ trans('admin.coupon_discount_label') }}</strong>
                                                <span>
                                                    {!! $order_data_by_id['discount'] ?? 0 !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </span>
                                            </p>
                                            <p>
                                                <strong>{{ trans('admin.order_total') }}</strong>
                                                <span>
                                                    {!! $order_data_by_id['sold_for'] !!} {!! $order_data_by_id['_ebay_order_currency'] !!}
                                                </span>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
