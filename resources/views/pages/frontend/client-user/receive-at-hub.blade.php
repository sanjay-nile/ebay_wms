@include('pages.frontend.client-user.breadcrumb', ['title' => 'Received at Hub Returns'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});    

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });
});
</script>
@endpush

<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <div class="alert alert-info">Total Orders : <strong>{{ $lists->total() }}</strong></div>
        <form class="form-horizontal fiter-form ml-1">
            <div class="card client-card">
                <div class="card-header">
                    <h2>Filters</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <select name="by_source" class="form-control">
                                    <option value="">-- Select By Source --</option>
                                    @forelse(getSource() as $k => $v)
                                        <option value="{!! $k !!}">{!! $v !!}</option>
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
                                        <option value="{!! $k !!}">{!! $v !!}</option>
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
                                        <option value="{!! $k !!}">{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group mb-2">
                                <input type="text" name="By Country" class="form-control" placeholder="By Country" value="" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="by_warehouse" class="form-control">
                                    <option value="">-- Select By Warehouse --</option>
                                    @forelse(getWareHouse($client->created_by) as $k => $v)
                                        <option value="{!! $k !!}">{!! $v !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="refund_status" class="form-control">
                                    <option value="">-- Select Refund Status --</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                        @php
                            $rtn_reason = reason_of_return();
                            if($client->client_type == '1'){
                                $rtn_reason = olive_reason_of_return();
                            }
                        @endphp
                        @if(in_array($client->client_type, ['1','2']))
                            <div class="col-md-3">
                                <div class="input-group">
                                    <select name="return_reson" class="form-control">
                                        <option value="">-- Select Reson Of Return --</option>
                                        @forelse($rtn_reason as $k => $v)
                                            <option value="{!! $k !!}">{!! $v !!}</option>
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
                            <button type="submit" class="btn btn-cyan btn-sm" id="search-btn"><i class="fa fa-search"></i> Search</button>
                            <a href="{{ route('client-user.receive-at-hub') }}" class="btn-refresh reset"><i class="la la-refresh"></i> Reset</a>
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
            {{-- <div class="card-header avn-card-header">
                <div class="alert alert-info">Total Orders : <strong>{{ $lists->total() }}</strong></div>
                <form class="form-horizontal fiter-form ml-1">
                    <div class="row">                        
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" name="way_bill_number" class="form-control" placeholder="Way Bill Number" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" name="start" class="form-control" placeholder="From Date" value="{{ app('request')->input('start') }}" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" name="end" class="form-control" placeholder="To Date" value="{{ app('request')->input('end') }}" autocomplete="off" />
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-cyan" id="search-btn"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div> --}}
            <div class="card-content collapse show">
                <div class="card-body booking-info-box card-dashboard table-responsive">
                    <table id="client_user_list" class="table table-striped table-hover nowrap avn-defaults table-sm">
                        <thead>
                            <tr>
                                <th>S no.</th>
                                <th class="not-export-column">Action</th>
                                <th>Return Option</th>
                                <th>RG Order No.</th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Return Request Date</th>
                                <th>Rcvd At Hub Date</th>
                                <th>Sku #</th>
                                <th>Carrier</th>
                                <th>Shipment Type</th>
                                <th>Tracking ID</th>
                                <th>Package Weight</th>
                                <th>Package Dimensions</th>
                                <th>Shipping Rate</th>
                                <th>Estimated Value</th>
                                <th>HS Code</th>
                                <th>Confirm Action</th>                                
                                <th>Refunded Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1 @endphp
                            @forelse($lists as $row)
                                @forelse($row->packages as $pakage)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>
                                            <a class="btn btn-view btn-success" href="{{ route('waybill.detail',$row) }}" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if($row->hasMeta('_drop_off') && $row->meta->_drop_off == 'By_ReturnBar')
                                                By Return Bar™
                                            @else
                                                {{ str_replace('_', ' ', $row->meta->_drop_off) ?? "N/A" }}
                                            @endif
                                        </td>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->way_bill_number }}</td>
                                        <td>{{ $row->meta->_customer_name }}</td>
                                        <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                        <td>{{ ($row->rcvd_at_hub_date) ? date('d/m/Y',strtotime($row->rcvd_at_hub_date)) : '' }}</td>
                                        <td>{{ $pakage->bar_code }}</td>
                                        {{-- <td>{{ $row->meta->_carrier_name }}</td>
                                        <td>{{ $row->meta->_shipment_name}}</td> --}}
                                        <td>{{ $row->shippingPolicy->carrier->name ?? 'N/A' }}</td>
                                        <td>{{ $row->shippingPolicy->shippingType->name ?? 'N/A' }}</td>
                                        <td>
                                            <?php
                                                $track_id = 'N/A';
                                                $tracking_detail = ($row->meta->_generate_waywill_status)?? NULL; 
                                                $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                                if($tracking_data){
                                                    $track_id = $tracking_data->carrierWaybill ?? 'N/A';             
                                                }
                                            ?>
                                            {{ $track_id }}
                                        </td>
                                        <td>{{ $pakage->weight }}</td>
                                        <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
                                        <td>{!! ($row->meta->_currency) ? get_currency_symbol($row->meta->_currency): get_currency_symbol('USD') !!} {{ (is_numeric($row->meta->_rate)) ? $row->meta->_rate : 0}}</td>
                                        <td>{{ $pakage->estimated_value }}</td>
                                        <td>{{ $pakage->hs_code }}</td>
                                        <td>{{ $pakage->status }}</td>
                                        <td>{{ $pakage->refund_status }}</td>
                                    </tr>
                                @empty
                                @endforelse                                
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>