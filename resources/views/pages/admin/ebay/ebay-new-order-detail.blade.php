@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

<?php
    $sales_id = 'None';
    $track_id = $order_data_by_id['tracking_number'] ?? '';
    $label_url = $order_data_by_id['label_url'] ?? '';    
?>

@push('js')
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    toastr.options ={
       "closeButton" : true,
       "progressBar" : true,
       "disableTimeOut" : true,
    }

    $('.generate-label').on('click',function(e){
        e.preventDefault();
        let button = $(this);
        // button.prop('disabled', true); // Disable the button

        var id = $(this).attr('data-id');
        var crr = $('input[name="optradio"]:checked').val();
        var length = $('#length').val();
        var width = $('#width').val();
        var height = $('#height').val();
        var weight = $('#weight').val();
        var location_id = $('#scan_i_location_id').val();
        if(location_id == null || location_id == ""){
            toastr.error('Location Id is required.');
            return;
        }

        $.ajax({
            type:'post',
            url: '{{ route("admin.generateLabel") }}',
            data:{
                post_id:id,
                location_id:location_id,
                carrier:crr,
                length:length,
                width:width,
                height:height,
                weight:weight,
            },
            dataType : 'json',
            beforeSend : function(){
                button.html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
            },
            success : function(response){
                console.log(response);
                if(response.status==201){
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                    return false;
                }

                if(response.status==202){
                    toastr.error(response.message);
                    $('.generate-label').html(`Generate`).attr('disabled',false);
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    return false;
                }

                if(response.status==203){
                    toastr.error(response.message);
                    $('.generate-label').html(`Generate`).attr('disabled',false);
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    return false;
                }

                if(response.status==200){
                    toastr.error(response.message);
                    $('.generate-label').html(`Generate`).attr('disabled',false);
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    return false;
                }
            },
            error : function(data){
                toastr.error(data.statusText);
                button.html(`Generate`).attr('disabled',false);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }
        });
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

    $(document).on('click', '#dim-weight', function(){
        $('#dim-div').toggle();
    });

    $(document).on('change', '#weight',function(e){
        let wegt = $(this).val();
        $('input[name="optradio"]').removeAttr('checked');
        if(wegt <= 1){
            $('input[name="optradio"][value="USPS"]').attr('checked',true);
        }

        if(wegt > 1){
            $('input[name="optradio"][value="UPS"]').attr('checked',true);
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
                <div class="col-md-12 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active"> eBay Order Details</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12 ">
                <div class="card booking-info-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-1 mr-4">
                                <a class="btn btn-blue" href="{{ route('admin.dispatch.list', 'new') }}">
                                    <i class="fa fa-hand-o-left" aria-hidden="true"></i> {!! trans('admin.back_button') !!}
                                </a>
                            </div>

                            {{-- <div class="col-sm-1 mr-5">
                                <a class="btn btn-red" href="{{ route('admin.order_invoice', $order_data_by_id['_order_id']) }}" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print Pick Sheet
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
            </div>

            <div class="col-xs-12 col-md-12 ">
                <div class="card booking-info-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body order_details-info">
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
                                            <strong>Shipping Carrier Code:</strong>
                                            {!! $order_data_by_id['shipping_carrier_code'] !!}
                                        </p>
                                        <p>
                                            <strong>Shipping Service Code:</strong>
                                            {!! $order_data_by_id['shipping_service_code'] !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body order_details-info">
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
                                    <div class="card-body order_details-info">
                                        <h5>Other Details</h5>
                                        @if(in_array($order_data_by_id['order_status'], ['IS-03']))
                                            {{-- <button type="button" class="btn-sm btn btn-red float-right" id="dim-weight"><i class="fa fa-edit"></i> Dims or Weight</button> --}}
                                        @endif
                                        <hr>
                                        <p>
                                            <strong>User Id:</strong>
                                            {!! $order_data_by_id['buyer_username'] !!}
                                        </p>
                                        <p>
                                            <strong>Order Payment Method:</strong>
                                            {!! $order_data_by_id['payment_method'] !!}
                                        </p>
                                        <p>
                                            <strong>Shipping Services:</strong>
                                            {!! $order_data_by_id['shipping_service'] !!}
                                        </p>
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
                                    <div class="card-footer">
                                        @if(isset($carrier) && !empty($carrier) && in_array($order_data_by_id['order_status'], ['IS-03']))
                                            <p>
                                                @forelse($carrier as $k => $crr)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="optradio" id="inlineRadio{{ $k }}" value="{{ $crr->name }}" @if($k == 0) checked @endif>
                                                        <label class="form-check-label" for="inlineRadio1">{{ $crr->name }} ({{ $crr->unit_type }})</label>
                                                    </div>
                                                @empty
                                                @endforelse
                                            </p>
                                            <p>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="text" id="scan_i_location_id" value="" placeholder="Location Id" class="form-control">
                                                    </div>
                                                    <div class="col-md-6 mt-1">
                                                        <label for="">Length (in)</label>
                                                        <input type="text" id="length" value="10" placeholder="Length" class="form-control">
                                                    </div>
                                                    <div class="col-md-6 mt-1">
                                                        <label for="">Width (in)</label>
                                                        <input type="text" id="width" value="8" class="form-control" placeholder="Width">
                                                    </div>
                                                    <div class="col-md-6 mt-1">
                                                        <label for="">Height (in)</label>
                                                        <input type="text" id="height" value="1" class="form-control" placeholder="Height">
                                                    </div>
                                                    <div class="col-md-6 mt-1">
                                                        <label for="">Weight</label>
                                                        <input type="text" id="weight" value="{{ (!empty($order_data_by_id['weight'])) ? $order_data_by_id['weight'] : 0.5 }}" class="form-control" placeholder="Weight">
                                                    </div>
                                                </div>
                                            </p>
                                            <p>
                                                <button type="button" class="btn btn-primary generate-label btn-sm" data-id="{{$order_data_by_id['_order_id']}}">Generate Label </button>
                                            </p>
                                        @endif

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
                </div>
            </div>

            <div class="col-xs-12 col-md-12 ">
                <div class="card booking-info-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ordered_items-heading">
                                    <h5>{{ trans('admin.ordered_items') }}</h5>
                                    <div class="ordered-items-action">
                                        <a href="{{ route('admin.order_invoice', $order_data_by_id['_order_id']) }}" class="btn btn-sm btn-red text-left" target="_blank">
                                            <i class="fa fa-print"></i> Print Dispatch Label
                                        </a>
                                    </div>
                                </div>
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
                                            <tr class="">
                                                <td class="order_product">
                                                </td>
                                                <td>
                                                    <p>
                                                        <strong>Line Item Id:</strong>
                                                        {!! $order_data_by_id['item_number'] !!}
                                                    </p>
                                                    <p>
                                                        <strong>Variation Id:</strong>
                                                        {!! $order_data_by_id['legacyVariationId'] !!}
                                                    </p>                                    
                                                </td>
                                                <td class="order_description">
                                                    <h6>{!! $order_data_by_id['item_title'] !!}</h6>
                                                    <p>
                                                        <strong>SKU:</strong>
                                                        {!! $order_data_by_id['scan_i_package_id'] !!}
                                                    </p>
                                                </td>
                                                <td class="order_price">
                                                    <p>
                                                        {!! $order_data_by_id['item_price'] ?? 0 !!}
                                                    </p>
                                                </td>
                                                <td class="order_quantity">
                                                    <p>
                                                        {!! $order_data_by_id['item_quantity'] !!}
                                                    </p>
                                                </td>
                                                <td class="order_line_total">
                                                    <p>
                                                        {!! $order_data_by_id['item_price'] !!}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="order-total" colspan="6">
                                                    <p>
                                                        <strong>{{ trans('admin.tax') }}</strong>
                                                        <span>
                                                            {!! $order_data_by_id['seller_collected_tax'] !!}
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <strong>{{ trans('admin.shipping_cost') }}</strong>
                                                        <span>
                                                            {!! $order_data_by_id['shipping_and_handling'] !!}
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <strong>{{ trans('admin.coupon_discount_label') }}</strong>
                                                        <span>
                                                            {!! $order_data_by_id['discount'] ?? 0 !!}
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <strong>{{ trans('admin.order_total') }}</strong>
                                                        <span>
                                                            {!! $order_data_by_id['total_price'] !!}
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
    </div>
</div>

@endsection
