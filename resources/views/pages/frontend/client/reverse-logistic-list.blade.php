@include('pages.frontend.client.breadcrumb', ['title' => 'List Reverse Logistic'])

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
                            <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-content collapse show">
                <a href="#" class="list-right-btn">Redirect to Eq8tor</a>
                <div class="card-body booking-info-box card-dashboard table-responsive">
                    <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                        <thead>
                            <tr>
                                <th>S no.</th>
                                <th>Way Bill No</th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Customer City</th>
                                <th>Warehouse Name</th>
                                <th>Warehouse City</th>
                                <th>Shipment Type</th>
                                <th>Sell Rate</th>
                                <th>Carrier</th>
                                <th>No Of Pkg</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1 @endphp
                            @forelse($lists as $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row->way_bill_number }}</td>
                                    <td>{{ date('d/m/Y',strtotime($row->created_at)) }}</td>
                                    <td>{{ $row->meta->_customer_name }}</td>
                                    <td>{{ $row->meta->_customer_city }}</td>
                                    <td>{{ $row->meta->_consignee_name }}</td>
                                    <td>{{ $row->meta->_consignee_city }}</td>
                                    <td>{{ $row->meta->_shipment_name}}</td>
                                    <td>{{ $row->meta->_rate}}</td>
                                    <td>{{ $row->meta->_carrier_name }}</td>
                                    <td>{{ $row->meta->_number_of_packages }}</td>
                                    <td>
                                        <a class="btn btn-view btn-primary" href="{{ route('waybill.detail',$row) }}" title="View">
                                            <i class="la la-eye"></i>
                                        </a>
                                    </td>
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

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatable/css/datatables.min.css') }}">
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/datatable/js/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    var defaults= {
        dom: 'Bfrtip', buttons: [ {
            extend:'copy', attr: {
                id: 'allan'
            },text: '<i class="la la-copy"></i> Copy', exportOptions: {
                columns: ':not(:last-child)'
            }
        },{
            extend:'excel', text: '<i class="la la-file-excel-o"></i> Excel', exportOptions: {
                columns: ':not(:last-child)'
            }
        },{
            extend:'print', text: '<i class="la la-print"></i> Print', exportOptions: {
                columns: ':not(:last-child)'
            }
        },'colvis'], 'aoColumnDefs': [ {
            'bSortable': false, 'aTargets': [-1]/* 1st one, start by the right */
        }], exportOptions: {
            columns: [1, 2, 3, 4]
        },"searching": false, "ordering": true, "bPaginate": false, "bInfo": false
    };

    $('.avn-defaults').dataTable($.extend(true, {}, defaults, {}));

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary');

    //----------------------------------------------------------------------------------------------//

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });
});
</script>

@endpush