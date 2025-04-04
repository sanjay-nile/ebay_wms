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
        .QRcode {padding: 120px 0; }
        .QRcode .content ul li:last-child{padding-bottom: 0px;}
        .QRcode-container{border: 1px solid #ccc; border-radius: 30px; padding: 20px; width: 90%; margin: auto;}
    </style>
</head>
<body id="order_invoice" onload="window.print();">
    <section class="QRcode">
        <div class="container">
            <div class="QRcode-container">
                <table align="center" cellpadding="0" cellspacing="0" width="100%"  style="font-family: Helvetica , sans-serif;">
                    <tbody>
                        <tr>
                            <td style="padding:5px 0;width: 25%"><img src="{{ asset('public/images/mainlogo.png') }}" class="" alt="" width="92px"></td>
                            <td>
                                <ul style="margin:0; padding: 0;list-style: none;">
                                    <li style="font-size: 18px;display:inline-block;margin-right:20px;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin-bottom: 0;"><b>Category:</b> @if(isset($order_data_by_id['category_name'])) {!! getCategoryName($order_data_by_id['category_name']) !!} @endif</li>

                                    <li style="font-size: 18px;display:inline-block;margin-right:20px;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin-bottom: 0;"><b>Category Code:</b> @if(isset($order_data_by_id['category_name'])) {!! $order_data_by_id['category_name'] !!} @endif</li>
                            
                                    <li  style="font-size: 18px;display:inline-block;margin-right:20px;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin-bottom: 0;"> <b>Sub Category:</b> @if(isset($order_data_by_id['sub_category_name'])) {!! getCategoryName($order_data_by_id['sub_category_name']) !!} @endif</li>

                                    <li  style="font-size: 18px;display:inline-block;margin-right:20px;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin-bottom: 0;"> <b>Sub Category Code:</b> @if(isset($order_data_by_id['sub_category_name'])) {!! $order_data_by_id['sub_category_name'] !!} @endif</li>
                                </ul>
                                
                            </td>
                        </tr>
                        {{-- <tr>
                            <td style="font-size:14px; font-weight: 600;color: #6B6F82; ">{!! $order_data_by_id['customer_name'] !!}</td>
                        </tr> --}}
                        <tr>
                            <td style="width:100%" colspan="2"> 
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:20%; vertical-align: top;"> 
                                            @php
                                                $qrcode = $order_data_by_id['evtn_number'].'|';

                                                if(isset($order_data_by_id['order_status'])){
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
                                                }

                                                echo DNS2D::getBarcodeSVG($qrcode, 'QRCODE', 7,7);
                                            @endphp

                                            {{-- {!! $qrcode !!} --}}
                                        </td>
                                        <td style="width:65% ;  vertical-align: top;">
                                            <div class="content">
                                                <h3 style="font-size:22px; color: #3d2a67;">Item Details</h3>
                                                <ul>
                                                    <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> SKU:</b> {!! $order_data_by_id['sku'] ?? '' !!}</li>
                                                    <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> EVTN Number:</b> {!! $order_data_by_id['evtn_number'] ?? '' !!}</li>
                                                    {{-- <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Order Number:</b> {!! $order_data_by_id['order_number'] ?? '' !!}</li> --}}
                                                    {{-- <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Customer name:</b> {!! $order_data_by_id['customer_name'] ?? '' !!}</li> --}}
                                                    {{-- <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Tracking No:</b> {!! $order_data_by_id['tracking_number'] ?? '' !!}</li> --}}
                                                    <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> Ref no:</b> {!! $order_data_by_id['_order_id'] ?? '' !!}</li>
                                                    <!-- <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Category:</b> @if(isset($order_data_by_id['category_name'])) {!! getCategoryName($order_data_by_id['category_name']) !!} @endif</li>
                                                    <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Sub Category:</b> @if(isset($order_data_by_id['sub_category_name'])) {!! getCategoryName($order_data_by_id['sub_category_name']) !!} @endif</li> -->
                                                    <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Inspection Status:</b> {!! inception_status($order_data_by_id['order_status']) ?? '' !!}</li>
                                                    <!-- <li style="font-size: 14px;font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Condition:</b> {!! $order_data_by_id['condition_code'] ?? '' !!}</li> -->
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <p style="font-size: 18px;text-align:right; font-weight: normal;margin:0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Condition:</b> {!! $order_data_by_id['condition_code'] ?? '' !!}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html> 