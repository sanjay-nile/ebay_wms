@include('pages.frontend.client.breadcrumb', ['title' => 'Report'])

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
        <div class="card">
            <div class="card-header avn-card-header">
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
            </div>
            <div class="card-content collapse show">
                <div class="card-body booking-info-box card-dashboard">
                    <div class="info-list-inner">   
                    <div class="table-responsive">
                        <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults table-sm">
                            <thead>
                                <tr>
                                    <th>S no.</th>
                                    <th>Date</th>
                                    <th>Customer Order No</th>
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
                                    <th>Carrier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1 @endphp
                                @forelse($lists as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                        <td>{{ $row->way_bill_number }}</td>
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
                                        <td>{{ $row->meta->_carrier_name }}</td>
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