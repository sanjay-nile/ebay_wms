@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .rack-info-box .card-body {padding: 1.0rem; }
    .rack-info-box .card-header{display: flex;     align-items: center;justify-content: space-between; padding: 1.0rem;}
    .btn-cancel {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-Submit {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-bl-outline {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }

    a.btn-gr-fill {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('input[name="from_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="to_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
    $('input[name="dis_date"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    $("#search-btn").click(function () {
        $('#export_to').val('');
    });

    toastr.options ={
       "closeButton" : true,
       "progressBar" : true,
       "disableTimeOut" : true,
    }

    $("#create-form").on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        let formData = new FormData(this);
        var curSubmit = $(this).find("button.add-btn");
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend : function(){
                curSubmit.html(`Sending.. <i class="la la-spinner la-spin"></i>`).attr('disabled',true);
            },
            success : function(response){                    
                if(response.status==201){
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                    return false;
                }

                if(response.status==200){                   
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.error(response.message);
                    return false;
                }
            },
            error : function(data){
                if(data.status==422){
                    let li_htm = '';
                    $.each(data.responseJSON.errors,function(k,v){
                        const $input = form.find(`input[name=${k}],select[name=${k}],textarea[name=${k}]`);
                        if($input.next('small').length){
                            $input.next('small').html(v);
                            if(k == 'type_of_place' || k == 'safety' || k == 'p_value' || k == 'amenities' || k == 'features'){
                                $('.'+k).html(`<small class='text-danger'>${v[0]}</small>`);
                            }
                        }else{
                            $input.after(`<small class='text-danger'>${v}</small>`);
                            if(k == 'type_of_place' || k == 'safety' || k == 'p_value' || k == 'amenities' || k == 'features'){
                                $('.'+k).html(`<small class='text-danger'>${v[0]}</small>`);
                            }
                        }
                        li_htm += `<li>${v}</li>`;
                    });
                    curSubmit.html(`Submit`).attr('disabled',false);
                    return false;
                }else{                  
                    curSubmit.html(`Submit`).attr('disabled',false);
                    toastr.error(data.statusText);
                    return false;
                }
            }
        });
    });
});
</script>
@endpush

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-12 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">Move To Dispatched</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')

                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Move To Dispatch New</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.store.move.dispatch') }}" method="post" enctype="multipart/form-data" id="create-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Package ID / eBay ID</label>
                                        <input type="text" class="form-control" name="scan_i_package_id" placeholder="ORD-0-8763874237-0000037" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Disptach Location ID</label>
                                        <input type="text" class="form-control" name="scan_i_location_id" placeholder="A001-001-001" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Dispatch Tracking ID</label>
                                        <input type="text" class="form-control" name="tracking_number" placeholder="EPG071024002777252" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Dispatch Date</label>
                                        <input type="text" class="form-control" name="dis_date" placeholder="YYYY-MM-DD" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button class="btn-Submit add-btn btn-sm" type="submit">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection