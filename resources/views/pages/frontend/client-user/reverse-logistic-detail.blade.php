@include('pages.frontend.client-user.breadcrumb', ['title' => 'Reverse Logistic Details'])

<style type="text/css">
    .card-body .bg-light {background: #fef0f2 !important; border-bottom: 1px solid #ffdee3; border-radius: 3px; }
    .Head-secondary {font-size: 13px; color: #b52039; font-weight: 500; }
    .booking-info-box .info-list-section {
        padding: 0;
        border: 1px solid #fad7d7;
        margin-top: 15px;
    }

    .info-list-section h2 {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 0;
        background: #fbf7f7;
        padding: 10px;
        border-bottom: 1px solid #fad7d7;
    }
    .info-list-inner {
        padding: 10px;
    }  
    .attachment-sec-1 {
        border: 1px solid #fad7d7;
        border-radius: 4px;
        width: 100%;
        margin: 0 0 20px 0;
        padding: 5px;
        background: #fbf7f7;
    }
    .Head-track {
        color: #000;
        font-size: 13px;
    }
    .info-list-box.Img-attachment ul {display: flex; }

    .info-list-box ul li {margin: 10px 40px 0 20px; list-style-type: none; }
    .btn-dlt {
        background: #b52039;
        color: #fff;
        border: none;
        border-radius: 3px;
        position: absolute;
        bottom: 0;
        padding: 2px 10px;
        margin: 0px 0px 7px 10px;
    }

    .card-body .navbar .navbar-brand {
        font-weight: 600;
        font-size: 15px;
        color: #000;
    }
    .info-list-section .row p {
        color: #b9b2b2;
        font-weight: 500;
        font-size: 1rem;
    }

    .info-list-section .row label {
        font-size: 1rem;
    }
    .attachment-sec {
        background: #f9f7f7;
        border: 1px solid #f1ecec;
        border-radius: 4px;
    }
    .info-list-box.info-mt {margin-top: 1rem;}
</style>

@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#track-detail').click(function(){
            var obj = $(this);
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url:  '{!! url('/') !!}/get-tracking/{!! $waybill->way_bill_number !!}',
                method: "get",
                contentType: false,
                cache: false,
                processData: false,
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
    });

    function updatePackage(package_id){
        let st_v = $('#estimated_value').val();
        let hs_code = $('#hs_code').val();
        let ac = $("#status option:selected").val();
        if (st_v == '' || hs_code == '' || ac == '') {
            alert('Please fill the value');
        } else{
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            if(st_v){
                formData.append('estimated_value', st_v);
            }
            if(hs_code){
                formData.append('hs_code', hs_code);
            }
            if(ac){
                formData.append('status', ac);
            }

            formData.append('package_id', package_id);
            $.ajax({
                url: "{{route('update-package')}}",
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.fail) {
                        alert(response.error);
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    $(document).ready(function() {
        $(".frm-submit").click(function() {
            let id = $(this).attr('data-id');
            // alert('frm-submit');
            $('form#frm-'+id).submit();
        });
    });
</script>
@endpush

<div class="row">
    <div class="col-xs-12 col-md-12 table-responsive">
        <div class="card booking-info-box">
            <div class="card-content">
                <div class="card-header">
                    <h4 class="card-title">
                        <a href="{{ URL::previous() }}" class="btn btn-back"><i class="la la-arrow-left"></i> Back</a>

                        @if(empty($waybill->cancel_return_status) && !$waybill->hasMeta('_order_waywill_status'))
                            <a class="btn btn-back" data-toggle="modal" data-target="#myModal-{{ $waybill->id }}">Cancel Return</a>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    @if($waybill)
                        <h5 class="card-title">Order Number:- <strong>{{ $waybill->way_bill_number }}</strong></h5>
                        <div class="info-list-section">
                            <h2>Customer Details</h2>
                            <div class="info-list-inner">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Name</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Email</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Address</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_address }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Country</div>
                                            <div class="booking-value-info">
                                                {{ get_country_name_by_id($waybill->meta->_customer_country) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">State</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_state }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Postal code</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_pincode }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Mobile</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_customer_phone }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======= --}}
                        @php
                            $tracking_detail = ($waybill->meta->_order_tracking_id)?? NULL; 
                            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                            $new_array = [];
                            $track_id = 'Not Generated Yet';
                            $label_url = 'NA';
                            if($tracking_data){
                                foreach($tracking_data as $t){
                                    $d = date('Y-m-d', strtotime($t->modifiedOn));
                                    $track_id = $t->carrierWaybillNumber;
                                    $label_url = (isset($t->carrierWaybillURL) && !empty($t->carrierWaybillURL)) ? $t->carrierWaybillURL : 'NA';
                                    if (!isset($new_array[$d])) {
                                        $new_array[$d] = [$t];
                                    } else{
                                        array_push($new_array[$d], $t);
                                    }
                                }
                            }
                        @endphp
                        <div class="info-list-section">
                            <h2>Shipment Details</h2>
                            <div class="info-list-inner">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row attachment-sec-1">
                                            <div class="col-md-4">                                                        
                                                <a class="Head-track" href="javascript:void(0)"> Get Tracking Details :
                                                    <button type="button" class="btn btn-sm btn-warning"  @if($waybill->meta->_order_current_status) id="track-detail" data-id="{{ $track_id }}" @else disabled="true"  @endif>
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </a>
                                            </div>
                                            <div class="col-md-4">
                                                <a class="Head-track" href="#"> Tracking ID : {{ $track_id }}</a>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div class="dt-buttons">
                                                    @if($label_url!='NA')
                                                        <a href="{{ $label_url }}" target="_blank" class="btn btn-secondary buttons-excel buttons-html5 btn-primary"><span><i class="la la-download"></i> Download Label</span></a>
                                                    @else
                                                        <div class="text-center">Label not created yet</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Client Name</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->client->name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Warehouse Name </div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_consignee_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Shipment Type</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_shipment_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Sell Rate</div>
                                            <div class="booking-value-info">{!! ($waybill->meta->_currency)?get_currency_symbol($waybill->meta->_currency):get_currency_symbol('USD') !!}{{ $waybill->meta->_rate ?? '0'}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Carrier</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_carrier_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Payment Mode</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->payment_mode }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Number Of Packages</div>
                                            <div class="booking-value-info">
                                                @php $cnt = 0; @endphp
                                                @forelse($waybill->packages as $package)
                                                    @php $cnt += $package->package_count; @endphp
                                                @empty
                                                @endforelse
                                                {{ $cnt }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Weight Unit Type</div>
                                            <div class="booking-value-info">
                                                {{ displayUnitType($waybill->meta->_unit_type) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Actual Weight</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_actual_weight }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Charged Weight</div>
                                            <div class="booking-value-info">
                                                {{ $waybill->meta->_charged_weight }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ==== --}}
                        @if($waybill->hasMeta('_bar_address'))
                            <div class="info-list-section">
                                <h2>Return Bar Address</h2>
                                @php
                                    $address = json_decode($waybill->meta->_bar_address);
                                    // dd($address);
                                @endphp
                                <div class="col-lg-12 col-md-12 col-xs-12">
                                    <div class="address-info-inner-card-1">
                                        <div class="address-info-card">
                                            <div class="address-info-inner">
                                                <h3>{{ $address->name ?? '' }}</h3>
                                                <p>{{ $address->distance ?? '' }} mi</p>
                                                <div class="address-content">
                                                    <h4>Address</h4>
                                                    <p>{{ $address->address->address }}, {{ $address->address->city }}, {{ $address->address->state }} {{ $address->address->zipcode }}, {{ $address->address->phoneNumber }}</p>
                                                </div>
                                                <p>{{ $address->directions }}</p>
                                                <div class="address-content">
                                                    <h4>Hours</h4>
                                                    <p>{{ str_replace(',', ' | ', $address->hours) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="address-info-text">
                                            @if(isset($address->display))
                                                @forelse($address->display as $dis)
                                                    @if($dis['label'] != 'Promotions')
                                                    <div class="type address-type" style="margin-top: 10px;">
                                                        <h4>{{ $dis->label }}</h4>
                                                        <p class="address">{{ $dis->value }}</p>  
                                                    </div>
                                                    @endif
                                                @empty
                                                @endforelse
                                            @endif
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ======= --}}
                        <div class="info-list-section">
                            <h2>Item Details</h2>
                            <div class="info-list-inner">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>Item Bar Code</th>
                                                    <th>Title</th>
                                                    <th>Quantity</th>
                                                    <th>Color</th>
                                                    <th>Size</th>
                                                    <th>Dimension Unit</th>
                                                    <th>Lenght </th>
                                                    <th>Width</th>
                                                    <th>Height</th>
                                                    <th>Weight Unit</th>
                                                    <th>Weight</th>
                                                    <th>Estimated Value</th>
                                                    <th>HS Code</th>
                                                    <th>Confirm Action</th>
                                                    <th>Action</th>
                                                    <th> Images &nbsp; &nbsp; &nbsp; </th>
                                                </tr>
                                                @forelse($waybill->packages as $package)
                                                    <tr>
                                                        <td>{{ $package->bar_code??"N/A" }}</td>
                                                        <td>{{ $package->title??"N/A" }}</td>
                                                        <td>{{ $package->package_count }}</td>
                                                        <td>{{ $package->color }}</td>
                                                        <td>{{ $package->size }}</td>
                                                        <td>{{ $package->dimension }}</td>                      
                                                        <td>{{ $package->length }}</td>                      
                                                        <td>{{ $package->width }}</td>                      
                                                        <td>{{ $package->height }}</td>                      
                                                        <td>{{ $package->weight_unit_type }}</td>                      
                                                        <td>{{ $package->weight }}</td>
                                                        <td>
                                                            @if($package->estimated_value)
                                                                {{ $package->estimated_value }}
                                                            @else
                                                                <input type="text" class="form-control" id="estimated_value" value="">
                                                            @endif                                                                        
                                                        </td>
                                                        <td>
                                                            @if($package->hs_code)
                                                                {{ $package->hs_code }}
                                                            @else
                                                                <input type="text" class="form-control" id="hs_code" style="width: 100px;" value="">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($package->status)
                                                                {{ $package->status }}
                                                            @else
                                                                <select id="status" class="form-control">
                                                                    <option value="">-- Select --</option>
                                                                    <option value="Restock">Restock</option>
                                                                    <option value="Resell">Resell</option>
                                                                    <option value="Return">Return</option>
                                                                    <option value="Redirect">Redirect</option>
                                                                    <option value="Recycle">Recycle</option>
                                                                    <option value="Other">Other</option>
                                                                </select>
                                                            @endif                                                                    
                                                        </td>                                                                
                                                        <td>
                                                            @if(empty($package->status) || empty($package->hs_code) || empty($package->estimated_value))
                                                                <button type="btn btn-success" onclick="updatePackage({{ $package->id }});">
                                                                    <i class="fa fa-arrow-up"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                {{-- @php dd(json_decode($package->file_data)); @endphp --}}
                                                                @if(!empty($package->file_data))
                                                                    @forelse(json_decode($package->file_data) as $image)
                                                                        <div class="col-md-12">
                                                                            <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="100">
                                                                            </a>
                                                                        </div>
                                                                    @empty
                                                                        <p class="col-md-6">N/A</p>
                                                                    @endforelse
                                                                @else
                                                                    <p class="col-md-6">N/A</p>
                                                                @endif
                                                            </div> 
                                                        </td>                      
                                                    </tr>
                                                @empty

                                                <tr>
                                                    <td colspan="8">Package not added</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>                                   
                                </div>
                            </div>
                        </div>

                        {{-- model up --}}
                        <div class="modal fade" id="myModal-{{ $waybill->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content track-content">      
                                    <!-- Modal Header -->
                                    <div class="modal-header h-bg">
                                      <h5 class="modal-title p-0 text-white">Cancel Return</h5>
                                      <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>        
                                    <!-- Modal body -->
                                    <div class="modal-body body-bg">
                                        <h5> Are you sure you want to cancel your return?</h5>
                                        <form method="post" action="{{ route('missguided.cancel.return') }}" id="frm-{{ $waybill->id }}">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $waybill->id }}">
                                        </form>
                                    </div>        
                                    <!-- Modal footer -->
                                    <div class="modal-footer step-btn-content">
                                        <a type="button" class="btn popup-btns" data-dismiss="modal">Go Back</a>
                                        <a type="button" class="btn popup-btns frm-submit" data-id="{{ $waybill->id }}">Yes</a>
                                    </div>        
                                </div>
                            </div>
                        </div>
                    @else
                        <h2 class="text-center mt-2">Record not found</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>