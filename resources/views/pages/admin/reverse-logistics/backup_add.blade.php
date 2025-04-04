@extends('layouts.admin.layout')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
		<div class="we-page-title">
			<div class="row">
				<div class="col-md-8 align-self-left">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="javascript:void(0)"><i class="la la-dashboard"></i> Home</a></li>
						<li class="breadcrumb-item active">Create Reverse Logistic</li>
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
							<section class="list-your-service-section">
								<div class="list-your-service-content">
									<div class="container">
										<div class="list-your-service-form-box">
                                            <div class="row">
                                                <div class="col-md-12"><h2 class="card-title">Create Waybill</h2></div>
                                            </div>
											<form method="post" id="create-waybill">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5>Shipment</h5>
                                                    </div>
                                                </div>
                                                <div class="row create-waybill-bg">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Way Bill Number</label>
                                                            <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Way Bill Number" value="{{ mt_rand(100000,999999) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">From OU</label>
                                                            <input type="text" class="form-control" name="from_ou" placeholder="Enter From OU" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Delivery Date</label>
                                                            <input type="text" class="form-control" name="delivery_date" placeholder="Enter Delivery Date" value="" id="delivery_date">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reference Number</label>
                                                            <input type="text" class="form-control" name="reference_number" placeholder="Enter Reference Number" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Invoice Number</label>
                                                            <input type="text" class="form-control" name="invoice_number" placeholder="Enter Invoice Number" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Payment Mode</label>
                                                            <select class="form-control" name="payment_mode">
                                                                <option value="">Select</option>
                                                                <option value="FOD">FOD</option>
                                                                <option value="PAID">PAID</option>
                                                                <option value="TBB">TBB</option>
                                                                <option value="FOC">FOC</option>  
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Service Code</label>
                                                            <input type="text" class="form-control" name="service_code" placeholder="Enter Service Code" value="PARTLOAD">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="">Description</label>
                                                            <textarea name="description" placeholder="Enter Description" class="form-control" rows="5"></textarea>
                                                           
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 mt-1">
                                                        <h5>Customer</h5>
                                                    </div>
                                                </div>
                                                <div class="row create-waybill-bg">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Name</label>
                                                            <input type="text" class="form-control" name="consignee_name" placeholder="Enter Consignee Name" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Code</label>
                                                            <input type="text" class="form-control" name="consignee_code" placeholder="Enter Consignee Code" value="00000">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Address</label>
                                                            <input type="text" class="form-control" name="consignee_address" placeholder="Enter Consignee Address" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Country</label>
                                                            <input type="text" class="form-control" name="consignee_country" placeholder="Enter Consignee Country" value="IN">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee State</label>
                                                            <input type="text" class="form-control" name="consignee_state" placeholder="Enter Consignee State" value="DL">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee City</label>
                                                            <input type="text" class="form-control" name="consignee_city" placeholder="Enter Consignee City" value="DELHI">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Pincode</label>
                                                            <input type="text" class="form-control" name="consignee_pincode" placeholder="Enter Consignee Pincode" value="">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Phone</label>
                                                            <input type="text" class="form-control" name="consignee_phone" placeholder="Enter Consignee Phone" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Customer Code</label>
                                                            <input type="text" class="form-control" name="customer_code" placeholder="Enter Customer Code" value="HERO">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Client Code</label>
                                                            <input type="text" class="form-control" name="client_code" placeholder="Enter Client Code" value="HONDA">
                                                        </div>
                                                    </div>                                                        
                                                </div>
                                                 <div class="row">
                                                    <div class="col-md-12 mt-1">
                                                        <h5>Payment</h5>
                                                    </div>
                                                </div>
                                                <div class="row create-waybill-bg">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">COD</label>
                                                            <input type="text" class="form-control" name="cod" placeholder="Enter COD" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">COD Payment Mode</label>
                                                            <select class="form-control" name="cod_payment_mode">
                                                                <option value="">Select</option>
                                                                <option value="Cash">Cash</option>
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="PayMob">PayMob</option>
                                                                <option value="MPesa">MPesa</option>
                                                            </select>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Duty Paid By</label>
                                                            <select class="form-control" name="duty_paid_by">
                                                                <option value="">Select</option>
                                                                <option value="Sender">Sender</option>
                                                                <option value="Receiver">Receiver</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reverse Logistic Activity</label>
                                                            <select class="form-control" name="reverse_logistic_activity">
                                                                <option value="">Select</option>
                                                                <option value="PACKAGEPICKUP">PACKAGEPICKUP</option>
                                                                <option value="BOTH">BOTH</option>
                                                                <option value="CASHREFUND">CASHREFUND</option>
                                                            </select>
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reverse Logistic Refund Amount</label>
                                                            <input type="text" class="form-control" name="reverse_logistic_refund_amount" placeholder="Enter Reverse Logistic Refund Amount" value="">
                                                           
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">From OU</label>
                                                            <input type="text" class="form-control" name="from_ou" placeholder="Enter From OU" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Way Bill Number</label>
                                                            <input type="text" class="form-control" name="way_bill_number" placeholder="Enter Way Bill Number" value="{{ mt_rand(100000,999999) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Delivery Date</label>
                                                            <input type="text" class="form-control" name="delivery_date" placeholder="Enter Delivery Date" value="" id="delivery_date">
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Customer Code</label>
                                                            <input type="text" class="form-control" name="customer_code" placeholder="Enter Customer Code" value="HERO">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Code</label>
                                                            <input type="text" class="form-control" name="consignee_code" placeholder="Enter Consignee Code" value="00000">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Address</label>
                                                            <input type="text" class="form-control" name="consignee_address" placeholder="Enter Consignee Address" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Country</label>
                                                            <input type="text" class="form-control" name="consignee_country" placeholder="Enter Consignee Country" value="IN">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee State</label>
                                                            <input type="text" class="form-control" name="consignee_state" placeholder="Enter Consignee State" value="DL">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee City</label>
                                                            <input type="text" class="form-control" name="consignee_city" placeholder="Enter Consignee City" value="DELHI">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Pincode</label>
                                                            <input type="text" class="form-control" name="consignee_pincode" placeholder="Enter Consignee Pincode" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Name</label>
                                                            <input type="text" class="form-control" name="consignee_name" placeholder="Enter Consignee Name" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Consignee Phone</label>
                                                            <input type="text" class="form-control" name="consignee_phone" placeholder="Enter Consignee Phone" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Client Code</label>
                                                            <input type="text" class="form-control" name="client_code" placeholder="Enter Client Code" value="HONDA">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Number Of Packages</label>
                                                            <input type="text" class="form-control" name="number_of_packages" placeholder="Enter Number Of Packages" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Actual Weight</label>
                                                            <input type="text" class="form-control" name="actual_weight" placeholder="Enter Actual Weight" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Charged Weight</label>
                                                            <input type="text" class="form-control" name="charged_weight" placeholder="Enter Charged Weight" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Cargo Value</label>
                                                            <input type="text" class="form-control" name="cargo_value" placeholder="Enter Cargo Value" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reference Number</label>
                                                            <input type="text" class="form-control" name="reference_number" placeholder="Enter Reference Number" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Invoice Number</label>
                                                            <input type="text" class="form-control" name="invoice_number" placeholder="Enter Invoice Number" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Payment Mode</label>
                                                            <select class="form-control" name="payment_mode">
                                                                <option value="">Select</option>
                                                                <option value="FOD">FOD</option>
                                                                <option value="PAID">PAID</option>
                                                                <option value="TBB">TBB</option>
                                                                <option value="FOC">FOC</option>  
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Service Code</label>
                                                            <input type="text" class="form-control" name="service_code" placeholder="Enter Service Code" value="PARTLOAD">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reverse Logistic Activity</label>
                                                            <select class="form-control" name="reverse_logistic_activity">
                                                                <option value="">Select</option>
                                                                <option value="PACKAGEPICKUP">PACKAGEPICKUP</option>
                                                                <option value="BOTH">BOTH</option>
                                                                <option value="CASHREFUND">CASHREFUND</option>
                                                            </select>
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Reverse Logistic Refund Amount</label>
                                                            <input type="text" class="form-control" name="reverse_logistic_refund_amount" placeholder="Enter Reverse Logistic Refund Amount" value="">
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Weight Unit Type</label>
                                                            <select class="form-control" name="weight_unit_type">
                                                                <option value="GRAM">GRAM</option>
                                                                <option value="KILOGRAM" selected>KILOGRAM</option>
                                                                <option value="TONNE">TONNE</option>
                                                                <option value="POUND">POUND</option>
                                                            </select>                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Description</label>
                                                            <input type="text" class="form-control" name="description" placeholder="Enter Description" value="">
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">COD</label>
                                                            <input type="text" class="form-control" name="cod" placeholder="Enter COD" value="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">COD Payment Mode</label>
                                                            <select class="form-control" name="cod_payment_mode">
                                                                <option value="">Select</option>
                                                                <option value="Cash">Cash</option>
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="PayMob">PayMob</option>
                                                                <option value="MPesa">MPesa</option>
                                                            </select>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Duty Paid By</label>
                                                            <select class="form-control" name="duty_paid_by">
                                                                <option value="">Select</option>
                                                                <option value="Sender">Sender</option>
                                                                <option value="Receiver">Receiver</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <div class="row">
                                                    <div class="col-md-12 mt-2"><h5>Package Details</h5></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                 <thead>
                                                                    <tr>
                                                                        <th>Bar Code</th>
                                                                        <th>Package Count</th>
                                                                        <th>Length</th>
                                                                        <th>Width</th>
                                                                        <th>Height</th>
                                                                        <th>Weight</th>
                                                                        <th>Charged Weight</th>
                                                                        <th>Selected Package</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="add-1">
                                                                        <td>
                                                                            <input type="text" class="form-control" name="bar_code[]" placeholder="Enter Bar Code" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Enter Package Count" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control length_arr" name="length[]" placeholder="Enter Length" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control width_arr" name="width[]" placeholder="Enter width" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control height_arr" name="height[]" placeholder="Enter height" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control weight_arr" name="weight[]" placeholder="Enter weight" value="">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control charged__weight_arr" name="charged__weight[]" placeholder="Enter Charged Weight" value="">
                                                                        </td>
                                                                        <td>
                                                                            <select class="form-control" name="selected_package[]">
                                                                                <option value="DOCUMENT">DOCUMENT</option>
                                                                                <option value="NON DOCUMENT">NON DOCUMENT</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger delete-package" data-id="1"><i class="la la-trash"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12"><button type="button" class="btn btn-sm btn-info pull-right mt-1 mb-2 add-more-package">Add More</button></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-10 error-msg"></div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-success pull-right save-waybill">Submit</button>
                                                    </div>
                                                </div>
											</form>
										</div>
									</div>
								</div>
							</section>
							<!-- Main Slider Close -->
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
@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
<style>
    .create-waybill-bg{
        padding: 10px 0;
        background: #f1f2f7;
        border: 1px solid #cacfe7;
    }
    .list-your-service-form-box .col-md-12{padding: 0px !important;}
    #create-waybill h5{font-size: 16px; padding: 2px 0;}
</style>
@endpush
@push('scripts')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#delivery_date').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});

        var increment = 2;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click','.add-more-package',function(){
            let package = `<tr class="add-${increment}">
                    <td>
                        <input type="text" class="form-control" name="bar_code[]" placeholder="Enter Bar Code" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control package_count_arr" name="package_count[]" placeholder="Enter Package Count" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control length_arr" name="length[]" placeholder="Enter Length" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control width_arr" name="width[]" placeholder="Enter width" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control height_arr" name="height[]" placeholder="Enter height" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control weight_arr" name="weight[]" placeholder="Enter weight" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control charged__weight_arr" name="charged__weight[]" placeholder="Enter Charged Weight" value="">
                    </td>
                    <td>
                        <select class="form-control" name="selected_package[]">
                            <option value="DOCUMENT">DOCUMENT</option>
                            <option value="NON DOCUMENT">NON DOCUMENT</option>
                        </select>
                    </td>
                    <td>
                        <button type="subbmit" class="btn btn-sm btn-danger delete-package" data-id="${increment}"><i class="la la-trash"></i></button>
                    </td>
                </tr>`;
                increment++;
                $('tbody').append(package);
        });
        //----------------------------------------------------------------------------------------------
        $('body').on('click','.delete-package',function(){
            let id = $(this).data('id');
            $('.add-'+id).remove();
        });
