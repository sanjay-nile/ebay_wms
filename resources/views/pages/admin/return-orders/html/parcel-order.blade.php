<div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
    <button type="button" class="btn btn-sm btn-red pull-left mb-1" id="parcel-excel-btn">Export To Excel</button>
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
                <th>Email</th>
                <th>Country</th>
                <th>Carrier</th>
                <th>Expected Time of Delivery</th>
                <th>Tracking ID</th>
                <th>Warehouse</th>
                <th>Shipping BoxBarcode</th>
                {{-- <th>Shipment Update</th>
                <th>Incident Date</th>
                <th>Claim ID</th>
                <th>Action</th> --}}
            </tr>
        </thead>
        <tbody>
            @php $i=1 @endphp
            @forelse($lists as $order)
                @php
                    $meta = getMetaKeyValye($order);
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

                    $track_id = $order->tracking_id;
                    if(empty($track_id) && isset($meta['_generate_waywill_status'])){
                        $tracking_data = json_decode($meta['_generate_waywill_status']);
                        if($tracking_data){
                            $track_id = $tracking_data->carrierWaybill ?? 'N/A';
                        }
                    }

                    $sh = $cl = $dt = '';
                    if(isset($meta['shipment_status'])){
                        $sh = $meta['shipment_status'];
                    }
                    if(isset($meta['claim_id'])){
                        $cl = $meta['claim_id'];
                    }
                    if(isset($meta['shipment_date'])){
                        $dt = $meta['shipment_date'];
                    }

                    $total = 0;
                    foreach($order->packages as $pakage){
                        $total += $pakage->price;   
                    }

                    if($order->client->client_type == '1'){
                        $dte = dt($order->created_at);
                    } else {
                        $dte = date('d/m/Y h:i:s a',strtotime($order->created_at));
                    }

                    $carr = $meta['_carrier_name'] ?? '';
                    $ship = $meta['_shipment_name'] ?? '';
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if($order->status == 'Pending')
                            <a class="btn btn-edit btn-primary" href="{{ route('new-reverse-logistic.edit',$order) }}" title="Edit">
                                <i class="la la-edit"></i>
                            </a>
                            <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.show',$order) }}" title="View">
                                <i class="la la-eye"></i>
                            </a>
                        @else
                            <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.view',$order) }}" title="View">
                                <i class="la la-eye"></i>
                            </a>
                        @endif
                    </td>
                    <td>{{ $dte }}</td>
                    {{-- <td>{{ date('d/m/Y h:i:s a',strtotime($order->created_at)) }}</td> --}}
                    <td>{{ $order->client->name ?? 'N/A' }}</td>
                    <td>{{ $meta['_source'] ?? 'N/A' }}</td>
                    <td>{{ $meta['_source_name'] ?? 'N/A' }}</td>
                    <td>{{ getOrderType($order) }}</td>
                    <td>
                        @if(isset($meta['_order_waywill_status_date']))
                            {{ date('d/m/Y',strtotime($meta['_order_waywill_status_date'])) }}
                        @endif
                    </td>
                    <td>
                        @if(isset($meta['_order_waywill_status']))
                            {{ date('d/m/Y',strtotime(getTriggerValue($order->id, '_order_waywill_status'))) }}
                        @endif
                    </td>
                    <td>{{ $meta['_currency'] ?? '$' }} {{ $meta['_rtn_total'] ?? $total }}</td>
                    <!-- <td>{{ $meta['_currency'] ?? '$' }} {{ $order['rtn_total'] ?? $order['amount'] }}</td> -->
                    <td>{{ $rtn_option }}</td>
                    <td>{{ str_replace("#","",$order->rg_reference_number)  ?? str_replace("#","",$order->id) }}</td>
                    <td>{{ str_replace("#","",$order->way_bill_number) }}</td>
                    <td>{{ $meta['_customer_name'] }}</td>
                    <td>{{ $meta['_customer_email'] }}</td>
                    <td>{{ $meta['_customer_country'] }}</td>
                    <td>{{ $meta['_carrier_name'] }}</td>
                    <!-- <td>{{ $order->shippingPolicy->carrier->name ?? $carr }}</td> -->
                    <td>{{ $order->shippingPolicy->shippingType->name ?? $ship }}</td>
                    <td>{{ $track_id }}</td>
                    <td>{{ $meta['_consignee_name'] ?? 'N/A' }}</td>
                    {{-- @if($order->status == 'Pending')
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @else
                        @if($order->hasMeta('_order_waywill_status') && $order->status == 'Success' && $order->process_status == 'unprocessed')
                            <td>
                                <select name="shipment_status" id="{{ $order->id }}-shipment-status" class="">
                                    <option value="">-- select --</option>
                                    <option value="Lost" @if($sh == 'Lost') selected @endif>Lost</option>
                                    <option value="Damaged in transit" @if($sh == 'Damaged in transit') selected @endif>Damaged in transit</option>
                                    <option value="Destroyed" @if($sh == 'Destroyed') selected @endif>Destroyed </option>
                                    <option value="Undeliverable" @if($sh == 'Undeliverable') selected @endif>Undeliverable </option>
                                    <option value="Delivered" @if($sh == 'Delivered') selected @endif>Delivered </option>
                                </select>
                            </td>
                            <td><input type="text" name="shipment_date" id="{{ $order->id }}_shipment_date" value="{{ $dt }}" autocomplete="off"></td>
                            <td><input type="text" name="claim_id" id="{{ $order->id }}-claim-id" value="{{ $cl }}"></td>
                            <td><button type="button" data-id="{{ $order->id }}" class="btn btn-red btn-sm up-data">Update</button></td>
                        @else
                            @if($order->cancel_return_status != null)
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @else
                                @if($order->status == 'Success' && $order->process_status == 'unprocessed')
                                    <td>
                                        <select name="shipment_status" id="{{ $order->id }}-shipment-status" class="">
                                            <option value="">-- select --</option>
                                            <option value="Lost" @if($sh == 'Lost') selected @endif>Lost</option>
                                            <option value="Damaged in transit" @if($sh == 'Damaged in transit') selected @endif>Damaged in transit</option>
                                            <option value="Destroyed" @if($sh == 'Destroyed') selected @endif>Destroyed </option>
                                            <option value="Undeliverable" @if($sh == 'Undeliverable') selected @endif>Undeliverable </option>
                                            <option value="Delivered" @if($sh == 'Delivered') selected @endif>Delivered </option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="shipment_date" id="{{ $order->id }}_shipment_date" value="{{ $dt }}" autocomplete="off"></td>
                                    <td><input type="text" name="claim_id" id="{{ $order->id }}-claim-id" value="{{ $cl }}"></td>
                                    <td><button type="button" data-id="{{ $order->id }}" class="btn btn-red btn-sm up-data">Update</button></td>
                                @endif
                                @if($order->status == 'Success' && $order->process_status == 'processed')
                                    <td>
                                        <select name="shipment_status" id="{{ $order->id }}-shipment-status" class="">
                                            <option value="">-- select --</option>
                                            <option value="Lost" @if($sh == 'Lost') selected @endif>Lost</option>
                                            <option value="Damaged in transit" @if($sh == 'Damaged in transit') selected @endif>Damaged in transit</option>
                                            <option value="Destroyed" @if($sh == 'Destroyed') selected @endif>Destroyed </option>
                                            <option value="Undeliverable" @if($sh == 'Undeliverable') selected @endif>Undeliverable </option>
                                            <option value="Delivered" @if($sh == 'Delivered') selected @endif>Delivered </option>                                            
                                        </select>
                                    </td>
                                    <td><input type="text" name="shipment_date" id="{{ $order->id }}_shipment_date" value="{{ $dt }}" autocomplete="off"></td>
                                    <td><input type="text" name="claim_id" id="{{ $order->id }}-claim-id" value="{{ $cl }}"></td>
                                    <td><button type="button" data-id="{{ $order->id }}" class="btn btn-red btn-sm up-data">Update</button></td>
                                @endif
                            @endif
                        @endif
                    @endif --}}
                    <td>{{ $order->shippingBoxBarcode }}</td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
    @if(!empty($lists))
        <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
    @endif
</div>