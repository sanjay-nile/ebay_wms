@include('pages.frontend.client.breadcrumb', ['title' => 'UnProcessed Returns'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});

    //----------------------------------------------------------------------------------------------//
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
            <div class="card-header">
                <div class="alert alert-info">Total Orders : <strong>{{ $lists->total() }}</strong></div>
                <form class="form-horizontal fiter-form ml-1">
                    <div class="row">                        
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="way_bill_number" class="form-control" placeholder="Order ID" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" />
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
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-cyan btn-sm" id="search-btn"><i class="fa fa-search"></i></button>
                            <a href="{{ route('client.new-eqtor-reverse-logistic') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>
                        </div>
                    </div>
                </form>                
            </div>
            <div class="card-content collapse show">
                <div class="card-body booking-info-box card-dashboard table-responsive">
                    <table id="client_user_list" class="table table-striped table-hover nowrap avn-defaults table-sm">
                        <thead>
                            <tr>
                                <th>S no.</th>
                                <th>Action</th>
                                <th>Return Option</th>
                                <th>RG Order No.</th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Return Request Date</th>
                                <th>Sku #</th>
                                <th>Package Weight</th>
                                <th>Package Dimensions</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1 @endphp
                            @forelse($lists as $row)
                                @forelse($row->packages as $pakage)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>
                                            <a class="btn btn-edit btn-primary" href="{{ route('reverse-logistic.edit',$row) }}" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class="btn btn-view btn-success" href="{{ route('new.waybill.detail',$row) }}" title="View">
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
                                        <td>{{ $pakage->bar_code }}</td>                                                
                                        <td>{{ $pakage->weight }}</td>
                                        <td>{{ $pakage->length }} / {{ $pakage->width }} / {{ $pakage->height }}</td>
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