//----------------------------------------------------------------------------------------------        
        $("#create-waybill").validate({
            rules: {
                from_ou: { maxlength: 191 },
                way_bill_number: { maxlength: 191 },
                delivery_date: { maxlength: 50 },
                customer_code: { required:true,maxlength: 50 },
                consignee_code: { maxlength: 50 },
                consignee_address: { required:true,maxlength: 191 },
                consignee_country: { required:true,maxlength: 50 },
                consignee_state: { required:true,maxlength: 50 },
                consignee_city: { required:true,maxlength: 50 },
                consignee_pincode: { maxlength: 15 },
                consignee_name: { required:true,maxlength: 50 },
                consignee_phone: { required:true,maxlength: 15,minlength:8 },
                client_code: { maxlength: 50 },
                number_of_packages: { required:true,maxlength: 10,number: true },
                actual_weight: { required:true,maxlength: 10,number: true },
                charged_weight: { required:true,maxlength: 10,number: true },
                cargo_value: { required:true,maxlength: 10,number: true },
                reference_number: { maxlength: 50 },
                invoice_number: { maxlength: 50 },
                payment_mode: { required:true,maxlength: 50 },
                service_code: { required:true,maxlength: 50 },
                reverse_logistic_activity: { maxlength: 50 },
                reverse_logistic_refund_amount: { maxlength: 7 ,number: true },
                weight_unit_type: { required:true,maxlength: 50 },
                description: { maxlength: 5000 },
                cod: { maxlength: 50 },
                cod_payment_mode: { maxlength: 50 },
                duty_paid_by: { maxlength: 50 },
            },
            errorElement: "small",
            errorPlacement: function ( error, element ) {
                error.addClass( "text-danger" );
                error.insertAfter( element );
            },
            focusInvalid: false,
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids())
                    return;
                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).offset().top-60
                  }, 1000);
            },
            success: function() {
                return false;
            },
            submitHandler: function(form) { 
                var formData = $(form);
                let status = false;
                status = check_validation('package_count_arr');
                status = check_validation('length_arr');
                status = check_validation('width_arr');
                status = check_validation('height_arr');
                status = check_validation('weight_arr');
                status = check_validation('charged__weight_arr');

                if(status==true){return false;}

                $.ajax({
                    type : 'post',
                    url : "{{ route('reverse-logistic.store') }}",
                    data : formData.serialize(),
                    dataType: 'json',
                    beforeSend : function(){
                        $(".save-waybill").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
                    },
                    success : function(data){
                        if(data.status==201){
                            $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${data.message}    
                            </div>`);
                            return false;
                        }else{
                            $('.error-msg').html(`<div class="alert alert-info alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${data.message}    
                            </div>`);
                            $(".save-waybill").html(`Submit`).attr('disabled',false);
                            return false;
                        }
                    },
                    error : function(data){
                        console.log(data);
                        if(data.status==422){
                            $.each(data.responseJSON.errors,function(k,v){
                                const $input = formData.find(`input[name=${k}],select[name=${k}]`);                
                                if($input.next('small').length){
                                    $input.next('small').html(v); 
                                }else{
                                    $input.after(`<small class='text-danger'>${v}</small>`); 
                                }
                            });
                            $(".save-waybill").html(`Submit`).attr('disabled',false);
                            return false;
                        }else{
                            $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${data.statusText}    
                            </div>`);
                            $(".save-waybill").html(`Submit`).attr('disabled',false);
                            return false;
                        }
                    }
                });
                //return false;
            }
        
        });
//----------------------------------------------------------------------------------------------        
        $('body').on('keyup change','input,select',function(){
            let text = $(this).val();
            if(text.length>0){
                $(this).next('small').text('');
            }
        });
//----------------------------------------------------------------------------------------------        
    });

    window.onbeforeunload = function (event) {
        var message = 'Important: Please click on \'Save\' button to leave this page.';
        if (typeof event == 'undefined') {
            event = window.event;
        }
        if (event) {
            event.returnValue = message;
        }
        return message;
    };

    $(function () {
        $("a").not('#lnkLogOut').click(function () {
            window.onbeforeunload = null;
        });
        $(".btn").click(function () {
                window.onbeforeunload = null;
        });
    });

    function check_validation(cls){
        let status = false;
        $('.'+cls).each(function(i,v){
            let value =$(this).val();
            if(value!=0 && value!='' ){
                $(this).removeAttr('style');
            }else{
                status = true;
                $(this).css('border-color','#ff0000');
            }
        });
        return status;
    }
</script>
@endpush