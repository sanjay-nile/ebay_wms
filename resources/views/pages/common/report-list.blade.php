@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Waybill Report</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                      <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                        </ol>
                      </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12 ">
                @include('includes/admin/notify')
                <div class="card">
                    <div class="card-header avn-card-header">
                        <form class="form-horizontal fiter-form ml-1">
                            <div class="row">
                                @if($user->user_type_id==1 || $user->user_type_id==2)
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="client" id="" class="form-control">
                                            <option value="">Select Client</option>
                                            @forelse($clients as $client)
                                            <option value="{{ $client->id }}" {{ (app('request')->input('client')==$client->id)?"selected":'' }}>{{ $client->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                @endif
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
                                    <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
                                    <a href="{{ route('reverse-logistic') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>
                                </div>
                            </div>
                        </form>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body booking-info-box card-dashboard">
                            <div class="table-responsive">
                                <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                                    <thead>
                                        <tr>
                                            <th>S no.</th>
                                            <th>Date</th>
                                            <th>Customer Order No</th>
                                            <th>Client Name</th>
                                            <th>Customer Name</th>
                                            <th>Customer City</th>
                                            <th>Warehouse Detail</th>
                                            <th>No. of pkg</th>
                                            <th>Shipment Type</th>
                                            <th>Tracking Id</th>
                                            <th>Custom Duty</th>
                                            <th>Other Charges</th>
                                            <th>Delivery Date</th>
                                            <th>Buy Rate</th>                                        
                                            @if($user->user_type_id==1 || $user->user_type_id==1)
                                                <th>Sell Rate</th>
                                                <th>Margin</th>
                                            @endif
                                            <th>Carrier</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i=1 @endphp
                                        @forelse($lists as $row)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                                <td>{{ $row->way_bill_number }}</td>
                                                <td>{{ $row->client_name }}</td>
                                                <td>{{ $row->meta->_customer_name }}</td>
                                                <td>{{ $row->meta->_customer_city }}</td>
                                                <td>{{ $row->meta->_consignee_name }}, {{ $row->meta->_consignee_city }}</td>
                                                <td>{{ $row->meta->_number_of_packages??"N/A" }}</td>
                                                <td>{{ $row->meta->_shipment_name}}</td>
                                                <td>N/A</td>
                                                <td>{!! ($row->meta->_currency)?get_currency_symbol($row->meta->_currency):get_currency_symbol('USD') !!} 0</td>
                                                <td>N/A</td>
                                                <td>N/A</td>
                                                <td>{!! ($row->meta->_currency)?get_currency_symbol($row->meta->_currency):get_currency_symbol('USD') !!} 0</td>
                                                @if($user->user_type_id==1 || $user->user_type_id==1)
                                                    <td>{!! ($row->meta->_currency)?get_currency_symbol($row->meta->_currency):get_currency_symbol('USD') !!} {{ $row->meta->_rate}}</td>
                                                    <td>{!! ($row->meta->_currency)?get_currency_symbol($row->meta->_currency):get_currency_symbol('USD') !!} {{ $row->meta->_rate}}</td>
                                                @endif
                                                <td>{{ $row->meta->_carrier_name }}</td>
                                                <!-- <td>
                                                    <a class="btn btn-view btn-primary" href="{{ route('reverse-logistic.show',$row) }}" title="Edit"><i class="la la-eye"></i></a>
                                                </td> -->
                                            </tr>
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
        </div>
    </div>
</div>

@endsection

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
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
