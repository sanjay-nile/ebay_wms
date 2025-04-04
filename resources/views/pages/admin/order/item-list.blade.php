<div class="tab-pane fade show active" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
    <div class="alert alert-primary">
        @if(count($orders)>0)
        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
        @endif
    </div>

    <div class="text-left">
        <button type="button" id="add-to-warehouse" class="btn btn-red btn-sm mb-1">Add to Warehouse</button>
        <button type="button" class="btn btn-sm btn-blue pull-left mb-1 mr-1" id="item-excel-btn">Export To Excel</button>
    </div>

	<div class="table-responsive booking-info-box" style="padding: 0;">
		<form action="{{route('admin.return.orders')}}" method="post" id="process-save">
    		@csrf
            <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
                <thead>
                    <tr> 
                        <th class="ws">
                            <input name="select_all" value="1" id="select-all" type="checkbox">
                        </th>
                        <th class="ws">Action</th>
                        <th class="ws">Date Rcvd in Warehouse</th>
                        <th class="ws">Item Status</th>
                        <th class="ws">Sent to Scheduled</th>
                        <th class="ws">Order Ref. Number</th>
                        <th class="ws">Item Ref. Number</th>
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
                        <th class="ws">Pallet Type</th>
                        <th class="ws">Username</th>
                        <th class="ws">Invoiced</th>
                        <th class="ws">Date Invoiced</th>
                        <th class="ws">Invoice Number</th>
                        <th class="ws">BoxTop ref. number</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($orders as $row)
                		<tr>
                            @if($row->order_status != 'IS-01' && $row->process_status == 'unprocessed' && empty($row->pallet_id))
                                <td style="text-align: center;"><input name="order_ids[]" value="{{ $row->id }}" type="checkbox" class="selectone" /></td>
                            @else
                                <td></td>
                            @endif
                            <td class="ws" style="white-space:nowrap;">
                                <a href="{{url('admin/order/edit/'.$row->id)}}" class="btn btn-edit" target="_blank"><i class="fa fa-edit"></i></a>
                                <a class="btn btn-edit" href="{{ route('admin.evtn.invoice', $row->id) }}" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </a>
                                @if(Auth::user()->user_type_id == 1)
                                    <a class="btn btn-view" href="{{ route('admin.remove.order.package', $row->id) }}" onclick="return confirm('Are you sure you want to remove this?')">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-edit btn-danger" href="{{ route('admin.remove.pallet', $row->item_id) }}" onclick="return confirm('Are you sure you want to remove this from the pallet?')">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->ps_created_at)) !!}</td>
                            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status_value($row->status)) }}"> {{ inception_status_value($row->status) }} </span></td>
                            <td class="ws">
                                @if($row->inventory_status == 'Completed')
                                    {{ date('d/m/Y', strtotime($row->inventory_date)) }}
                                @else
                                    {{ $row->common_error ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="ws">{!! $row->reference_number ?? $row->id !!}</td>
                            <td class="ws">{!! $row->package_id ?? '' !!}</td>
                            <td class="ws">{!! $row->evtn_number ?? '' !!}</td>
                            <td class="ws">{!! $row->customer_name ?? '' !!}</td>
                            <td class="ws">{!! $row->tracking_number ?? '' !!}</td>
                            <td class="ws">{!! $row->serviceName ?? '' !!}</td>
                            <td class="ws">{!! $row->customer_address !!} {!! $row->customer_city !!} {!! $row->customer_state !!} {!! $row->customer_pincode !!}</td>
                            <td class="ws">{!! $row->post_extras_currency ?? '' !!} {!! $row->price !!}</td>
                            <td class="ws">{{ $row->itemSku ?? '' }}</td>
                            <td class="ws">{{ $row->hs_code ?? '' }}</td>
                            <td class="ws">{{ $row->coo ?? '' }}</td>
                            <td class="ws">{!! getCategoryName($row->category, 'main') !!}</td>
                            <td class="ws">{!! getCategoryName($row->sub_category_1) !!}</td>
                            <td class="ws">{{ $row->inspection_level ?? '' }}</td>
                            <td class="ws">
                                @if(!empty($row->received_condition))
                                    <span class=" badge badge-pill badge-{{ get_budge_value($row->received_condition) }}"> {{ $row->received_condition }} </span>
                                @endif
                            </td>
                            <td class="ws">{{ $row->condition ?? '' }}</td>
                            <td class="ws">{{ $row->title ?? '' }}</td>
                            <td class="ws">{{ $row->expected_quantity ?? $row->itemQuantity }}</td>
                            <td class="ws">
                                @if($row->match_quantity == 'Yes')
                                    {{ $row->itemQuantity ?? '1' }}
                                @else
                                    {{ $row->actual_quantity ?? 'TBC' }}
                                @endif
                            </td>
                            <td class="ws">{{ (!empty($row->oversize)) ? $row->oversize : 'No' }}</td>
                            <td class="ws">{{ (!empty($row->empty_box)) ? $row->empty_box : 'No' }}</td>
                            <td class="ws">{{ $row->pallet_id ?? '' }}</td>
                            <td class="ws">{{ $row->pallet_type ?? '' }}</td>
                            <td class="ws">
                                @php
                                    $user = '';
                                    if(count($row->history) > 0){
                                        foreach($row->history as $his){
                                            $user = $his->user;
                                        }
                                    }
                                    echo $user;
                                @endphp
                            </td>
                            <td class="ws">{!! $row->invoiced ?? '' !!}</td>
                            <td class="ws">{!! $row->date_invoiced ?? '' !!}</td>
                            <td class="ws">{!! $row->invoice_number ?? '' !!}</td>
                            <td class="ws">{!! $row->box_number ?? '' !!}</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </form>
	</div>
</div>