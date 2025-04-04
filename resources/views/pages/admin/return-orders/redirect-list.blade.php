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
                    {{-- <h3 class="content-header-title mb-0 d-inline-block">Waybill</h3> --}}
                    <div class="row breadcrumbs-top d-inline-block">
                      <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>
                            <li class="breadcrumb-item active">Redirect Return Orders List</li>
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
                        <div class="alert alert-info">Total Orders : <strong>{{ $lists->total() }}</strong></div>
                        <form class="form-horizontal fiter-form ml-1">
                            <div class="row">
                                @if(Auth::user()->user_type_id==1 || Auth::user()->user_type_id==2)
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
                                    <a href="{{ route('redirect.orders') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-content collapse show">
                        <!-- <a href="#" class="list-right-btn">Redirect to Eq8tor</a> -->
                        <div class="card-body booking-info-box card-dashboard table-responsive">
                            <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Order No.</th>
                                        <th>Customer Name</th>
                                        <th>Return Request Date</th>
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
                                        <th>Return Option</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @forelse($lists as $row)
                                        @forelse($row->packages as $pakage)
                                            <tr>
                                                <?php
                                                    if($pakage->status != 'Redirect'){
                                                        continue;
                                                    }
                                                ?>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $row->way_bill_number }}</td>
                                                <td>{{ $row->meta->_customer_name }}</td>
                                                <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                                <td>{{ $pakage->bar_code }}</td>
                                                <td>{{ $row->shippingPolicy->carrier->name ?? 'N/A' }}</td>
                                                <td>{{ $row->shippingPolicy->shippingType->name ?? 'N/A' }}</td>
                                                <td>
                                                    <?php
                                                        $track_id = 'Not Generated';
                                                        if($row->meta->_order_tracking_id){
                                                            $track = json_decode($row->meta->_order_tracking_id);
                                                            foreach($track as $t){
                                                                if (empty($t->carrierWaybillNumber)) {
                                                                    continue;
                                                                }
                                                                $track_id = $t->carrierWaybillNumber;
                                                            }
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
                                                <td>{{ str_replace('_', ' ', $row->meta->_drop_off) ?? "N/A" }}</td>
                                                <td>
                                                    <a class="btn btn-view btn-primary" href="{{ route('reverse-logistic.view',$row) }}" title="Edit"><i class="la la-eye"></i></a>
                                                </td>
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
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
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
