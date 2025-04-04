@php $i=1 @endphp
@forelse($lists as $row)
    <tr>
        <td>{{ $i++ }}</td>
        <td>
            @if($row->status == 'Pending')
                <a class="btn btn-edit btn-primary" href="{{ route('new-reverse-logistic.edit',$row) }}" title="Edit">
                    <i class="la la-edit"></i>
                </a>
                <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.show',$row) }}" title="View">
                    <i class="la la-eye"></i>
                </a>
            @else
                <a class="btn btn-view btn-success" href="{{ route('reverse-logistic.view',$row) }}" title="View">
                    <i class="la la-eye"></i>
                </a>
            @endif
        </td>
        <td>{{ $row->client->name ?? 'N/A' }}</td>
        <td>{{ $row->meta->_source ?? 'N/A' }}</td>
        <td>{{ $row->meta->_source_name ?? 'N/A' }}</td>
        <td>
            @if($row->status == 'Pending')
                Label Failed
            @else
                @if($row->hasMeta('_order_waywill_status'))
                    InScan
                @else
                    @if($row->cancel_return_status != null)
                        Cancelled
                    @else
                        @if($row->status == 'Success' && $row->process_status == 'unprocessed')
                            Processed
                        @endif
                        @if($row->status == 'Success' && $row->process_status == 'processed')
                            Received at Hub
                        @endif
                    @endif
                @endif
            @endif
        </td>
        <td>
            @if($row->hasMeta('_drop_off') && $row->meta->_drop_off == 'By_ReturnBar')
                By Return Bar™
            @else
                {{ str_replace('_', ' ', $row->meta->_drop_off ?? '') ?? "N/A" }}
            @endif
        </td>
        <td>{{ $row->id }}</td>
        <td>{{ $row->way_bill_number }}</td>
        <td>{{ $row->meta->_customer_name }}</td>
        <td>{{ $row->meta->_customer_email }}</td>
        <td>{{ date('d/m/Y h:i:s a',strtotime($row->created_at)) }}</td>
        <td>{{ $row->meta->_customer_country }}</td>
        <td>{{ $row->shippingPolicy->carrier->name ?? $row->meta->_carrier_name }}</td>
        <td>{{ $row->shippingPolicy->shippingType->name ?? $row->meta->_shipment_name }}</td>
        <td>
            <?php
                $track_id = 'N/A';
                $tracking_detail = ($row->meta->_generate_waywill_status)?? NULL; 
                $tracking_data = ($tracking_detail)?json_decode($tracking_detail):NULL;
                if($tracking_data){
                    $track_id = $tracking_data->carrierWaybill ?? 'N/A';             
                }
            ?>

            {{ $track_id }}
        </td>
        <td>{{ $row->meta->_consignee_name ?? 'N/A' }}</td>                                            
    </tr>
@empty
@endforelse