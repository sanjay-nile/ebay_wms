@include('pages.frontend.client.breadcrumb', ['title' => 'Edit Package Detail'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<style type="text/css">
    .required-field::before {
        content: "*";
        color: red;
    }

    .rg-pack-card {
        background: #fbfbfb;
        margin-bottom: 10px;
        position: relative;
        border-radius: 10px;
        padding: 10px;
    }

    .rg-pack-card h2 {
        margin: 0;
        line-height: 1;
        color: #213051;
        font-size: 14px;
        font-weight: 800;
        padding: 0px 0px 10px 0;
        margin-bottom: 0;
    }
    form .yellow{
        border: 2px solid yellow !important;
    }
    form .purple{
        border: 2px solid purple !important;
    }
    form .pink{
        border: 2px solid pink !important;
    }
    form .green{
        border: 2px solid green !important;
    }
    .modal-xl {
        max-width: 94%;
        margin-left: 3%;
        margin-right: 3%;
    }
</style>
@endpush

@push('js')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('change','.cat-list',function(){
            let id = $('.cat-list option:selected').attr('data-id');;
            $.ajax({
                type:'get',
                url : "{{ route('admin.sub.categories') }}",
                data:{cat_id:id, level: 1},
                dataType : 'json',
                success : function(data){
                    $(".sub-cat-list").replaceWith(data.html);
                }
            });
        });

        $(document).on('change','.sub-cat',function(){
            let id = $('.sub-cat option:selected').attr('data-id');;
            $.ajax({
                type:'get',
                url : "{{ route('admin.sub.categories') }}",
                data:{cat_id:id, level: 2},
                dataType : 'json',
                success : function(data){
                    $(".2-tier-cat-list").replaceWith(data.html);
                }
            });
        });

        $(document).on('change','.2-tier-cat',function(){
            let id = $('.2-tier-cat option:selected').attr('data-id');;
            $.ajax({
                type:'get',
                url : "{{ route('admin.sub.categories') }}",
                data:{cat_id:id, level: 3},
                dataType : 'json',
                success : function(data){
                    $(".3-tier-cat-list").replaceWith(data.html);
                }
            });
        });

        // toastr.options.timeOut = 10000;
        toastr.options ={
           "closeButton" : true,
           "progressBar" : true,
           "disableTimeOut" : true,
        }

        $('#myselect').on('change', function(e){
            let ll = $( "#myselect option:selected").val();
            let lv = $( "#leveldata").val();
            if(ll == 'L1'){
                $('#myModal').modal({ show: true });                
            } else {
                $('#myModal_2').modal({ show: true });
            }

            if(lv == 'L1'){
                $('#level-form-2 input[type="radio"]').each(function(){
                    $(this).removeAttr("checked");
                });
            } else {
                $('#level-form input[type="radio"]').each(function(){
                    $(this).removeAttr("checked");
                });
            }
        });

        $("#create-form, #find-order").on('submit',function(e){
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

        $("#level-form-2, #level-form").on('submit',function(e){
            e.preventDefault();
            var form = $(this);
            let formData = new FormData(this);
            var curSubmit = $(this).find("button.level-btn");
            
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
                        $('#myModal').modal('hide');
                        $('#myModal_2').modal('hide');
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

        $('input[name="dd_in"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
        $('input[name="dd_out"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});

        $(document).on('change','.order_status',function(){
            let id = $('.order_status option:selected').val();
            if(id == 'IS-04'){
                $('#dd_in').val('{{ date("Y/m/d") }}');
                $('#discrepancy_status').val('DS-01');
            } else {
                $('#dd_in').val(' ');
                $('#discrepancy_status').val(' ');
            }
        });

        $(document).on('change','.exist_pallet',function(){
            let m_cid = $('#main_category option:selected').val();
            let con_id = $('#condition option:selected').val();
            let cid_1 = $('#category_tier_1 option:selected').val();
            let cid_2 = $('#category_tier_2 option:selected').val();
            let cid_3 = $('#category_tier_3 option:selected').val();
            $.ajax({
                type:'get',
                url : "{{ route('admin.find.pallet') }}",
                data:{m_cid:m_cid, con_id: con_id, cid_1:cid_1, cid_2:cid_2, cid_3:cid_3},
                dataType : 'json',
                success : function(data){
                    $("#exist_pallet_list").replaceWith(data.html);
                }
            });
        });

        $(document).on('click','.comment-history',function(){
            var id = $(this).attr('data-id');
            var comment = 'comment';
            $.ajax({
                type:'get',
                url : "{{ route('admin.history.status') }}",
                data:{post_id:id, type:comment},
                dataType : 'json',
                success : function(response){
                    console.log(response.history);
                    $('#history-data').html(response.history);
                    $('#historymyModal').modal({show:true});
                    /*var myModal = new bootstrap.Modal(document.getElementById('historymyModal'))
                    myModal.show();*/
                }
            });
        });

        $(document).on('click','.dispencry-history',function(){
            var id = $(this).attr('data-id');
            var comment = 'dis_status';
            $.ajax({
                type:'get',
                url : "{{ route('admin.history.status') }}",
                data:{post_id:id, type:comment},
                dataType : 'json',
                success : function(response){
                    console.log(response.history);
                    $('#history-data').html(response.history);
                    $('#historymyModal').modal({show:true});
                    /*var myModal = new bootstrap.Modal(document.getElementById('historymyModal'))
                    myModal.show();*/
                }
            });
        });
    });
</script>
@endpush

<div class="app-contents contents"> 
    <div class="content-wrapper">
        <div class="row">
			<div class="col-md-12">
				@include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
			</div>

            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{-- <a href="{{ route('client.order.list') }}" class="btn btn-outline-primary btn-sm">
                                <i class="la la-arrow-left"></i> Back
                            </a> --}}
                            {{-- <a href="{{ route('client.order.invoice', $order['_order_id']) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                                <i class="fa fa-print"></i> Print the QR Code
                            </a>
                            <a href="javascript:void(0)" class="btn btn-outline-info btn-sm" id="comment-history">
                                <i class="fa fa-refresh"></i> Comment History
                            </a> --}}
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <section class="list-your-service-section">
                                <div class="list-your-service-content">
                                    <div class="">
                                        <div class="list-your-service-form-box">
                                           <form method="post" action="{{ route('client.order.update') }}" enctype="multipart/form-data" id="create-form">
                                                @csrf
                                                <input type="hidden" name="post_id" value="{{ $order['_order_id'] }}">
                                                <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">

                                                {{-- order details --}}
                                                <div class="rg-pack-card">
                                                    <div class="row">
                                                        <div class="col-md-12"><h2>Edit eBay Package :-</h2></div>
                                                    </div>
                                                </div>

                                                <div class="rg-pack-card">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">EVTN Number</label>
                                                                <input type="text" class="form-control" name="evtn_number" placeholder="Enter EVTN Number" value="{!! $order['evtn_number'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Label No./ Tracking No.</label>
                                                                <input type="text" class="form-control" name="tracking_number" placeholder="Enter Label No./ Tracking No." value="{!! $order['tracking_number'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Package Weight</label>
                                                                <input type="text" class="form-control" name="weight" placeholder="Enter weight" value="{!! $order['weight'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        {{-- customer detail --}}
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Customer Name</label>
                                                                <input type="text" class="form-control" name="customer_name" placeholder="Enter Customer Name" value="{!! $order['customer_name'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">City</label>
                                                                <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="{!! $order['customer_city'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">ZipCode</label>
                                                                <input type="text" class="form-control" name="customer_pincode" placeholder="Enter ZipCode" value="{!! $order['customer_pincode'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">State</label>
                                                                <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="{!! $order['customer_state'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Country</label>
                                                                <input type="text" class="form-control" name="customer_country" placeholder="Enter Country" value="{!! $order['customer_country'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        {{-- customer detail --}}
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Seller Name</label>
                                                                <input type="text" class="form-control" name="seller_name" placeholder="Enter Seller Name" value="{!! $order['seller_name'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Seller City</label>
                                                                <input type="text" class="form-control" name="seller_city" placeholder="Enter City" value="{!! $order['seller_city'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Seller ZipCode</label>
                                                                <input type="text" class="form-control" name="seller_pincode" placeholder="Enter ZipCode" value="{!! $order['seller_pincode'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Seller State</label>
                                                                <input type="text" class="form-control" name="seller_state" placeholder="Enter State" value="{!! $order['seller_state'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Seller Country</label>
                                                                <input type="text" class="form-control" name="seller_country" placeholder="Enter Country" value="{!! $order['seller_country'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">eBay Order No.</label>
                                                                <input type="text" class="form-control" name="order_number" placeholder="Enter Order No." value="{!! $order['order_number'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Order Status</label>
                                                                <select name="order_status" class="form-control order_status" disabled>
                                                                    <option value="">-- Select --</option>
                                                                    @forelse(inception_status() as $st => $sv)
                                                                        <option value="{{ $st }}" @if($order['order_status'] == $st) selected @endif>{{$sv}}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                        </div>

                                                        @if(isset($order['orderCost']))
                                                            @forelse(json_decode($order['orderCost']) as $cost)
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">{{ ucfirst(str_replace('_', ' ', $cost->type)) }}</label>
                                                                        <input type="text" class="form-control" value="{!! $cost->value->amount ?? '' !!}" disabled>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                            @endforelse
                                                        @endif

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="">Customer Comment</label>
                                                                <textarea name="comment" class="form-control" disabled>{!! $order['comment'] ?? '' !!}</textarea>
                                                            </div>
                                                        </div>

                                                        @if(isset($order['image']))
                                                            <div class="col-md-2">
                                                                <div class="edit-form-group">
                                                                    <div class="edit-form-text">Photo</div>
                                                                    <div class="edit-form-value">
                                                                        <div class="edit-form-value-img">
                                                                            <a href="{{ asset('public/uploads/'.$order['image'])}}" target="_blank"><img src="{{ asset('public/uploads/'.$order['image'])}}"></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(isset($order['image_url']) && !empty($order['image_url']))
                                                            <div class="col-md-5">
                                                                <div class="edit-form-group">
                                                                    <div class="edit-form-text">SCX photo</div>
                                                                    <div class="edit-form-value">
                                                                        <div class="row">
                                                                            {{-- <div class="edit-form-value-img">
                                                                                <a href="{{ $order['image_url'] }}" target="_blank"><img src="{{ $order['image_url'] }}"></a>
                                                                            </div> --}}
                                                                            @if(isset($order['media_urls']) && !empty($order['media_urls']))
                                                                                @forelse(json_decode($order['media_urls']) as $k)
                                                                                    <div class="edit-form-value-img ml-1">
                                                                                        <a href="{{ route('admin.order.image', $order['_order_id']) }}" target="_blank"><img src="{{ $k }}"></a>
                                                                                    </div>
                                                                                @empty
                                                                                @endforelse
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- item details --}}
                                                <div class="rg-pack-card mt-2">
                                                    <div class="row">
                                                        <div class="col-md-12"><h2>Item Details :-</h2></div>
                                                    </div>
                                                </div>
                                                @if(count($posts->package) > 0)
                                                    <div class="rg-pack-card">
                                                        @forelse($posts->package as $item)
                                                            @php
                                                                $item_data = json_decode($item->package_data);
                                                                // dd($item_data);
                                                            @endphp
                                                            <input type="hidden" id="item_id" value="{{ $item->id }}">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">SKU #</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->itemSku ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Title #</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->title ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Expected Qty</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->itemQuantity ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Original Sales Incoterm</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->serviceName ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">HS Code</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->hs_code ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">COO</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->coo ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                @if(isset($item_data->lineItemCost))
                                                                    @forelse($item_data->lineItemCost as $cost)
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="">{{ ucfirst(str_replace('_', ' ', $cost->type)) }}</label>
                                                                                <input type="text" class="form-control" value="{!! $cost->value->amount ?? '' !!}" disabled>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                @endif

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">SC Master Category</label>
                                                                        <select name="category_name" class="form-control cat-list" disabled>
                                                                            <option value="">--- Select Category ---</option>
                                                                            @forelse($categories as $cat)
                                                                                <option value="{{ $cat->id }}" data-id="{{ $cat->id }}" @if($cat->id == $item->category) selected @endif>{{ $cat->name }}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group sub-cat-list">
                                                                        <label for="">Category Tier 1</label>
                                                                        <select name="sub_category_name" class="form-control sub-cat" disabled>
                                                                            <option value="">--- Select Sub Category ---</option>
                                                                            @forelse($sub_categories as $cat)
                                                                                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}" @if($cat->code == $item->sub_category_1) selected @endif>{{ $cat->name }}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group 2-tier-cat-list">
                                                                        <label for="">Category Tier 2</label>
                                                                        <select name="sub_category_name_2" class="form-control 2-tier-cat" disabled>
                                                                            <option value="">--- Select Sub Category ---</option>
                                                                            @forelse($sub_categories_2_tier as $cat)
                                                                                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}" @if($cat->code == $item->sub_category_2) selected @endif>{{ $cat->name }}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group 3-tier-cat-list">
                                                                        <label for="">Category Tier 3</label>
                                                                        <select name="sub_category_name_3" class="form-control 3-tier-cat" disabled>
                                                                            <option value="">--- Select Sub Category ---</option>
                                                                            @forelse($sub_categories_3_tier as $cat)
                                                                                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}" @if($cat->code == $item->sub_category_3) selected @endif>{{ $cat->name }}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Inspection Level Required</label>
                                                                        <div class="row g-1">
                                                                            <div class="col-md-10">
                                                                                <select name="item[{{$item->id}}][ins_level][]" class="form-control" id="myselect"  disabled>
                                                                                    <option value="">-- Select --</option>
                                                                                    <option value="L1" @if($item->inspection_level == 'L1') selected @endif>Level 1</option>
                                                                                    <option value="L2" @if($item->inspection_level == 'L2') selected @endif>Level 2</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                @if($item->inspection_level == 'L1')
                                                                                    <button type="button" class="btn-View-level" data-toggle="modal" data-target="#myModal-{{$item->id}}">
                                                                                        <i class="fa fa-eye"></i>
                                                                                    </button>
                                                                                @else
                                                                                    <button type="button" class="btn-View-level" data-toggle="modal" data-target="#myModal-2-{{$item->id}}">
                                                                                       <i class="fa fa-eye"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Inspection Status</label>
                                                                        <select name="item[{{$item->id}}][ins_status][]" class="form-control order_status" disabled>
                                                                            <option value="">-- Select --</option>
                                                                            @forelse(inception_status() as $st => $sv)
                                                                                <option value="{{ $st }}" @if($item->status == $st) selected @endif>{{$sv}}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group condition-list">
                                                                        <label for="">Received Condition</label>
                                                                        <select name="item[{{$item->id}}][condition][]" class="form-control" disabled>
                                                                            <option value="">-- Select --</option>
                                                                            @foreach(conditionCode() as $code)
                                                                                <option value="{{ $code }}" @if($item->received_condition == $code) selected @endif>{{ $code }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-3 dis_status">
                                                                    <div class="form-group">
                                                                        <label for="">Discrepancy Status</label>
                                                                        <select name="item[{{$item->id}}][dis_status][]" id="discrepancy_status" class="form-control yellow">
                                                                            <option value="">-- Select --</option>
                                                                            @forelse(discrepancy_status() as $st => $sv)
                                                                                <option value="{{ $st }}" @if($item->discrepancy_status == $st) selected @endif>{{$sv}}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Discrepancy Date In</label>
                                                                        <input type="text" class="form-control" name="item[{{$item->id}}][dis_dt_in][]" id="dd_in" placeholder="Discrepancy Date In" value="{!! $item->discrepancy_date_in ?? '' !!}" disabled>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Discrepancy Date Out</label>
                                                                        <input type="text" class="form-control" name="item[{{$item->id}}][dis_dt_out][]" placeholder="Discrepancy Date Out" value="{!! $item->discrepancy_date_out ?? '' !!}" disabled>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group condition-list">
                                                                        <label for="">Listing Condition</label>
                                                                        <input type="text" class="form-control" disabled value="{!! $item->condition ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Item Date Type</label>
                                                                        <select name="item[{{$item->id}}][item_date_type][]" id="item_date_type" class="form-control green">
                                                                            <option value="">-- Select --</option>
                                                                            @forelse(getItemDate() as $sv)
                                                                                <option value="{{ $sv }}" @if($item->item_date_type == $sv) selected @endif>{{$sv}}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Item Date</label>
                                                                        <input type="text" class="form-control green" name="item[{{$item->id}}][item_date][]" id="item_date" placeholder="Item Date" value="{!! $item->item_date ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="edit-form-group">
                                                                        <div class="edit-form-text">eBay Listing link</div>
                                                                        <div class="edit-form-value ml-1">
                                                                            <a href="{!! $item->itemUrl ?? '#' !!}" class="btn btn-outline-primary" target="_blank"> View</a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if(isset($item_data->imageUrls) && !empty($item_data->imageUrls))
                                                                    <div class="col-md-6">
                                                                        <div class="edit-form-group">
                                                                            <div class="edit-form-text">eBay photo</div>
                                                                            <div class="edit-form-value">
                                                                                <div class="row">
                                                                                    @forelse($item_data->imageUrls as $k)
                                                                                        <div class="edit-form-value-img ml-1">
                                                                                            <a href="{{ route('admin.order.ebay.image', $item->id) }}" target="_blank"><img src="{{ $k }}"></a>
                                                                                        </div>
                                                                                    @empty
                                                                                    @endforelse
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Comment</label>
                                                                        <textarea name="item[{{$item->id}}][comments][]" class="form-control yellow"></textarea>
                                                                    </div>
                                                                </div>

                                                                @if(isset($item_data->itemAttributes))
                                                                    @forelse($item_data->itemAttributes as $attr)
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="">{{ $attr->name }}</label>
                                                                                <input type="text" class="form-control" value="{!! $attr->value ?? '' !!}" disabled>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                @endif

                                                                <div class="col-md-3">
                                                                    <div class="form-group condition-list">
                                                                        <label for="">Listing Condition</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->condition ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Reason of Return</label>
                                                                        <input type="text" class="form-control" placeholder="Reason of Return" value="{!! $item->reason_of_return ?? '' !!}" disabled>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0)" class="btn btn-info btn-sm comment-history" data-id="{{ $item->id }}">
                                                                        <i class="fa fa-refresh"></i> Comment History
                                                                    </a>
                                                                    <a href="javascript:void(0)" class="btn btn-dark btn-sm dispencry-history" data-id="{{ $item->id }}">
                                                                        <i class="fa fa-refresh"></i> Discrepancy History
                                                                    </a>
                                                                    <a href="{{ route('admin.item.invoice', $item->id) }}" class="btn btn-danger btn-sm" target="_blank">
                                                                        <i class="fa fa-print"></i> Print the QR Code
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                @endif

                                                {{--  Liquidation Information --}}
                                                <div class="rg-pack-card mt-2">
                                                    <div class="row">
                                                        <div class="col-md-12"><h2>Liquidation Information :-</h2></div>
                                                    </div>
                                                </div>

                                                <div class="rg-pack-card mt-2">
                                                    <div class="row">
                                                        @php
                                                            $pallet = getPalletDetails($order['pallet_id']);
                                                            $cn = (!empty($pallet)) ? $pallet->posts()->count() : 1;
                                                        @endphp
                                                        @if(!empty($pallet))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Customer Name</label>
                                                                    <input type="text" name="l_cname" class="form-control" value="{{ $pallet->meta->l_cname ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Customer Address</label>
                                                                    <input type="text" name="l_address" class="form-control" value="{{ $pallet->meta->l_address ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Price</label>
                                                                    @if($pallet->hasMeta('l_price'))
                                                                        <input type="text" name="l_price" class="form-control" value="{{ round($pallet->meta->l_price / $cn , 2) }}" disabled>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">IncoTerm</label>
                                                                    <input type="text" name="l_incoterm" class="form-control" value="{{ $pallet->meta->l_incoterm ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Duty Paid</label>
                                                                    @if($pallet->hasMeta('l_duty_paid'))
                                                                        <input type="text" name="l_duty_paid" class="form-control" value="{{ round($pallet->meta->l_duty_paid / $cn , 2) }}" disabled>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Tax Paid</label>
                                                                    @if($pallet->hasMeta('l_tax_paid'))
                                                                        <input type="text" name="l_tax_paid" class="form-control" value="{{ round($pallet->meta->l_tax_paid / $cn, 2) }}" disabled>
                                                                    @endif                                                                
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Customs Broker</label>
                                                                    <input type="text" name="l_custom_broker" class="form-control" value="{{ $pallet->meta->l_custom_broker ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Currency</label>
                                                                    <input type="text" name="l_currency" class="form-control" value="{{ $pallet->meta->l_currency ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Channel Sold By</label>
                                                                    <input type="text" name="l_chanel" class="form-control" value="{{ $pallet->meta->l_chanel ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Sold Type</label>
                                                                    <input type="text" name="l_stype" class="form-control" value="{{ $pallet->meta->l_stype ?? '' }}" disabled>
                                                                </div>
                                                            </div>

                                                            @if($pallet->hasMeta('certificate'))
                                                                <div class="col-md-3">
                                                                    <div class="edit-form-group">
                                                                        <div class="edit-form-text">Destruction Certificate</div>
                                                                        <div class="edit-form-value">
                                                                            <div class="edit-form-value-img">
                                                                                <a href="{{ asset('public/uploads/'.$pallet->meta->certificate)}}" target="_blank">
                                                                                    Download
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>                                                
                                                
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn-red pull-right add-btn">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
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

