@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="date_invoiced"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="dd_in"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="dd_out"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="ins_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="ins_date_to"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="sort_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="shipment_date"]').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom left"
    });

    $(".filter-frm").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    /*$("#parcel-excel-btn").click(function () {
        $('#export_to').val('parcel-excel');
        $("#filter-frm").submit();
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('item-excel');
        $("#filter-frm").submit();
    });

    $(".search-btn").click(function () {
        $('#export_to').val('');
    });*/

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#filter-frm").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.level-btn");
        
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : formData,
            xhrFields: {
                responseType: 'blob',
            },
            contentType: false,
            processData: false,
            beforeSend : function(){
                curSubmit.html(`<i class="fa fa-spinner" aria-hidden="true"></i> Please Wait...`).attr('disabled',true);
            },
            success : function(result, status, xhr){
                curSubmit.html(`Export To Excel`).attr('disabled',false);
                if (result.flag) {
                    alert(result.msg);
                } else {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-inspection-report.csv');

                    // The actual download
                    var blob = new Blob([result], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                }
            },
            error : function(data){
                curSubmit.html(`Export To Excel`).attr('disabled',false);
                return false;
            }
        });
    });

    $("#ebay-filter-frm").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.level-btn");
        
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : formData,
            xhrFields: {
                responseType: 'blob',
            },
            contentType: false,
            processData: false,
            beforeSend : function(){
                curSubmit.html(`<i class="fa fa-spinner" aria-hidden="true"></i> Please Wait...`).attr('disabled',true);
            },
            success : function(result, status, xhr){
                curSubmit.html(`Export To Excel`).attr('disabled',false);
                if (result.flag) {
                    alert(result.msg);
                } else {
                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : $.now()+'-ebay-report.csv');

                    // The actual download
                    var blob = new Blob([result], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                }
            },
            error : function(data){
                curSubmit.html(`Export To Excel`).attr('disabled',false);
                return false;
            }
        });
    });
});
</script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
@endpush
@push('scripts')
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

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">Report List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Inspection Report Fillters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="{{ route('admin.dwn.inspection.report') }}" method="post" class="form-horizontal" autocomplete="off" id="filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="ins_date" value="{{ Request::get('ins_date') }}" class="form-control datepicker" placeholder="From Inspection Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="ins_date_to" value="{{ Request::get('ins_date_to') }}" class="form-control datepicker" placeholder="To Inspection Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="sort_date" value="{{ Request::get('sort_date') }}" class="form-control datepicker" placeholder="Sortation Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="ins_status" class="form-control">
                                                <option value=""> -- Inspection Status --</option>
                                                @forelse(inception_status() as $st => $sv)
                                                    <option value="{{ $st }}" {{ (request('ins_status') == $st) ? 'selected' : '' }}>{{$sv}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="dd_in" value="{{ Request::get('dd_in') }}" class="form-control datepicker" placeholder="Discrepancy Date In">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="dd_out" value="{{ Request::get('dd_out') }}" class="form-control datepicker" placeholder="Discrepancy Date out">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="dis_status" class="form-control">
                                                <option value=""> -- Discrepancy Status --</option>
                                                @forelse(discrepancy_status() as $st => $sv)
                                                    <option value="{{ $st }}" {{ (request('ins_status') == $st) ? 'selected' : '' }}>{{$sv}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="date_format" class="form-control">
                                                <option value="m-d-Y" selected>-- US Format --</option>
                                                <option value="d-m-Y">-- UK Format --</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="item-excel">
                                            <button type="submit" class="btn btn-Search level-btn" id="item-excel-btn">Export To Excel</button>
                                            <a href="{{ route('admin.inspection.report') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">eBay Report Fillters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="{{ route('admin.dwn.inspection.report') }}" method="post" class="form-horizontal" autocomplete="off" id="ebay-filter-frm">
                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3">
                                            <input type="text" name="from_date" value="{{ Request::get('from_date') }}" class="form-control datepicker" placeholder="From Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="text" name="to_date" value="{{ Request::get('to_date') }}" class="form-control datepicker" placeholder="To Date">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select name="date_format" class="form-control">
                                                <option value="m-d-Y" selected>-- US Format --</option>
                                                <option value="d-m-Y">-- UK Format --</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="hidden" name="export_to" id="export_to" value="ebay-report">
                                            <button type="submit" class="btn btn-Search level-btn" id="ebay-excel-btn">Export To Excel</button>
                                            <a href="{{ route('admin.inspection.report') }}" class="btn btn-Reset">Reset</a>
                                        </div>
                                    </div>

                                    {{-- <textarea name="content" id="ck-editor"></textarea> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Inspection Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="products-pagination"> @if(count($orders)>0) {!! $orders->appends(Request::capture()->except('page'))->render() !!} @endif</div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@endsection

@push('js')
<script type="text/javascript">
    /*CKEDITOR.replace('ck-editor', {
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Save', 'Preview'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
            { name: 'alignment', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'styles', items: ['Format'] },
            { name: 'clipboard', items: ['Undo', 'Redo'] },
            { name: 'tools', items: ['Maximize'] }
        ]
    });*/
</script>
@endpush