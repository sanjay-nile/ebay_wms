<!doctype html>
<html>
<head>
    <title>Rack Label</title>  
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
                <table align="center" cellpadding="0" cellspacing="0" width="384px"  style="font-family: Helvetica , sans-serif;">
                    <tbody>
                        @forelse($post as $order_data_by_id)
                            <tr style="height:288px;">
                                <td colspan="2" style="vertical-align:middle;">
                                    <p style="text-align:center;">
                                        @php
                                            echo DNS2D::getBarcodeSVG($order_data_by_id['location_id'], 'QRCODE', 6, 6);
                                        @endphp
                                    </p>
                                </td>
                                <td style="width:100%" colspan="2"> 
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="vertical-align: top;"></td>
                                            <td style="vertical-align: top; padding:0 0px 0 20px;">
                                                <div class="content">
                                                    <p style="font-size: 40px; margin:0;font-weight: bold;text-align:center;line-height: normal;color: #000;">{{ $order_data_by_id['location_id'] }}</p>
                                                    <p style="text-align:center;">
                                                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order_data_by_id['location_id'], 'C128B', 2, 63)}}" alt="barcode" />
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html> 