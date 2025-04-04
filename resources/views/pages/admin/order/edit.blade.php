@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
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
    form .purple{
        border: 2px solid purple !important;
    }
    form .pink{
        border: 2px solid pink !important;
    }
    form .green{
        border: 2px solid green !important;
    }
</style>
@endpush

@php
    $ref = 'SC-ORD-I-'.date('mdY').'-'.$order['_order_id'].'-'.count($posts->package).'A';
@endphp

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/js/select2.min.js') }}"></script>

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

        /*$(document).on('change','.sub-cat',function(){
            let id = $('.cat-list option:selected').attr('data-id');
            let sid = $('.sub-cat option:selected').attr('data-id');
            $.ajax({
                type:'get',
                url : "{{ route('admin.fillter.sub.candition') }}",
                data:{cat_id:id, sub_cat_id: sid},
                dataType : 'json',
                success : function(data){
                    $(".condition-list").replaceWith(data.html);
                }
            })
        });*/

        // toastr.options.timeOut = 10000;
        toastr.options ={
           "closeButton" : true,
           "progressBar" : true,
           "disableTimeOut" : true,
        }

        $('.myselect').on('change', function(e){
            let ll = $( ".myselect option:selected").val();
            let id = $(this).attr('data-id');
            let lv = $( "#leveldata").val();
            if(ll == 'L1'){
                $('#myModal-'+id).modal({ show: true });                
            } else {
                $('#myModal-2-'+id).modal({ show: true });
            }

            /*if(lv == 'L1'){
                $('#level-form-2 input[type="radio"]').each(function(){
                    $(this).removeAttr("checked");
                });
            } else {
                $('#level-form input[type="radio"]').each(function(){
                    $(this).removeAttr("checked");
                });
            }*/
        });

        $("#create-form").on('submit',function(e){
            e.preventDefault();
            var form = $(this);
            let formData = new FormData(this);
            var curSubmit = $(this).find("button.add-btn");
            let id = $('#rcv_condition option:selected').val();
            if(id == '' || id == 'undefined'){
                toastr.error('Received Condition is mandatory.');
                return false;
            } else{
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
            }
        });

        $("#find-order").on('submit',function(e){
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

        $(".level-form").on('submit',function(e){
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

        let explt =`
            <div class="col-md-3">
                <label>SC Main Category <span class="required-field"></span></label>
                <div class="form-group">
                    <select name="main_category" class="form-control exist_pallet" id="main_category">
                        <option value="">--- Select Category ---</option>
                        @forelse($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label>Received Condition <span class="required-field"></span></label>
                <div class="form-group">
                    <select name="condition" class="form-control exist_pallet" id="condition">
                        <option value="">-- Select --</option>
                        @foreach(conditionCode() as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label>Category Tier 1</label>
                <div class="form-group">
                    <select name="category_tier_1" class="form-control exist_pallet" id="category_tier_1">
                        <option value="">-- Select --</option>
                        @forelse($sub_categories as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label>Category Tier 2</label>
                <div class="form-group">
                    <select name="category_tier_2" class="form-control exist_pallet" id="category_tier_2">
                       <option value="">-- Select --</option>
                        @forelse($sub_categories_2_tier as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label>Category Tier 3</label>
                <div class="form-group">
                    <select name="category_tier_3" class="form-control exist_pallet" id="category_tier_3">
                        <option value="">-- Select --</option>
                        @forelse($sub_categories_3_tier as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <label>Existing Pallet</label>
                <div class="form-group" id="exist_pallet_list">
                    <select name="pallet_name" id="" class="form-control">
                        @forelse($pallets as $pallet)
                            <option value="{{ $pallet->pallet_id }}" @if($pallet->pallet_type == 'InProcess') style="color:green" @else style="color:red" @endif>{{ $pallet->pallet_id }}</option>
                        @empty
                            <option value="">No Existing pallet exists</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="col-md-3 mt-1">
                <label>Select Package <span class="required-field"></span></label>
                <div class="form-group">
                    @forelse($posts->package as $item)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="pallet_orders[]" value="{{$item->id}}" id="{{$item->sku}}" @if(!empty($item->pallet_id)) checked disabled @endif>
                            <label class="form-check-label" for="{{$item->sku}}">{{$item->package_id}}</label>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <div class="col-md-3">
                <input type="hidden" name="form_type" value="edit">
                <button type="button" id="add-to-old-pallet" class="btn add-to-pallet mt-2">Submit</button>
            </div>`;


        let crplt =`<div class="col-md-3"><label>Pallet ID </label>
        <div class="form-group">
            <input type="text" name="pallet_name" value="{{ generateUniquePalletNames() }}" class="form-control" readonly="readonly">
        </div></div>

        <div class="col-md-3">
            <label>Condition <span class="required-field"></span></label>
            <div class="form-group">
                <select name="condition" id="" class="form-control" required>
                    <option value="">-- Select --</option>
                    @foreach(conditionCode() as $code)
                        <option value="{{ $code }}">{{ $code }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>Original Sales Incoterm <span class="required-field"></span></label>
            <div class="form-group">
                <select name="sales_incoterm" id="" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option value="DDU">EXPORTS DDU</option>
                    <option value="DDP">EXPORTS DDP</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>Reselling Grade</label>
            <div class="form-group">
                <select name="reselling_grade" id="" class="form-control" required>
                    <option value="">-- Select --</option>
                    @foreach(getResellingGrade() as $code)
                        <option value="{{ $code }}">{{ $code }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>From Warehouse <span class="required-field"></span></label>
            <div class="form-group">
                <select name="fr_warehouse_id" id="to_client_warehouse_list" class="form-control">
                    <option value="">-- Select --</option>
                        @forelse($warehouse_list as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @empty
                            <option value="">Warehouse not added yet</option>
                        @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>To Warehouse </label>
            <div class="form-group">
                <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                    <option value="">-- Select --</option>
                        @forelse($warehouse_list as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @empty
                            <option value="">Warehouse not added yet</option>
                        @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>SC Main Category <span class="required-field"></span></label>
            <div class="form-group">
                <select name="main_category" class="form-control">
                    <option value="">--- Select Category ---</option>
                    @forelse($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>Category Tier 1</label>
            <div class="form-group">
                <select name="category_tier_1" class="form-control">
                    <option value="">-- Select --</option>
                    @forelse($sub_categories as $cat)
                        <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>Category Tier 2</label>
            <div class="form-group">
                <select name="category_tier_2" class="form-control">
                   <option value="">-- Select --</option>
                    @forelse($sub_categories_2_tier as $cat)
                        <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label>Category Tier 3</label>
            <div class="form-group">
                <select name="category_tier_3" class="form-control">
                    <option value="">-- Select --</option>
                    @forelse($sub_categories_3_tier as $cat)
                        <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>

        <div class="col-md-3 mt-1">
            <label>Select Package <span class="required-field"></span></label>
            <div class="form-group">
                @forelse($posts->package as $item)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="pallet_orders[]" value="{{$item->id}}" id="{{$item->sku}}" @if(!empty($item->pallet_id)) checked disabled @endif>
                        <label class="form-check-label" for="{{$item->sku}}">{{$item->package_id}}</label>
                    </div>
                @empty
                @endforelse
            </div>
        </div>

        <div class="col-md-3">
            <input type="hidden" name="form_type" value="add">
            <button type="button" id="add-to-pallet" class="btn add-to-pallet mt-2 mb-2">Submit</button>
        </div>`;

        $("#create-pallet").click(function () {
            $("#ex-pallet").html(crplt);
        });

        $(document).on('click','#existing-pallet',function(){
            $("#ex-pallet").html(explt);
        });

        $(document).on('click',"#add-to-pallet", function(){            
            $("#pallet-save").submit();            
        });

        $(document).on('click',"#add-to-old-pallet", function(){           
            $("#pallet-save").submit();
        });

        $('input[name="dd_in"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
        $('input[name="dd_out"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
        $('#item_date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd", orientation: "bottom left"});
        $('.dd-date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

        $('.option-radio').on('change', function() {
            var array1 = ['match_quantity', 'ebay_listing_item'];
            var selectedValue = $(this).attr('data-name');
            var selected = $(this).val();

            if(selectedValue == 'visible_damage' && selected == 'Yes'){
                $('.'+selectedValue).removeClass('collapse');
            } else if (selectedValue == 'ebay_listing_item' && selected == 'No'){
                $('.'+selectedValue).removeClass('collapse');
            } else if (selectedValue == 'match_quantity' && selected == 'No'){
                $('.'+selectedValue).removeClass('collapse');
            } else if (selectedValue == 'inspection' && selected == 'Yes'){
                $('.'+selectedValue).removeClass('collapse');
            } else {
                $('.'+selectedValue).addClass('collapse');   
            }
        });

        $(document).on('change','.inspection_status',function(){
            let id = $('.inspection_status option:selected').val();
            if(id == 'IS-04'){
                $('#dd_in').val('{{ date("Y/m/d") }}');
                $('#discrepancy_status').val('DS-01');
            }/* else {
                $('#dd_in').val(' ');
                $('#discrepancy_status').val(' ');
            }*/

            $('.order_status').val(id);
        });

        $(document).on('change','.discrepancy_status',function(){
            let id = $('.discrepancy_status option:selected').val();
            if(id == 'DS-04'){
                $('#dd_out').val('{{ date("Y/m/d") }}');
            } else {
                $('#dd_out').val(' ');
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

        $(document).on('click','#publish-ebay',function(){
            var id = $(this).attr('data-id');
            if (confirm('Are you sure you want to publish this item on eBay.?')) {
                $.ajax({
                    type:'get',
                    url : "{{ route('admin.ebay.inventory') }}",
                    data:{post_id:id},
                    dataType : 'json',
                    beforeSend : function(){
                        $('#publish-ebay').html(`Sending.. <i class="la la-spinner la-spin"></i>`).attr('disabled',true);
                    },
                    success : function(response){
                        if(response.status==201){
                            $('#publish-ebay').html(`<i class="fa fa-bars" aria-hidden="true"></i> Active on eBay`).attr('disabled',false);
                            toastr.success(response.message);
                            return false;
                        }

                        if(response.status==200){
                            $('#publish-ebay').html(`<i class="fa fa-bars" aria-hidden="true"></i> Active on eBay`).attr('disabled',false);
                            toastr.error(response.message);
                            return false;
                        }
                    },
                });
            }
        });

        $(document).on('click','#draft-ebay',function(){
            var id = $(this).attr('data-id');
            if (confirm('Are you sure you want to draft this item on eBay.?')) {
                $.ajax({
                    type:'get',
                    url : "{{ route('admin.ebay.draft') }}",
                    data:{post_id:id},
                    dataType : 'json',
                    beforeSend : function(){
                        $('#draft-ebay').html(`Sending.. <i class="la la-spinner la-spin"></i>`).attr('disabled',true);
                    },
                    success : function(response){
                        if(response.status==201){
                            $('#draft-ebay').html(`<i class="fa fa-bars" aria-hidden="true"></i> Draft on eBay`).attr('disabled',false);
                            toastr.success(response.message);
                            location.reload();
                            return false;
                        }

                        if(response.status==200){
                            $('#draft-ebay').html(`<i class="fa fa-bars" aria-hidden="true"></i> Draft on eBay`).attr('disabled',false);
                            toastr.error(response.message);
                            return false;
                        }
                    },
                });
            }
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

        $("#addRow").click(function () {
            var html = '';
            html += '<div id="inputFormRow" class="row">';
            html += '<div class="col-md-3"><div class="form-group"><label>Item Ref. Number</label><input type="text" name="package_id[]" value="{{ $ref }}" class="form-control"></div></div>';
            html += '<div class="col-md-3"><div class="form-group"><label>SKU Number</label><input type="text" name="sku_number[]" class="form-control"></div></div>';
            html += '<div class="col-md-3"><div class="form-group"><label>Description</label><input type="text" name="description[]" class="form-control"></div></div>';
            html += '<div class="col-md-3"><div class="form-group"><label>Brand <span class="astrick">*</span></label><input type="text" name="brand[]" class="form-control"></div></div>';
            html += '<div class="col-md-3"><div class="form-group"><label>HS Code</label><input type="text" name="hs_code[]" class="form-control"></div></div>';
            html += '<div class="col-md-3"><div class="form-group"><label>Country of Origin</label><input type="text" name="country_of_origin[]" class="form-control"></div></div>';

            html += `<div class="col-md-3"><div class="form-group"><label>SC Main Category</label><select name="main_cat[]" class="form-control select2 assigncountry">
                                            <option value="">-- Select --</option>
                                            @forelse($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @empty
                                            @endforelse
                                        </select></div></div>`;

            html += `<div class="col-md-3"><div class="form-group"><label>Category Tier 1</label><select name="cat_tier_1[]" class="form-control select2 assigncountry">
                                            <option value="">-- Select --</option>
                                            @forelse($sub_categories as $cat)
                                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                            @empty
                                            @endforelse
                                        </select></div></div>`;

            html += `<div class="col-md-3"><div class="form-group"><label>Category Tier 2</label><select name="cat_tier_2[]" class="form-control select2 assigncountry">
                                            <option value="">-- Select --</option>
                                            @forelse($sub_categories_2_tier as $cat)
                                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                            @empty
                                            @endforelse
                                        </select></div></div>`;

            html += `<div class="col-md-3"><div class="form-group"><label>Category Tier 3</label><select name="cat_tier_3[]" class="form-control select2 assigncountry">
                                            <option value="">-- Select --</option>
                                            @forelse($sub_categories_3_tier as $cat)
                                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                            @empty
                                            @endforelse
                                        </select></div></div>`;

            html += '<div class="col-md-3"><div class="form-group"><label>Item Price<span class="astrick">*</span></label><input type="text" name="item_price[]" class="form-control"></div></div>';
            html += `<div class="col-md-3"><div class="form-group"><label>Received Condition</label><select name="r_condition[]" class="form-control">
                                            <option value="">-- Select --</option>
                                            @foreach(conditionCode() as $code)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select></div></div>`;
            html += '<div class="col-md-3"><div class="form-group"><a href="javascript::void(0)" class="btn btn-add btn-danger" id="removeRow">Remove</a></div></div>';
            html += '</div>';

            $('#newRow').append(html);
        });

        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
        });

        $(document).on('click',"#remove-pallet", function(){
            $("#move-warehouse").submit();
        });

        $('#flexCheckDefault').change(function() {
            if(this.checked) {
                $('.ovrsize').removeClass('collapse');
            } else {
                $('.ovrsize').addClass('collapse');
            }
        });
    });
    
    $(window).on('load', function() {
        $('.assigncountry').select2({
            placeholder: '--- Select ---',
            allowClear: true
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
        
        document.getElementById('local_time').value = formattedDateTime;
        document.getElementById('system_time').value = formattedDateTime;
        document.getElementById('pallet_time').value = formattedDateTime;
    }

    // Call this function before the form is submitted
    window.onload = setClientDateTime;
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
						<li class="breadcrumb-item active">Edit eBay Package</li>
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

            <div class="col-xs-12 col-md-12 ">
                <div class="card booking-info-box">            
                    <div class="card-header">
                        <div class="pallet-div">
                            <div class="row">
                                {{-- @if(!empty($order['pallet_id']))
                                    <p class="alert alert-success ml-1 mt-2"><b>Pallet ID :- </b> {{ $order['pallet_id'] }}</p>
                                @else --}}
                                    @if(Auth::user()->user_type_id == 1)
                                        <div class="col-md-2">
                                            <button type="button" id="existing-pallet" class="btn btn-blue">Add to Existing Pallet</button>
                                        </div>
                                    @endif
                                    <div class="col-md-2">
                                        <button type="button" id="create-pallet" class="btn btn-blue">Create New Pallet</button>
                                    </div>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="rg-pack-table">
                            <div class="booking-info-box">
                                <form action="{{ route('admin.add.pallet.orders') }}" method="post" id="pallet-save">
                                    @csrf
                                    {{-- <input name="pallet_orders[]" type="hidden" value="{{ $order['_order_id'] }}"> --}}
                                    <input type="hidden" name="pallet_time" value="" id="pallet_time">
                                    <div class="row ml-1" id="ex-pallet"></div>
                                </form>
                            </div>
                        </div>
                    </div>            
                </div>
            </div>

            {{-- @if(!isset($order['evtn_number']) || (isset($order['evtn_number']) && empty($order['evtn_number'])))
                <div class="col-xs-12 col-md-12 table-responsive">
                    <div class="card booking-info-box">
                        <div class="card-body">
                            <div class="rg-pack-table">
                                <div class="booking-info-box">
                                    <form action="{{ route('admin.order.insert') }}" method="post" id="find-order">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $order['_order_id'] }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Find EVTN Number</label>
                                                    <input type="text" class="form-control" name="evtn_number" placeholder="Enter EVTN Number" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <button type="submit" class="btn-red insert-btn btn-sm add-btn">Fetch Order</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif --}}

            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ route('admin.order.list') }}" class="btn btn-outline-primary btn-sm">
                                <i class="la la-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('admin.evtn.invoice', $order['_order_id']) }}" class="btn btn-outline-success btn-sm" target="_blank">
                                <i class="fa fa-print"></i> Print EVTN Label
                            </a>
                            {{-- <a href="{{ route('admin.order.invoice', $order['_order_id']) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                                <i class="fa fa-print"></i> Print the QR Code
                            </a> --}}
                            {{-- @if(!empty($order['pallet_id']) && Auth::user()->user_type_id == 1)
                                <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm" id="remove-pallet">
                                    <i class="fa fa-close"></i> Remove from pallet
                                </a>
                            @endif --}}
                            <form action="{{ route('admin.order.insert') }}" method="post" id="find-order">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $order['_order_id'] }}">
                                <input type="hidden" name="evtn_number" value="{!! $order['evtn_number'] ?? '' !!}">
                                <button type="submit" class="btn btn-warning btn-sm add-btn pull-right"> <i class="fa fa-refresh"></i> Refresh Order</button>
                            </form>
                            <form action="{{ route('admin.remove.pallet.orders') }}" method="post" id="move-warehouse">
                                @csrf
                                <input name="pkg_orders[]" type="hidden" value="{{ $order['_order_id'] }}">
                            </form>
                        </h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <section class="list-your-service-section">
                                <div class="list-your-service-content">
                                    <div class="">
                                        <div class="list-your-service-form-box">
                                           <form method="post" action="{{ route('admin.order.update') }}" enctype="multipart/form-data" id="create-form">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="post_id" value="{{ $order['_order_id'] }}">
                                                <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                                                <input type="hidden" name="system_time" value="" id="system_time">

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
                                                                <input type="text" class="form-control" name="evtn_number" placeholder="Enter EVTN Number" value="{!! $order['evtn_number'] ?? '' !!}" @if(Auth::user()->user_type_id != 1) disabled @endif>
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
                                                                <label for="">Package Weight (LBS)</label>
                                                                <input type="text" class="form-control purple" name="weight" placeholder="Enter weight" value="{!! $order['weight'] ?? '' !!}">
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
                                                                <label for="">Photo</label>
                                                                <input type="file" class="form-control" name="images[]" multiple>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Order Status</label>
                                                                <select name="order_status" class="form-control order_status">
                                                                    <option value="">-- Select --</option>
                                                                    @forelse(order_status() as $st => $sv)
                                                                        <option value="{{ $st }}" class="text-{{ get_budge_value($sv) }}" @if($order['order_status'] == $st) selected @endif>{{$sv}}</option>
                                                                    @empty
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Package Discrepancy Status</label>
                                                                <select name="package_dis_status" class="form-control">
                                                                    <option value="">-- Select --</option>
                                                                    @forelse(package_dis_status() as $st => $sv)
                                                                        <option value="{{ $st }}" class="text-{{ get_budge_value($sv) }}" @if(isset($order['package_dis_status']) && $order['package_dis_status'] == $st) selected @endif>{{$sv}}</option>
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

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Invoiced</label>
                                                                <input name="invoiced" class="form-control" value="{!! $order['invoiced'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Date Invoiced</label>
                                                                <input name="date_invoiced" class="form-control" value="{!! $order['date_invoiced'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Invoice Number</label>
                                                                <input name="invoice_number" class="form-control" value="{!! $order['invoice_number'] ?? '' !!}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="">Customer Comment</label>
                                                                <textarea name="comment" class="form-control" readonly>{!! $order['comment'] ?? '' !!}</textarea>
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

                                                        @if(isset($order['system_images']) && !empty($order['system_images']))
                                                            <div class="col-md-5">
                                                                <div class="edit-form-group">
                                                                    <div class="edit-form-text">Photos</div>
                                                                    <div class="edit-form-value">
                                                                        <div class="row">
                                                                            @forelse(json_decode($order['system_images']) as $k)
                                                                                <div class="edit-form-value-img ml-1">
                                                                                    <a href="{{ asset('public/uploads/'.$k)}}" target="_blank"><img src="{{ asset('public/uploads/'.$k)}}"></a>
                                                                                </div>
                                                                            @empty
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(isset($order['image_url']) && !empty($order['image_url']))
                                                            @php
                                                                // dd($order['media_urls']);
                                                            @endphp
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

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="">eBay Comment</label>
                                                                <textarea name="ebay_comment" class="form-control">{!! $order['ebay_comment'] ?? '' !!}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- item details --}}
                                                <div class="rg-pack-card mt-2">
                                                    <div class="row">
                                                        <div class="col-md-12"><h2>Item Details :-</h2></div>
                                                    </div>
                                                </div>
                                                
                                                @if(count($posts->package) > 0)
                                                    @forelse($posts->package as $item)
                                                        @php
                                                            $item_data = json_decode($item->package_data);
                                                            // dd($item_data);
                                                        @endphp
                                                        <div class="rg-pack-card">
                                                            @if(!empty($item->pallet_id))
                                                                <p class="alert alert-success"><b>Pallet ID :- </b> {{ $item->pallet_id }}</p>
                                                            @endif
                                                            <input type="hidden" id="item_id" value="{{ $item->id }}">
                                                            <div class="row mb-2 mt-2">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">SKU #</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->itemSku ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Item Ref.</label>
                                                                        <input type="text" name="item[{{$item->id}}][package_id][]" class="form-control" value="{!! $item->package_id ?? '' !!}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Title #</label>
                                                                        <input type="text" name="item[{{$item->id}}][title][]" class="form-control" value="{!! $item->title ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Original eBay Listing Qty</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->itemQuantity ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Original eBay Listing Qty</label>
                                                                        <input type="text" class="form-control" name="item[{{$item->id}}][original_qty][]" value="{!! $item->original_qty ?? '' !!}">
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Original Sales Incoterm</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->serviceName ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">HS Code</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->hs_code ?? '' !!}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">COO</label>
                                                                        <input type="text" class="form-control" readonly value="{!! $item->coo ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                @if(isset($item_data->lineItemCost))
                                                                    @forelse($item_data->lineItemCost as $cost)
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="">{{ ucfirst(str_replace('_', ' ', $cost->type)) }}</label>
                                                                                <input type="text" class="form-control" value="{!! $cost->value->amount ?? '' !!}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                @else
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="">Item Cost</label>
                                                                            <input type="text" class="form-control" value="{!! $item->price ?? '' !!}" readonly>
                                                                        </div>
                                                                    </div>
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

                                                                @if(isset($item_data->itemAttributes))
                                                                    @forelse($item_data->itemAttributes as $attr)
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="">{{ $attr->name }}</label>
                                                                                <input type="text" class="form-control" value="{!! $attr->value ?? '' !!}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                @else
                                                                    @php
                                                                        $orderResponse = json_decode(get_post_extra($item->post_id, 'find_order_response'), true);
                                                                        $final_order = reset($orderResponse['orders']);
                                                                    @endphp
                                                                    @forelse($final_order['lineItems'] as $li => $line)
                                                                        @if ($item->lineItemId == $line['lineItemId'])
                                                                            @foreach ($line['itemAttributes'] as $key => $attr)
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group">
                                                                                        <label for="">{{ $attr['name'] }}</label>
                                                                                        <input type="text" class="form-control" value="{!! $attr['value'] ?? '' !!}" readonly>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    @empty
                                                                    @endforelse
                                                                @endif

                                                                @if(isset($item_data->variationAttributes))
                                                                    @forelse($item_data->variationAttributes as $attr)
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="">{{ $attr->name }}</label>
                                                                                <input type="text" class="form-control" value="{!! $attr->value ?? '' !!}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="row mb-4">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Inspection Level Required</label>
                                                                        <div class="row g-1">
                                                                            <div class="col-md-10">
                                                                                <select name="item[{{$item->id}}][ins_level][]" class="form-control purple myselect" data-id="{{$item->id}}">
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
                                                                        <select name="item[{{$item->id}}][ins_status][]" class="form-control inspection_status purple">
                                                                            <option value="">-- Select --</option>
                                                                            @forelse(inception_status() as $st => $sv)
                                                                                <option value="{{ $st }}" class="text-{{ get_budge_value($sv) }}" @if($item->status == $st) selected @endif>{{$sv}}</option>
                                                                            @empty
                                                                            @endforelse
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                @php
                                                                    /*if ($item->reason_of_return == 'DEFECTIVE_ITEM' && in_array($item->condition, ['Used', 'used', 'For parts or not working'])) {
                                                                        $item->received_condition = 'Grade 3 D';
                                                                    } elseif ($item->reason_of_return == 'DEFECTIVE_ITEM' && in_array($item->condition, ['New'])) {
                                                                        $item->received_condition = 'Grade 3 D';
                                                                    } elseif ($item->reason_of_return == 'DEFECTIVE_ITEM') {
                                                                        $item->received_condition = 'Grade 3 D';
                                                                    } elseif (in_array($item->itemCondition, ['Used', 'used', 'For parts or not working'])) {
                                                                        $item->received_condition = 'Grade 1 N';
                                                                    }*/
                                                                @endphp

                                                                <div class="col-md-3">
                                                                    <div class="form-group condition-list">
                                                                        <label for="">Received Condition</label>
                                                                        <select name="item[{{$item->id}}][condition][]" class="form-control purple" id="rcv_condition">
                                                                            <option value="">-- Select --</option>
                                                                            @foreach(conditionCode() as $code)
                                                                                <option value="{{ $code }}" class="text-{{ get_budge_value($code) }}" @if($item->received_condition == $code) selected @endif>{{ $code }}</option>
                                                                            @endforeach
                                                                        
                                                                            {{-- <option value="{{ $item->condition }}" selected>{{ $item->condition }}</option> --}}
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3 dis_status">
                                                                    <div class="form-group">
                                                                        <label for="">Discrepancy Status</label>
                                                                        <select name="item[{{$item->id}}][dis_status][]" id="discrepancy_status" class="form-control discrepancy_status pink">
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
                                                                        <input type="text" class="form-control pink dd-date" name="item[{{$item->id}}][dis_dt_in][]" id="dd_in" placeholder="Discrepancy Date In" value="{!! $item->discrepancy_date_in ?? '' !!}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="">Discrepancy Date Out</label>
                                                                        <input type="text" class="form-control pink dd-date" name="item[{{$item->id}}][dis_dt_out][]" id="dd_out" placeholder="Discrepancy Date Out" value="{!! $item->discrepancy_date_out ?? '' !!}">
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

                                                                <div class="col-md-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="Yes" id="flexCheckDefault" name="item[{{$item->id}}][oversize][]" @if($item->oversize == 'Yes') checked @endif>
                                                                        <label class="form-check-label" for="flexCheckDefault">Oversize</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="Yes" id="flexCheckChecked" name="item[{{$item->id}}][dang_gud][]" @if($item->dang_gud == 'Yes') checked @endif>
                                                                        <label class="form-check-label" for="flexCheckChecked">Dangerous Goods</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="Yes" id="flexCheckChecked1" name="item[{{$item->id}}][empty_box][]" @if($item->empty_box == 'Yes') checked @endif>
                                                                        <label class="form-check-label" for="flexCheckChecked1">Empty Box</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3 ovrsize @if(empty($item->measure_unit)) collapse @endif">
                                                                    <div class="form-group">
                                                                        <label for="">Measure Unit</label>
                                                                        <input type="text" class="form-control" value="{!! $item->measure_unit ?? '' !!}" name="item[{{$item->id}}][measure_unit][]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3 ovrsize @if(empty($item->inbound_dim)) collapse @endif">
                                                                    <div class="form-group">
                                                                        <label for="">Inbound Package Dims</label>
                                                                        <input type="text" class="form-control"  value="{!! $item->inbound_dim ?? '' !!}" name="item[{{$item->id}}][inbound_dim][]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-3 ovrsize @if(empty($item->inspected_dim)) collapse @endif">
                                                                    <div class="form-group">
                                                                        <label for="">Inspected Package Dims</label>
                                                                        <input type="text" class="form-control"  value="{!! $item->inspected_dim ?? '' !!}" name="item[{{$item->id}}][inspected_dim][]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="">Comment</label>
                                                                        <textarea name="item[{{$item->id}}][comments][]" class="form-control" id="editor-{{$item->id}}"></textarea>
                                                                    </div>
                                                                </div>

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

                                                                <div class="col-md-6">
                                                                    <a href="javascript:void(0)" class="btn btn-info btn-sm comment-history" data-id="{{ $item->id }}">
                                                                        <i class="fa fa-refresh"></i> Comment History
                                                                    </a>
                                                                    <a href="javascript:void(0)" class="btn btn-dark btn-sm dispencry-history" data-id="{{ $item->id }}">
                                                                        <i class="fa fa-refresh"></i> Discrepancy History
                                                                    </a>
                                                                    <a href="{{ route('admin.item.invoice', $item->id) }}" class="btn btn-danger btn-sm" target="_blank">
                                                                        <i class="fa fa-print"></i> Print the QR Code
                                                                    </a>
                                                                    {{-- @if($item->hasMeta('inventory_status') && $item->meta->inventory_status == 'Pending' && $item->reason_of_return  != 'DEFECTIVE_ITEM' && $item->status == 'IS-02')
                                                                        <a class="btn btn-dark btn-sm" href="javascript:void(0)" id="publish-ebay" data-id="{{ $item->id }}">
                                                                            <i class="fa fa-bars" aria-hidden="true"></i> Active on eBay
                                                                        </a>

                                                                        <a class="btn btn-warning btn-sm" href="javascript:void(0)" id="draft-ebay" data-id="{{ $item->id }}">
                                                                            <i class="fa fa-bars" aria-hidden="true"></i> Send to Scheduled
                                                                        </a>
                                                                    @endif --}}
                                                                </div>

                                                                <div class="col-md-6">
                                                                    @if($item->hasMeta('inventory_status') && $item->meta->inventory_status == 'Completed')
                                                                        <p>Item sucessfully send to scheduled on {{ date('d/m/Y', strtotime($item->meta->inventory_date)) }} by user {{ $item->meta->inventory_user ?? ''}}.</p>
                                                                    @endif
                                                                    @if($item->hasMeta('inventory_status') && $item->meta->inventory_status == 'Pending')
                                                                        <p>Item is not listed.Error occurred upon send to scheduled on {{ date('d/m/Y', strtotime($item->meta->inventory_date)) }} by user {{ $item->meta->inventory_user ?? ''}}. To check the reason visit eBay sell - Not Scheduled items menu.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                    @endforelse
                                                @endif

                                                <div class="rg-pack-card">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn-View-level" id="addRow"> <i class="fa fa-plus"></i> Add Item </button>
                                                        </div>
                                                    </div>

                                                    <div id="newRow"></div>
                                                </div>

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
                                                                    <input type="text" name="l_cname" class="form-control" value="{{ $pallet->meta->l_cname ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Customer Address</label>
                                                                    <input type="text" name="l_address" class="form-control" value="{{ $pallet->meta->l_address ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Price</label>
                                                                    @if($pallet->hasMeta('l_price'))
                                                                        <input type="text" name="l_price" class="form-control" value="{{ round($pallet->meta->l_price / $cn , 2) }}" readonly>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">IncoTerm</label>
                                                                    <input type="text" name="l_incoterm" class="form-control" value="{{ $pallet->meta->l_incoterm ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Duty Paid</label>
                                                                    @if($pallet->hasMeta('l_duty_paid'))
                                                                        <input type="text" name="l_duty_paid" class="form-control" value="{{ round($pallet->meta->l_duty_paid / $cn , 2) }}" readonly>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Tax Paid</label>
                                                                    @if($pallet->hasMeta('l_tax_paid'))
                                                                        <input type="text" name="l_tax_paid" class="form-control" value="{{ round($pallet->meta->l_tax_paid / $cn, 2) }}" readonly>
                                                                    @endif                                                                
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Customs Broker</label>
                                                                    <input type="text" name="l_custom_broker" class="form-control" value="{{ $pallet->meta->l_custom_broker ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Currency</label>
                                                                    <input type="text" name="l_currency" class="form-control" value="{{ $pallet->meta->l_currency ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Channel Sold By</label>
                                                                    <input type="text" name="l_chanel" class="form-control" value="{{ $pallet->meta->l_chanel ?? '' }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">Sold Type</label>
                                                                    <input type="text" name="l_stype" class="form-control" value="{{ $pallet->meta->l_stype ?? '' }}" readonly>
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
                                                    <div class="col-md-9">
                                                        <p class="text text-danger">Please ensure the inspection form is completed before updating the inspection status. The item data will not be saved if the inspection form is not filled out.</p>
                                                    </div>
                                                    <div class="col-md-3">
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
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="shipcycle-modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Level 1</h4>
                        <form method="post" action="{{ route('admin.item.update') }}" enctype="multipart/form-data" class="level-form" autocomplete="false">
                            <div class="question-list">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $item->id }}">
                                <input type="hidden" name="ins_level" value="model">
                                <input type="hidden" name="local_time" value="" id="local_time">
                                @php 
                                    $i = 1; $j = 2;
                                @endphp
                                @foreach(newFirstLevel() as $k => $v)
                                    @php $uid = uniqid(); @endphp
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
                                                    <input type="radio" data-name="{{ $k }}" class="option-radio" id="answer-{{ $uid }}-yes" name="{{ $k }}" value="Yes" @if($item->hasMeta($k) && $item->getMeta($k) == 'Yes') checked @endif>
                                                    <label for="answer-{{ $uid }}-yes">
                                                        <span class="radiocheck-icon"></span>
                                                        <span class="radiocheck-text">Yes</span>
                                                    </label>
                                                </div>
                                                <div class="answer-radio">
                                                    <input type="radio" data-name="{{ $k }}" class="option-radio" id="answer-{{ $uid }}-no" name="{{ $k }}" value="No" @if($item->hasMeta($k) && $item->getMeta($k) == 'No') checked @endif>
                                                    <label for="answer-{{ $uid }}-no"> No </label>
                                                </div>
                                            </div>
                                            @if(!empty(firstLevelFieldType($k)))
                                                @php
                                                    $class = 'collapse';
                                                    if($item->hasMeta($k) && $item->getMeta($k) == 'No' && in_array($k, ['ebay_listing_item', 'match_quantity'])){
                                                        $class = ' ';
                                                    } elseif ($item->hasMeta($k) && $item->getMeta($k) == 'Yes' && in_array($k, ['visible_damage', 'inspection'])) {
                                                        $class = ' ';
                                                    }
                                                @endphp
                                                <div class="field-item-box {{ $class }} {{ $k }}">
                                                    @if(firstLevelFieldType($k) == 'text')
                                                        @forelse(firstLevelExtra($k) as $kk => $vv)
                                                            <div class="answer-radio mt-1">
                                                                <input type="text" style="{{ ($kk != 'pin_number') ? '' : 'text-security: disc;-webkit-text-security: disc;' }}" name="{{ $kk }}" value="@if($item->hasMeta($kk)) {{ $item->getMeta($kk) }} @endif" class="form-control" placeholder="{{ $vv }}" autocomplete="false">
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
            <div class="modal-dialog modal-sm">
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
@endsection

@push('js')
<script type="text/javascript">
    @forelse($posts->package as $item)
        // CKEDITOR.replace('editor-{{ $item->id }}', {
        //     toolbar: [
        //         { name: 'document', items: ['Source', '-', 'Save', 'Preview'] },
        //         { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
        //         { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
        //         { name: 'alignment', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        //         { name: 'styles', items: ['Format'] },
        //         { name: 'clipboard', items: ['Undo', 'Redo'] },
        //         { name: 'tools', items: ['Maximize'] }
        //     ]
        // });
    @empty
    @endforelse
</script>
@endpush