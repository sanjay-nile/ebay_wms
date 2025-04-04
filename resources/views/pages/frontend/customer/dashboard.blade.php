@push('css')
<link href="{{ asset('admin/css/new-admin-app.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/datatable/css/datatables.min.css') }}" rel="stylesheet">
<style type="text/css">
	.order-card{position: relative;
    /*margin-bottom: 1.875rem;*/
    border: none;
    -webkit-box-shadow: 0 1px 15px 1px rgba(62,57,107,.07);
    box-shadow: 0 1px 15px 1px rgba(62,57,107,.07);
    background: #fff;
    /*border-radius: 5px;}*/
    .order-info h2 {font-size: 16px; color: #0d1136; font-weight: bold; margin: 0; padding: 0; } 
    .order-card-header{ padding: 10px 20px;background: #b51f37; }
    .order-card-header .order-content span {color: #fff; font-weight: 600;}
    /*.order-card-footer{ background: #fff6f7;padding: 5px 20px;border-radius:0 0 5px 5px; }*/
    .order-content span{font-size: 13px; color: #0e1036;}
    .order-content span.order-bold{font-weight: 600;padding-right: 10px;}

    .order-btn-info a{border-radius: 4px; padding: 5px 15px; border: 1px solid #f1f3f4; font-size: 12px; background: #ffffff; background: -moz-linear-gradient(top, #ffffff 0%, #eef0f2 100%); background: -webkit-linear-gradient(top, #ffffff 0%, #eef0f2 100%); background: linear-gradient(to bottom, #ffffff 0%, #eef0f2 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#eef0f2', GradientType=0); color: #0e1036; display: inline-block; margin: 0 auto 8px auto; /*width:48%; */text-align: center; }
    .order-info {
        border-bottom: 1px solid #f4f5fa;
        padding: 2px;
        margin: 2px 0;
    }

    .order-info-header {display: flex; border-bottom: 2px solid #f4f5fa; }
    .order-info-header span {width: 33%; font-size: 13px; color: #0e1036; font-weight: 600; margin-bottom: 5px; }
    .order-info-body{display: flex;}
    .order-info-body span{width: 33%;font-size: 12px;}
    a.disabled {
        /* Make the disabled links grayish*/
        color: gray;
        /* And disable the pointer events */
        pointer-events: none;
        cursor: not-allowed;
    }
    .alert-info{
        font-size: 13px;
    }
</style>
@endpush

@push('js')
<script type="text/javascript" src="{{ asset('plugins/datatable/js/datatables.min.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var defaults= {
            dom: 'Bfrtip', buttons: [],
            'aoColumnDefs': [ {
                'bSortable': false, 'aTargets': [-1]/* 1st one, start by the right */
            }], exportOptions: {
                columns: [1, 2, 3, 4]
            }, "searching": true, "ordering": true, "bPaginate": false, "bInfo": false
        };
        $('.avn-defaults').dataTable($.extend(true, {}, defaults, {}));
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary');
        $('#client_user_list_wrapper').removeClass('container-fluid');
    });
</script>
@endpush

<section class="tips-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12">
                @forelse($lists as $row)                    
                	<div class="order-card">
                		<div class="order-card-header">
                			<div class="row">
                				<div class="col-md-6 col-sm-6 col-lg-3">
                					<div class="order-content">
                						<span class="order-bold">Order No.:</span>
                						<span>{{ $row->way_bill_number }}</span>
                					</div>
                				</div>
                				<div class="col-md-6 col-sm-6 col-lg-3">
                                    <div class="order-content">
                                        <span class="order-bold">Client Name:</span>
                                        <span>{{ $row->client_name }}</span>
                                    </div>
                                </div>
                                
                				<div class="col-md-6 col-sm-6 col-lg-3">
                					<div class="order-content">
                						<span class="order-bold">Date:</span>
                						<span>{{ date('d/m/Y',strtotime($row->created_at)) }}</span>
                					</div>
                				</div>
                				<div class="col-md-6 col-sm-6 col-lg-3">
                					<div class="order-content">
                						<span class="order-bold">Return Option:</span>
                                        @if($row->meta->_drop_off == 'By_ReturnBar')
            						        <span>By Return Bar™</span>
                                        @else
                                            <span>{{ str_replace('_', ' ', $row->meta->_drop_off) ?? "N/A" }}</span>
                                        @endif
                					</div>
                				</div>

                			</div>
                		</div>
                		<div class="order-card-body">
                			<div class="row">
                                <div class="order-btn-info">                                    
                                    <a class="btn btn-secondry btn-view" href="{{route('customer.order-detail', $row->id) }}">View Order</a>
                                    @if($row->meta->_drop_off != 'By_ReturnBar')
                                        @php
                                            $tracking_detail = ($row->meta->_order_tracking_id)?? NULL; 
                                            $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                                            $label_url = '';
                                            if($tracking_data){
                                                foreach($tracking_data as $t){
                                                    $d = date('Y-m-d', strtotime($t->modifiedOn));
                                                    $label_url = (isset($t->carrierWaybillURL) && !empty($t->carrierWaybillURL)) ? $t->carrierWaybillURL : '';
                                                }
                                            }
                                        @endphp
                                        @if(!empty($label_url))
                                    	<a target="_blank" class="btn btn-secondry btn-download" href="{{ $label_url }}">Download label</a>
                                        @endif
                                    @else
                                        @php
                                            $hrs = $row->getMeta('_happy_return_status');
                                        @endphp
                                        @if($hrs)
                                            @php $hrsv = json_decode($hrs); @endphp
                                            <a class="btn btn-secondry btn-view" href="{{ $hrsv->qr_code }}" target="_blank">
                                                <i class="fa fa-eye"></i> QR Code
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                      	<tr>
                                        	<th>Product Title</th>
                                        	<th>Item Bar Code</th>
                                        	<th>Quantity</th>
                                      	</tr>
                                    </thead>
                                    <tbody>
                                    	@php $cnt = 0; @endphp
                                    	@forelse($row->packages as $package)
                                    		<tr>
                                    		  	<td>{{ $package->title??"N/A" }}</td>
                                    		  	<td>{{ $package->bar_code??"N/A" }}</td>
                                    		  	<td>{{ $package->package_count }}</td>
                                    		</tr>                                    	    
                                    	    @php $cnt += $package->package_count; @endphp
                                    	@empty
                                			<tr rowspan="3">
                                    		  	<p>Package not added</p>
                                			</tr>
                                    	@endforelse                                      	
                                    </tbody>
                                    <thead>
                                      	<tr>
                                        	<th>No Of Pkg:</th>
                                        	<th>Remarks</th>
                                        	<th>Customer Address:</th>
                                      	</tr>
                                    </thead>
                                    <tbody>
                                      	<tr>
                                        	<td>{{ $cnt }}</td>
                                        	<td>{{ $row->meta->_remark??"No Remarks" }}</td>
                                        	<td>{{ $row->meta->_customer_address }},{{ $row->meta->_customer_city }},{{ $row->meta->_customer_state }},{{ $row->meta->_customer_pincode }},{{ $row->meta->_customer_country }}</td>
                                      	</tr>
                                    </tbody>
                                </table>
                			</div>
                		</div>            		
                	</div>                    
                    @if($row->meta->_drop_off == 'By_ReturnBar')
                        <div class="alert alert-info">Thank you for confirming your return. You can view your QR code by clicking on the ‘QR Code’ button, you’ll also receive the QR code via email shortly.</div>
                    @else
                        <div class="alert alert-info">Thank you for confirming your return. You can download the return label by clicking on the 'Download label’ button, you’ll also receive the label via email shortly</div>
                    @endif
            	@empty
                    <div class="order-card">
                        <div class="order-card-body">
                            <p>No Orders Found</p>
                        </div>
                    </div>
    			@endforelse
            </div>
        </div>
    </div>
</section>