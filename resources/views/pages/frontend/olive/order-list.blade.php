<section class="tips-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12">
                <div class="">
                    <div class="card-content">
                        <h4>Return Request Order List</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="">
                            <div class="row">
                                <div class="col-lg-5">
                                    <input type="tex" name="s" value="" class="form-control" placeholder="Enter Order Number">
                                </div>
                                <div class="col-lg-5">
                                    <input type="tex" name="email" value="" class="form-control" placeholder="Enter Customer Email">
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-red save-waybill">Submit</button>
                                </div>
                            </div>
                        </form>                            
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if($waybill)
                                @forelse($waybill as $row)
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
                                                    <a class="btn btn-secondry btn-view" href="{{route('olive.order-detail', $row->id) }}">View Order</a>
                                                    @php
                                                        $tracking_detail = ($row->meta->_generate_waywill_status)?? NULL; 
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
                                                    @if(!empty($label_url))
                                                        <a target="_blank" class="btn btn-secondry btn-download" href="{{ $label_url }}">Download label</a>
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
                                    <div class="alert alert-info">Thank you for confirming your return. You can download the return label by clicking on the 'Download label’ button, you’ll also receive the label via email shortly</div>
                                @empty
                                    <div class="order-card">
                                        <div class="order-card-body">
                                            <p>No Orders Found</p>
                                        </div>
                                    </div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</section>