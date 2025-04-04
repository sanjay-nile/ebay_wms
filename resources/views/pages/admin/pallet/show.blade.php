@extends('layouts.admin.layout')

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
                                <a href="{{ route('admin.pallet.list') }}" class="btn btn-primary btn-sm">
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

                            <div class="info-list-section">
                                <div class="card1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Pallet Id</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->pallet_id }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Client Name</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->client->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">From Warehouse Name</div>
                                                <div class="booking-value-info">
                                                    @php $fr = $pallet->meta->fr_warehouse_id ?? '' @endphp {{getWareHouseName($fr)}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">To Warehouse Name</div>
                                                <div class="booking-value-info">
                                                    {{ getWareHouseName($pallet->warehouse_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Pallet Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->return_type ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Shipment Type</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->shipmentType->name ?? 'N/A'}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Sell Rate</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->rate}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Carrier</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->carrier }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Tracking ID</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->tracking_id }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Freight Charges</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->fright_charges }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 collapse">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Custom Duty</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->custom_duty }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">RRP price</div>
                                                <div class="booking-value-info">
                                                    {{ getPackageValue($pallet->pallet_id) ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Preferred listing price</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->pl_price ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Preferred listing price %</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->meta->ppl_price ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Authorised by</div>
                                                <div class="booking-value-info">
                                                    {{ $pallet->authorised_by ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                        $ordr = getPalletItemValueData($orders);
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Line Item Cost</div>
                                                <div class="booking-value-info">
                                                    {{ $ordr['item_cost'] ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Total Received Qty</div>
                                                <div class="booking-value-info">
                                                    {{ $ordr['rcvd_qty'] ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-box">
                                                <div class="booking-title-info">Total Recvd Line Item Cost</div>
                                                <div class="booking-value-info">
                                                    {{ $ordr['rcvd_qty_cost'] ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="pallet-div">
                                <div class="">
                                    <button type="button" id="remove-pallet" class="btn btn-blue">Move Back to Warehouse</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
            	            <div class="rg-pack-table">
                                <div class="alert alert-primary">
                                    @if(count($orders)>0)
                                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                    @endif
                                </div>
                            	<div class="table-responsive booking-info-box">
                            		<form action="{{ route('admin.remove.pallet.orders') }}" method="post" id="move-warehouse">
            	                		@csrf
            	                		<div class="row ml-1" id="ex-pallet"></div>
            		                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
            		                        <thead>
            		                            <tr>
            		                                <th class="ws">
            	                                        <input name="select_all" value="1" id="select-all" type="checkbox">
            	                                    </th>
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
                                                    <th class="ws">Expected Qty</th>
                                                    <th class="ws">Received Qty</th>
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
                                                        <td  style="text-align: center;">
                                                            @if(isset($row->pallet_id) && $row->pallet_id != null )
                                                                <input name="pkg_orders[]" type="checkbox" class="selectone" value="{{ $row->id }}">
                                                            @endif
                                                        </td>                                                    
                                                        <td class="ws" style="white-space: nowrap;">
                                                            @if(isset($row->created_at))
                                                                {!! date('d-m-Y', strtotime($row->created_at)) !!}
                                                            @endif
                                                        </td>
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

@endsection

@push('js')
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
    </script>
@endpush