@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('public/admin/js/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/js/select2.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('.assigncountry').select2({
          placeholder: 'Select Pallet Id',
          allowClear: true
        });
    });

    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        $("#select-all").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $("#dwn-btn").click(function () {
            $('#export_to').val('parcel-excel');
            $("#frm-sbmit").submit();
        });

        $("#dwn-itm-btn").click(function () {
            $('#export_to').val('item-excel');
            $("#frm-sbmit").submit();
        });

        $("#search-btn").click(function () {
            $('#export_to').val('');
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
                        <li class="breadcrumb-item active">eBay {{ ucfirst($status) }} Orders List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Fillters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="frm-sbmit">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="" class="form-control" placeholder="Eq8tor Order ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="ebay_id" value="" class="form-control" placeholder="Client Order ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="return_id" value="" class="form-control" placeholder="Client Return ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="user_name" value="" class="form-control" placeholder="User Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="user_id" value="" class="form-control" placeholder="User Id">
                                        </div>                               
                                        <div class="form-group col-md-3">
                                            <input type="text" name="user_mail" value="" class="form-control" placeholder="User Mail">
                                        </div>
                                        @if($status != 'new')
                                            <div class="form-group col-md-3">
                                                <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <select class="form-control select2" name="order_status">
                                                    <option value="">-- Select --</option>
                                                    <option value="Completed" @if($or_status == 'Completed') selected @endif>Completed</option>
                                                    <option value="Cancelled" @if($or_status == 'Cancelled') selected @endif>Cancelled</option>
                                                    <option value="Returned" @if($or_status == 'Returned') selected @endif>Returned</option>
                                                    <option value="P_Returned" @if($or_status == 'P_Returned') selected @endif>P_Returned</option>
                                                </select>
                                            </div>
                                        @endif
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <input type="hidden" name="export_to" id="export_to" value="">
                                        <div class="form-group col-md-1">
                                            <button type="submit" class="btn btn-red" id="search-btn">Search</button>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <a href="{{ route('admin.ebay.order.list', $status) }}" class="btn btn-blue">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
            </div>

            <div class="col-12">
                <div class="card card-info">
                    <form action="" method="post">
                        <div class="card-header">
                            <h5 class="card-title">
                                @if($status == 'view')
                                    Processed Orders
                                @else
                                    {{ ucfirst($status) }} Order List
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="text-left mb-2">
                                    <button class="btn btn-primary btn-sm" id="dwn-btn" type="button">
                                        <i class="fa fa-download"></i> Excel By Order
                                    </button>

                                    <button class="btn btn-primary btn-sm" id="dwn-itm-btn" type="button">
                                        <i class="fa fa-download"></i> Excel By SKU
                                    </button>
                                </div>
                                <div class="tab-pane fade show active" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
                                    <div class="alert alert-primary">
                                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                    </div>

                                    <div class="table-responsive booking-info-box mt-2" style="padding: 0;">
                                        <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="ws">Action</th>
                                                    <th class="ws">Date</th>
                                                    <th class="ws">#Id</th>
                                                    <th class="ws">Client Order Id</th>
                                                    @if($status != 'new')
                                                        <th class="ws">Return Id</th>
                                                    @endif
                                                    <th class="ws">Name</th>
                                                    <th class="ws">Client User ID</th>
                                                    <th class="ws">Email id</th>
                                                    <th class="ws">Address</th>
                                                    <th class="ws">Amount</th>                  
                                                    <th class="ws">Payment Mode</th>
                                                    @if($status != 'new')
                                                        <th class="ws">Tracking Id</th>
                                                        <th class="ws">Invoice No.</th>
                                                        <th class="ws">Refund Date</th>                   
                                                    @endif
                                                    <th class="ws">Order Status</th>                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($orders)>0)
                                                    @foreach($orders as $row)
                                                        <?php
                                                            $track_id = $invoice = '';
                                                            if (isset($row->_sales_order_status) && !empty($row->_sales_order_status)) {
                                                                $sales_id = json_decode($row->_sales_order_status);
                                                                if (isset($sales_id->salesInvoiceNumber)) {
                                                                    $invoice = $sales_id->salesInvoiceNumber;
                                                                }
                                                            }
                                                        ?>
                                                        <tr>
                                                            @if($status == 'new')
                                                                <td class="ws">
                                                                    <a href="{{ route('admin.new_ebay_order_details', $row->id) }}" class="btn btn-edit" target="_blank">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.cancelled-ebay-orders', $row->id) }}" class="btn btn-view" onclick="return confirm('Are you sure you want to cancle this?')"><i class="fa fa-close"></i></a>
                                                                </td>
                                                            @else
                                                                <td class="ws">
                                                                    <a href="{{ route('admin.view_ebay_order_details', $row->id) }}" class="btn btn-edit"><i class="fa fa-eye"></i></a>
                                                                    <a href="{{ route('admin.order_invoice', $row->id) }}" class="btn btn-view" target="_blank"><i class="fa fa-print"></i></a>
                                                                </td>
                                                            @endif
                                                            <td class="ws">{!! date('d-M-Y', strtotime($row->sale_date)) !!}</td>
                                                            <td class="ws">{!! $row->id !!}</td>
                                                            <td class="ws">{!! $row->order_number !!}</td>
                                                            @if($status != 'new')
                                                                <td class="ws">{!! $row->_ebay_return_order_id ?? 'N/A' !!}</td>
                                                            @endif
                                                            <td class="ws">{!! $row->ship_to_name !!}</td>
                                                            <td class="ws">{!! $row->buyer_username !!}</td>
                                                            <td class="ws">{!! $row->ship_to_email !!}</td>
                                                            <td class="ws">{!! $row->ship_to_address_1 !!} {!! $row->ship_to_address_2 !!} {!! $row->ship_to_city !!} {!! $row->ship_to_state !!} {!! $row->ship_to_country !!}</td>
                                                            <td class="ws">{{ $row->_ebay_order_currency }} {!! $row->sold_for !!}</td>
                                                            <td class="ws"> {!! $row->payment_method !!}</td>

                                                            @if($status != 'new')
                                                                <td class="ws">
                                                                    @php
                                                                        if(isset($row->_order_tracking_id)){
                                                                            $track = json_decode($row->_order_tracking_id);
                                                                            foreach($track as $t){
                                                                                $track_id = $t->carrierWaybillNumber;
                                                                            }
                                                                        }

                                                                        if (empty($track_id)) {
                                                                            $tracking_detail = $row->_generate_waybill_status ?? NULL; 
                                                                            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                                                            if($tracking_data){
                                                                                if (!empty($tracking_data->carrierWaybill)) {
                                                                                    $track_id = $tracking_data->carrierWaybill;
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    {{ $track_id }}
                                                                </td>
                                                                <td class="ws">{{ $invoice }}</td>
                                                                <td class="ws">{!! $row->_ebay_order_refund_date ?? 'N/A' !!}</td>
                                                            @endif

                                                            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value($row->_ebay_order_status) }}"> {{ $row->_ebay_order_status }} </span></td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="12">
                                                            <i class="fa fa-exclamation-triangle"></i> There are no {!! $status !!} orders
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="products-pagination">{!! $orders->appends(Request::capture()->except('page'))->render() !!}</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection