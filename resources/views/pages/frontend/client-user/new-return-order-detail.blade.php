@include('pages.frontend.client-user.breadcrumb', ['title' => 'New Return Order Detail'])

<style type="text/css">
    .card-body .bg-light {background: #fef0f2 !important; border-bottom: 1px solid #ffdee3; border-radius: 3px; }
    .Head-secondary {font-size: 13px; color: #b52039; font-weight: 500; }
    .booking-info-box .info-list-section {
        padding: 20px 30px 30px;
        /*border: 1px solid #fad7d7;*/
        margin-top: 15px;
        border-radius: 12px;
        background: #fff;
    }

    .info-list-box{padding: 5px 5px !important;}

    .info-list-section h2 {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 0;
        /*background: #fbf7f7;*/
        padding: 0px 0px 10px 0px;
        /*border-bottom: 1px solid #fad7d7;*/
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
    .address-type h4{font-size: 13px; font-weight: bold; margin: 0; padding: 0;} 

</style>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="booking-info-box">
            <div class="card-content">
                <div class="">
                    <h4 class="card-title">
                        <a href="{{ URL::previous() }}" class="btn btn-back"><i class="la la-arrow-left"></i> Back</a>
                    </h4>
                </div>
                <div class="">
                    @if($waybill)
                        <div class="info-list-section">
                            <nav>
                                <h2 class="navbar-text">Customer Details</h2>
                                <a class="Head-secondary pull-right" href="javascript:void(0);">
                                    Order Number / Date : {{ $waybill->way_bill_number }} / {{ date('d-m-Y', strtotime($waybill->created_at)) }}
                                </a>
                            </nav>
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
                        <div class="info-list-section">
                            <h2>Shipment Details</h2>
                            <div class="info-list-inner">                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">RG Order Number</div>
                                            <div class="booking-value-info">{{ $waybill->id??"N/A" }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Order Number</div>
                                            <div class="booking-value-info">{{ $waybill->way_bill_number??"N/A" }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Number Of Packages </div>
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
                                            <div class="booking-title-info">RMA Number</div>
                                            <div class="booking-value-info">{{ $waybill->meta->_rma_number??"N/A" }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Remarks</div>
                                            <div class="booking-value-info">{{ $waybill->meta->_remark??"N/A" }}</div>
                                        </div>
                                    </div>                                    
                                    <div class="col-md-6">
                                        <div class="info-list-box">
                                            <div class="booking-title-info">Return Option</div>
                                            <div class="booking-value-info">
                                                @if($waybill->hasMeta('_drop_off') && $waybill->meta->_drop_off == 'By_ReturnBar')
                                                    By Return Bar™
                                                @else
                                                    {{ str_replace('_', ' ', $waybill->meta->_drop_off) ?? "N/A" }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- ======= --}}
                        <div class="info-list-section">
                            <h2>Item Details</h2>
                            <div class="info-list-inner table-responsive">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="client_user_list" class="table table-striped table-bordered table-sm">
                                            <tbody>
                                                <tr>
                                                    <th>Item Bar Code</th>
                                                    <th>Title</th>
                                                    <th>Quantity</th>
                                                    <th>Color</th>
                                                    <th>Size</th>
                                                    <th>Dimension Unit</th>
                                                    <th>Pkg. Dimensions</th>
                                                    <th>Weight / Unit</th>                                                   
                                                    <th>Reason of Return</th>
                                                    <th>Images &nbsp; &nbsp; &nbsp; </th>
                                                </tr>
                                                @forelse($waybill->packages as $package)
                                                    <tr>
                                                        <td>{{ $package->bar_code??"N/A" }}</td>
                                                        <td>{{ $package->title??"N/A" }}</td>
                                                        <td>{{ $package->package_count }}</td>
                                                        <td>{{ $package->color }}</td>
                                                        <td>{{ $package->size }}</td>
                                                        <td>{{ $package->dimension }}</td>                      
                                                        <td>
                                                            {{ $package->length }} / {{ $package->width }} / {{ $package->height }}
                                                        </td>
                                                        <td>{{ $package->weight }} / {{ $package->weight_unit_type }}</td>
                                                        <td>
                                                            @if(Auth::user()->client_type == '2')
                                                                {{ displayMissguidedReason($package->return_reason) }}
                                                            @else
                                                                {{ displayOliveReason($package->return_reason) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                {{-- @php dd(json_decode($package->file_data)); @endphp --}}
                                                                @if(!empty($package->file_data))
                                                                    @forelse(json_decode($package->file_data) as $image)
                                                                        <div class="col-md-9">
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
                    @else
                        <h2 class="text-center mt-2">Record not found</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
