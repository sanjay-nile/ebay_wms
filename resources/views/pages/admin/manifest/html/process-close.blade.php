<table id="client_user_list" class="table table-striped table-hover nowrap">
    <thead>
        <tr>
            <th>S no.</th>
            <th>Pallet ID</th>
            <th>From Warehouse</th>
            <th>To Warehouse</th>
            <th>Pallet Type</th>
            <th>Reference No</th>
            <th>EVTN Number</th>
            <th>Label No./ Tracking No.</th>
            <th>Customer Name</th>
            <th>City</th>
            <th>ZipCode</th>
            <th>State</th>
            <th>Order No.</th>
            <th>SKU #</th>
            <th>Hs Code</th>
            <th>Country Of Origin</th>
            <th>Category</th>
            <th>Sub Category</th>
        </tr>
    </thead>
    <tbody>
        @php $i=1 @endphp
        @if(count($orders) > 0)
            {{-- @foreach($orders->chunk(100) as $chunk) --}}
                @foreach($orders as $package)
                    <tr>
                        @php
                            $fr = $pallet->meta->fr_warehouse_id ?? '';
                            $cn = $package->country_of_origin ?? '';
                            if (empty($cn)) {
                                $cn = $package['coo'] ?? '';
                            }
                        @endphp
                        <td>{{ $i++ }}</td>
                        <td>{{ $pallet->pallet_id }}</td>
                        <td>{{ getWareHouseName($fr) }}</td>
                        <td>{{ $pallet->warehouse->name ?? 'N/A' }}</td>
                        <td>{{ $pallet->return_type }}</td>
                        <td>{{ $package['_post_id'] ?? '' }}</td>
                        <td>{{ $package['evtn_number'] ?? '' }}</td>
                        <td>{{ $package['tracking_number'] ?? '' }}</td>
                        <td>{{ $package['customer_name'] ?? '' }}</td>
                        <td>{{ $package['customer_city'] ?? ''  }}</td>
                        <td>{{ $package['customer_pincode'] ?? ''  }}</td>
                        <td>{{ $package['customer_state'] ?? '' }}</td>
                        <td>{{ $package['order_number'] ?? '' }}</td>
                        <td>{{ $package['sku'] ?? '' }}</td>
                        <td>{{ $package['hs_code'] ?? '' }}</td>
                        <td>{{ $cn }}</td>
                        <td>{{ $package['category_name'] ?? '' }}</td>
                        <td>{{ $package['sub_category_name'] ?? '' }}</td>
                    </tr>
                @endforeach
            {{-- @endforeach --}}
        @endif
    </tbody>
</table>