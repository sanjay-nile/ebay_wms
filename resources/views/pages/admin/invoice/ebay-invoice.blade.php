<!doctype html>
<html>
<head>
    <title>{!! trans('admin.order_invoice_label') !!}</title>  
    <link rel="stylesheet" href="{{ URL::asset('public/css/bootstrap/css/bootstrap.min.css') }}" />
    <script type="text/javascript" src="{{ URL::asset('public/css/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('public/jquery/jquery-1.10.2.js') }}"></script>
    <style>
        .invoice-title h4{
            display: inline-block;
        }

        .table > tbody > tr > .no-line {
            border-top: none;
        }

        .table > thead > tr > .no-line {
            border-bottom: none;
        }

        .table > tbody > tr > .thick-line {
            border-top: 4px solid #e1e1e1;
        }

        #order_invoice hr{ border-bottom:4px solid #e1e1e1;}
        .order_product img {
            width: 80px;
        }
        .invoice-title{
          margin-top: 10px;
        }
        .invoice-title img{
            width: 150px;            
            margin-bottom: 5px;
        }
        /*.botom-m{
            margin-top: 10%;
        }*/
        #scissors {
            height: auto;
            width: 100%;
            margin:12% auto 25px;
            position: relative;
            background-size: 25px;
            border-top: 2px dashed #bda2a2;
        }
       #scissors div {
            position: absolute;
            top: -12px;
            height: 22px;
            width: 100%;
            margin: 0;
            background-image: url(http://i.stack.imgur.com/cXciH.png);
            background-repeat: no-repeat;
            background-position: right;
            background-size: 25px;
            z-index: 99;
        }
        .b-img img{
            width: 200px;
            height: 35px;
        }
    </style>
