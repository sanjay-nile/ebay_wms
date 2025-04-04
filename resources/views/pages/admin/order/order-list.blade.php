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
	                <th class="ws">Date Rcvd in Warehouse</th>
	                <th class="ws">Order Status</th>
	                <th class="ws">Order Ref. Number</th>
	                <th class="ws">EVTN Number</th>
	                <th class="ws">Name</th>
	                <th class="ws">Tracking No.</th>
	                <th class="ws">Address</th>
	                <th class="ws">Amount</th>
	                {{-- <th class="ws">Pallet Id</th> --}}
	                <th class="ws">Invoiced</th>
	                <th class="ws">Date Invoiced</th>
	                <th class="ws">Invoice Number</th>
	                <th class="ws">BoxTop ref. number</th>
	            </tr>
	        </thead>
	        <tbody>
	        	@forelse($orders as $row)
		        	@php
		        		if(empty(get_post_extra($row->id, 'order_status'))){
		        			continue;
		        		}
		        	@endphp
	        		<tr>
	                    <td class="ws" style="white-space:nowrap;">
	                        <a href="{{url('admin/order/edit/'.$row->id)}}" class="btn btn-edit" target="_blank"><i class="fa fa-edit"></i></a>
	                        <a class="btn btn-edit" href="{{ route('admin.evtn.invoice', $row->id) }}" target="_blank">
	                            <i class="fa fa-print" aria-hidden="true"></i>
	                        </a>
	                        @if(Auth::user()->user_type_id == 1)
	                            <a class="btn btn-view" href="{{ route('admin.remove.order.package', $row->id) }}" onclick="return confirm('Are you sure you want to remove this?')">
	                                <i class="fa fa-trash" aria-hidden="true"></i>
	                            </a>
	                        @endif
	                    </td>
	                    {{-- <td>
	                        @php
	                            $pallet = getPalletDetails($row->pallet_id);
	                        @endphp
	                        @if(!empty($pallet) && $pallet->hasMeta('certificate'))
	                            <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank" class="btn btn-view"><i class="fa fa-arrow-down"></i></a>
	                        @endif
	                    </td> --}}
	                    <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->created_at)) !!}</td>
	                    <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($row->order_status)) }}"> {{ inception_status($row->order_status) }} </span></td>
	                    <td class="ws">{!! $row->reference_number ?? $row->id !!}</td>
	                    <td class="ws">{!! $row->evtn_number ?? '' !!}</td>
	                    <td class="ws">{!! $row->customer_name ?? '' !!}</td>
	                    <td class="ws">{!! $row->tracking_number ?? '' !!}</td>
	                    <td class="ws">{!! $row->customer_address ?? '' !!} {!! $row->customer_city ?? '' !!} {!! $row->customer_state ?? '' !!} {!! $row->customer_pincode ?? '' !!}</td>
	                    <td class="ws">{!! $row->post_extras_currency !!} {!! $row->order_price ?? 0 !!}</td>
	                    {{-- <td class="ws">{{ $row->pallet_id ?? '' }}</td> --}}
	                    <td class="ws">{!! $row->invoiced ?? '' !!}</td>
	                    <td class="ws">{!! $row->date_invoiced ?? '' !!}</td>
	                    <td class="ws">{!! $row->invoice_number ?? '' !!}</td>
	                    <td class="ws">{!! $row->box_number ?? '' !!}</td>
	                </tr>
	            @empty
	            @endforelse
	        </tbody>
	    </table>
	</div>
</div>