@extends('layouts.admin.layout')

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet"/>
@endpush

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<style type="text/css">
#confirmBox
{
    display: none;
    background-color: #eee;
    border-radius: 5px;
    border: 1px solid #aaa;
    z-index: 41;
    width: 350px;
    margin: 0 auto;
    padding: 15px 8px 20px;
    box-sizing: border-box;
    text-align: center;
    position: fixed;
    top: calc(50% - 25px);
    left: calc(50% - 50px);
}
#confirmBox .button {
    background-color: #ccc;
    display: inline-block;
    border-radius: 3px;
    border: 1px solid #aaa;
    padding: 2px;
    text-align: center;
    width: 80px;
    cursor: pointer;
}
#confirmBox .button:hover
{
    background-color: #ddd;
}
#confirmBox .message
{
    margin-bottom: 8px;
}
</style>
@endpush

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        @include('pages-message.notify-msg-error')
        @include('pages-message.notify-msg-success')

        <div class="row">
            <div class="col-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h5 class="box-title">Pallet Detail</h5>
                        <h4 class="card-title">
                            @if($pallet->pallet_type == 'Closed')
                                <a href="{{ route('admin.closedpallet.list') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ route('admin.shipped.pallet.list') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            @endif

                            @if($pallet->pallet_type == 'Closed')
                                {{-- <a href="javascript:void(0);" class="btn btn-red btn-sm" id="frm-sbt">
                                    <i class="la la-arrow-up"></i> Generate Custom Manifest
                                </a> --}}
                            @endif
                        </h4>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" id="product-form">
                                @csrf
                                <input type="hidden" name="pallet_id" id="pallet_id" value="{{ $pallet->pallet_id }}">
                            </form>
                        
                            <form action="{{ route('admin.ship.pallet.update') }}" method="post" class="form-horizontal" enctype="multipart/form-data" autocomplete="off" id="pallet-update">
                                @csrf
                                <input type="hidden" name="p_id" value="{{ $pallet->id }}">
                                <input type="hidden" name="pallet_id" value="{{ $pallet->pallet_id }}">
                                <div class="card1">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <label for="">Pallet Name</label>
                                            <input type="text" name="pp_id" value="{{ $pallet->pallet_id }}" class="form-control" placeholder="Pallet Name" readonly>
                                            @error('name')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">From Warehouse</label>
                                            <select name="fr_warehouse_id" id="client_warehouse_list" class="form-control">
                                                <option value="">-- Select --</option>
                                                @php $fr = $pallet->meta->fr_warehouse_id ?? '' @endphp 
                                                @forelse($warehouse as $wh)
                                                    <option value="{{ $wh->id }}" @if($pallet->meta->fr_warehouse_id == $wh->id) selected="selected" @endif>{{ $wh->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('warehouse_id')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">To Warehouse</label>
                                            <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                <option value="">-- Select --</option>
                                                @forelse($warehouse as $wh)
                                                    <option value="{{ $wh->id }}" @if($pallet->warehouse_id == $wh->id) selected="selected" @endif>{{ $wh->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('warehouse_id')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Pallet Type</label>
                                            <select class="form-control" name="return_type">
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}" @if($pallet->return_type == $code) selected="selected" @endif>{{ $code }}</option>
                                                @endforeach
                                                {{-- <option value="Charity" @if($pallet->return_type == 'Charity') selected="selected" @endif>Charity</option>
                                                <option value="Discrepency" @if($pallet->return_type == 'Discrepency') selected="selected" @endif>Discrepency</option>
                                                <option value="Restock" @if($pallet->return_type == 'Restock') selected="selected" @endif>Restock</option>
                                                <option value="Resell" @if($pallet->return_type == 'Resell') selected="selected" @endif>Resell</option>
                                                <option value="Return" @if($pallet->return_type == 'Return') selected="selected" @endif>Return</option>
                                                <option value="Redirect" @if($pallet->return_type == 'Redirect') selected="selected" @endif>Redirect</option>
                                                <option value="Recycle" @if($pallet->return_type == 'Recycle') selected="selected" @endif>Recycle</option>
                                                <option value="Other"  @if($pallet->return_type == 'Other') selected="selected" @endif>Other</option>
                                                <option value="Short Shipment" @if($pallet->return_type == 'Short Shipment') selected="selected" @endif>Short Shipment</option> --}}
                                            </select>
                                            @error('pallet_type')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Shipper Name</label>
                                                <input type="text" class="form-control" name="shipper_name" value="{{ $pallet->meta->shipper_name ?? '' }}">
                                                @error('shipper_name')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">MAWB Number</label>
                                                <input type="text" name="mawb_number" class="form-control" value="{{ $pallet->mawb_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">No. of Packages</label>
                                                <input type="text" name="no_of_packages" class="form-control" value="{{ $pallet->meta->no_of_packages ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">No. of Boxes</label>
                                                <input type="text" name="no_of_box" class="form-control" value="{{ $pallet->meta->no_of_box ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Date of Shipment</label>
                                                <input type="text" name="shipment_date" class="form-control" value="{{ $pallet->meta->shipment_date ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tracking id</label>
                                                <input type="text" class="form-control" name="tracking_id" value="{{ $pallet->tracking_id }}">
                                                @error('tracking_id')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Freight Buy Rate</label>
                                                <input type="text" class="form-control" name="fright_charges" value="{{ $pallet->fright_charges }}">
                                                @error('fright_charges')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Custom Duty</label>
                                                <input type="text" class="form-control" name="custom_duty" value="{{ $pallet->custom_duty }}">
                                                @error('custom_duty')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Taxes</label>
                                                <input type="text" class="form-control" name="custom_vat" value="{{ $pallet->custom_vat }}">
                                                @error('custom_vat')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">HAWB#</label>
                                                <input type="text" name="hawb_number" class="form-control" value="{{ $pallet->hawb_number }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Delivery Date</label>
                                                <input type="text" name="delivery_date" class="form-control" value="{{ $pallet->meta->delivery_date ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Time of Delivery</label>
                                                <input type="text" name="delivery_time" class="datetimepicker form-control" value="{{ $pallet->meta->delivery_time ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Signed by</label>
                                                <input type="text" name="signed_by" class="form-control" value="{{ $pallet->meta->signed_by ?? '' }}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="">Remarks #</label>
                                                <textarea name="remarks" class="form-control">{{ $pallet->meta->remarks ?? '' }}</textarea>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group col-md-3">
                                            <label for="">RRP Price</label>
                                            <input type="text" class="form-control" name="rrp_price" value="{{ $pallet->meta->rrp_price ?? '' }}">
                                            @error('rrp_price')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div> --}}

                                        <div class="form-group col-md-3">
                                            <label for="">Preferred Listing Price</label>
                                            <input type="text" class="form-control" name="pl_price" value="{{ $pallet->meta->pl_price ?? '' }}">
                                            @error('pl_price')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Preferred Listing Price %</label>
                                            <input type="text" class="form-control" name="ppl_price" value="{{ $pallet->meta->ppl_price ?? '' }}">
                                            @error('ppl_price')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Actual Sold Price</label>
                                            <input type="text" class="form-control" name="as_price" value="{{ $pallet->meta->as_price ?? '' }}">
                                            @error('as_price')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Actual Sold Price %</label>
                                            <input type="text" class="form-control" name="asp_price" value="{{ $pallet->meta->asp_price ?? '' }}">
                                            @error('asp_price')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Date Auctioned</label>
                                            <input type="text" class="form-control" name="date_a" value="{{ $pallet->meta->date_a ?? '' }}">
                                            @error('date_a')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-12">
                                            <h5><b><u> Liquidation Information :- </u></b></h5>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Customer Name</label>
                                                <input type="text" name="l_cname" class="form-control" value="{{ $pallet->meta->l_cname ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Customer Address</label>
                                                <input type="text" name="l_address" class="form-control" value="{{ $pallet->meta->l_address ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Price</label>
                                                <input type="text" name="l_price" class="form-control" value="{{ $pallet->meta->l_price ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">IncoTerm</label>
                                                <input type="text" name="l_incoterm" class="form-control" value="{{ $pallet->meta->l_incoterm ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Duty Paid</label>
                                                <input type="text" name="l_duty_paid" class="form-control" value="{{ $pallet->meta->l_duty_paid ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Tax Paid</label>
                                                <input type="text" name="l_tax_paid" class="form-control" value="{{ $pallet->meta->l_tax_paid ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Customs Broker</label>
                                                <input type="text" name="l_custom_broker" class="form-control" value="{{ $pallet->meta->l_custom_broker ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Currency</label>
                                                <input type="text" name="l_currency" class="form-control" value="{{ $pallet->meta->l_currency ?? '' }}"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Channel Sold By</label>
                                                <select class="form-control" name="l_chanel">
                                                    <option value="AUCTION" @if(isset($pallet->meta->l_chanel) && $pallet->meta->l_chanel == 'AUCTION') selected="selected" @endif>AUCTION</option>
                                                    <option value="EBAY" @if(isset($pallet->meta->l_chanel) && $pallet->meta->l_chanel == 'EBAY') selected="selected" @endif>EBAY</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Sold Type</label>
                                                <select class="form-control" name="l_stype">
                                                    <option value="PALLET" @if($pallet->meta->l_stype == 'PALLET') selected="selected" @endif>PALLET</option>
                                                    <option value="BOX" @if($pallet->meta->l_stype == 'BOX') selected="selected" @endif>BOX</option>
                                                    <option value="INDIVIDUAL" @if($pallet->meta->l_stype == 'INDIVIDUAL') selected="selected" @endif>INDIVIDUAL</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Destruction Certificate</label>
                                                <input type="file" name="image" class="form-control">
                                            </div>
                                        </div>

                                        @if(isset($pallet->meta->certificate))
                                            <div class="col-md-3">
                                                <div class="edit-form-group">
                                                    <div class="edit-form-text">Destruction Certificate</div>
                                                    <div class="edit-form-value">
                                                        <div class="edit-form-value-img">
                                                            <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank">
                                                                Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($pallet->hasMeta('certificate'))
                                            <div class="col-md-3">
                                                <div class="edit-form-group">
                                                    <div class="edit-form-text">Destruction certificate</div>
                                                    <div class="edit-form-value">
                                                        <div class="edit-form-value-img">
                                                            <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank">
                                                                Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @php
                                        $ordr = getPalletItemValueData($orders);
                                        @endphp
                                        
                                        <div class="form-group col-md-3">
                                            <label for="">Line Item Cost</label>
                                            <input type="text" class="form-control" value="{{ $ordr['item_cost'] ?? 0 }}" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Total Received Qty</label>
                                            <input type="text" class="form-control" value="{{ $ordr['rcvd_qty'] ?? 0 }}" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Total Recvd Line Item Cost</label>
                                            <input type="text" class="form-control" value="{{ $ordr['rcvd_qty_cost'] ?? 0 }}" readonly>
                                        </div>

                                        <input type="hidden" name="pallet_type" id="pallet_type" value="">                            
                                        <div class="form-group col-md-1">
                                            <button type="submit" class="btn btn-blue save-client mt-2">Save</button>
                                        </div>
                                        <!-- <div class="form-group col-md-2">
                                            <button type="button" class="btn btn-red btn-sm" id="ship-pallet">Ship Pallet</button>
                                        </div> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="pallet-div">
                                    <!-- <div class="col-md-2">
                                        <button type="button" id="existing-pallet" class="btn btn-blue">Add to existing Pallet </button>
                                    </div> -->
                                    <!-- <div class="col-md-2">
                                        <button type="button" id="remove-pallet" class="btn btn-blue">Move Back to Warehouse</button>
                                    </div> -->
                                
                                    <!-- <div class="text-right col-md-8">
                                        <button type="button" id="add-to-pallet" class="btn btn-red">Submit</button>
                                    </div> -->
                                </div>
                            </div>
                        
                            <div class="card-body">
                	            <div class="rg-pack-table">
                                    <div class="alert alert-primary">
                                        @if(count($orders)>0)
                                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                        @endif
                                    </div>
                                    <div id="confirmBox">
                                        <div class="message"></div>
                                        <span class="btn btn-success btn-sm yes">OK</span>
                                        <span class="btn btn-danger btn-sm no">No I don’t want to close the Pallet</span>
                                    </div>
                                	<div class="table-responsive booking-info-box">
                                		<form action="{{ route('admin.remove.pallet.orders') }}" method="post" id="move-warehouse">
                	                		@csrf
                	                		<div class="row ml-1" id="ex-pallet"></div>
                		                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
                		                        <thead>
                		                            <tr>
                		                                <!-- <th class="ws">
                	                                        <input name="select_all" value="1" id="select-all" type="checkbox">
                	                                    </th> -->
                		                                {{-- <th>Action</th> --}}
                                                        <th class="ws">Date</th>
                                                        <th class="ws">Ref. Number</th>
                                                        <th class="ws">Item Ref. Number</th>
                                                        <th class="ws">EVTN Number</th>
                                                        <th class="ws">Item Description</th>
                                                        <th class="ws">Item Brand</th>
                                                        <th class="ws">Item Price</th>
                                                        <th class="ws">Original Sales Incoterm</th>
                                                        <th class="ws">Buyer Country</th>
                                                        <th class="ws">Hs Code</th>
                                                        <th class="ws">COO</th>
                                                        <th class="ws">SC Main Category</th>
                                                        <th class="ws">Category Tier 1</th>
                                                        <th class="ws">Category Tier 2</th>
                                                        <th class="ws">Category Tier 3</th>
                                                        <th class="ws">Level</th>
                                                        <th class="ws">Received Condition</th>
                                                        <th class="ws">Listing Condition</th>
                                                        <th class="ws">Inspection Status</th>
                                                        <th class="ws">Reason of Return</th>
                		                            </tr>
                		                        </thead>
                		                        <tbody>
                		                        	@forelse($orders as $row)
                                                        @php
                                                            $brand = 'N/A';
                                                            if(isset($row->package_data)){
                                                                $attributes = json_decode($row->package_data);
                                                                if(isset($attributes->itemAttributes) && count($attributes->itemAttributes)){
                                                                    foreach($attributes->itemAttributes as $attr){
                                                                        if($attr->name == 'Brand'){
                                                                            $brand = $attr->value;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                		                        		<tr>
                                                            {{-- @if(empty($row['_pallet_id']) && isset($row['status_id']))
                                                                <td  style="text-align: center;"><input name="order_ids[]" value="{{ $row['_post_id'] }}" type="checkbox" class="selectone" /></td>
                                                            @else --}}
                                                                <!-- <td>@if($row['pallet_id'] != null )<input name="pkg_orders[]" type="checkbox" class="selectone" value="{{ $row['_post_id'] }}"> @endif</td> -->
                                                            {{-- @endif --}}
                                                            {{-- <td class="ws" style="text-align: center;">
                                                                <a href="{{url('admin/orders/details/'.$row['_post_id'])}}" class="btn-sm btn btn-blue"><i class="fa fa-eye"></i></a>
                                                                <a href="{{url('admin/order/invoice/'.$row['_post_id'])}}" class="btn-sm btn btn-red" target="_blank"><i class="fa fa-print"></i></a>
                                                                <a class="btn btn-red btn-sm" href="" onclick="return confirm('Are you sure you want to cancle this?')">
                                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                                </a>
                                                            </td> --}}
                                                            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->created_at)) !!}</td>
                                                            <td class="ws">{!! $row->reference_number ?? $row->id !!}</td>
                                                            <td class="ws">{!! $row->package_id ?? '' !!}</td>
                                                            <td class="ws">{!! $row->evtn_number ?? '' !!}</td>
                                                            <td class="ws">{!! $row->title ?? '' !!}</td>
                                                            <td class="ws">{!! $brand !!}</td>
                                                            <td class="ws">{!! $row->post_extras_currency ?? '' !!} {!! $row->price ?? '' !!}</td>
                                                            <td class="ws">{!! $row->serviceName ?? '' !!}</td>
                                                            <td class="ws">{!! $row->customer_country ?? '' !!}</td>
                                                            <td class="ws">{{ $row->hs_code ?? '' }}</td>
                                                            <td class="ws">{{ $row->coo ?? '' }}</td>
                                                            <td class="ws">@if(isset($row->category)) {!! getCategoryName($row->category, 'main') !!} @endif</td>
                                                            <td class="ws">@if(isset($row->sub_category_1)) {!! getCategoryName($row->sub_category_1) !!} @endif</td>
                                                            <td class="ws">@if(isset($row->sub_category_2)) {!! getCategoryName($row->sub_category_2) !!} @endif</td>
                                                            <td class="ws">@if(isset($row->sub_category_3)) {!! getCategoryName($row->sub_category_3) !!} @endif</td>
                                                            <td class="ws">{{ $row->inspection_level ?? '' }}</td>
                                                            <td class="ws">
                                                                @if(isset($row->received_condition) && !empty($row->received_condition))
                                                                    {{ $row->received_condition ?? '' }}
                                                                @endif
                                                            </td>
                                                            <td class="ws">{{ $row->condition ?? '' }}</td>
                                                            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($row->order_status)) }}"> {{ inception_status($row->order_status) }} </span></td>
                                                            <td class="ws">{{ getMetaValue($row->item_id, 'expected_quantity') ?? $row->itemQuantity }}</td>
                                                            <td class="ws">
                                                                @if(getMetaValue($row->item_id, 'match_quantity') == 'Yes')
                                                                    {{ $row->itemQuantity ?? '' }}
                                                                @else
                                                                    {{ getMetaValue($row->item_id, 'actual_quantity') ?? '' }}
                                                                @endif
                                                            </td>
                                                            <td class="ws">{{ $row->reason_of_return ?? '' }}</td>
                                                        </tr>
                		                            @empty
                		                            @endforelse
                		                        </tbody>
                		                    </table>
                		                </form>
                                	</div>
                                	<div class="box-footer">
                                	    <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
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

