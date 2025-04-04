@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
        $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    })
</script>


@endpush

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    {{-- <h3 class="content-header-title mb-0 d-inline-block">Waybill</h3> --}}
                    <div class="row breadcrumbs-top d-inline-block">
                      <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                            <li class="breadcrumb-item active">Happy Return Orders List</li>
                        </ol>
                      </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12 ">

                @include('includes/admin/notify')

                <div class="card">
                    <div class="card-header avn-card-header">
                        <form class="form-horizontal fiter-form ml-1">
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <div class="input-group">
                                        <input type="text" name="customername" class="form-control" placeholder="Customer Name" value="{{ app('request')->input('customername') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="emailid" class="form-control" placeholder="Email ID" value="{{ app('request')->input('emailid') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="sku" class="form-control" placeholder="#Sku" value="{{ app('request')->input('sku') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="trackingid" class="form-control" placeholder="Tracking Id" value="{{ app('request')->input('trackingid') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="orderno" class="form-control" placeholder="Order No." value="{{ app('request')->input('orderno') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="barcode" class="form-control" placeholder="Box BarCode" value="{{ app('request')->input('barcode') }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <div class="input-group">
                                        <input type="text" name="start" class="form-control" value="{{ app('request')->input('start') }}" autocomplete="off" placeholder="Select From Date">
                                    </div>
                                </div>

                                <div class="col-md-4 mt-2">
                                    <div class="form-group">
                                        <input type="text" name="end" class="form-control" value="{{ app('request')->input('end') }}" autocomplete="off" placeholder="Select To Date" />
                                    </div>
                                </div>
                                
                                <div class="col-md-2 mt-2">
                                    <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
                                    <a href="{{ route('admin.webhook.orders') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>
                                    <a href="{{ route('admin.webhook.syncwebhook') }}" class="syncbutton btn existing-pallet">Sync</a>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-content collapse show">
                        <!-- <a href="#" class="list-right-btn">Redirect to Eq8tor</a> -->
                        <div class="sync-button-div ml-2">

                        </div>
                        <div class="card-body booking-info-box card-dashboard table-responsive">
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Order Number</th>
                                        <th>Order Id</th>
                                        <th>Purchased Date</th>
                                        <th>Sku #</th>
                                        <th> HS Code</th>
                                        <th>Label Barcode</th>
                                        <th>Product Name</th>
                                        <th>Return Reason</th>
                                        <th>Refund Type</th>
                                        <th>Country</th>
                                        <th>poNumber</th>
                                        <th>Shipping BoxBarcode</th>
                                        <th>Tracking</th>
                                        <th>Carrier</th>
                                        
                                        <th>Email Id</th>
                                        <th>Outbound Tracking No</th>
                                        <th>Origin Country</th>
                                        <th>Goods Description</th>
                                        <!-- <th>Commodity Code</th> -->
                                        <th>Item Net Mass</th>
                                        <th>Quantity</th>
                                        <th>Currency</th>
                                        <th>Selling Price</th>
                                        <th>Consignor Name</th>
                                        <th>Consignor Street</th>
                                        <th>Consignor City</th>
                                        <th>Consignor Postcode</th>
                                        <th>Consignor Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @forelse($lists as $row)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ str_replace('#', '', $row->orderNumber) ?? 'N/A' }}</td>
                                            <!-- <td>{{ $row->orderNumber ?? 'N/A' }}</td> -->
                                            <td>{{ $row->orderID ?? 'N/A' }}</td>
                                            <td>{{ $row->purchasedDate ?? 'N/A' }}</td>
                                            <td>{{ $row->sku ?? 'N/A' }}</td>
                                            <td>{{ $row->HSCode ?? 'N/A' }}</td>
                                            <td>{{ $row->labelBarcode ?? 'N/A' }}</td>
                                            <td>{{ $row->productName ?? 'N/A' }}</td>
                                            <td>{{ $row->returnReason ?? 'N/A' }}</td>
                                            <td>{{ $row->refundType ?? 'N/A' }}</td>
                                            <td>{{ $row->country ?? 'N/A' }}</td>
                                            <td>{{ $row->poNumber ?? 'N/A' }}</td>
                                            <td>{{ $row->shippingBoxBarcode ?? 'N/A' }}</td>
                                            <td>{{ $row->tracking ?? 'N/A' }}</td>
                                            <td>{{ $row->carrier ?? 'N/A' }}</td>
                                            <td>{{ $row->Email ?? 'N/A' }}</td>
                                            <td>{{ $row->fullfillmenttrackingnumber ?? 'N/A' }}</td>
                                            <td>{{ $row->OriginCountry ?? 'N/A' }}</td>
                                            <td>{{ $row->GoodsDescription ?? 'N/A' }}</td>
                                            <!-- <td>{{ $row->CommodityCode ?? 'N/A' }}</td> -->
                                            <td>{{ $row->ItemNetMass ?? 'N/A' }}</td>
                                            <td>{{ $row->Quantity ?? 'N/A' }}</td>
                                            <td>{{ $row->Currency ?? 'N/A' }}</td>
                                            <td>{{ $row->SellingPrice ?? 'N/A' }}</td>
                                            <td>{{ $row->ConsignorName ?? 'N/A' }}</td>
                                            <td>{{ $row->ConsignorStreet ?? 'N/A' }}</td>
                                            <td>{{ $row->ConsignorCity ?? 'N/A' }}</td>
                                            <td>{{ $row->ConsignorPostcode ?? 'N/A' }}</td>
                                            <td>{{ $row->ConsignorCountry ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection
