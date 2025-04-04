<div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
	<div class="alert alert-primary">
	    @if(count($orders)>0)
	    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
	    @endif
	</div>
	
    <button type="button" class="btn btn-sm btn-red pull-left mb-1" id="parcel-excel-btn">Export To Excel</button>

	<div class="table-responsive booking-info-box mt-1" style="padding: 0;">
	    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
	        <thead>
	            <tr> 
	                <th class="ws">Action</th>
	                {{-- <th class="ws">Certificate</th> --}}
	                <th class="ws">Date Received in Warehouse</th>
	                <th class="ws">Order Status</th>
	                <th class="ws">Ref. Number</th>
	                <th class="ws">EVTN Number</th>
	                <th class="ws">Name</th>
	                <th class="ws">Tracking No.</th>
	                <th class="ws">Address</th>
	                <th class="ws">Amount</th>
	                <th class="ws">Pallet Id</th>
	                <th class="ws">Invoiced</th>
	                <th class="ws">Date Invoiced</th>
	                <th class="ws">Invoice Number</th>
	                <th class="ws">BoxTop ref. number</th>
	            </tr>
	        </thead>
	        <tbody>
	        	@forelse($orders as $row)
	        		<tr>
	                    <td class="ws" style="white-space:nowrap;">
	                        <a href="{{url('client/'.$row->id.'/edit-order')}}" class="btn btn-edit"><i class="fa fa-edit"></i></a>
                            <a class="btn btn-edit" href="{{ route('client.order.invoice', $row->id) }}" target="_blank">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </a>
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
	                    <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status(get_post_extra($row->id, 'order_status'))) }}"> {{ inception_status(get_post_extra($row->id, 'order_status')) }} </span></td>
	                    <td class="ws">{!! get_post_extra($row->id, 'reference_number') ?? $row->id !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'evtn_number') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'customer_name') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'tracking_number') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'customer_address_line_1') !!} {!! get_post_extra($row->id, 'customer_address_line_2') !!} {!! get_post_extra($row->id, 'customer_city') !!} {!! get_post_extra($row->id, 'customer_state') !!} {!! get_post_extra($row->id, 'customer_pincode') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'currency') !!} {!! get_post_extra($row->id, 'value') !!}</td>
	                    <td class="ws">{{ $row->pallet_id ?? '' }}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'invoiced') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'date_invoiced') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'invoice_number') !!}</td>
	                    <td class="ws">{!! get_post_extra($row->id, 'box_number') !!}</td>
	                </tr>
	            @empty
	            @endforelse
	        </tbody>
	    </table>
	</div>
</div>