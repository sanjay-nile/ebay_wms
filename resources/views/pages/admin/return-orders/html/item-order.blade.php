<div class="tab-pane fade" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
    <div class="text-left">
        <button type="button" id="add-to-warehouse" class="btn btn-red btn-sm mb-1">Add to Warehouse</button>
        <button type="button" class="btn btn-sm btn-blue pull-left mb-1 mr-1" id="item-excel-btn">Export To Excel</button>
    </div>
    <form action="{{ route('process.orders') }}" method="post" id="process-save">
        @csrf
        <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm">
            <thead>
                <tr>
                    <th><input name="select_all" value="1" id="select-all" type="checkbox" /></th>
                    <th>S no.</th>
                    <th class="not-export-column">Action</th>
                    <th>Request Date</th>
                    <th>Client Name</th>
                    <th>Source</th>
                    <th>Source Name</th>
                    <th>Return Status</th>
                    <th>InScan Date</th>
                    <th>Exception</th>
                    <th>Return Option</th>
                    <th>RG Order ID</th>
                    <th>Customer Order ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>                    
                    <th>Reason of Return</th>
                    <th>Sku #</th>
                    <th>Item Price #</th>
                    <th>Country</th>
                    <th>Carrier</th>
                    <th>Expected Time of Delivery</th>
                    <th>Tracking ID</th>
                    <th>Package Weight</th>
                    <th>Package Dimensions</th>
                    <th>Warehouse</th>
                    <th>MAWB #</th>
                    {{-- <th>Delivery Date</th> --}}
                    <th>HS Code</th>
                    <th>Origin Country</th>
                    <th>Pallet ID</th>
                    <th>Pallet Status</th>
                    <th>Shipment Status</th>
                    <th>Delivery Date</th>
                    <th>Delivery Time</th>
                    <th>Delivery Signature</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @forelse($lists as $row)
                    @forelse($row->packages as $pakage)
                        @php
                            $pallet = $pakage->pallet;
                            $meta = getMetaKeyValye($row);
                            // $p_meta = getMetaKeyValye($pallet);
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

                            if($row->client->client_type == '1'){
                                $dte = dt($row->created_at);
                            } else {
                                $dte = date('d/m/Y h:i:s a',strtotime($row->created_at));
                            }

                            $carr = $meta['_carrier_name'] ?? '';
                            $ship = $meta['_shipment_name'] ?? '';
                        @endphp
                        <tr>                                                                
                            <td>
                                @php $chk = $row->id.'-'.$pakage->id; @endphp
                                @if (isset($meta['_order_waywill_status']) && $row->status == 'Success' && $row->process_status == 'unprocessed')
                                    <input name="order_ids[]" type="checkbox" class="selectone" value="{{ $chk }}">
                                @elseif ($row->status == 'Success' && $row->process_status == 'unprocessed')
                                    <input name="order_ids[]" type="checkbox" class="selectone" value="{{ $chk }}">
                                @endif
                            </td>
                            <td>{{ $i++ }}</td>
                            <td>
                                @if($row->status == 'Pending')
                                    <a class="btn btn-edit btn-primary" href="{{ route('new-reverse-logistic.edit',$row) }}" title="Edit">
                                        <i class="la la-edit"></i>
                                    </a>
                                    <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.show',$row) }}" title="View">
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
                            <td>
                                @if(isset($meta['_order_waywill_status_date']))
                                    {{ date('d/m/Y',strtotime($meta['_order_waywill_status_date'])) }}
                                @endif
                            </td>
                            <td>
                                @if(isset($meta['_waiver']))
                                    {!! displayWaiver($meta['_waiver']) !!}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $rtn_option }}</td>
                            <td>{{ $row->rg_reference_number ?? $row->id }}</td>
                            <td>{{ $row->way_bill_number }}</td>
                            <td>{{ $meta['_customer_name'] }}</td>
                            <td>{{ $meta['_customer_email'] }}</td>  
                            @if($row->client->client_type == '1')
                                <td>{{ displayOliveReason($pakage->return_reason) }}</td>
                            @else
                                <td>{{ displayMissguidedReason($pakage->return_reason) }}</td>
                            @endif
                            <td>{{ $pakage->bar_code }}</td>
                            <td>{{ $meta['_currency'] ?? '' }} {{ $pakage->price }}</td>
                            <td>{{ $meta['_customer_country'] }}</td>
                            <td>{{ $row->shippingPolicy->carrier->name ?? $carr }}</td>
                            <td>{{ $row->shippingPolicy->shippingType->name ?? $ship }}</td>
                            <td>
                                {{ $track_id }}
                            </td>
                            <td>{{ $pakage->weight }}</td>
                            <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
                            <td>{{ $meta['_consignee_name'] ?? 'N/A' }}</td>
                            <td>{{ $pallet->mawb_number ?? 'N/A' }}</td>
                            {{-- <td>{{ $pallet->meta->delivery_date ?? 'N/A' }}</td>                             --}}
                            <td>{{ $pakage->hs_code }}</td>
                            <td>{{ $pakage->country_of_origin }}</td>
                            <td>{{ $pallet->pallet_id ?? '' }}</td>
                            <td>{{ $pallet->pallet_type ?? '' }}</td>
                            <td>{{ $pakage->shipment_status }}</td>
                            <td>{{ $pallet->meta->delivery_date ?? '' }}</td>
                            <td>{{ $pallet->meta->delivery_time ?? '' }}</td>
                            <td>{{ $pallet->meta->signed_by ?? '' }}</td>
                        </tr>
                    @empty
                    @endforelse
                @empty
                @endforelse
            </tbody>
        </table>
    </form>
    @if(!empty($lists))
        <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
    @endif
</div>