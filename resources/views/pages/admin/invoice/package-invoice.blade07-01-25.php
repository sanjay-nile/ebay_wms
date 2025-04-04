<!doctype html>
<html>
<head>
    <title>{!! trans('admin.order_invoice_label') !!}</title>  
    <link rel="stylesheet" href="{{ URL::asset('public/css/bootstrap/css/bootstrap.min.css') }}" />
    <script type="text/javascript" src="{{ URL::asset('public/css/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/jquery/jquery-1.10.2.js') }}"></script>
    <style type="text/css">
        .QRcode .content h3 {font-weight: 600; font-size: 32px; color: #2c4964; }
        .QRcode .content ul {list-style: none; padding: 0; margin: 0px; }
        .QRcode .content ul li {padding-bottom: 5px; font-size: 14px; font-weight: bold; color: #263238; }
        .QRcode .content ul li span {font-size: 15px; font-weight: 400; color: #6B738D; }
        .QRcode .content ul li:last-child{padding-bottom: 0px;}
        .QRcode-container{border: 1px solid #ccc; border-radius: 30px; padding: 20px; width: 70%; margin: auto;}

        .invoice-barcode-info img {width: 100%; }
        .invoice-QRcode-info{text-align: center;}
        .invoice-QRcode-info img {width: 100%; }
        .invoice-barcode-info {text-align: center; margin-top: 20px; border-top: 1px solid #d7d7d7; padding-top: 10px; }
        .QRcode-header {padding:0 0 10px 0; border-bottom: 1px solid #d7d7d7; margin-bottom: 1rem; }
        .invoice-content-info.content h3 {font-size: 24px; font-weight: 600; padding: 0;margin-bottom: 1rem; color: #263238; }
        .invoice-customer-name {font-size: 14px; font-weight: bold; color: #263238; }

    </style>
</head>
<body id="order_invoice" onload="window.print();">
    <section class="QRcode">
        <div class="container">
            <div class="QRcode-container">
                <div class="QRcode-header">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="invoice-logo">
                                <img src="{{ asset('public/images/mainlogo.png') }}" class="" alt="" height="70px">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 d-flex align-items-center justify-content-end">
                            <div class="invoice-customer-name">{!! $order_data_by_id['scan_i_package_id'] !!}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <div class="invoice-QRcode-info">
                            @php
                                $qrcode = (!empty($order_data_by_id['scan_i_package_id'])) ?  $order_data_by_id['scan_i_package_id'] : $order_data_by_id['_order_id'];
                                // echo DNS2D::getBarcodeSVG($qrcode, 'QRCODE', 5,5);
                                echo '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG($qrcode, 'QRCODE', 5,5) . '" alt="barcode"   />';
                            @endphp
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7">
                        <div class="invoice-content-info content">
                            <h3>Customer Details</h3>
                            <ul>
                                <li> Name: <span>{!! $order_data_by_id['ship_to_name'] ?? '' !!}</span></li>
                                <li> Phone: <span>{!! $order_data_by_id['ship_to_phone'] ?? '' !!}</span></li>
                                <li> Address 1: <span> {!! $order_data_by_id['ship_to_address_1'] ?? '' !!}</span></li>
                                <li> Address 2: <span>{!! $order_data_by_id['ship_to_address_2'] ?? '' !!}</span></li>
                                <li> City: <span> {!! $order_data_by_id['ship_to_city'] ?? '' !!}</span></li>
                                <li> State: <span> {!! $order_data_by_id['ship_to_state'] ?? '' !!}</span></li>
                                <li> Zip Code: <span> {!! $order_data_by_id['ship_to_zip'] ?? '' !!}</span></li>
                                <li> Country: <span> {!! $order_data_by_id['ship_to_country'] ?? '' !!}</span></li>
                                <li> eBay Order Number: <span> {!! $order_data_by_id['order_number'] ?? '' !!}</span></li>
                                <li> Qty: <span> {!! $order_data_by_id['item_quantity'] ?? '' !!}</span></li>
                                <li> Description: <span> {!! $order_data_by_id['item_title'] ?? '' !!}</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="invoice-barcode-info">
                            @php
                                $barcode = (!empty($order_data_by_id['reference_number'])) ?  $order_data_by_id['reference_number'] : $order_data_by_id['_order_id'];
                                // echo DNS1D::getBarcodeSVG($barcode, 'C128' );
                                //echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($barcode, 'C128') . '" alt="barcode"   />';
                                //echo $barcode;
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>