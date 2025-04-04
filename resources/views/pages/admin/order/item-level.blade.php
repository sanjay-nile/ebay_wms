<div class="tab-pane fade" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
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
                        <th class="ws">Certificate</th>
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
                        <th class="ws">Pallet Id</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($orders as $row)
                		@php
                		// dd($row);
                		@endphp

                		@if(count($row['packages']) > 0)
                			@forelse($row['packages'] as $package)
        						<tr>
        				            @if($row['order_status'] != 'Pending' && $row['process_status'] == 'unprocessed')
        				                <td style="text-align: center;"><input name="order_ids[]" value="{{ $row['_post_id'] }}" type="checkbox" class="selectone" /></td>
        				            @else
        				                <td>
        				                </td>
        				            @endif
        				            <td class="ws" style="white-space:nowrap;">
        				                <a href="{{url('admin/order/edit/'.$row['_post_id'])}}" class="btn btn-edit"><i class="fa fa-edit"></i></a>
        				                {{-- <a href="{{url('admin/order/details/'.$row['_post_id'])}}" class="btn btn-view"><i class="fa fa-eye"></i></a> --}}
        				                <a class="btn btn-edit" href="{{ route('admin.evtn.invoice', $row['_post_id']) }}" target="_blank">
        				                    <i class="fa fa-print" aria-hidden="true"></i>
        				                </a>
        				                @if(Auth::user()->user_type_id == 1)
        				                    <a class="btn btn-view" href="{{ route('admin.remove.order.package', $row['_post_id']) }}" onclick="return confirm('Are you sure you want to remove this?')">
        				                        <i class="fa fa-trash" aria-hidden="true"></i>
        				                    </a>
        				                @endif
        				            </td>
        				            <td>
        				                @php
        				                    $pallet = getPalletDetails($row['pallet_id']);
        				                @endphp
        				                @if(!empty($pallet) && $pallet->hasMeta('certificate'))
        				                    <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank" class="btn btn-view"><i class="fa fa-arrow-down"></i></a>
        				                @endif
        				            </td>
        				            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row['_order_date'])) !!}</td>
        				            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($package['status'])) }}"> {{ inception_status($package['status']) }} </span></td>
        				            <td class="ws">
        				                {!! $row['reference_number'] ?? $row['_post_id'] !!}
        				            </td>
        				            <td class="ws">{!! $row['evtn_number'] ?? '' !!}</td>
        				            <td class="ws">{!! $row['customer_name'] ?? '' !!}</td>
        				            <td class="ws">{!! $row['tracking_number'] ?? '' !!}</td>
        				            <td class="ws">{!! $package['serviceName'] ?? '' !!}</td>
        				            <td class="ws">{!! $row['customer_address_line_1'] ?? '' !!} {!! $row['customer_address_line_2'] ?? '' !!} {!! $row['customer_city'] ?? '' !!} {!! $row['customer_state'] ?? '' !!} {!! $row['customer_pincode'] ?? '' !!}</td>
        				            <td class="ws">{!! $row['currency'] ?? '' !!} {!! $row['value'] ?? '' !!}</td>
        				            <td class="ws">{{ $package['itemSku'] ?? '' }}</td>
        				            <td class="ws">{{ $package['hs_code'] ?? '' }}</td>
        				            <td class="ws">{{ $package['coo'] ?? '' }}</td>
        				            <td class="ws">@if(isset($package['category'])) {!! getCategoryName($package['category'], 'main') !!} @endif</td>
        				            <td class="ws">@if(isset($package['sub_category_1'])) {!! getCategoryName($package['sub_category_1']) !!} @endif</td>
        				            <td class="ws">{{ $package['inspection_level'] ?? '' }}</td>
        				            <td class="ws">
        				                @if(isset($package['received_condition']) && !empty($package['received_condition']))
        				                    {{ $package['received_condition'] ?? '' }}
        				                @endif
        				            </td>
        				            <td class="ws">{{ $package['condition'] ?? '' }}</td>
                                    <td class="ws">{{ $package['title'] ?? '' }}</td>
        				            <td class="ws">{{ $row['pallet_id'] ?? '' }}</td>
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