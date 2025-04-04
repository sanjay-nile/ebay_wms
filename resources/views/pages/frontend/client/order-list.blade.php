@include('pages.frontend.client.breadcrumb', ['title' => 'All Package List'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/super-admin.css') }}"> --}}
<style type="text/css">
    .admin-data-table tr .ws {
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="shipment_date"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom left"
    });

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#add-to-warehouse").click(function () {
        if($('.selectone:checkbox:checked').length < 1) {
            alert('Please select at least one checkbox');
            return false;
        } else {
            $("#process-save").submit();
        }
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('change','.cat-list',function(){
        let id = $('.cat-list option:selected').attr('data-id');;
        $.ajax({
            type:'get',
            url : "{{ route('admin.fillter.sub.categories') }}",
            data:{cat_id:id},
            dataType : 'json',
            success : function(data){
                $(".sub-cat-list").replaceWith(data.html);
            }
        })
    });

    $("#dwn-btn").click(function () {
        $('#export_to').val('item-excel');
        $("#frm-sbmit").submit();
    });

    $(".search-btn").click(function () {
        $('#export_to').val('');
    });

    $("#parcel-excel-btn").click(function () {
        $('#export_to').val('parcel-excel');
        $("#frm-sbmit").submit();
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('item-excel');
        $("#frm-sbmit").submit();
    });
});
</script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
@endpush
@push('js')
    <script src="{{ asset('plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.assigncountry').select2({
              placeholder: 'Select Pallet Id',
              allowClear: true
            });
        })
    </script>
@endpush

{{-- @include('pages-message.notify-msg-error')
@include('pages-message.notify-msg-success')
@include('pages-message.form-submit') --}}

