@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<style type="text/css">
#confirmBox
{
    display: none;
    background-color: #eee;
    border-radius: 5px;
    border: 1px solid #aaa;
    z-index: 41;
    width: 350px;
    margin: 0 auto;
    padding: 15px 8px 20px;
    box-sizing: border-box;
    text-align: center;
    position: fixed;
    top: calc(50% - 25px);
    left: calc(50% - 50px);
}
#confirmBox .button {
    background-color: #ccc;
    display: inline-block;
    border-radius: 3px;
    border: 1px solid #aaa;
    padding: 2px;
    text-align: center;
    width: 80px;
    cursor: pointer;
}
#confirmBox .button:hover
{
    background-color: #ddd;
}
#confirmBox .message
{
    margin-bottom: 8px;
}
</style>
@endpush

<div class="app-contents contents"> 
    <div class="content-wrapper">
        @include('pages-message.notify-msg-error')
        @include('pages-message.notify-msg-success')

        <div class="row">
            <div class="col-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h5 class="box-title">Pallet Detail</h5>
                        <h4 class="card-title">
                            @if($pallet->pallet_type == 'Closed')
                                <a href="{{ route('client.closedpallet.list') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ route('client.pallet.list') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            @endif

                            @if($pallet->pallet_type == 'Closed')
                                {{-- <a href="javascript:void(0);" class="btn btn-red btn-sm" id="frm-sbt">
                                    <i class="la la-arrow-up"></i> Generate Custom Manifest
                                </a> --}}
                            @endif
                        </h4>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" id="product-form">
                                @csrf
                                <input type="hidden" name="pallet_id" id="pallet_id" value="{{ $pallet->pallet_id }}">
                            </form>
                            <form action="{{ route('client.pallet.update') }}" method="post" class="form-horizontal" autocomplete="off" id="pallet-update">
                                @csrf
                                <input type="hidden" name="p_id" value="{{ $pallet->id }}">
                                <div class="card1">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <label for="">Pallet Name</label>
                                            <input type="text" name="pallet_id" value="{{ $pallet->pallet_id }}" class="form-control" placeholder="Pallet Name">
                                            @error('name')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Order Number</label>
                                            <input type="text" name="order_no" value="" class="form-control" placeholder="Order Number">
                                            @error('order_no')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">From Warehouse</label>
                                            <select name="fr_warehouse_id" id="client_warehouse_list" class="form-control">
                                                <option value="">-- Select --</option>
                                                @php $fr = $pallet->meta->fr_warehouse_id ?? '' @endphp 
                                                @forelse($warehouse as $wh)
                                                    <option value="{{ $wh->id }}" @if($pallet->meta->fr_warehouse_id == $wh->id) selected="selected" @endif>{{ $wh->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('warehouse_id')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">To Warehouse</label>
                                            <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                <option value="">-- Select --</option>
                                                @forelse($warehouse as $wh)
                                                    <option value="{{ $wh->id }}" @if($pallet->warehouse_id == $wh->id) selected="selected" @endif>{{ $wh->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('warehouse_id')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Shipment Type</label>
                                            <select name="shipping_type_id" id="client_shipment_list" class="form-control">
                                                <option value="">-- Select --</option>
                                            </select>
                                            @error('shipping_type_id')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Freight Buy Rate</label>
                                            <input type="text" class="form-control" name="fright_charges" value="{{ $pallet->fright_charges }}">
                                            @error('fright_charges')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Custom Duty</label>
                                            <input type="text" class="form-control" name="custom_duty" value="{{ $pallet->custom_duty }}">
                                            @error('custom_duty')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Sell Rate</label>
                                            <input type="text" class="form-control" name="rate" value="{{ $pallet->rate }}">
                                            @error('rate')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Carrier</label>
                                            <input type="text" class="form-control" name="carrier" value="{{ $pallet->carrier }}">
                                            @error('carrier')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Weight of shipment</label>
                                            <input type="text" class="form-control" name="weight_of_shipment" value="{{ $pallet->weight_of_shipment }}">
                                            @error('weight_of_shipment')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-3 collapse">
                                            <label for="">Weight Unit Type</label>
                                            <select class="form-control" name="weight_unit_type">
                                                <option value="LBS" @if($pallet->weight_unit_type == 'LBS') selected="selected" @endif>LBS</option>
                                                <option value="KGS" @if($pallet->weight_unit_type == 'KGS') selected="selected" @endif>KGS</option>
                                            </select>
                                            @error('weight_unit_type')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="">Pallet Type</label>
                                            <select class="form-control" name="return_type">
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}" @if($pallet->return_type == $code) selected="selected" @endif>{{ $code }}</option>
                                                @endforeach
                                                {{-- <option value="Charity" @if($pallet->return_type == 'Charity') selected="selected" @endif>Charity</option>
                                                <option value="Discrepency" @if($pallet->return_type == 'Discrepency') selected="selected" @endif>Discrepency</option>
                                                <option value="Restock" @if($pallet->return_type == 'Restock') selected="selected" @endif>Restock</option>
                                                <option value="Resell" @if($pallet->return_type == 'Resell') selected="selected" @endif>Resell</option>
                                                <option value="Return" @if($pallet->return_type == 'Return') selected="selected" @endif>Return</option>
                                                <option value="Redirect" @if($pallet->return_type == 'Redirect') selected="selected" @endif>Redirect</option>
                                                <option value="Recycle" @if($pallet->return_type == 'Recycle') selected="selected" @endif>Recycle</option>
                                                <option value="Other"  @if($pallet->return_type == 'Other') selected="selected" @endif>Other</option>
                                                <option value="Short Shipment" @if($pallet->return_type == 'Short Shipment') selected="selected" @endif>Short Shipment</option> --}}
                                            </select>
                                            @error('pallet_type')
                                                <small class="error">The field is required</small>
                                            @enderror
                                        </div>

                                        @if($pallet->pallet_type == 'Closed')
                                            {{-- <div class="form-group col-md-3">
                                                <label for="">RRP Price</label>
                                                <input type="text" class="form-control" name="rrp_price" value="{{ $pallet->meta->rrp_price ?? '' }}">
                                                @error('rrp_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div> --}}

                                            <div class="form-group col-md-3">
                                                <label for="">Preferred Listing Price</label>
                                                <input type="text" class="form-control" name="pl_price" value="{{ $pallet->meta->pl_price ?? '' }}">
                                                @error('pl_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Preferred Listing Price %</label>
                                                <input type="text" class="form-control" name="ppl_price" value="{{ $pallet->meta->ppl_price ?? '' }}">
                                                @error('ppl_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            {{-- <div class="form-group col-md-3">
                                                <label for="">Authorised By</label>
                                                <input type="text" class="form-control" name="authorised_by" value="{{ $pallet->authorised_by ?? '' }}">
                                                @error('authorised_by')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div> --}}

                                            <div class="form-group col-md-3">
                                                <label for="">Close Pallet Status</label>
                                                <select class="form-control" name="pallet_status">
                                                    <option value="">-- Select ---</option>
                                                    <option value="Under Review" @if($pallet->pallet_status == 'Under Review') selected="selected" @endif>Under Review - Ecom</option>
                                                    <option value="Ready for Price" @if($pallet->pallet_status == 'Ready for Price') selected="selected" @endif>Ready for Price - Ecom</option>
                                                    <option value="Price done" @if($pallet->pallet_status == 'Price done') selected="selected" @endif>Price done - eBay</option>
                                                    <option value="Passed for Liquidation" @if($pallet->pallet_status == 'Passed for Liquidation') selected="selected" @endif>Passed for Liquidation - Ecom</option>
                                                </select>
                                                @error('pallet_type')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Date</label>
                                                <input type="text" class="form-control" name="pallet_date" value="{{ $pallet->pallet_date ?? '' }}">
                                                @error('pallet_date')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>
                                        @endif
                                        
                                        <input type="hidden" name="pallet_type" id="pallet_type" value="">                            
                                        <div class="form-group col-md-1">
                                            <button type="submit" class="btn btn-blue save-client">Save</button>
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <button type="button" class="btn btn-blue" id="ship-pallet">Ship Pallet</button>
                                        </div> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
            	            <div class="rg-pack-table">
                                <div class="alert alert-primary">
                                    @if(count($orders)>0)
                                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                    @endif
                                </div>
                                <div id="confirmBox">
                                    <div class="message"></div>
                                    <span class="btn btn-success btn-sm yes">OK</span>
                                    <span class="btn btn-danger btn-sm no">No I don’t want to close the Pallet</span>
                                </div>
                            	<div class="table-responsive booking-info-box">
                            		<form action="{{ route('admin.remove.pallet.orders') }}" method="post" id="move-warehouse">
            	                		@csrf
            	                		<div class="row ml-1" id="ex-pallet"></div>
            		                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
            		                        <thead>
            		                            <tr>
                                                    <th class="ws">Date</th>
                                                    <th class="ws">Ref. Number</th>
                                                    <th class="ws">EVTN Number</th>
                                                    <th class="ws">Item Description</th>
                                                    <th class="ws">Item Brand</th>
                                                    <th class="ws">Item Price</th>
                                                    <th class="ws">Original Sales Incoterm</th>
                                                    <th class="ws">Buyer Country</th>
                                                    <th class="ws">Hs Code</th>
                                                    <th class="ws">COO</th>
                                                    <th class="ws">SC Main Category</th>
                                                    <th class="ws">Category Tier 1</th>
                                                    <th class="ws">Category Tier 2</th>
                                                    <th class="ws">Category Tier 3</th>
                                                    <th class="ws">Level</th>
                                                    <th class="ws">Received Condition</th>
                                                    <th class="ws">Listing Condition</th>
                                                    <th class="ws">Inspection Status</th>
            		                            </tr>
            		                        </thead>
            		                        <tbody>
            		                        	@forelse($orders as $row)
                                                    @php
                                                        $brand = 'N/A';
                                                        if(isset($row['packages'][0]['package_data'])){
                                                            $attributes = json_decode($row['packages'][0]['package_data']);
                                                            if(isset($attributes->itemAttributes) && count($attributes->itemAttributes)){
                                                                foreach($attributes->itemAttributes as $attr){
                                                                    if($attr->name == 'Brand'){
                                                                        $brand = $attr->value;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    @endphp
            		                        		<tr>
                                                        <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row['_order_date'])) !!}</td>
                                                        <td class="ws">
                                                            {!! $row['reference_number'] ?? $row['_post_id'] !!}
                                                        </td>
                                                        <td class="ws">{!! $row['evtn_number'] ?? '' !!}</td>
                                                        <td class="ws">{!! $row['packages'][0]['title'] ?? '' !!}</td>
                                                        <td class="ws">{!! $brand !!}</td>
                                                        <td class="ws">{!! $row['currency'] ?? '' !!} {!! $row['value'] ?? '' !!}</td>
                                                        <td class="ws">{!! $row['packages'][0]['serviceName'] ?? '' !!}</td>
                                                        <td class="ws">{!! $row['customer_country'] ?? '' !!}</td>
                                                        <td class="ws">
                                                            {{ $row['hs_code'] ?? '' }}
                                                        </td>
                                                        <td class="ws">{{ $row['coo'] ?? '' }}</td>
                                                        <td class="ws">
                                                            @if(isset($row['category_name'])) {!! getCategoryName($row['category_name'], 'main') !!} @endif
                                                        </td>
                                                        <td class="ws">
                                                            @if(isset($row['sub_category_name'])) {!! getCategoryName($row['sub_category_name']) !!} @endif
                                                        </td>
                                                        <td class="ws">
                                                            @if(isset($row['packages'][0]['sub_category_2'])) {!! getCategoryName($row['packages'][0]['sub_category_2']) !!} @endif
                                                        </td>
                                                        <td class="ws">
                                                            @if(isset($row['packages'][0]['sub_category_3'])) {!! getCategoryName($row['packages'][0]['sub_category_3']) !!} @endif
                                                        </td>
                                                        <td class="ws">
                                                            {{ $row['in_level'] ?? '' }}
                                                        </td>
                                                        <td class="ws">
                                                            @if(isset($row['packages'][0]['received_condition']) && !empty($row['packages'][0]['received_condition']))
                                                                {{ $row['packages'][0]['received_condition'] ?? '' }}
                                                            @endif
                                                        </td>
                                                        <td class="ws">
                                                            {{ $row['packages'][0]['condition'] ?? '' }}
                                                        </td>
                                                        <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($row['order_status'])) }}"> {{ inception_status($row['order_status']) }} </span></td>
                                                    </tr>
            		                            @empty
            		                            @endforelse
            		                        </tbody>
            		                    </table>
            		                </form>
                            	</div>
                            	<div class="box-footer">
                            	    <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                            	</div>
                            </div>
                        </div>   
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).on('click',"#remove-pallet", function(){
            if($('.selectone:checkbox:checked').length < 1) {
                alert('Please select at least one checkbox');
                return false;
            } else {
                $("#move-warehouse").submit();
                // $(".pallet-div").toggle();
            }
        });

        $(document).on('click',"#ship-pallet", function(e){
            e.preventDefault();
            doConfirm("You will not be able to add additional items if you ship the pallet. Please confirm ok to proceed?", function yes() {
                // alert('yes');
                $('#pallet_type').val('Shipped')
                $('#pallet-update').submit();
            }, function no() {
                // alert('no');
            });        
        });

        function doConfirm(msg, yesFn, noFn) {
            var confirmBox = $("#confirmBox");
            confirmBox.find(".message").text(msg);
            confirmBox.find(".yes,.no").unbind().click(function () {
                confirmBox.hide();
            });
            confirmBox.find(".yes").click(yesFn);
            confirmBox.find(".no").click(noFn);
            confirmBox.show();
        }
    </script>
@endpush