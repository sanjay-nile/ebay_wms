@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
<style type="text/css">
    .required-field::before {
        content: "*";
        color: red;
    }

    .rg-pack-card {
        background: #fbfbfb;
        margin-bottom: 10px;
        position: relative;
        border-radius: 10px;
        padding: 10px;
    }

    .rg-pack-card h2 {
        margin: 0;
        line-height: 1;
        color: #213051;
        font-size: 14px;
        font-weight: 800;
        padding: 0px 0px 10px 0;
        margin-bottom: 0;
    }
    form .purple{
        border: 2px solid purple !important;
    }
    form .pink{
        border: 2px solid pink !important;
    }
    form .green{
        border: 2px solid green !important;
    }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/js/select2.min.js') }}"></script>

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


            let explt =`
                <div class="col-md-3">
                    <label>SC Main Category <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="main_category" class="form-control exist_pallet" id="main_category">
                            <option value="">--- Select Category ---</option>
                            @forelse($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Received Condition <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="condition" class="form-control exist_pallet" id="condition">
                            <option value="">-- Select --</option>
                            @foreach(conditionCode() as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 1</label>
                    <div class="form-group">
                        <select name="category_tier_1" class="form-control exist_pallet" id="category_tier_1">
                            <option value="">-- Select --</option>
                            @forelse($sub_categories as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 2</label>
                    <div class="form-group">
                        <select name="category_tier_2" class="form-control exist_pallet" id="category_tier_2">
                           <option value="">-- Select --</option>
                            @forelse($sub_categories_2_tier as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 3</label>
                    <div class="form-group">
                        <select name="category_tier_3" class="form-control exist_pallet" id="category_tier_3">
                            <option value="">-- Select --</option>
                            @forelse($sub_categories_3_tier as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label>Existing Pallet</label>
                    <div class="form-group" id="exist_pallet_list">
                        <select name="pallet_name" id="" class="form-control">
                            @forelse($pallets as $pallet)
                                <option value="{{ $pallet->pallet_id }}" @if($pallet->pallet_type == 'InProcess') style="color:green" @else style="color:red" @endif>{{ $pallet->pallet_id }}</option>
                            @empty
                                <option value="">No Existing pallet exists</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <input type="hidden" name="form_type" value="edit">
                    <button type="button" id="add-to-old-pallet" class="btn add-to-pallet mt-2">Submit</button>
                </div>`;


            let crplt =`<div class="col-md-3"><label>Pallet ID </label>
                <div class="form-group">
                    <input type="text" name="pallet_name" value="{{ generateUniquePalletNames() }}" class="form-control" readonly="readonly">
                </div></div>

                <div class="col-md-3">
                    <label>Condition <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="condition" id="" class="form-control" required>
                            <option value="">-- Select --</option>
                            @foreach(conditionCode() as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Original Sales Incoterm <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="sales_incoterm" id="" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="DDU">EXPORTS DDU</option>
                            <option value="DDP">EXPORTS DDP</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Reselling Grade</label>
                    <div class="form-group">
                        <select name="reselling_grade" id="" class="form-control" required>
                            <option value="">-- Select --</option>
                            @foreach(getResellingGrade() as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>From Warehouse <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="fr_warehouse_id" id="to_client_warehouse_list" class="form-control">
                            <option value="">-- Select --</option>
                                @forelse($warehouse_list as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @empty
                                    <option value="">Warehouse not added yet</option>
                                @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>To Warehouse </label>
                    <div class="form-group">
                        <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                            <option value="">-- Select --</option>
                                @forelse($warehouse_list as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @empty
                                    <option value="">Warehouse not added yet</option>
                                @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>SC Main Category <span class="required-field"></span></label>
                    <div class="form-group">
                        <select name="main_category" class="form-control">
                            <option value="">--- Select Category ---</option>
                            @forelse($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 1</label>
                    <div class="form-group">
                        <select name="category_tier_1" class="form-control">
                            <option value="">-- Select --</option>
                            @forelse($sub_categories as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 2</label>
                    <div class="form-group">
                        <select name="category_tier_2" class="form-control">
                           <option value="">-- Select --</option>
                            @forelse($sub_categories_2_tier as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label>Category Tier 3</label>
                    <div class="form-group">
                        <select name="category_tier_3" class="form-control">
                            <option value="">-- Select --</option>
                            @forelse($sub_categories_3_tier as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <input type="hidden" name="form_type" value="add">
                    <button type="button" id="add-to-pallet" class="btn add-to-pallet mt-2 mb-2">Submit</button>
                </div>`;

            $("#create-pallet").click(function () {
                $("#ex-pallet").html(crplt);
            });

            $(document).on('click',"#add-to-pallet", function(){
                if($('.selectone:checkbox:checked').length < 1) {
                    alert('Please select at least one checkbox');
                    return false;
                } else {
                    $("#pallet-save").submit();
                }
            });

            $(document).on('click','#existing-pallet',function(){
                $("#ex-pallet").html(explt);
            })


            $(document).on('click',"#add-to-old-pallet", function(){
                if($('.selectone:checkbox:checked').length < 1) {
                    alert('Please select at least one checkbox');
                    return false;
                } else {
                    $("#pallet-save").submit();
                    // $(".pallet-div").toggle();
                }
            });
        });
    </script>
