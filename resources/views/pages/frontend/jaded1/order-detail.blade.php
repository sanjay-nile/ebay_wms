@if($waybill)
    @if($waybill->meta->_drop_off != 'By_ReturnBar')
        @php
            $tracking_detail = ($waybill->meta->_generate_waywill_status)?? NULL; 
            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
            $label_url = $tracking_id = '';
            if($tracking_data && isset($tracking_data->carrierWaybill)){
                $tracking_id = $tracking_data->carrierWaybill;
                foreach($tracking_data->labelDetailList as $t){
                    if (isset($t->artifactUrl) && !empty($t->artifactUrl)) {
                        # code...
                        $label_url = $t->artifactUrl;
                    }
                }
            }
            // $tracking_id = '1Z7F27A20319855509';
        @endphp
    @endif
@endif

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
                url:  '{!! url('/') !!}/get-tracking/{!! $tracking_id !!}',
                method: "get",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $(obj).prop('disabled', false);
                    $(obj).html('Tracking Detail <i class="fa fa-eye"></i>');
                    $('#track-data').html(response);
                    $('#myModal').modal({show:true});
                },
                error : function(jqXHR, textStatus, errorThrown){
                    $(obj).prop('disabled', false);
                    $(obj).html('Tracking Detail <i class="fa fa-eye"></i>');
                }
            });
        });        
    });
</script>
@endpush

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
                                <div class="card-btn-section">
                                    <div class="row">
                                        <div class="col-6 text-left">
                                            <div class="download-back-btn">
                                                <a class="btn-black download-label" href="{{ url()->previous() }}">Back</a>
                                            </div>
                                        </div>
                                        <div class="col-6 text-right">
                                        	<div class="row pull-right">
                                        @if(empty($waybill->cancel_return_status))
                                            @if(!empty(showPdf($waybill->id)))
                                                <div class="col-md-6 col-sm-12 text-right">
                                                    <div class="download-back-btn">
                                                        <a style="width: 158px; text-align: center;" href="{{ url(showPdf($waybill->id)) }}" class="btn-black download-label" target="_blank" download type="application/octet-stream">Download Label</a>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(!empty($tracking_id))                                            
                                                <div class="col-md-6 text-right ">
                                                    <div class="download-back-btn mob-mt-1">
                                                        <button type="button" class="btn btn-black download-label" id="track-detail" data-id="{{ $tracking_id }}" style="height: 46px;">
                                                            Tracking Detail <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            @endif
                                        @endif

                                        <!-- The Modal -->
                                        <div aria-hidden="true" aria-labelledby="updater" class="modal fade" id="myModal" role="dialog" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content track-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header bg-black">
                                                        <h4 class="modal-title p-0">Tracking Details</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <div class="modal-body">
                                                        <h4>Tracking ID: <b>{{ $tracking_id }}</b></h4>
                                                        <div id="track-data"></div>
                                                    </div>
                                                </div>
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
                                                        <div class="booking-title-info">Email Id</div>
                                                        <div class="booking-value-info">
                                                            {{ $waybill->meta->_customer_email??"N/A" }}
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
                                                <table id="client_user_list" class="table table-striped table-bordered table-sm mb-0">
                                                    <tr>
                                                        <th>Item Bar Code</th>
                                                        <th>Title</th>
                                                        <th>Qty</th>
                                                        {{-- <th>Color</th> --}}
                                                        <th>Size</th>
                                                        <th>Reason of Return</th>
                                                        {{-- <th>Images &nbsp; &nbsp; &nbsp; </th> --}}
                                                    </tr>
                                                    @forelse($waybill->packages as $package)
                                                        <tr>
                                                            <td>{{ $package->bar_code??"N/A" }}</td>
                                                            <td>{{ $package->title??"N/A" }}</td>
                                                            <td>{{ $package->package_count }}</td>
                                                            {{-- <td>{{ $package->color }}</td> --}}
                                                            <td>{{ $package->size }}</td>
                                                            <td>{{ displayMissguidedReason($package->return_reason) }}</td>
                                                            {{-- <td>
                                                                @if(!empty($package->file_data))
                                                                    @forelse(json_decode($package->file_data) as $image)
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <a href="{{ asset('public/'.$image) }}" target="_blank">
                                                                                <img src="{{ asset('public/'.$image) }}" class="img-thumbnail" width="168" height="100">
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    @empty
                                                                        <p>N/A</p>
                                                                    @endforelse
                                                                @else
                                                                    <p>N/A</p>
                                                                @endif
                                                            </td> --}}
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