</head>
<body id="order_invoice" onload="window.print();">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="invoice-title">
                    <h4>             
                        <img class="img-responsive" src="{{ asset('public/images/Moschino_logo_black.jpg') }}"><br>
                        {!! trans('admin.invoice_label') !!}
                    </h4>
                    <h4 class="float-right">{!! trans('admin.order') !!} # {!! $order_data_by_id['_order_id'] !!}</h4>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <?php  
                            $sales_id = 'None';
                            if (isset($order_data_by_id['_sales_order_status']) && !empty($order_data_by_id['_sales_order_status'])) {
                                $invoice = json_decode($order_data_by_id['_sales_order_status']);
                                if (isset($invoice->salesInvoiceNumber)) {
                                    $sales_id = $invoice->salesInvoiceNumber;
                                }
                            }
                        ?>
                        <strong>{!! trans('admin.order_details') !!}:</strong><br>
                        <p><strong>{{ trans('admin.order') }} #:</strong> {!! $order_data_by_id['_order_id'] !!}</p>
                        <p><strong>{{ trans('admin.order_date') }}:</strong> {!! $order_data_by_id['_order_date'] !!}</p>
                        <p><strong>Ebay Order Id:</strong> {!! $order_data_by_id['order_number'] !!}</p>
                        {{-- <p><strong>Creation Date:</strong> {!! $order_data_by_id['sale_date'] !!}</p> --}}
                        {{-- <p><strong>Shipping Services:</strong> {!! $order_data_by_id['shipping_service'] !!}</p> --}}
                        <p><strong>Sales Invoice Number:</strong> {!! $sales_id !!}</p>
                        <p><strong>{!! trans('admin.payment_method_label') !!}:</strong> {!! $order_data_by_id['payment_method'] !!}</p>
                        <p><strong>Currency:</strong> {!! $order_data_by_id['_ebay_order_currency'] !!}</p>
                        {{-- <p><strong>Seller Id:</strong> @if($order_data_by_id['_ebay_order_seller_id']) {!! $order_data_by_id['_ebay_order_seller_id'] !!} @endif</p> --}}
                    </div>
                    <div class="col text-right">
                        <address>
                            <strong>{!! trans('admin.shipped_to_label') !!}:</strong><br>
                            {!! $order_data_by_id['ship_to_name'] !!}<br>
                            {!! $order_data_by_id['ship_to_address_1'] !!}<br>

                            @if($order_data_by_id['ship_to_address_2'] != 'None')
                            {!! $order_data_by_id['ship_to_address_2'] !!}<br>
                            @endif

                            {!! $order_data_by_id['ship_to_city'] !!}, {!! $order_data_by_id['ship_to_zip'] !!}<br>
                            {!! $order_data_by_id['ship_to_phone'] !!}<br>
                            {!! get_country_by_code( $order_data_by_id['ship_to_country'] ) !!}<br>
                            {!! $order_data_by_id['ship_to_email'] !!}
                        </address>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col">
                        <address>
                            <strong>{!! trans('admin.payment_method_label') !!}:</strong><br>
                            {!! $order_data_by_id['payment_method'] !!}
                        </address>
                    </div>
                    <div class="col text-right">
                        <address>
                            <strong>{!! trans('admin.order_date') !!}:</strong><br>
                            {!! $order_data_by_id['_order_date'] !!}<br><br>
                        </address>
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><strong>{!! trans('admin.order_summary_label') !!}</strong></h5><hr>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <td class="text-center"><strong>{{ trans('admin.images') }}</strong></td>
                                        <td class="text-center"><strong>{!! trans('admin.item') !!}</strong></td>
                                        <td class="text-center"><strong>{!! trans('admin.price') !!}</strong></td>
                                        <td class="text-center"><strong>{!! trans('admin.quantity') !!}</strong></td>
                                        <td class="text-right"><strong>{!! trans('admin.totals') !!}</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = 0;?>  
                                    @if(count(json_decode($order_data_by_id['items'])) > 0)  
                                        @foreach(json_decode($order_data_by_id['items']) as $items)
                                            <tr>
                                                <td class="order_product text-center">
                                                    
                                                </td>
                                                <td class="text-center">
                                                    <p class="invoice-title">{!! $items->item_title !!}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="invoice-title">{!! $items->price !!} {!! $order_data_by_id['_ebay_order_currency'] !!}</p>
                                                </td>
                                                <td class="text-center"><p class="invoice-title">{!! $items->quantity !!}</p></td>
                                                <td class="text-right">
                                                    <p class="invoice-title">{!! $items->price !!} {!! $order_data_by_id['_ebay_order_currency'] !!}</p>
                                                </td>
                                            </tr>
                                            <?php $subtotal += $items->price;?>
                                        @endforeach
                                    @endif

                                    <tr>
                                        <td class="thick-line"></td>
                                        <td class="thick-line"></td>
                                        <td class="thick-line"></td>
                                        <td class="thick-line text-center"><strong>{!! trans('admin.subtotal_label') !!}</strong></td>
                                        <td class="thick-line text-right">{!! $subtotal !!} {!! $order_data_by_id['_ebay_order_currency']  !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>{!! trans('admin.tax') !!}</strong></td>
                                        <td class="no-line text-right">{!! $order_data_by_id['seller_collected_tax'] !!} {!! $order_data_by_id['_ebay_order_currency']  !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>{!! trans('admin.shipping_cost') !!}</strong></td>
                                        <td class="no-line text-right">{!! $order_data_by_id['shipping_and_handling'] !!} {!! $order_data_by_id['_ebay_order_currency']  !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>{!! trans('admin.coupon_discount_label') !!}</strong></td>
                                        <td class="no-line text-right">{!! $order_data_by_id['discount'] ?? 0 !!} {!! $order_data_by_id['_ebay_order_currency']  !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center"><strong>{!! trans('admin.order_total') !!}</strong></td>
                                        <td class="no-line text-right">{!! $order_data_by_id['sold_for'] !!} {!! $order_data_by_id['_ebay_order_currency']  !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- <div style="text-align: center;padding-bottom: 50px;margin-top:30px;">
                <div class="site-logo"><img style="margin:0px auto;" class="img-responsive" src="{{ get_site_logo_image() }}"></div>
                </div> --}}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>If there is an issue with the product pls contact or reply on eBay site to Return and request an RMA. We will send you shipping label.Once we receive the item/s back in original state and package and all is accounted for we will issue you refund.In the event there are discrepancies we will contact you via eBay</p>
                        <p>Thank you</p>
                        <p>ReturnsGear.Net</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="scissors">
            <div></div>
        </div>

        <div class="row botom-m">
            <div class="col-md-12">
                <div class="panel panel-default">                    
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <td class="text-center"><strong>Sr No.</strong></td>
                                        <td class="text-center"><strong>Order No.</strong></td>
                                        <td class="text-center"><strong>Sales Invoice No.</strong></td>
                                        <td class="text-center"><strong>Sku</strong></td>
                                        <td class="text-center"><strong>Bar Code</strong></td>
                                        <td class="text-center"><strong>Box</strong></td>
                                        <td class="text-right"><strong>Source</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;?>  
                                    @if(count(json_decode($order_data_by_id['items'])) > 0)  
                                        @foreach(json_decode($order_data_by_id['items']) as $items)
                                            <tr>
                                                <td class="text-center">{!! $i !!}</td>
                                                <td class="text-center">{!! $order_data_by_id['_order_id'] !!}</td>
                                                <td class="text-center">{!! $sales_id !!}</td>
                                                <td class="text-center">{!! $items->sku !!}</td>
                                                <td class="text-center b-img">
                                                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($items->sku, 'C128A')}}" alt="barcode" /><br>
                                                        {{ $items->sku }}
                                                </td>
                                                <td class="text-center">
                                                    
                                                </td>
                                                <td class="text-right">
                                                    
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row botom-m mt-5">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                Order Picked ...............
                            </div>
                            <div class="col-md-3 mb-3">
                                By Name .................
                            </div>
                            <div class="col-md-3 mb-3">
                                Date ...................
                            </div>
                            <div class="col-md-3 mb-3">
                                Time .................
                            </div>
                            <div class="col-md-3 mb-3">
                                Checked By ...............
                            </div>
                            <div class="col-md-3 mb-3">
                                Ready to Ship Date ..............
                            </div>
                            <div class="col-md-3 mb-3">
                                Discrepancies ..................
                            </div>
                            <div class="col-md-3 mb-3">
                                Office Admin Check ...............
                            </div>
                            <div class="col-md-3 mb-3">
                                Sorted ....................
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>