@endpush

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        @include('pages-message.notify-msg-error')
        @include('pages-message.notify-msg-success')
        @include('pages-message.form-submit')
        
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">eBay Order List</li>
                    </ol>
                </div>
            </div>
        </div>
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
                                                <option value="">--- Select Category ---</option>
                                                @forelse($categories as $cat)
                                                    <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3 sub-cat-list">
                                            <select name="sub_category_name" class="form-control">
                                                <option value="">--- Select Sub Category ---</option>
                                                @forelse($sub_categories as $cat)
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
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.return.orders') }}" class="btn btn-Reset">Reset</a>
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
                    <div class="card-header">
                        <div class="pallet-div">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" id="existing-pallet" class="btn btn-blue">Add to existing Pallet </button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="create-pallet" class="btn btn-blue">Create New Pallet</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="rg-pack-table">
                            <div class="alert alert-primary">
                                @if(count($orders)>0)
                                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of total {{$orders->total()}} Orders
                                @endif
                            </div>
                            <div class="table-responsive booking-info-box">
                                <form action="{{ route('admin.add.pallet.orders') }}" method="post" id="pallet-save">
                                    @csrf
                                    <div class="row ml-1" id="ex-pallet"></div>
                                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
                                        <thead>
                                            <tr>
                                                <th class="ws">
                                                    <input name="select_all" value="1" id="select-all" type="checkbox">
                                                </th>
                                                {{-- <th>Action</th> --}}
                                                <th class="ws">Date</th>
                                                <th class="ws">Order Ref. Number</th>
                                                <th class="ws">Item Ref. Number</th>
                                                <th class="ws">Pallet Id</th>
                                                <th class="ws">EVTN Number</th>
                                                <th class="ws">Name</th>
                                                <th class="ws">Tracking No.</th>
                                                <th class="ws">Address</th>
                                                <th class="ws">Amount</th>
                                                <th class="ws">Hs Code</th>
                                                <th class="ws">COO</th>
                                                <th class="ws">Category</th>
                                                <th class="ws">Sub Category</th>
                                                <th class="ws">Level</th>
                                                <th class="ws">Conditon</th>
                                                {{-- <th class="ws">Picture</th> --}}
                                                <th class="ws">Inspection Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $row)
                                                <tr>                                                    
                                                    <td style="text-align: center;">
                                                        @if(empty($row->pallet_id))
                                                            <input name="pallet_orders[]" type="checkbox" class="selectone" value="{{ $row->id }}">
                                                        @endif
                                                    </td>                                                    
                                                    <td class="ws" style="white-space: nowrap;">{!! date('d-m-Y', strtotime($row->ps_created_at)) !!}</td>
                                                    <td class="ws">{!! $row->reference_number ?? $row->id !!}</td>
                                                    <td class="ws">{!! $row->package_id ?? '' !!}</td>
                                                    <td class="ws">{!! $row->pallet_id ?? '' !!}</td>
                                                    <td class="ws">{!! $row->evtn_number ?? '' !!}</td>
                                                    <td class="ws">{!! $row->customer_name ?? '' !!}</td>
                                                    <td class="ws">{!! $row->tracking_number ?? '' !!}</td>
                                                    <td class="ws">{!! $row->customer_address !!} {!! $row->customer_city !!} {!! $row->customer_state !!} {!! $row->customer_pincode !!}</td>
                                                    <td class="ws">{!! $row->post_extras_currency ?? '' !!} {!! $row->price !!}</td>
                                                    <td class="ws">{{ $row->hs_code ?? '' }}</td>
                                                    <td class="ws">{{ $row->coo ?? '' }}</td>
                                                    <td class="ws">{!! getCategoryName($row->category, 'main') !!}</td>
                                                    <td class="ws">{!! getCategoryName($row->sub_category_1) !!}</td>
                                                    <td class="ws">{{ $row->inspection_level ?? '' }}</td>
                                                    <td class="ws">{{ $row->condition ?? '' }}</td>
                                                    {{-- <td>
                                                        @if(isset($row['image']))
                                                            <a href="{{ asset('public/uploads/'.$row['image'])}}" target="_blank"><img src="{{ asset('public/uploads/'.$row['image'])}}" class="img-thumbnail" width="50"></a>
                                                        @endif
                                                    </td> --}}
                                                    <td class="ws"><span class=" badge badge-pill badge-{{ get_budge_value(inception_status_value($row->status)) }}"> {{ inception_status_value($row->status) }} </span></td>
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

@endsection