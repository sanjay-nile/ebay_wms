@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script>
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        toastr.options ={
           "closeButton" : true,
           "progressBar" : true,
           "disableTimeOut" : true,
        }

        $("#create-form, #formid").on('submit',function(e){
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
                            $('#myModal').modal({ show: false });
                            $('#myModal_2').modal({ show: false });
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

<script type="text/javascript">
    function setClientDateTime() {
        var currentDate = new Date();
        var formattedDateTime = currentDate.getFullYear() + '-' +
                                (currentDate.getMonth() + 1).toString().padStart(2, '0') + '-' +
                                currentDate.getDate().toString().padStart(2, '0') + ' ' +
                                currentDate.getHours().toString().padStart(2, '0') + ':' +
                                currentDate.getMinutes().toString().padStart(2, '0') + ':' +
                                currentDate.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('system_time').value = formattedDateTime;
    }

    // Call this function before the form is submitted
    window.onload = setClientDateTime;
</script>
@endpush

@push('css')
<style type="text/css">
    .rack-info-box .card-body {padding: 1.0rem; }
    .rack-info-box .card-header{display: flex;     align-items: center;justify-content: space-between; padding: 1.0rem;}
    .btn-cancel {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-Submit {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 10px; font-size: 13px; outline: none; display: inline-block; }

    .btn-bl-outline {color: #3d2a67; background-color: #fff; border:1px solid #3d2a67; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }

    .btn-gr-fill {color: #fff; background-color: #35bd64; border:1px solid #35bd64; border-radius: 2px; padding: 10px 26px; margin-bottom: 0px; font-size: 13px; outline: none; display: inline-block; }
    .required-field::before {
        content: "*";
        color: red;
    }
</style>
@endpush

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
		<div class="we-page-title">
			<div class="row">
				<div class="col-md-12 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Add Rack Detail</li>
					</ol>
				</div>
			</div>
		</div>

        <!-- Main content -->
        <div class="row">
			<div class="col-md-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
			</div>
            <div class="col-xs-12 col-md-12">
                <div class="card rack-info-box">
                    <div class="card-body">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.import.rack') }}" id="formid">
                            @csrf
                            <div class="rg-pack-card">
                                <h5>Import Rack Data</h5>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="file" name="postdata_file" id="postdata_file" class="form-control" required="">
                                        </div>
                                        <a href="{{ asset('public/uploads/import-rack-data.csv') }}" download=""> <u>Download Sample</u></a>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-blue add-btn">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Rack Detail</h5>
                        <div class="card-header-action">
                            <a href="{{ route('admin.rack.list', $parameters = [], $absolute = true) }}" class="btn-gr-fill"> Rack List</a>    
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <section class="list-your-service-section">
                                <div class="list-your-service-content">
                                    <div class="list-your-service-form-box">
                                       <form method="post" action="{{ route('admin.rack.store') }}" enctype="multipart/form-data" id="create-form">
                                            @csrf
                                            <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                                            <input type="hidden" name="create_system_time" value="" id="system_time">
                                            <input type="hidden" name="post_id" value="{{ $location['post_id'] ?? '' }}">
                                            <div class="rg-pack-card">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Client ID <span class="required-field"></span></label>
                                                            <select name="client_id" class="form-control">
                                                                <option value="">-- Select Client --</option>
                                                                @forelse($clients as $client)
                                                                    <option value="{{ $client->id }}" @if(isset($location['client_id']) && $location['client_id'] == $client->id) selected @endif>{{ $client->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Warehouse <span class="required-field"></span></label>
                                                            <select name="warehouse_id" class="form-control">
                                                                <option value="">-- Select Warehouse --</option>
                                                                @forelse($list as $wr)
                                                                    <option value="{{ $wr->id }}" @if(isset($location['warehouse_id']) && $location['warehouse_id'] == $wr->id) selected @endif>{{ $wr->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Measurement Type <span class="required-field"></span></label>
                                                            <select name="measurement" class="form-control">
                                                                <option value="">-- Select Measurement --</option>
                                                                <option value="Metric (Cm, KG)" @if(isset($location['measurement']) && $location['measurement'] == "Metric (Cm, KG)") selected @endif>Metric (Cm, KG)</option>
                                                                <option value="Imperial (Inches, Pound)" @if(isset($location['measurement']) && $location['measurement'] == "Imperial (Inches, Pound)") selected @endif>Imperial (Inches, Pound)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Scan Location ID</label>
                                                            <input type="text" class="form-control" name="location_id" placeholder="A001-001-001" value="{{ $location['location_id'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Title <span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="title" placeholder="Enter Title" value="{{ $location['post_title'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Short Title <span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="short_title" placeholder="Enter Short Title" value="{{ $location['post_content'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Levels</label>
                                                            <select name="level" class="form-control">
                                                                <option value="">Select Levels</option>
                                                                @for($i=1; $i<=4; $i++)
                                                                    <option value="{{ $i }}" @if(isset($location['level']) && $location['level'] == $i) selected @endif> Level {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Shelves</label>
                                                            <select name="shelves" class="form-control">
                                                                <option value="">Select Shelves</option>
                                                                @for($i=1; $i<=10; $i++)
                                                                    <option value="{{ $i }}" @if(isset($location['shelves']) && $location['shelves'] == $i) selected @endif> Shelves {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Length<span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="length" placeholder="Shelf Length" value="{{ $location['length'] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Width<span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="width" placeholder="Shelf Width" value="{{ $location['width'] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Height<span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="height" placeholder="Shelf Height" value="{{ $location['height'] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Weight<span class="required-field"></span></label>
                                                            <input type="text" class="form-control" name="weight" placeholder="Shelf Max Weight" value="{{ $location['weight'] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Is Active</label>
                                                            <select name="status" class="form-control">
                                                                <option value="1" selected>Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn-Submit1 add-btn">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection