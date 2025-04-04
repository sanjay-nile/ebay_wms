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

        $(document).on('change','.cat-list',function(){
            let id = $('.cat-list option:selected').attr('data-id');
            $.ajax({
                type:'get',
                url : "{{ route('admin.sub.categories') }}",
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

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
		<div class="we-page-title">
			<div class="row">
				<div class="col-md-8 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Create Template</li>
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
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">                        
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <section class="list-your-service-section">
                            <div class="list-your-service-content">
                                <div class="list-your-service-form-box">
                                    <div class="row">
                                        <div class="col-md-12"><h5 class="card-title">Create Template</h5></div>
                                    </div>                                    
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Template Name</label>
                                    <input type="text" name="name" value="" class="form-control" id="templatename">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">SC Main Category</label>
                                    <select name="category_name" class="form-control cat-list">
                                        <option value="">--- Select Category ---</option>
                                        @forelse($categories as $cat)
                                            <option value="{{ $cat->id }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group sub-cat-list">
                                    <label for="">Category Tier 1</label>
                                    <select name="sub_category_name" class="form-control sub-cat">
                                        <option value="">--- Select Sub Category ---</option>
                                        @forelse($sub_categories as $cat)
                                            <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group sub-cat-list">
                                    <label for="">Category Tier 2</label>
                                    <select name="sub_category_name_2" class="form-control sub-cat">
                                        <option value="">--- Select Sub Category ---</option>
                                        @forelse($sub_categories_2_tier as $cat)
                                            <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group sub-cat-list">
                                    <label for="">Category Tier 3</label>
                                    <select name="sub_category_name_3" class="form-control sub-cat">
                                        <option value="">--- Select Sub Category ---</option>
                                        @forelse($sub_categories_3_tier as $cat)
                                            <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="formrender"></div>
                        <div class="build-wrap"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/highlight.min.js"></script>
<script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>
<script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    jQuery(function($) {
        const formData = '[{"type":"button","label":"Button","subtype":"button","className":"btn-default btn","name":"button-1701945548704-0","access":false,"style":"default"},{"type":"header","subtype":"h1","label":"Header","access":false},{"type":"text","required":false,"label":"Text Field","className":"form-control","name":"text-1701945553435-0","access":false,"subtype":"text"},{"type":"text","required":false,"label":"Text Field","className":"form-control","name":"text-1701945555694-0","access":false,"subtype":"text"},{"type":"number","required":false,"label":"Number","className":"form-control","name":"number-1701945574093-0","access":false,"subtype":"number"}]';
        var container = $('#formrender');
        const code = document.getElementById("formrender");
        const addLineBreaks = html => html.replace(new RegExp("><", "g"), ">\n<");
        var frm_options = {
            container,
            dataType:'json',
            formData:formData
        }; 
        // container.formRender(frm_options);
        // set < code > innerText with escaped markup
        // code.innerHTML = addLineBreaks(container.formRender("html"));
        // hljs.highlightBlock(code);

        // toastr.options.timeOut = 10000;
        toastr.options ={
           "closeButton" : true,
           "progressBar" : true,
           "disableTimeOut" : true,
        }

        var options = {
            onSave: function(evt, formData) {
                // This is the respected form's data
                // console.log('MY DATA_________', formData);
                var categoryname = $('.cat-list option:selected').attr('data-id');
                var sub_categoryname = $('.sub-cat option:selected').attr('data-id');
                var templatename = $('#templatename').val();
                if(!templatename){
                    alert("Template name should not be empty");
                } else{
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "post",
                        url: '{{ route('admin.store.template') }}',
                        data: {
                            categoryname: categoryname,
                            sub_categoryname: sub_categoryname,
                            templatename: templatename,
                            formData: formData,
                        },
                        success: function(response) {
                            if(response.status==201){
                                toastr.success(response.message);
                                setTimeout(function () {
                                    location.reload(true);
                                }, 1000);
                                return false;
                            }

                            if(response.status==200){
                                toastr.error(response.message);
                                return false;
                            }
                        }
                    });
                }
            }
        };
        $(document.getElementsByClassName('build-wrap')).formBuilder(options); 
    });
</script>
@endpush