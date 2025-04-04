<div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
	<div class="alert alert-primary">
	    @if(count($orders)>0)
	    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
	    @endif
	</div>
	
    <button type="button" class="btn btn-sm btn-red pull-left mb-1" id="parcel-excel-btn">Export To Excel</button>

	<div class="table-responsive booking-info-box" style="padding: 0;">
	    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
	        <thead>
	            <tr> 
	                <th class="ws">Action</th>
	                <th class="ws">Certificate</th>
	                <th class="ws">Date Received in Warehouse</th>
	                <th class="ws">Order Status</th>
	                <th class="ws">Ref. Number</th>
	                <th class="ws">EVTN Number</th>
	                <th class="ws">Name</th>
	                <th class="ws">Tracking No.</th>
	                <th class="ws">Address</th>
	                <th class="ws">Amount</th>
	                <th class="ws">Pallet Id</th>
	            </tr>
	        </thead>
	        <tbody>
	        	@forelse($orders as $row)
	        		<tr>
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
	                    <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($row['order_status'])) }}"> {{ inception_status($row['order_status']) }} </span></td>
	                    <td class="ws">
	                        {!! $row['reference_number'] ?? $row['_post_id'] !!}
	                    </td>
	                    <td class="ws">{!! $row['evtn_number'] ?? '' !!}</td>
	                    <td class="ws">{!! $row['customer_name'] ?? '' !!}</td>
	                    <td class="ws">{!! $row['tracking_number'] ?? '' !!}</td>
	                    <td class="ws">{!! $row['customer_address_line_1'] ?? '' !!} {!! $row['customer_address_line_2'] ?? '' !!} {!! $row['customer_city'] ?? '' !!} {!! $row['customer_state'] ?? '' !!} {!! $row['customer_pincode'] ?? '' !!}</td>
	                    <td class="ws">{!! $row['currency'] ?? '' !!} {!! $row['value'] ?? '' !!}</td>
	                    <td class="ws">
	                        {{ $row['pallet_id'] ?? '' }}
	                    </td>
	                </tr>
	            @empty
	            @endforelse
	        </tbody>
	    </table>
	</div>
</div>