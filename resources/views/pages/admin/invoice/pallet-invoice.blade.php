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
        .QRcode-container{ width: 100%; margin: auto;}
    </style>
</head>
<body id="order_invoice" onload="window.print();">
    <section class="QRcode">
        <div class="container">
            <div class="QRcode-container">
                <table align="center" cellpadding="0" cellspacing="0" width="484px"  style="font-family: Helvetica , sans-serif;">
                    <tbody> 
                        <tr>
                            <td colspan="2" style="vertical-align:top">
                                <img src="{{ asset('public/images/mainlogo.png') }}" class="" alt="" width="70px">
                                @php
                                    $qrcode = $pallet->pallet_id;
                                    echo DNS2D::getBarcodeSVG($qrcode, 'QRCODE', 5,5);
                                @endphp
                            </td>
                            <td style="width:100%" colspan="2"> 
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="vertical-align: top;"></td>
                                        <td style="vertical-align: top; padding:0 0px 0 20px;">
                                            <div class="content">
                                                <h3 style="font-size:18px; color: #3d2a67; margin:0 0 5px 0; padding: 0;">
                                                    @if($pallet->pallet_type == 'InProcess')
                                                        InProcess Pallet Details
                                                    @elseif($pallet->pallet_type == 'Closed')
                                                        Closed Pallet Details
                                                    @else
                                                        Shipped Pallet Details
                                                    @endif
                                                </h3>

                                                <div style="margin:0; padding: 0 0 5px 0;">
                                                    <p style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"><b>SC Master Category:</b> {!! getCategoryName($pallet->meta->main_category ?? '', 'main') !!}</p><br>

                                                    <p style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"><b>Category Tier 1:</b> {!! getCategoryName($pallet->meta->category_tier_1 ?? '') !!}</p><br>
                                            
                                                    <p  style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin:  0 0 5px 0;"> <b>Category Tier 2:</b> {!! getCategoryName($pallet->meta->category_tier_2 ?? '') !!}</p><br>

                                                    <p  style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin: 0 0 5px 0;"> <b>Category Tier 3:</b> {!! getCategoryName($pallet->meta->category_tier_3 ?? '') !!}</p>

                                                    @if($pallet->pallet_type == 'Closed')
                                                    <p  style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin: 0 0 5px 0;"> <b>Reselling Grade:</b> {!! $pallet->reselling_grade ?? '' !!}</p>

                                                    <p  style="font-size: 14px;display:inline-block;font-weight: normal;text-align:left;line-height: normal;color: #6B6F82;margin: 0 0 5px 0;"> <b>RRP:</b> {!! getPackageValue($pallet->pallet_id) ?? 0 !!}</p>
                                                    @endif
                                                </div>
                                                <div style="margin:0; padding: 0 0 5px 0;">
                                                    <p style="font-size: 14px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b>Received Condition:</b> {!! $pallet->return_type ?? '' !!}</p>

                                                    <p style="font-size: 14px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i><b> From Warehouse Name:</b> @php $fr = $pallet->meta->fr_warehouse_id ?? '' @endphp {{getWareHouseName($fr)}}</p>

                                                    <p style="font-size: 14px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>To Warehouse Nam:</b> {!! getWareHouseName($pallet->warehouse_id) !!}</p>

                                                    <p style="font-size: 14px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>Pallet Id:</b> {!! $pallet->pallet_id ?? '' !!}</p>

                                                    <p style="font-size: 14px;font-weight: normal;margin:0 0 5px 0;line-height: normal;color: #6B6F82;"><i class="icofont-check-circled"></i> <b>OSI:</b> {!! $pallet->sales_incoterm ?? '' !!}</p>
                                                </div>
                                                @php
                                                    // echo DNS1D::getBarcodeSVG($pallet->pallet_id, 'C128A');
                                                    // echo $qrcode = str_replace('-', '', $qrcode);
                                                @endphp
                                                {{-- {!! $barcodeHtml !!} --}}
                                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($qrcode, 'C39', 1, 43) }}" alt="barcode" />
                                                {{-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($qrcode, 'C128A', 2, 30)}}" alt="barcode" /> --}}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        {{-- <tr>
                            <td style="font-size:14px; font-weight: 600;color: #6B6F82; ">{!! $order_data_by_id['customer_name'] !!}</td>
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