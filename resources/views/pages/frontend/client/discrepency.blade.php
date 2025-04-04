@include('pages.frontend.client.breadcrumb', ['title' => 'Discrepency List'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
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
});
</script>

@endpush

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


@include('pages-message.notify-msg-error')
@include('pages-message.notify-msg-success')
@include('pages-message.form-submit')

<div class="app-contents contents"> 
    <div class="content-wrapper ebay-content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card Order-info-box">
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
                                                @forelse($sub_categories as $scat)
                                                    <option value="{{ $scat->code }}">{{ $scat->name }}</option>
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
                                            <select name="code" class="form-control">
                                                <option value="">-- Select Condition--</option>
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}" @if(isset($order['condition_code']) && $order['condition_code'] == $code)selected @endif>{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <select name="discrepancy_status" id="discrepancy_status" class="form-control">
                                                <option value="">-- Select Discrepancy Status --</option>
                                                @forelse(discrepancy_status() as $st => $sv)
                                                    <option value="{{ $st }}">{{$sv}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <input type="text" name="tracking_number" value="{{ Request::get('tracking_number') }}" class="form-control" placeholder="Tracking Number">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <input type="text" name="order_number" value="{{ Request::get('order_number') }}" class="form-control" placeholder="Order Number">
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('client.discrepency.list') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <div class="card-footer">
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
        		                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
        		                        <thead>
        		                            <tr>        		                                
        		                                <th>Action</th>
                                                <th class="ws">Date</th>
                                                <th class="ws">Pallet Id</th>
                                                <th class="ws">Discrepancy Date In</th>
                                                <th class="ws">Discrepancy Date Out</th>
                                                <th class="ws">Discrepancy Status</th>
                                                <th class="ws">Ref. Number</th>
                                                <th class="ws">EVTN Number</th>
                                                <th class="ws">Name</th>
                                                <th class="ws">Tracking No.</th>
                                                <th class="ws">Order No.</th>
                                                <th class="ws">Address</th>
                                                <th class="ws">Amount</th>
                                                <th class="ws">Hs Code</th>
                                                <th class="ws">COO</th>
                                                <th class="ws">SC Main Category</th>
                                                <th class="ws">Category Tier 1</th>
                                                <th class="ws">Level</th>
                                                <th class="ws">Inspection Status</th>
                                                <th class="ws">Remarks</th>
                                                <th class="ws">eBay comments</th>
        		                            </tr>
        		                        </thead>
        		                        <tbody>
        		                        	@forelse($orders as $row)
                                                @php
                                                // dd($row);
                                                @endphp
        		                        		<tr>                                                    
                                                    <td class="ws">
                                                        <a href="{{url('client/'.$row['_post_id'].'/edit-order')}}" class="btn btn-view"><i class="fa fa-edit"></i></a>
                                                    </td>
                                                    <td class="ws">{!! date('d-m-Y', strtotime($row['_order_date'])) !!}</td>
                                                    <td class="ws">{!! $row['pallet_id'] ?? '' !!}</td>
                                                    <td class="ws">
                                                        @if(isset($row['dd_in']) && !empty($row['dd_in']))
                                                            {!! date('d-m-Y', strtotime($row['dd_in'])) !!}
                                                        @elseif(isset($row['packages'][0]['discrepancy_date_in']) && !empty($row['packages'][0]['discrepancy_date_in']))
                                                            {!! date('d-m-Y', strtotime($row['packages'][0]['discrepancy_date_in'])) !!}
                                                        @endif
                                                    </td>
                                                    <td class="ws">
                                                        @if(isset($row['dd_out']) && !empty($row['dd_out']))
                                                            {!! date('d-m-Y', strtotime($row['dd_out'])) !!}
                                                        @elseif(isset($row['packages'][0]['discrepancy_date_out']) && !empty($row['packages'][0]['discrepancy_date_out']))
                                                            {{-- {!! date('d-m-Y', strtotime($row['packages'][0]['discrepancy_date_out'])) !!} --}}
                                                        @endif
                                                    </td>
                                                    <td class="ws">
                                                        @if(isset($row['packages'][0]['discrepancy_status']) && !empty($row['packages'][0]['discrepancy_status']))
                                                            {!! discrepancy_status($row['packages'][0]['discrepancy_status']) !!}
                                                        @endif
                                                    </td>
                                                    <td class="ws">
                                                        {!! $row['reference_number'] ?? $row['_post_id'] !!}
                                                    </td>
                                                    <td class="ws">{!! $row['evtn_number'] ?? '' !!}</td>
                                                    <td class="ws">{!! $row['customer_name'] ?? '' !!}</td>
                                                    <td class="ws">{!! $row['tracking_number'] ?? '' !!}</td>
                                                    <td class="ws">{!! $row['order_number'] ?? '' !!}</td>
                                                    <td class="ws">{!! $row['customer_address_line_1'] ?? '' !!} {!! $row['customer_address_line_2'] ?? '' !!} {!! $row['customer_city'] ?? '' !!} {!! $row['customer_state'] ?? '' !!} {!! $row['customer_pincode'] ?? '' !!}</td>
                                                    <td class="ws">{!! $row['currency'] ?? '' !!} {!! $row['value'] ?? '' !!}</td>
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
                                                        {{ $row['in_level'] ?? '' }}
                                                    </td>
                                                    <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status($row['order_status'])) }}"> {{ inception_status($row['order_status']) }} </span></td>
                                                    <td></td>
                                                    <td></td>
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