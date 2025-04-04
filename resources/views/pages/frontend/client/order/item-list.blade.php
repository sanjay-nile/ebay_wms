<div class="tab-pane fade" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
    <div class="alert alert-primary">
        @if(count($orders)>0)
        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
        @endif
    </div>

    <div class="text-left">
        <button type="button" class="btn btn-sm btn-blue pull-left mb-1 mr-1" id="item-excel-btn">Export To Excel</button>
    </div>

	<div class="table-responsive booking-info-box" style="padding: 0;">
		<form action="{{route('admin.return.orders')}}" method="post" id="process-save">
            @csrf
            <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
                <thead>
                    <tr> 
                        <th class="ws">Action</th>
                        <th class="ws">Date Received in Warehouse</th>
                        <th class="ws">Item Status</th>
                        <th class="ws">Ref. Number</th>
                        <th class="ws">EVTN Number</th>
                        <th class="ws">Name</th>
                        <th class="ws">Tracking No.</th>
                        <th class="ws">Original Sales Incoterm</th>
                        <th class="ws">Address</th>
                        <th class="ws">Amount</th>
                        <th class="ws">Item Sku</th>
                        <th class="ws">Hs Code</th>
                        <th class="ws">COO</th>
                        <th class="ws">SC Main Category</th>
                        <th class="ws">Category Tier 1</th>
                        <th class="ws">Level</th>
                        <th class="ws">Received Condition</th>
                        <th class="ws">Listing Condition</th>
                        <th class="ws">Description</th>
                        <th class="ws">Expected Qty</th>
                        <th class="ws">Received Qty</th>
                        <th class="ws">Oversized Packages</th>
                        <th class="ws">Empty Box</th>
                        <th class="ws">Pallet Id</th>
                        <th class="ws">Username</th>
                        <th class="ws">Invoiced</th>
                        <th class="ws">Date Invoiced</th>
                        <th class="ws">Invoice Number</th>
                        <th class="ws">BoxTop ref. number</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($orders as $row)
                		@php
                		// dd($row);
                		@endphp

                		@if(count($row->package) > 0)
                			@forelse($row->package as $package)
                                @php
                                    $tt = app('request')->input('rcvd_qty');
                                    if(!empty($tt) && $tt == 0 && $package->meta->actual_quantity != $tt){
                                        continue;
                                    }

                                    $et = app('request')->input('empty_box');
                                    if(!empty($et) && $et != $package->empty_box){
                                        continue;
                                    }
                                @endphp
        						<tr>
        				            <td class="ws" style="white-space:nowrap;">
        				                <a href="{{url('client/'.$row->id.'/edit-order')}}" class="btn btn-edit"><i class="fa fa-edit"></i></a>
                                    <a class="btn btn-edit" href="{{ route('client.order.invoice', $row->id) }}" target="_blank">
                                        <i class="fa fa-print" aria-hidden="true"></i>
                                    </a>
        				            </td>
        				            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->created_at)) !!}</td>
        				            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status_value($package->status)) }}"> {{ inception_status_value($package->status) }} </span></td>
        				            <td class="ws">{!! get_post_extra($row->id, 'reference_number') ?? $row->id !!}</td>
        				            <td class="ws">{!! get_post_extra($row->id, 'evtn_number') !!}</td>
                                    <td class="ws">{!! get_post_extra($row->id, 'customer_name') !!}</td>
                                    <td class="ws">{!! get_post_extra($row->id, 'tracking_number') !!}</td>
        				            <td class="ws">{!! $package->serviceName ?? '' !!}</td>
        				            <td class="ws">{!! get_post_extra($row->id, 'customer_address_line_1') !!} {!! get_post_extra($row->id, 'customer_address_line_2') !!} {!! get_post_extra($row->id, 'customer_city') !!} {!! get_post_extra($row->id, 'customer_state') !!} {!! get_post_extra($row->id, 'customer_pincode') !!}</td>
        				            <td class="ws">{!! get_post_extra($row->id, 'currency') !!} {!! get_post_extra($row->id, 'value') !!}</td>
        				            <td class="ws">{{ $package->itemSku ?? '' }}</td>
        				            <td class="ws">{{ $package->hs_code ?? '' }}</td>
        				            <td class="ws">{{ $package->coo ?? '' }}</td>
        				            <td class="ws">{!! getCategoryName($package->category, 'main') !!}</td>
        				            <td class="ws">{!! getCategoryName($package->sub_category_1) !!}</td>
        				            <td class="ws">{{ $package->inspection_level ?? '' }}</td>
                                    <td class="ws">
                                        @if(!empty($package->received_condition))
                                            <span class=" badge badge-pill badge-{{ get_budge_value($package->received_condition) }}"> {{ $package->received_condition }} </span>
                                        @endif
                                    </td>
        				            <td class="ws">{{ $package->condition ?? '' }}</td>
                                    <td class="ws">{{ $package->title ?? '' }}</td>
                                    <td class="ws">{{ $package->meta->expected_quantity ?? $package->itemQuantity }}</td>
                                    <td class="ws">
                                        @if($package->meta->match_quantity == 'Yes')
                                            {{ $package->itemQuantity ?? '1' }}
                                        @else
                                            {{ $package->meta->actual_quantity ?? 'TBC' }}
                                        @endif
                                    </td>
        				            <td class="ws">{{ (!empty($package->oversize)) ? $package->oversize : 'No' }}</td>
                                    <td class="ws">{{ (!empty($package->empty_box)) ? $package->empty_box : 'No' }}</td>
                                    <td class="ws">{{ $row->pallet_id ?? '' }}</td>
                                    <td class="ws">
                                        @php
                                            $user = '';
                                            if(count($package->history) > 0){
                                                foreach($package->history as $his){
                                                    $user = $his->user;
                                                }
                                            }
                                            echo $user;
                                        @endphp
                                    </td>
                                    <td class="ws">{!! get_post_extra($row->id, 'invoiced') !!}</td>
                                    <td class="ws">{!! get_post_extra($row->id, 'date_invoiced') !!}</td>
                                    <td class="ws">{!! get_post_extra($row->id, 'invoice_number') !!}</td>
                                    <td class="ws">{!! get_post_extra($row->id, 'box_number') !!}</td>
        				        </tr>
                			@empty
                			@endforelse
                		@endif
                    @empty
                    @endforelse
                </tbody>
            </table>
        </form>
	</div>
</div>