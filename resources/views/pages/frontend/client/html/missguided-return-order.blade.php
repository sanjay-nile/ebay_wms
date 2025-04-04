<div class="row">
    <div class="col-xs-12 col-md-12 ">
        @if(!empty($lists))
    	   <div class="alert alert-info">Total Orders : <strong>{{ $lists->total() }}</strong></div>
        @endif
    	<form class="form-horizontal ml-1" id="filter-frm">
	        <div class="card client-card">
	            <div class="card-header">
	                <h2>Filters</h2>
	            </div>
	            <div class="card-body">
	                <div class="row">
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="order_type" class="form-control">
                                    <option value="">-- Select Return Type --</option>
                                    <option value="intransit" {{ (app('request')->input('order_type')=='intransit')?"selected":'' }}>Processed Returns</option>
                                    <option value="new" {{ (app('request')->input('order_type')=='new')?"selected":'' }}>Failed Label</option>
                                    <option value="inscan" {{ (app('request')->input('order_type')=='inscan')?"selected":'' }}>InScan Returns</option>
                                    <option value="cancel" {{ (app('request')->input('order_type')=='cancel')?"selected":'' }}>Cancelled Returns</option>
                                    <option value="at_hub" {{ (app('request')->input('order_type')=='at_hub')?"selected":'' }}>Received at Hub Returns</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="by_source" class="form-control">
                                    <option value="">-- Select By Source --</option>
                                    @forelse(getSource() as $k => $v)
                                        <option value="{!! $k !!}" {{ (app('request')->input('by_source')==$k)?"selected":'' }}>{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="by_csr" class="form-control">
                                    <option value="">-- Select By CSR --</option>
                                    @forelse(getSourceName(Auth::id()) as $k => $v)
                                        <option value="{!! $k !!}" {{ (app('request')->input('by_csr')==$k)?"selected":'' }}>{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="by_exception" class="form-control">
                                    <option value="">-- Select By Exception --</option>
                                    @forelse(getWaiver() as $k => $v)
                                        <option value="{!! $k !!}" {{ (app('request')->input('by_exception')==$k)?"selected":'' }}>{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="by_country" class="form-control">
                                    <option value="">-- Select By Country --</option>
                                    @forelse($country as $k => $v)
                                        <option value="{!! $v->sortname !!}" {{ (app('request')->input('by_country')==$v->sortname)?"selected":'' }}>{!! $v->name !!}</option>
                                    @empty
                                    @endforelse
                                </select>                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="by_warehouse" class="form-control">
                                    <option value="">-- Select By Warehouse --</option>
                                    @forelse(getWareHouse(Auth::id()) as $k => $v)
                                        <option value="{!! $k !!}" {{ (app('request')->input('by_warehouse')==$k)?"selected":'' }}>{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="refund_status" class="form-control">
                                    <option value="">-- Select Refund Status --</option>
                                    <option value="Yes" {{ (app('request')->input('refund_status')=='Yes')?"selected":'' }}>Yes</option>
                                    <option value="No" {{ (app('request')->input('refund_status')=='No')?"selected":'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        @if(in_array($client->client_type, ['1','2']))
                            <div class="col-md-3">
                                <div class="input-group">
                                    <select name="return_reson" class="form-control">
                                        <option value="">-- Select Reson Of Return --</option>
                                        @forelse(reason_of_return() as $k => $v)
                                            <option value="{!! $k !!}" {{ (app('request')->input('refund_status')==$k)?"selected":'' }}>{!! $v !!}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        @endif
	                </div>
	            </div>
	        </div>
	        <div class="card client-card">
	            <div class="card-header">
	                <h2>Search</h2>
	            </div>
	            <div class="card-body">
	                <div class="row">
	                    <div class="col-md-3">
	                        <div class="input-group mb-2">
	                            <input type="text" name="customer_name" class="form-control" placeholder="By Cust Name" value="{{ app('request')->input('customer_name') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="input-group mb-2">
	                            <input type="text" name="customer_email" class="form-control" placeholder="By Email ID" value="{{ app('request')->input('customer_email') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="input-group mb-2">
	                            <input type="text" name="way_bill_number" class="form-control" placeholder="Customer Order No." value="{{ app('request')->input('way_bill_number') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="input-group mb-2">
	                            <input type="text" name="sku" class="form-control" placeholder="#SKU" value="{{ app('request')->input('sku') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="input-group">
	                            <input type="text" name="hs_code" class="form-control" placeholder="By HS Code" value="{{ app('request')->input('hs_code') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="input-group">
	                            <input type="text" name="tracking_id" class="form-control" placeholder="By Tracking ID" value="{{ app('request')->input('tracking_id') }}" />
	                        </div>
	                    </div>
	                    <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="start" class="form-control" placeholder="From Date" value="{{ app('request')->input('start') }}" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="end" class="form-control" placeholder="To Date" value="{{ app('request')->input('end') }}" autocomplete="off" />
                            </div>
                        </div>
	                    <div class="col-md-6 mt-2">
                            <input type="hidden" name="export_to" id="export_to" value="">
	                        <button type="submit" class="btn btn-cyan btn-sm" id="search-btn"><i class="fa fa-search"></i> Search</button>
	                        <a href="{{ route('client.return.orders') }}" class="btn-refresh reset"><i class="la la-refresh"></i> Reset</a>
	                    </div>
	                </div>
	            </div>
	        </div>
    	</form>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <div class="card">
            <div class="card-content collapse show">
                <div class="card-body booking-info-box card-dashboard table-responsive">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active tab-inactive" id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="true">Parcel level</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tab-inactive" id="pills-item-tab" data-toggle="pill" href="#pills-item" role="tab" aria-controls="pills-item" aria-selected="false">Item level</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        {{-- parcel level --}}
                        <div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
                            <button type="button" class="btn btn-sm btn-danger pull-left mb-2 mt-2" id="parcel-excel-btn">Export To Excel</button>
                            <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th class="not-export-column">Action</th>
                                        <th>Source</th>
                                        <th>Source Name</th>
                                        <th>Order Type</th>
                                        <th>Return Option</th>
                                        <th>RG Order ID</th>
                                        <th>Customer Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th>Country</th>
                                        <th>Carrier</th>
                                        <th>Expected Time of Delivery</th>
                                        <th>Tracking ID</th>
                                        <th>Warehouse</th>
                                        {{-- <th>Attempts</th> --}}
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
                                                $rtn_option = str_replace('_', ' ', $meta['_drop_off']) ?? "N/A";
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

                                            $carr = $meta['_carrier_name'] ?? '';
                                            $ship = $meta['_shipment_name'] ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>
                                                @if($row->status == 'Pending')
                                                    <a class="btn btn-edit btn-primary" href="{{ route('reverse-logistic.edit',$row->id) }}" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-view btn-success" href="{{ route('new.waybill.detail',$row->id) }}" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-view btn-success" href="{{ route('waybill.detail',$row) }}" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $meta['_source'] ?? 'N/A' }}</td>
                                            <td>{{ $meta['_source_name'] ?? 'N/A' }}</td>
                                            <td>{{ getOrderType($row) }}</td>                                            
                                            <td>{{ $rtn_option }}</td>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->way_bill_number }}</td>
                                            <td>{{ $meta['_customer_name'] ?? '' }}</td>
                                            <td>{{ $meta['_customer_email'] ?? '' }}</td>
                                            <td>{{ date('d/m/Y h:i:s a',strtotime($row->created_at)) }}</td>
                                            <td>{{ $meta['_customer_country'] ?? '' }}</td>
                                            <td>{{ $order->shippingPolicy->carrier->name ?? $carr }}</td>
                                            <td>{{ $order->shippingPolicy->shippingType->name ?? $ship }}</td>
                                            <td>{{ $track_id }}</td>
                                            <td>{{ $meta['_consignee_name'] ?? 'N/A' }}</td>
                                            {{-- <td>{{ getFailedReturnOrders($row->way_bill_number) }}</td> --}}
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            @if(!empty($lists))
                                <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
                            @endif
                        </div>
                        
                        {{-- item level --}}
                        <div class="tab-pane fade" id="pills-item" role="tabpanel" aria-labelledby="pills-item-tab">
                            <button type="button" class="btn btn-sm btn-danger pull-left mb-2 mt-2" id="item-excel-btn">Export To Excel</button>
                            <table id="client_user_list" class="table table-striped table-bordered nowrap table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th class="not-export-column">Action</th>
                                        <th>Source</th>
                                        <th>Source Name</th>
                                        <th>Order Type</th>
                                        <th>Exception</th>
                                        <th>Return Option</th>
                                        <th>RG Order ID</th>
                                        <th>Customer Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th>Reason of Return</th>
                                        <th>Sku #</th>
                                        <th>Country</th>
                                        <th>Carrier</th>
                                        <th>Expected Time of Delivery</th>
                                        <th>Tracking ID</th>
                                        <th>Package Weight</th>
                                        <th>Package Dimensions</th>
                                        <th>Warehouse</th>
                                        {{-- <th>Estimated Value</th> --}}
                                        <th>HS Code</th>
                                        <th>Refunded Status</th>
                                        <th>Confirm Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @forelse($lists as $row)
                                        @forelse($row->packages as $pakage)
                                            @php
                                                $meta = getMetaKeyValye($row);
                                                $rtn_option = 'N/A';
                                                if(isset($meta['_drop_off']) && $meta['_drop_off'] == 'By_ReturnBar'){
                                                    $rtn_option = 'By Return Bar™';
                                                } else {
                                                    $rtn_option = str_replace('_', ' ', $meta['_drop_off']) ?? "N/A";
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
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    @if($row->status == 'Pending')
                                                        <a class="btn btn-edit btn-primary" href="{{ route('reverse-logistic.edit',$row->id) }}" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a class="btn btn-view btn-success" href="{{ route('new.waybill.detail',$row->id) }}" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @else
                                                        <a class="btn btn-view btn-success" href="{{ route('waybill.detail',$row) }}" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $meta['_source'] ?? 'N/A' }}</td>
                                                <td>{{ $meta['_source_name'] ?? 'N/A' }}</td>
                                                <td>{{ getOrderType($row) }}</td>
                                                <td>
                                                    @if(isset($meta['_waiver']))
                                                        {!! displayWaiver($meta['_waiver']) !!}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $rtn_option }}</td>
                                                <td>{{ $row->id }}</td>
                                                <td>{{ $row->way_bill_number }}</td>
                                                <td>{{ $meta['_customer_name'] ?? '' }}</td>
                                                <td>{{ $meta['_customer_email'] ?? '' }}</td>
                                                <td>{{ date('d/m/Y h:i:s a',strtotime($row->created_at)) }}</td>
                                                <td>{{ displayMissguidedReason($pakage->return_reason) }}</td>
                                                <td>{{ $pakage->bar_code }}</td>
                                                <td>{{ $meta['_customer_country'] ?? '' }}</td>
                                                <td>{{ $row->shippingPolicy->carrier->name ?? $carr }}</td>
                                                <td>{{ $row->shippingPolicy->shippingType->name ?? $ship }}</td>
                                                <td>{{ $track_id }}</td>
                                                <td>{{ $pakage->weight }}</td>
                                                <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
                                                <td>{{ $meta['_consignee_name'] ?? 'N/A' }}</td>
                                                <td>{{ $pakage->hs_code }}</td>
                                                <td>{{ $pakage->refund_status }}</td>
                                                <td>{{ $pakage->status }}</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            @if(!empty($lists))
                                <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
                            @endif
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>