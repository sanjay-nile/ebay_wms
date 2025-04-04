@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')

@include('pages-message.notify-msg-error')
@include('pages-message.notify-msg-success')
@include('pages-message.form-submit')

<?php
    $sales_id = 'None';
    $track_id = $order_data_by_id['tracking_id'] ?? '';
    $label_url = $order_data_by_id['label_url'] ?? '';    
?>

@push('js')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#refund_mod').on('change', function() {
        if(this.value == 'Paypal'){
            $('#bank-div').hide();
        }else{
            $('#bank-div').show();
        }
    });

    $(document).on('click', '#show-frm', function(){
        $('#status-frm').toggle();
    });

    $('#track-detail').click(function(){
        var obj = $(this);
        $(obj).prop('disabled', true);
        $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
        $.ajax({
            url: $('#hf_base_url').val() + '/admin/get-tracking/{!! $track_id !!}',
            method: "get",
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // console.log(response);
                $(obj).prop('disabled', false);
                $(obj).html('<i class="fa fa-eye"> Get Details</i>');
                $('#track-data').html(response);
                $('#myModal').modal({show:true});
            },
            error : function(jqXHR, textStatus, errorThrown){
                $(obj).prop('disabled', false);
                $(obj).html('<i class="fa fa-eye"> Get Details</i>');
            }
        });
    });

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
});
</script>
@endpush

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">eBay Order Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-1 mb-2">
                        <a class="btn btn-blue" href="{{url('admin/order/list')}}">
                            <i class="fa fa-hand-o-left" aria-hidden="true"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-section">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card eBay-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">EVTN Number</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['evtn_number'] ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Label No./ Tracking No.</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['tracking_number'] ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <!-- customer detail -->
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Customer Name</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['customer_name'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">City</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['customer_city'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">ZipCode</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['customer_pincode'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">State</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['customer_state'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Country</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['customer_country'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <!-- seller detail -->
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Seller Name</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['seller_name'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Seller City</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['seller_city'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Seller ZipCode</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['seller_pincode'] ?? '' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Seller State</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['seller_state'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Seller Country</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['seller_country'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Order No.</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['order_number'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>                                    

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">SKU #</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['sku'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Package Weight</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['weight'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Purchased Qty</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['itemQuantity'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Original Sales Incoterm</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['serviceName'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">HS Code</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['hs_code'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">COO</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['coo'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Value of the Product</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['value'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>                                    

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">VAT Charged</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['vat_charged'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Duty Paid</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['duty_paid'] ?? 'N/A' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">SC Master Category</div>
                                            <div class="edit-form-value">@if(isset($order_data_by_id['category_name'])) {!! getCategoryName($order_data_by_id['category_name'], 'main') !!} @endif</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Category Tier 1</div>
                                            <div class="edit-form-value">@if(isset($order_data_by_id['sub_category_name'])) {!! getCategoryName($order_data_by_id['sub_category_name']) !!} @endif</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Category Tier 2</div>
                                            <div class="edit-form-value">@if(isset($order_data_by_id['sub_category_name_2'])) {!! getCategoryName($order_data_by_id['sub_category_name_2']) !!} @endif</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Category Tier 3</div>
                                            <div class="edit-form-value">@if(isset($order_data_by_id['sub_category_name_3'])) {!! getCategoryName($order_data_by_id['sub_category_name_3']) !!} @endif</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="row g-1">
                                            <div class="col-md-10">
                                                <div class="edit-form-group">
                                                    <div class="edit-form-text">Inspection Level Required</div>
                                                    <div class="edit-form-value">Level {!! $order_data_by_id['in_level'] ?? 'N/A' !!}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 mt-1">
                                                @if(isset($order_data_by_id['in_level']) && $order_data_by_id['in_level'] == 'L1')
                                                    <button type="button" class="btn-View-level" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn-View-level" data-toggle="modal" data-target="#myModal_2">
                                                       <i class="fa fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Inspection Status</div>
                                            <div class="edit-form-value">{!! inception_status($order_data_by_id['order_status']) ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Condition</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['condition_code'] ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">Pallet ID</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['pallet_id'] ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">User Comment</div>
                                            <div class="edit-form-value">{!! $order_data_by_id['comment'] ?? '' !!}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="edit-form-group">
                                            <div class="edit-form-text">eBay Listing link</div>
                                            <div class="edit-form-value"><a href="{!! $order_data_by_id['itemUrl'] ?? '#' !!}" class="btn btn-View-level" target="_blank"> View</a></div>
                                        </div>
                                    </div>

                                    @if(isset($order_data_by_id['image']))
                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Photo</div>
                                                <div class="edit-form-value">
                                                    <div class="edit-form-value-img">
                                                        <img src="{{ asset('public/uploads/'.$order_data_by_id['image'])}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($order_data_by_id['image_url']) && !empty($order_data_by_id['image_url']))
                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">SCX photo</div>
                                                <div class="edit-form-value">
                                                    {{-- <div class="edit-form-value-img">
                                                        <a href="{{ $order_data_by_id['image_url'] }}" target="_blank"><img src="{{ $order_data_by_id['image_url'] }}"></a>
                                                    </div> --}}
                                                    @if(isset($order_data_by_id['media_urls']) && !empty($order_data_by_id['media_urls']))
                                                        @forelse(json_decode($order_data_by_id['media_urls']) as $k)
                                                            <div class="edit-form-value-img ml-1">
                                                                <a href="{{ $k }}" target="_blank"><img src="{{ $k }}"></a>
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($order_data_by_id['imageUrls_data']) && !empty($order_data_by_id['imageUrls_data']))
                                        <div class="col-md-6">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">eBay photo</div>
                                                <div class="edit-form-value">
                                                    <div class="row">
                                                        @forelse(json_decode($order_data_by_id['imageUrls_data']) as $k)
                                                            <div class="edit-form-value-img ml-1">
                                                                <a href="{{ $k }}" target="_blank"><img src="{{ $k }}"></a>
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <h5><b><u> Liquidation Information :- </u></b></h5>
                                    </div>
                                    @php
                                        $pallet = getPalletDetails($order_data_by_id['pallet_id']);
                                        $cn = (!empty($pallet)) ? $pallet->posts()->count() : 1;                                        
                                    @endphp
                                    @if(!empty($pallet))
                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Customer Name</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_cname ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Customer Address</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_address ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Price</div>
                                                <div class="edit-form-value">
                                                    @if($pallet->hasMeta('l_price'))
                                                        {{ round($pallet->meta->l_price / $cn, 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">IncoTerm</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_incoterm ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Duty Paid</div>
                                                <div class="edit-form-value">
                                                    @if($pallet->hasMeta('l_duty_paid'))
                                                        {{ round($pallet->meta->l_duty_paid / $cn, 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Tax Paid</div>
                                                <div class="edit-form-value">
                                                    @if($pallet->hasMeta('l_tax_paid'))
                                                        {{ round($pallet->meta->l_tax_paid / $cn , 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Customs Broker</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_custom_broker ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Currency</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_currency ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Channel Sold By</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_chanel ?? '' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="edit-form-group">
                                                <div class="edit-form-text">Sold Type</div>
                                                <div class="edit-form-value">{{ $pallet->meta->l_stype ?? '' }}</div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div aria-hidden="true" class="modal fade" id="statusModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content track-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Status History
                </h4>
                <button class="close" data-dismiss="modal" type="button">×</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">                
                <div id="history-data"></div>
            </div>
        </div>
    </div>
</div>

{{-- level 1 --}}
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="shipcycle-modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Level 1</h4>
                <form method="post" action="{{ route('admin.order.update') }}" enctype="multipart/form-data" id="level-form">
                    <div class="question-list">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $order_data_by_id['_order_id'] }}">
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
                                            <input type="radio" data-name="{{ $k }}" id="answer-{{ $i }}-yes" class="option-radio" name="{{ $k }}" value="Yes" @if(isset($order_data_by_id[$k]) && $order_data_by_id[$k] == 'Yes') checked @endif>
                                            <label for="answer-{{ $i }}-yes">
                                                <span class="radiocheck-icon"></span>
                                                <span class="radiocheck-text">Yes</span>
                                            </label>
                                        </div>
                                        <div class="answer-radio">
                                            <input type="radio" data-name="{{ $k }}" id="answer-{{ $j }}-no" class="option-radio" name="{{ $k }}" value="No" @if(isset($order_data_by_id[$k]) && $order_data_by_id[$k] == 'No') checked @endif>
                                            <label for="answer-{{ $j }}-no"> No </label>
                                        </div>
                                    </div>
                                    @if(!empty(firstLevelFieldType($k)))
                                        <div class="field-item-box @if(!isset($order_data_by_id[$k])) collapse @endif {{ $k }}">
                                            @if(firstLevelFieldType($k) == 'text')
                                                @forelse(firstLevelExtra($k) as $kk => $vv)
                                                    <div class="answer-radio mt-1">
                                                        <input type="text" name="{{ $kk }}" value="@if(isset($order_data_by_id[$kk])) {{ $order_data_by_id[$kk] }} @endif" class="form-control" placeholder="{{ $vv }}">
                                                    </div>
                                                @empty
                                                @endforelse
                                            @endif
                                            @if(firstLevelFieldType($k) == 'select')
                                                <div class="answer-radio">
                                                    <select name="{{ $k }}_option" class="form-control">
                                                        <option value="">-- Select --</option>
                                                        @forelse(firstLevelExtra($k) as $kv => $vk)
                                                            <option value="{{ $kv }}" @if(isset($order_data_by_id[$k.'_option']) && $kv == $order_data_by_id[$k.'_option']) selected @endif>{{ $vk }}</option>
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
<div class="modal fade" id="myModal_2" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="shipcycle-modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Level 2</h4>
                <form method="post" action="{{ route('admin.order.update') }}" enctype="multipart/form-data" id="level-form-2">
                    <div class="question-list">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $order_data_by_id['_order_id'] }}">
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
                                            <input type="radio" id="second-{{ $m }}-yes" name="{{ $kk }}" value="Yes" @if(isset($order_data_by_id[$kk]) && $order_data_by_id[$kk] == 'Yes') checked @endif>
                                            <label for="second-{{ $m }}-yes">
                                                <span class="radiocheck-icon"></span>
                                                <span class="radiocheck-text">Yes</span>
                                            </label>
                                        </div>
                                        <div class="answer-radio">
                                            <input type="radio" id="second-{{ $l }}-no" name="{{ $kk }}" value="No" @if(isset($order_data_by_id[$kk]) && $order_data_by_id[$kk] == 'No') checked @endif>
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

@endsection