<input type="hidden" id="leveldata" value="{{ $order['in_level'] ?? ''}}">

@if(count($posts->package) > 0)
    @forelse($posts->package as $item)
        {{-- level 1 --}}
        <div class="modal fade" id="myModal-{{ $item->id }}" role="dialog">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="shipcycle-modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Level 1</h4>
                        <form method="post" action="{{ route('admin.item.update') }}" enctype="multipart/form-data" class="level-form">
                            <div class="question-list">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $item->id }}">
                                <input type="hidden" name="ins_level" value="model">
                                @php $i = 1; $j = 2; @endphp
                                @foreach(newFirstLevel() as $k => $v)
                                    <div class="question-item">
                                        <div class="question-item-box">
                                            <div class="question-item-label">
                                                <div class="pmu-q-badge">Q</div> 
                                            </div>
                                            <div class="question-item-text">
                                                {{ $v }}
                                            </div>
                                        </div>
                                        <div class="answer-item-list">
                                            <div class="answer-item-box">
                                                <div class="answer-radio">
                                                    <input type="radio" data-name="{{ $k }}" class="option-radio" id="answer-{{ $i }}-yes" name="{{ $k }}" value="Yes" @if($item->hasMeta($k) && $item->getMeta($k) == 'Yes') checked @endif>
                                                    <label for="answer-{{ $i }}-yes">
                                                        <span class="radiocheck-icon"></span>
                                                        <span class="radiocheck-text">Yes</span>
                                                    </label>
                                                </div>
                                                <div class="answer-radio">
                                                    <input type="radio" data-name="{{ $k }}" class="option-radio" id="answer-{{ $j }}-no" name="{{ $k }}" value="No" @if($item->hasMeta($k) && $item->getMeta($k) == 'No') checked @endif>
                                                    <label for="answer-{{ $j }}-no"> No </label>
                                                </div>
                                            </div>
                                            @if(!empty(firstLevelFieldType($k)))
                                                <div class="field-item-box @if(!$item->hasMeta($k)) collapse @endif {{ $k }}">
                                                    @if(firstLevelFieldType($k) == 'text')
                                                        @forelse(firstLevelExtra($k) as $kk => $vv)
                                                            <div class="answer-radio mt-1">
                                                                <input type="text" name="{{ $kk }}" value="@if($item->hasMeta($kk)) {{ $item->getMeta($kk) }} @endif" class="form-control" placeholder="{{ $vv }}">
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    @endif
                                                    @if(firstLevelFieldType($k) == 'select')
                                                        @php
                                                            $sk = $k.'_option';
                                                            $sv = $item->getMeta($sk);
                                                        @endphp
                                                        <div class="answer-radio">
                                                            <select name="{{ $k }}_option" class="form-control">
                                                                <option value="">-- Select --</option>
                                                                @forelse(firstLevelExtra($k) as $kv => $vk)
                                                                    <option value="{{ $kv }}" @if($item->hasMeta($sk) && $kv == $sv) selected @endif>{{ $vk }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @php $i++; $j++; @endphp
                                @endforeach
                            </div>
                            <div class="shipcycle-modal-footer">
                                <button type="button" class="btn-Cancel close" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn-Submit level-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- level 2 --}}
        <div class="modal fade" id="myModal-2-{{ $item->id }}" role="dialog">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="shipcycle-modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Level 2</h4>
                        <form method="post" action="{{ route('admin.item.update') }}" enctype="multipart/form-data" class="level-form">
                            <div class="question-list">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $item->id }}">
                                <input type="hidden" name="ins_level" value="model">
                                @php $m = 3; $l = 4; @endphp
                                @foreach(secondLevel() as $kk => $v)
                                    <div class="question-item">
                                        <div class="question-item-box">
                                            <div class="question-item-label">
                                                <div class="pmu-q-badge">Q</div> 
                                            </div>
                                            <div class="question-item-text">
                                                {{ $v }}
                                            </div>
                                        </div>
                                        <div class="answer-item-list">
                                            <div class="answer-item-box">
                                                <div class="answer-radio">
                                                    <input type="radio" id="second-{{ $m }}-yes" name="{{ $kk }}" value="Yes" @if(isset($order[$kk]) && $order[$kk] == 'Yes') checked @endif>
                                                    <label for="second-{{ $m }}-yes">
                                                        <span class="radiocheck-icon"></span>
                                                        <span class="radiocheck-text">Yes</span>
                                                    </label>
                                                </div>
                                                <div class="answer-radio">
                                                    <input type="radio" id="second-{{ $l }}-no" name="{{ $kk }}" value="No" @if(isset($order[$kk]) && $order[$kk] == 'No') checked @endif>
                                                    <label for="second-{{ $l }}-no"> No </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $m++; $l++; @endphp
                                @endforeach
                            </div>
                            <div class="shipcycle-modal-footer">
                                <button type="button" class="btn-Cancel close" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn-Submit level-btn">Submit</button>
                            </div>
                        </form>
                    </div>            
                </div>
            </div>
        </div>
    @empty
    @endforelse
@endif

<div class="modal fade" id="historymyModal" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content track-content">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">                
                <div id="history-data"></div>
            </div>
        </div>
    </div>
</div>