@endsection

@push('js')
<script>
    $(document).ready(function(){
        $('input[name="export_declaration_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        // $('input[name="rtn_import_entry_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $('input[name="shipment_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $('input[name="date_a"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $('input[name="delivery_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
        $('.datetimepicker').datetimepicker({
            format: 'HH:mm:ss'
        });
    })
</script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js" type="text/javascript" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>
        $(document).on('click',"#remove-pallet", function(){
            if($('.selectone:checkbox:checked').length < 1) {
                alert('Please select at least one checkbox');
                return false;
            } else {
                $("#move-warehouse").submit();
                // $(".pallet-div").toggle();
            }
        });

        $(document).on('click',"#ship-pallet", function(e){
            e.preventDefault();
            doConfirm("You will not be able to add additional items if you ship the pallet. Please confirm ok to proceed?", function yes() {
                // alert('yes');
                $('#pallet_type').val('Shipped')
                $('#pallet-update').submit();
            }, function no() {
                // alert('no');
            });        
        });

        function doConfirm(msg, yesFn, noFn) {
            var confirmBox = $("#confirmBox");
            confirmBox.find(".message").text(msg);
            confirmBox.find(".yes,.no").unbind().click(function () {
                confirmBox.hide();
            });
            confirmBox.find(".yes").click(yesFn);
            confirmBox.find(".no").click(noFn);
            confirmBox.show();
        }
    </script>
@endpush