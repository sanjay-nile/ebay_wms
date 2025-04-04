@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#track-detail').click(function(){

            var obj = $(this);
            let carrierWaybill = obj.data('id');
            if(carrierWaybill=='undefined') {alert('Tracking Id Not Generated'); return false;}
            
            $(obj).prop('disabled', true);
            $(obj).html('<i class="fa fa-spinner" aria-hidden="true"></i> Loading...');
            $.ajax({
                url:  '{!! url('/') !!}/get-tracking/'+carrierWaybill,
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
                        <li class="breadcrumb-item active">Return Order Details</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="row">
            <div class="col-md-12">
                @include('includes/admin/notify')
            </div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ route('reverse-logistic') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
                        </h4>
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
                    <div class="card-content">
                        <div class="card-body">
                            @if($waybill)
                                <h5 class="card-title">WayBill Number:- <strong>{{ $waybill->way_bill_number }}</strong></h5>
                            @endif
                            <ul class="nav nav-tabs nav-tabs-list nav-underline">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#about-customer" aria-controls="homeIcon11" aria-expanded="true">Customer Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Packages" aria-controls="aboutIcon11" aria-expanded="false">Packages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Labels" aria-controls="aboutIcon11" aria-expanded="false">Labels</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Traking" aria-controls="aboutIcon11" aria-expanded="false">Tracking Detail</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Duties" aria-controls="aboutIcon11" aria-expanded="false">Custom Duties</a>
                                </li>
                            </ul>

                            @if($waybill)
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="about-customer" aria-labelledby="about-customer" aria-expanded="true">
                                    <div class="info-list-section">
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
                                                    <div class="booking-title-info">Pincode</div>
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

                                <div class="tab-pane" id="Packages" role="tabpanel" aria-labelledby="profileIcon1-tab1" aria-expanded="false">
                                    <div class="info-list-section">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered">
                                                    <tr>
                                                        <th>Bar Code</th>
                                                        <th>Title</th>
                                                        <th>Package Count</th>
                                                        <th>Length(In)</th>
                                                        <th>Width(In)</th>
                                                        <th>Height(In)</th>
                                                        <th>Weight(Kg)</th>
                                                        <th>Charged Weight(Kg)</th>
                                                        <th>Selected Package</th>
                                                    </tr>

                                                    @forelse($waybill->packages as $package)
                                                        <tr>
                                                            <td>{{ $package->bar_code??"N/A" }}</td>
                                                            <td>{{ $package->title??"N/A" }}</td>
                                                            <td>{{ $package->package_count }}</td>
                                                            <td>{{ $package->length }}</td>
                                                            <td>{{ $package->width }}</td>
                                                            <td>{{ $package->height }}</td>
                                                            <td>{{ $package->weight }}</td>
                                                            <td>{{ $package->charged_weight }}</td>
                                                            <td>{{ $package->selected_package_type_code }}</td>
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
                                <div class="tab-pane" id="Labels" role="tabpanel" aria-labelledby="profileIcon1-tab1" aria-expanded="false">
                                    <div class="info-list-section">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered">
                                                    <tr>
                                                        <th>Label</th>
                                                        {{-- <th>Package Sticker Url</th> --}}
                                                        <th>Message</th>
                                                    </tr>
                                                    @if($label_url!='NA')
                                                        <tr>
                                                            <td><a href="{{ $label_url }}" target="_blank">Download Label</a></td>
                                                            
                                                            <td>{{ $waybill->meta->_label_message }}</td>
                                                        </tr>
                                                    @else
                                                    <tr>
                                                        <td colspan="3"><p class="text-center">Label not created yet</p></td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="Traking" role="tabpanel" aria-labelledby="profileIcon1-tab1" aria-expanded="false">
                                    <div class="info-list-section">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered">
                                                    <tr>
                                                        <th>Tracking ID</th>
                                                        {{-- <th>Current Status</th> --}}
                                                        <th>Get Tracking Details</th>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>{{ $track_id }}</td>
                                                        {{-- <td>{{ $waybill->meta->_order_current_status ?? 'None' }}</td> --}}
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-warning"  @if($waybill->meta->_order_current_status) id="track-detail" data-id="{{ $track_id }}" @else disabled="true"  @endif>
                                                                <i class="fa fa-eye"></i>
                                                            </button>

                                                            <!-- The Modal -->
                                                            <div aria-hidden="true" aria-labelledby="updater" class="modal fade" id="myModal" role="dialog" tabindex="-1">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content track-content">
                                                                        <!-- Modal Header -->
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Tracking Details</h4>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <!-- Modal body -->
                                                                        <div class="modal-body">
                                                                            <h5>Tracking ID: <b>{{ $track_id }}</b></h5>
                                                                            <div id="track-data"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="Duties" role="tabpanel" aria-labelledby="profileIcon1-tab1" aria-expanded="false">
                                    @if($waybill->hasMeta('_custom_duties'))
                                        @php
                                            $duties = json_decode($waybill->meta->_custom_duties);
                                            //echo '<pre>';
                                            //print_r($duties);
                                            //echo '</pre>';
                                        @endphp
                                        @if(isset($duties->customerInvoiceAmount))
                                            <p>Custom Duty Amount:- {!! $duties->customerInvoiceAmount !!}</p>
                                        @endif

                                        @if(isset($duties->printUrl))
                                            <p>
                                                <a href="{!! $duties->printUrl !!}" target="_blank" class="btn btn-red">Print</a>
                                            </p>
                                            <p>
                                                <a href="#" target="_blank" class="btn btn-blue">Pay Now</a>
                                            </p>
                                            <p>
                                                <a href="{{ route('custom.duty.mail', $waybill->way_bill_number) }}" class="btn btn-red">Mail To Customer</a>
                                            </p>
                                        @endif
                                    @else
                                        <p>Duties and taxes not found</p>
                                    @endif
                                </div>
                            </div>
                            @else
                                <h2 class="text-center mt-2">Record not found</h2>
                            @endif
                        </div>
                    </div>
                    <!--  <div class="card-content collapse"></div> -->
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>

@endsection
