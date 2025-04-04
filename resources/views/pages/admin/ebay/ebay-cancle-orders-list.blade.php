@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">eBay Cancel Orders List</li>
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
                                <form action="" method="get" class="form-horizontal" autocomplete="off">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_id" value="" class="form-control" placeholder="Eq8tor Order ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="ebay_id" value="" class="form-control" placeholder="EBay Order ID">
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
                                        <div class="form-group col-md-1">
                                            <button type="submit" class="btn btn-red">Search</button>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <a href="{{ route('admin.ebay.cancle.orders', $status) }}" class="btn btn-blue">Reset</a>
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
                            <h5 class="card-title">Cancelled {!! trans('admin.order_list_label') !!} (These orders are not shipped)</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-primary">
                                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                            </div>          
                            <div class="table-responsive booking-info-box mt-2" style="padding: 0;">
                                <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
                                    <thead>
                                        <tr>
                                            @if($status == 'new')
                                                <th class="ws"><input name="select_all" value="1" id="select-all" type="checkbox" /></th>
                                            @endif
                                            <th class="ws">Date</th>
                                            <th class="ws">#Id</th>
                                            <th class="ws">Client Order Id</th>
                                            <th class="ws">Name</th>
                                            <th class="ws">Email id</th>
                                            <th class="ws">Address</th>
                                            <th class="ws">Amount</th>                  
                                            <th class="ws">Payment Mode</th>
                                            @if($status != 'new')
                                                <th class="ws">Tracking Id</th>
                                                <th class="ws">Invoice No.</th>
                                            @else
                                                <th class="ws">Sync Status</th>                    
                                            @endif
                                            <th class="ws">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($orders)>0)
                                            @foreach($orders as $row)
                                                <?php
                                                    $track_id = $invoice = 'None';
                                                    if(isset($row->_order_tracking_id)){
                                                        $track = json_decode($row->_order_tracking_id);
                                                        foreach($track as $t){
                                                            $track_id = $t->carrierWaybillNumber;
                                                        }
                                                    }

                                                    if (isset($row->_sales_order_status) && !empty($row->_sales_order_status)) {
                                                        $sales_id = json_decode($row->_sales_order_status);
                                                        if (isset($sales_id->salesInvoiceNumber)) {
                                                            $invoice = $sales_id->salesInvoiceNumber;
                                                        }
                                                    }
                                                ?>
                                                <tr>
                                                    @if($status == 'new')
                                                        <td class="ws"><input name="order_ids[]" value="{{ $row->id }}" type="checkbox" /></td>
                                                    @endif
                                                    <td class="ws">{!! date('d-M-Y', strtotime($row->sale_date)) !!}</td>
                                                    <td class="ws">
                                                        <a href="{{ route('admin.view_ebay_order_details', $row->id) }}">{!! $row->id !!}</a>
                                                    </td>
                                                    <td class="ws">{!! $row->order_number !!}</td>
                                                    <td class="ws">{!! $row->ship_to_name !!}</td>
                                                    <td class="ws">{!! $row->ship_to_email !!}</td>
                                                    <td class="ws">{!! $row->ship_to_address_1 !!} {!! $row->ship_to_address_2 !!} {!! $row->ship_to_city !!} {!!$row->ship_to_state !!} {!! $row->ship_to_country !!}</td>
                                                    <td class="ws">{!! $row['sold_for'] !!} {!! $row->_ebay_order_currency !!}</td>
                                                    <td class="ws"> {!! $row->payment_method !!}</td>

                                                    @if($status != 'new')
                                                        <td class="ws">{{ $track_id }}</td>
                                                        <td class="ws">{{ $invoice }}</td>
                                                    @else
                                                        <td class="ws"> {{ $row->_ebay_cancel_order_status ?? 'Pending' }} </td>
                                                    @endif

                                                    @if($status == 'new')
                                                        <td class="ws">{{-- <a href="{{ route('admin.delete-ebay-orders', $row['_post_id']) }}" class="btn-sm btn btn-red" onclick="return confirm('Are you sure?')">Cancel</a> --}}</td>
                                                    @else
                                                        <td class="ws">
                                                            <a href="{{ route('admin.view_ebay_order_details', $row->id) }}" class="btn-sm btn btn-blue"><i class="fa fa-eye"></i></a>
                                                            {{-- <a href="{{ route('admin.order_invoice', $row['_post_id']) }}" class="btn-sm btn btn-red" target="_blank"><i class="fa fa-print"></i></a> --}}
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8"><i class="fa fa-exclamation-triangle"></i> There are no cancel orders</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>            
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