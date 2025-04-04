<div class="page-wrapper return-request-detail">
    <div class="container">
        <section class="detail-section">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="">
                        <div class="card-content">
                            <div class="info-Detail-section">
                                <h4>Return Request Detail</h4>
                            </div>
                            @if($waybill)
                                @if($waybill->meta->_drop_off != 'By_ReturnBar')
                                    @php
                                        $tracking_detail = ($waybill->meta->_generate_waywill_status)?? NULL; 
                                        $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                        $label_url = '';
                                        if($tracking_data){
                                            foreach($tracking_data->labelDetailList as $t){
                                                if (isset($t->artifactUrl) && !empty($t->artifactUrl)) {
                                                    # code...
                                                    $label_url = $t->artifactUrl;
                                                }
                                            }
                                        }
                                    @endphp
                                @endif
                                <div class="col-md-12 mb-3">
                                    <div class="download-back-btn float-left">
                                        <a class="btn btn-secondry download-label" href="{{ url()->previous() }}">Back</a>
                                    </div>
                                    @if(!empty($label_url))
                                        <div class="download-back-btn float-right">
                                            <a target="_blank" href="{{ $label_url }}" class="btn btn-secondry download-label">Download label</a>
                                        </div>
                                    @endif
                                </div>

                                <div id="myModal-{{$waybill->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Detail</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                @if($waybill->hasMeta('_custom_duties'))
                                                    <?php $duties = json_decode($waybill->meta->_custom_duties); ?>
                                                    <div class="row mb-1">
                                                        <div class="col-md-6">
                                                            <label><strong>Amount:-</strong> {!! $duties->customerInvoiceAmount !!}</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label><strong>Status:-</strong> 
                                                                @if($waybill->hasMeta('_custom_duties_status'))
                                                                    PAID
                                                                @else
                                                                    UNPAID
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="btn-group row">
                                                        @if(isset($duties->printUrl))
                                                            <a href="{!! $duties->printUrl !!}" target="_blank" class="btn btn-outline-info ml-2">Print</a>
                                                            <a href="#" target="_blank" class="btn btn-outline-warning ml-1">Pay Now</a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <p>Duties and taxes not found</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="return-form-item">
                                        <h2>Pickup Address</h2>
                                        <div class="info-list-inner">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Name</div>
                                                        <div class="booking-value-info">
                                                          {{ $waybill->meta->_customer_name??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Address</div>
                                                        <div class="booking-value-info">
                                                          {{ $waybill->meta->_customer_address??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Country</div>
                                                        <div class="booking-value-info">
                                                          {{ $waybill->meta->_customer_country??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">State</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_customer_state??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Postal code</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_customer_pincode??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Mobile</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_customer_phone??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                
                                        </div>
                                    </div>
                                    <div class="return-form-item">
                                        <h2>Shipment Details</h2>
                                        <div class="info-list-inner">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Order Number</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->way_bill_number??"N/A" }}
                                                        </div>
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
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_rma_number??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Remarks</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_remark??"N/A" }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">RG Reference Number</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->id }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-list-box">
                                                        <div class="booking-title-info">Return Option</div>
                                                        <div class="booking-value-info">
                                                            {{ str_replace('_', ' ', $waybill->meta->_drop_off) ?? "N/A" }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="return-form-item">
                                        <h2>Item Details</h2>
                                        <div class="row">
                                            <div class="col-md-12 table-responsive">
                                                <table id="client_user_list" class="table table-striped table-bordered table-sm">
                                                    <tr style="background-image: linear-gradient(#bbbdbf, #e7e9e9);">
                                                        <th>Item Bar Code</th>
                                                        <th>Title</th>
                                                        <th>Qty</th>
                                                        <th>Color</th>
                                                        <th>Size</th>                                                        
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
                                                            <td>{{ $package->return_reason }}</td>                      
                                                            <td>
                                                                <div class="row">
                                                                    {{-- @php dd(json_decode($package->file_data)); @endphp --}}
                                                                    @if(!empty($package->file_data))
                                                                        @forelse(json_decode($package->file_data) as $image)
                                                                            <div class="col-md-4">
                                                                                <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                    <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="100">
                                                                                </a>
                                                                            </div>
                                                                        @empty
                                                                            <p>N/A</p>
                                                                        @endforelse
                                                                    @else
                                                                        <p>N/A</p>
                                                                    @endif
                                                                </div> 
                                                            </td>                      
                                                        </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8">Package not added</td>
                                                    </tr>
                                                    @endforelse
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>