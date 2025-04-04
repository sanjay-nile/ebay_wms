<!doctype html>
<html>
<head>
    <title>{!! trans('admin.order_invoice_label') !!}</title>  
    <link rel="stylesheet" href="{{ URL::asset('public/bootstrap/css/bootstrap.min.css') }}" />
    <script type="text/javascript" src="{{ URL::asset('public/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/jquery/jquery-1.10.2.js') }}"></script>
    <style type="text/css">
        .QRcode .content h3 {font-weight: 600; font-size: 32px; color: #2c4964; }
        .QRcode .content ul {list-style: none; padding: 0; margin: 0px; }
        .QRcode .content ul li {padding-bottom: 10px; }
        .QRcode {padding: 15px 0; }
        .QRcode .content ul li:last-child{padding-bottom: 0px;}
        .QRcode-container{ width: 100%; margin: auto;}
    </style>
</head>
<body id="order_invoice" onload="window.print();">
    <section class="QRcode">
        <div class="container">
            <div class="QRcode-container">
                <table align="center" cellpadding="0" cellspacing="0" width="384px"  style="font-family: Helvetica , sans-serif;">
                    <tbody> 
                        <tr>
                            <td colspan="2" style="vertical-align:top">
                                <img src="{{ asset('public/images/mainlogo.png') }}" class="" alt="" width="70px">
                                @php
                                    $qrcode = $order_data_by_id['evtn_number'];

                                    /*if(isset($order_data_by_id['order_status'])){
                                        $qrcode .= $order_data_by_id['order_status'].'|';
                                    }

                                    if(isset($order_data_by_id['condition_code'])){
                                        $qrcode .= $order_data_by_id['condition_code'].'|';
                                    }

                                    if(isset($order_data_by_id['category_name'])){
                                        $qrcode .= $order_data_by_id['category_name'].'|';
                                    }

                                    if(isset($order_data_by_id['sub_category_name'])){
                                        $qrcode .= $order_data_by_id['sub_category_name'];
                                    }*/

                                    echo DNS2D::getBarcodeSVG($qrcode, 'QRCODE', 5,5);
                                @endphp
                                <p>{{ $history->user ?? ''}}</p> <p> {{ $history->status_date ?? ''}}</p>
                            </td>
                            <td style="width:100%" colspan="2"> 
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="vertical-align: top;"></td>
                                        <td style="vertical-align: top; padding:0 0px 0 20px;">
                                            <div class="content">
                                                <div style="margin:0; padding: 0 0 5px 0;">
                                                    <p style="font-size: 16px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"><b>SC Master Category:</b>@if(isset($order_data_by_id['category_name'])) {!! getCategoryName($order_data_by_id['category_name'], 'main') !!} @endif</p><br>

                                                    <p style="font-size: 16px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"><b>Category Tier 1:</b>@if(isset($order_data_by_id['sub_category_name'])) {!! getCategoryName($order_data_by_id['sub_category_name']) !!} @endif</p><br>
                                            
                                                    <p  style="font-size: 16px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"> <b>Category Tier 2:</b>@if(isset($order_data_by_id['sub_category_name_2'])) {!! getCategoryName($order_data_by_id['sub_category_name_2']) !!} @endif</p><br>

                                                    <p  style="font-size: 16px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin: 0 0 5px 0;"> <b>Category Tier 3:</b>@if(isset($order_data_by_id['sub_category_name_3'])) {!! getCategoryName($order_data_by_id['sub_category_name_3']) !!} @endif</p>
                                                </div>
                                                <h3 style="font-size:18px; color: #3d2a67; margin:0 0 5px 0; padding: 0;">Order Details</h3>
                                                <div style="margin:0; padding: 0 0 5px 0;">
                                                    {{-- <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> SKU:</b> {!! $order_data_by_id['sku'] ?? '' !!}</p>

                                                    <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> EVTN Number:</b> {!! $order_data_by_id['evtn_number'] ?? '' !!}</p>

                                                    <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Inspection Status:</b> {!! inception_status($order_data_by_id['order_status']) ?? '' !!}</p> --}}

                                                    <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Received Condition:</b> 
                                                        @if(isset($order_data_by_id['packages'][0]['received_condition']) && !empty($order_data_by_id['packages'][0]['received_condition']))
                                                            {{ $order_data_by_id['packages'][0]['received_condition'] ?? '' }}
                                                        @else
                                                            {{ $order_data_by_id['packages'][0]['condition'] ?? '' }}
                                                        @endif</p>

                                                    <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>SC Order ID: </b> {!! $order_data_by_id['reference_number'] ?? '' !!}</p>
                                                    {{-- <p style="font-size: 16px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>OSI: </b>@if(isset($order_data_by_id['serviceName'])) {!! str_replace('EXPORTS_', '', $order_data_by_id['serviceName']) !!} @endif</p> --}}
                                                </div>
                                                @php
                                                    //echo DNS1D::getBarcodeSVG($order_data_by_id['reference_number'], 'C128A');
                                                @endphp
                                                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order_data_by_id['reference_number'], 'C128B', 2, 63)}}" alt="barcode" />
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        {{-- <tr>
                            <td style="font-size:16px; font-weight: 600;color: #6B6F82; ">{!! $order_data_by_id['customer_name'] !!}</td>
                        </tr> --}}
                        <tr>
                            
                        </tr>
                        <!-- <tr>
                            <td colspan="3">
                                <p style="font-size: 18px;text-align:right; font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Condition:</b> {!! $order_data_by_id['condition_code'] ?? '' !!}</p>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html> 