<div class="app-contents contents"> 
    <div class="content-wrapper ebay-content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Order List</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="frm-sbmit">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="evtn_number" value="{{ Request::get('evtn_number') }}" class="form-control" placeholder="EVTN Number">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="customer_name" value="{{ Request::get('customer_name') }}" class="form-control" placeholder="Customer Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="eq_id" value="{{ Request::get('eq_id') }}" class="form-control" placeholder="Ref. Id">
                                        </div>                                
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="tracking_number" value="{{ Request::get('tracking_number') }}" class="form-control" placeholder="Tracking Number">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="in_level" class="form-control" id="myselect">
                                                <option value="">-- Select Inspection Level --</option>
                                                <option value="L1">Level 1</option>
                                                <option value="L2">Level 2</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="price_from" value="{{ Request::get('price_from') }}" class="form-control" placeholder="Price From">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="price_to" value="{{ Request::get('price_to') }}" class="form-control" placeholder="Price To">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="warehouse_name" class="form-control">
                                                <option value="">-- Select Warehouse --</option>
                                                @forelse($Warehouse as $pid)
                                                    <option value="{{ $pid->id }}">{{ $pid->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="pallet_id" class="form-control select2 assigncountry">
                                                <option value="">-- Pallet Id --</option>
                                                @forelse($PalletDeatil as $pid)
                                                    <option value="{{ $pid->pallet_id }}">{{ $pid->pallet_id }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="return_reason" class="form-control" id="myselect">
                                                <option value="">-- Select Reason of Return --</option>
                                                @forelse($reason as $ror)
                                                    <option value="{{ $ror['reason_of_return'] }}">{{ $ror['reason_of_return'] }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="category_name" class="form-control cat-list">
                                                <option value="">--- Select SC Main Category ---</option>
                                                @forelse($categories as $cat)
                                                    <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list">
                                            <select name="sub_category_name" class="form-control">
                                                <option value="">--- Select Category Tier 1 ---</option>
                                                @forelse($sub_categories as $cat)
                                                    <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list">
                                            <select name="sub_category_name_1" class="form-control">
                                                <option value="">---Select Category Tier 2 ---</option>
                                                @forelse($sub_categories_2_tier as $cat)
                                                    <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3 sub-cat-list">
                                            <select name="sub_category_name_2" class="form-control">
                                                <option value="">---Select Category Tier 3 ---</option>
                                                @forelse($sub_categories_3_tier as $cat)
                                                    <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <select name="order_status" class="form-control">
                                                <option value=""> -- Inspection Status --</option>
                                                @forelse(inception_status() as $st => $sv)
                                                    <option value="{{ $st }}">{{$sv}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <select name="code" class="form-control">
                                                <option value="">-- Select Condition--</option>
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}" @if(isset($order['condition_code']) && $order['condition_code'] == $code)selected @endif>{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group  col-md-3">
                                            <select name="ovrsize" id="" class="form-control">
                                                <option value="">-- Oversized packages --</option>
                                                <option value="Yes" {{ (request('ovrsize') == 'Yes') ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ (request('ovrsize') == 'No') ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group  col-md-3">
                                            <select name="empty_box" id="" class="form-control">
                                                <option value="">-- Empty Box --</option>
                                                <option value="Yes" {{ (request('empty_box') == 'Yes') ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ (request('empty_box') == 'No') ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group  col-md-3">
                                            <select name="sales_incoterm" id="" class="form-control">
                                                <option value="">-- Original Sales Incoterm --</option>
                                                <option value="EXPORTS_DDU" {{ (request('sales_incoterm') == 'EXPORTS_DDU') ? 'selected' : '' }}>EXPORTS DDU</option>
                                                <option value="EXPORTS_DDP" {{ (request('sales_incoterm') == 'EXPORTS_DDP') ? 'selected' : '' }}>EXPORTS DDP</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <input type="hidden" name="export_to" id="export_to" value="">
                                            <a href="{{ route('client.order.list') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <div class="float-left">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active tab-inactive btn-sm" id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="true">Order level</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link tab-inactive btn-sm" id="pills-item-tab" data-toggle="pill" href="#pills-item" role="tab" aria-controls="pills-item" aria-selected="false">Item level</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
            </div>

            {{-- <div class="col-12">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="text-left mb-2">
                            <button class="btn btn-primary btn-sm" id="dwn-btn" type="button">
                                <i class="fa fa-download"></i> Generate Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            @include('pages.frontend.client.order.order-list')
                            @include('pages.frontend.client.order.item-list')
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                    </div>

                    {{-- <div class="card-header">
                        <div class="pallet-div ">
                            <button type="button" id="add-to-warehouse" class="btn btn-blue1">Add to Warehouse</button>
                        </div>
                    </div>
                    <div class="card-body">
        	            <div class="rg-pack-table">
                            <div class="alert alert-primary">
                                @if(count($orders)>0)
                                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                @endif
                            </div>
                        	<div class="table-responsive booking-info-box" style="padding: 0;">
                        		<form action="{{route('admin.return.orders')}}" method="post" id="process-save">
        	                		@csrf
        	                		<div class="row ml-1" id="ex-pallet"></div>
        		                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm">
        		                        <thead>
        		                            <tr>
        		                                <th class="ws">Action</th>
                                                <th class="ws">Certificate</th>
                                                <th class="ws">Date Received in Warehouse</th>
                                                <th class="ws">Inspection Status</th>
                                                <th class="ws">Ref. Number</th>
                                                <th class="ws">EVTN Number</th>
                                                <th class="ws">Name</th>
                                                <th class="ws">Tracking No.</th>
                                                <th class="ws">Original Sales Incoterm</th>
                                                <th class="ws">Address</th>
                                                <th class="ws">Amount</th>
                                                <th class="ws">Hs Code</th>
                                                <th class="ws">COO</th>
                                                <th class="ws">SC Main Category</th>
                                                <th class="ws">Category Tier 1</th>
                                                <th class="ws">Level</th>
                                                <th class="ws">Received Condition</th>
                                                <th class="ws">Listing Condition</th>
                                                <th class="ws">Pallet Id</th>
                                                <th class="ws">Oversized Packages</th>
                                                <th class="ws">Empty Box</th>
                                                <th class="ws">Expected Qty</th>
                                                <th class="ws">Received Qty</th>
        		                            </tr>
        		                        </thead>
        		                        <tbody>
        		                        	@forelse($orders as $row)
                                                @if(count($row->package) > 0)
                                                    @forelse($row->package as $package)
                                                        @php
                                                            $tt = app('request')->input('empty_box');
                                                            if(!empty($tt) && $tt != $package->empty_box){
                                                                continue;
                                                            }
                                                        @endphp
                		                        		<tr>
                                                            <td class="ws" style="white-space:nowrap;">
                                                                <a href="{{url('client/'.$row->id.'/edit-order')}}" class="btn btn-edit"><i class="fa fa-edit"></i></a>
                                                                <a href="{{url('client/'.$row['_post_id'].'/order-details')}}" class="btn btn-view"><i class="fa fa-eye"></i></a>
                                                                <a class="btn btn-edit" href="{{ route('client.order.invoice', $row->id) }}" target="_blank">
                                                                    <i class="fa fa-print" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $pallet = getPalletDetails($row->pallet_id);
                                                                @endphp
                                                                @if(!empty($pallet) && $pallet->hasMeta('certificate'))
                                                                    <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank" class="btn btn-view"><i class="fa fa-arrow-down"></i></a>
                                                                @endif
                                                            </td>
                                                            <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->created_at)) !!}</td>
                                                            <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status(get_post_extra($row->id, 'order_status'))) }}"> {{ inception_status(get_post_extra($row->id, 'order_status')) }} </span></td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'reference_number') ?? $row->id !!}</td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'evtn_number') !!}</td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'customer_name') !!}</td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'tracking_number') !!}</td>
                                                            <td class="ws">{!! $package->serviceName ?? '' !!}</td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'customer_address_line_1') !!} {!! get_post_extra($row->id, 'customer_address_line_2') !!} {!! get_post_extra($row->id, 'customer_city') !!} {!! get_post_extra($row->id, 'customer_state') !!} {!! get_post_extra($row->id, 'customer_pincode') !!}</td>
                                                            <td class="ws">{!! get_post_extra($row->id, 'currency') !!} {!! get_post_extra($row->id, 'value') !!}</td>
                                                            <td class="ws">{{ $package->hs_code ?? '' }}</td>
                                                            <td class="ws">{{ $package->coo ?? '' }}</td>
                                                            <td class="ws">{!! getCategoryName($package->category, 'main') !!}</td>
                                                            <td class="ws">{!! getCategoryName($package->sub_category_1) !!}</td>
                                                            <td class="ws">{{ $package->inspection_level ?? '' }}</td>
                                                            <td class="ws">
                                                                @if(!empty($package->received_condition))
                                                                    <span class=" badge badge-pill badge-{{ get_budge_value($package->received_condition) }}"> {{ $package->received_condition }} </span>
                                                                @endif
                                                            </td>
                                                            <td class="ws">{{ $package->condition ?? '' }}</td>
                                                            <td class="ws">{{ $row->pallet_id ?? '' }}</td>
                                                            <td class="ws">{{ (!empty($package->oversize)) ? $package->oversize : 'No' }}</td>
                                                            <td class="ws">{{ (!empty($package->empty_box)) ? $package->empty_box : 'No' }}</td>
                                                            <td class="ws">{{ $package->meta->expected_quantity ?? $package->itemQuantity }}</td>
                                                            <td class="ws">
                                                                @if($package->meta->match_quantity == 'Yes')
                                                                    {{ $package->itemQuantity ?? '' }}
                                                                @else
                                                                    {{ $package->meta->actual_quantity ?? '' }}
                                                                @endif
                                                            </td>
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
                        	<div class="box-footer">
                        	    <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                        	</div>
                        </div>
                    </div>  --}}           
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $("#select-all").click(function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $("#add-to-warehouse").click(function () {
                if($('.selectone:checkbox:checked').length < 1) {
                    alert('Please select at least one checkbox');
                    return false;
                } else {
                    $("#process-save").submit();
                }
            });

        })
    </script>
@endpush