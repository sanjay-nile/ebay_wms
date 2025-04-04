<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<style type="text/css">
	.search-btn{ position: absolute; top: 0px;}
</style>
<script type="text/javascript">
	$(document).ready(function() {
	    $(".frm-submit").click(function() {
	        let id = $(this).attr('data-id');
	        $('form#frm-'+id).submit();
	    });
	});
</script>
<div class="tracking-wrapper">
	@if($msg)
		<div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{!! $msg !!}
        </div>
    @endif
   	<div class="tracking-search-container mb-0">
      	<h5 class="font-weight-bold m-0">Where is my return?</h5>
      	{{-- <form action=""  class="help-search-form" method="get">
      		<h6 class="m-0">Track your return by entering your Tracking ID</h6>
         	<div class="search-form-group row">	         	
	        	<div class="col-md-12">
	        		<label class="font-weight-bold text-dark">Search by Tracking ID</label>
		            <input type="search" name="track_id" class="form-control"  placeholder="Please enter Tracking ID">
		            <button type="submit" title="help-Search" class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
		        </div>
         	</div>
      	</form> --}}
      	<form action=""  class="help-search-form" method="get">
      		{{-- <p>Search by Order Number and Email id</p> --}}
      		<h6 class="text-left mt-3">Track your return by entering your tracking number and email address</h6>
         	<div class="search-form-group row">
	         	<div class="col-md-6">
	         		{{-- <label class="font-weight-bold text-dark">Enter an order number</label> --}}
	            	<input type="search" name="track_id" class="form-control"  placeholder="Please enter your tracking number">
	        	</div>
	        	<div class="col-md-6 mob-mt">
	        		{{-- <label class="font-weight-bold text-dark mob-font">Enter the email address associated with the order</label> --}}
		            <input type="search" name="email" class="form-control"  placeholder="Please enter your email">
		            <button type="submit" title="help-Search" class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
		        </div>
         	</div>
      	</form>
      	<form action=""  class="help-search-form" method="get">
      		{{-- <p>Search by Order Number and Email id</p> --}}
      		<h6 class="text-left mt-3">Track your return by entering your order number and email address</h6>
         	<div class="search-form-group row">
	         	<div class="col-md-6">
	         		{{-- <label class="font-weight-bold text-dark">Enter an order number</label> --}}
	            	<input type="search" name="s" class="form-control"  placeholder="Please enter your order number">
	        	</div>
	        	<div class="col-md-6 mob-mt">
	        		{{-- <label class="font-weight-bold text-dark mob-font">Enter the email address associated with the order</label> --}}
		            <input type="search" name="email" class="form-control"  placeholder="Please enter your email">
		            <button type="submit" title="help-Search" class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
		        </div>
         	</div>
      	</form>
   	</div>

   	@if($waybill)
        @forelse($waybill as $row)
            @php
                $tracking_detail = ($row->meta->_generate_waywill_status)?? NULL; 
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
		   	<div class="order-card mt-3">
		      	<div class="order-card-header">
		         	<div class="row">
			            <div class="col-md-6 col-sm-6 col-lg-4">
			               <div class="order-content">
			                  	<span class="order-bold">Order No.:</span>
			                  	<span>{{ $row->way_bill_number }}</span>
			               </div>
			            </div>
			            
			            <div class="col-md-6 col-sm-6 col-lg-4">
			               <div class="order-content">
			                  	<span class="order-bold">Return Date:</span>
			                  	<span>{{ date('d/m/Y',strtotime($row->created_at)) }}</span>
			               </div>
			            </div>
			            @if(empty($row->cancel_return_status))
				            <div class="col-md-6 col-sm-6 col-lg-4">
				               <div class="order-content">
				                  	<span class="order-bold">Tracking No.:</span>
				                  	<span>{{ !empty($tracking_id) ? $tracking_id : 'N/A' }} </span>
				               </div>
				            </div>
				        @else
				        	<div class="col-md-6 col-sm-6 col-lg-4">
				               <div class="order-content">
				                  	<span class="order-bold">Order Status:</span>
				                  	<span>Cancelled</span>
				               </div>
				            </div>
				        @endif
		         	</div>
		      	</div>		      	
		      	<div class="order-card-body">
		         	<div class="row">            
			            {{-- <table class="table table-striped">
			               	<thead>
			                  	<tr>
			                     	<th>SKU #</th>
			                     	<th>Product Name</th>
			                     	<th>Quantity</th>
			                     	<th>Reason for return</th>
			                  	</tr>
			               	</thead>
			               	@forelse($row->packages as $package)
				               	<tbody>
				                  	<tr>
				                     	<td>{{ $package->bar_code??"N/A" }}</td>
				                     	<td>{{ $package->title??"N/A" }}</td>
				                     	<td>{{ $package->package_count }}</td>
				                     	<td>{{ reason_of_return($package->return_reason) }}</td>
				                  	</tr>
				               	</tbody>
			               	@empty
                                <tr rowspan="4">
                                    <p>Package not added</p>
                                </tr>
                            @endforelse
			            </table> --}}

			            {{-- track detail content --}}
			            @if(empty($row->cancel_return_status) && !empty($tracking_id))				            
				            <div class="modal-content track-content">
				                <div class="modal-header bg-black rounded-0">
				                    <h6 class="modal-title p-0 text-light">Tracking Details</h6>
				                </div>
				                <div class="modal-body">
				                    <h6 class="p-0">Tracking ID: <b>{{ !empty($tracking_id) ? $tracking_id : 'N/A' }}</b></h6>
				                    <div class="font-weight-bold text-dark" id="track-data-{{ $row->id }}">{!! getTrackingDetail($tracking_id) !!}</div>
				                </div>
				            </div>
				        @endif

		            	<div class="container mt-2">
		              		<div class="row">
		              			@if(empty($row->cancel_return_status) && !$row->hasMeta('_order_waywill_status'))
			                    	<div class="col-md-6">
			                      		<div class="step-btn-group">
			                        		<div class="step-btn-content">		                        			
			                          			<a class="btn-next" data-toggle="modal" data-target="#myModal-{{ $row->id }}">Cancel Return</a>
			                          			<!-- The Modal -->
		                          			  	<div class="modal" id="myModal-{{ $row->id }}">
		                          			    	<div class="modal-dialog">
		                          			      		<div class="modal-content">      
		                          					        <!-- Modal Header -->
		                          					        <div class="modal-header h-bg">
		                          					          <h5 class="modal-title p-0 text-light">Cancel Return</h5>
		                          					          <button type="button" class="close text-light" data-dismiss="modal">&times;</button>
		                          					        </div>        
		                          					        <!-- Modal body -->
		                          				        	<div class="modal-body body-bg">
		                          					         	<h5> Are you sure you want to cancel your return?</h5>
		                          					         	<form method="post" action="{{ route('missguided.cancel.return') }}" id="frm-{{ $row->id }}">
		                          					         		@csrf
		                          					         		<input type="hidden" name="order_id" value="{{ $row->id }}">
		                          					         	</form>
		                          					        </div>        
		                          					        <!-- Modal footer -->
		                          					        <div class="modal-footer step-btn-content">
		                          					          	<a type="button" class="btn-next back-tab" data-dismiss="modal">Go Back</a>
		                          					          	<a type="button" class="btn-next back-tab frm-submit" data-id="{{ $row->id }}">Yes</a>
		                          					        </div>        
		                          			      		</div>
		                          			    	</div>
		                          			  	</div>
			                        		</div>
			                  			</div>
			                    	</div>
			                    @else
			                    	<div class="col-md-6">&nbsp;</div>
		                        @endif
		                     	<div class="col-md-6">
		                    		<div class="step-btn-group">
		                      			<div class="step-btn-content">
		                        			<a class="btn-next back-tab" href="{{route('missguided.order-detail', $row->id) }}" data-id="first">View Return Information</a>
		                      			</div>
		                    		</div>
		                  		</div>
		                  	</div>
		              	</div>
		         	</div>
		      	</div>
		   	</div>

		   	@php
		   		sleep(1);
		   	@endphp
   		@empty
            <div class="order-card">
                <div class="order-card-body">
                    <!-- <p>No Orders Found</p> -->
                </div>
            </div>
        @endforelse
    @endif  	
</div>

