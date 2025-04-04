@extends('layouts.admin.layout')


@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="close_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
});
</script>
@endpush

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">
                            @if(Request::is('admin/pallet/lists')) In Process Pallet Lists 
                            @elseif(Request::is('admin/pallet/closed-list')) Closed Pallet Lists
                            @else
                            Shipped Pallet Lists
                            @endif
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
            </div>

            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">
                            @if(Request::is('admin/pallet/lists')) In Process Pallet Lists 
                            @elseif(Request::is('admin/pallet/closed-list')) Closed Pallet Lists
                            @else
                            Shipped Pallet Lists
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" method="get" class="form-horizontal" autocomplete="off" id="frm-sbmit">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="start" value="{{ Request::get('start') }}" class="form-control datepicker" placeholder="Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="close_date" value="{{ Request::get('close_date') }}" class="form-control datepicker" placeholder="Close Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="pallet_id" value="{{ Request::get('pallet_id') }}" class="form-control datepicker" placeholder="Pallet Id">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="category_name" class="form-control cat-list">
                                                <option value="">---Select SC Main Category ---</option>
                                                @forelse($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="return_type" class="form-control">
                                                <option value="">-- Received Condition--</option>
                                                @foreach(conditionCode() as $code)
                                                    <option value="{{ $code }}">{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="reselling_grade" class="form-control">
                                                <option value="">-- Reselling Condition--</option>
                                                @foreach(getResellingGrade() as $code)
                                                    <option value="{{ $code }}">{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="sales_incoterm" id="" class="form-control">
                                                <option value="">-- Select --</option>
                                                <option value="DDU">EXPORTS DDU</option>
                                                <option value="DDP">EXPORTS DDP</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="pallet_status" id="" class="form-control">
                                                <option value="">-- Select --</option>
                                                <option value="Under Review">Under Review - Ecom</option>
                                                <option value="Ready for Price">Ready for Price - Ecom</option>
                                                <option value="Price done">Price done - eBay</option>
                                                <option value="Passed for Liquidation">Passed for Liquidation - Ecom</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-Search" id="search-btn">Search</button>
                                            <a href="{{ route('admin.closedpallet.list') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive booking-info-box">
                            <table class="table table-striped table-bordered admin-data-table admin-data-list table-sm avn-defaults">
                                <thead >
                                    <tr>
                                        <th class="ws">S no.</th>
                                        <th class="ws">Date</th>
                                        <th class="ws">Close Date</th>
                                        <th class="ws">Closed Pallet Status</th>
                                        <th class="ws">Pallet ID</th>
                                        <th class="ws">Pallet Type</th>
                                        <th class="ws">Received Condition</th>
                                        <th class="ws">Reselling Grade</th>
                                        <th class="ws">Total Received Qty</th>
                                        <th class="ws">SC Master Category</th>
                                        <th class="ws">From Warehouse Name</th>                                
                                        <th class="ws">To Warehouse Name</th>
                                        <th class="ws">Preferred Listing Price</th>
                                        <th class="ws">Preferred Listing Price %</th>
                                        <th class="ws">Authorised by</th>
                                        <th class="ws">Original Sales Incoterm</th>
                                        <th class="ws">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @forelse($lists as $row)
                                        <tr>
                                            <td class="ws">{{ $i++ }}</td>
                                            <td class="ws">{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                            <td class="ws">
                                                @if(!empty($row->close_date))
                                                    {{ date('d/m/Y',strtotime($row->close_date)) }}
                                                @else
                                                    {{ date('d/m/Y',strtotime($row->updated_at)) }}
                                                @endif
                                            </td>
                                            <td class="ws">{{ $row->pallet_status }}</td>
                                            <td class="ws">{{ $row->pallet_id }}</td>
                                            <td class="ws">{{ $row->pallet_type }}</td>
                                            <td class="ws">{{ $row->return_type }}</td>
                                            <td class="ws">{{ $row->reselling_grade }}</td>
                                            <td class="ws">{{ getTotalRcvdQty($row->pallet_id) }}</td>
                                            <td class="ws">{{ getCategoryName($row->meta->main_category ?? '', 'main') }}</td>
                                            <td class="ws">@php $fr = $row->meta->fr_warehouse_id ?? '' @endphp {{getWareHouseName($fr)}}</td>
                                            <td class="ws">{{ getWareHouseName($row->warehouse_id) }}</td>
                                            <td class="ws">{{ $row->meta->pl_price ?? '' }}</td>
                                            <td class="ws">{{ $row->meta->ppl_price ?? '' }}</td>
                                            <td class="ws">{{ $row->authorised_by ?? '' }}</td>
                                            <td class="ws">{{ (!empty($row->sales_incoterm)) ? $row->sales_incoterm : 'DDU' }}</td>
                                            <td class="ws">
                                                <a class="btn btn-view" href="{{ route('admin.show.pallet',$row) }}" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-edit" href="{{ route('admin.edit.closedpallet',$row) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="btn btn-edit" href="{{ route('admin.pallet.invoice', $row->id) }}" target="_blank">
                                                    <i class="fa fa-print" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12">
                                                <i class="fa fa-exclamation-triangle"></i> There are no data
                                            </td>
                                         </tr>
                                    @endforelse                            
                                </tbody>
                            </table>            
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="products-pagination"> @if(count($lists)>0) {!! $lists->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    
@endpush