{{-- parcel level --}}
<div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
    <button type="button" class="btn btn-sm btn-red pull-left mb-1 mt-1" id="parcel-excel-btn">Export To Excel</button>
    <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm">
        <thead>
            <tr>
                <th>S no.</th>
                <th class="not-export-column">Action</th>
                <th>Request Date</th>
                <th>Client Name</th>
                <th>Source</th>
                <th>Source Name</th>
                <th>Return Status</th>
                <th>InScan Date</th>
                <th>Refund Trigger</th>
                <th>Refund Value</th>
                <th>Return Option</th>
                <th>Order ID</th>
                <th>Customer Order ID</th>
                <th>Customer Name</th>
                <th>No. Of Attempts</th>
                <th>Email</th>
                <th>Country</th>
                <th>Carrier</th>
                <th>Expected Time of Delivery</th>
                <th>Tracking ID</th>
                <th>Warehouse</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1 @endphp
            @forelse($lists as $row)
                @php
                    $meta = getMetaKeyValye($row);
                    $rtn_option = 'N/A';
                    if(isset($meta['_drop_off']) && $meta['_drop_off'] == 'By_ReturnBar'){
                        $rtn_option = 'By Return Bar™';
                    } else {
                        $rtn_option = str_replace('_', ' ', $meta['_drop_off'] ?? '') ?? "N/A";
                    }

                    $inscan_date = $meta['_order_waywill_status_date'] ?? '';
                    if(!empty($inscan_date)){
                        $inscan_date = date('d/m/Y',strtotime($inscan_date));
                    }

                    $track_id = $row->tracking_id;
                    if(empty($track_id) && isset($meta['_generate_waywill_status'])){
                        $tracking_data = json_decode($meta['_generate_waywill_status']);
                        if($tracking_data){
                            $track_id = $tracking_data->carrierWaybill ?? 'N/A';
                        }
                    }

                    $total = 0;
                    foreach($row->packages as $pakage){
                        $total += $pakage->price;   
                    }

                    if($row->client->client_type == '1'){
                        $dte = dt($row->created_at);
                    } else {
                        $dte = date('d/m/Y h:i:s a',strtotime($row->created_at));
                    }

                    $carr = $meta['_carrier_name'] ?? '';
                    $ship = $meta['_shipment_name'] ?? '';
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if($row->status == 'Pending')
                            <a class="btn btn-edit btn-primary" href="{{ route('new-reverse-logistic.edit',$row->id) }}" title="Edit">
                                <i class="la la-edit"></i>
                            </a>
                            <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.show',$row->id) }}" title="View">
                                <i class="la la-eye"></i>
                            </a>
                        @else
                            <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.view',$row) }}" title="View">
                                <i class="la la-eye"></i>
                            </a>
                        @endif
                    </td>
                    <td>{{ $dte }}</td>
                    {{-- <td>{{ date('d/m/Y h:i:s a',strtotime($row->created_at)) }}</td> --}}
                    <td>{{ $row->client->name ?? 'N/A' }}</td>
                    <td>{{ $meta['_source'] ?? 'N/A' }}</td>
                    <td>{{ $meta['_source_name'] ?? 'N/A' }}</td>
                    <td>{{ getOrderType($row) }}</td>
                    </td>
                    <td>
                        @if(isset($meta['_order_waywill_status_date']))
                            {{ date('d/m/Y',strtotime($meta['_order_waywill_status_date'])) }}
                        @endif
                    </td>
                    <td>
                        @if(isset($meta['_order_waywill_status']))
                            {{ getTriggerValue($row->id, '_order_waywill_status') }}
                        @endif
                    </td>
                    <td>{{ $meta['_currency'] ?? '$' }} {{ $meta['_rtn_total'] ?? $total }}</td>
                    <td>{{ $rtn_option }}</td>
                    <td>{{ $row->rg_reference_number ?? $row->id }}</td>
                    <td>{{ str_replace("#","",$row->way_bill_number)  }}</td>
                    <td>{{ $meta['_customer_name'] ?? '' }}</td>
                    <td>{{ getFailedReturnOrders($row->way_bill_number) }}</td>
                    <td>{{ $meta['_customer_email'] ?? '' }}</td>
                    {{-- <td>{{ date('d/m/Y h:i:s a',strtotime($row->created_at)) }}</td> --}}
                    <td>{{ $meta['_customer_country'] ?? '' }}</td>
                    <td>{{ $meta['_carrier_name'] ?? '' }}</td>
                    <!-- <td>{{ $row->shippingPolicy->carrier->name ?? $carr }}</td> -->
                    <td>{{ $row->shippingPolicy->shippingType->name ?? $ship }}</td>
                    <td>{{ $track_id }}</td>
                    <td>{{ $meta['_consignee_name'] ?? 'N/A' }}</td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
    @if(!empty($lists))
        <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
    @endif
</div>