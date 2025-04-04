@forelse($lists as $list)
	@forelse($list->processed_item as $pakage)
	    <tr class="add-{{ $pakage->id }}">
	        <td>{{ $list->way_bill_number }}</td>
	        <td>{{ $pakage->bar_code }}</td>
	        <td>{{ $list->client->name ?? 'N/A' }}</td>
	        <td>{{ $list->meta->_customer_name ?? 'N/A' }}</td>
	        <td>{{ $list->tracking_id ?? 'N/A' }}</td>
	        <td>{{ $pakage->process_status }}</td>
	        <td>
	            <input type="hidden" name="pallet_orders[]" value="{{ $pakage->id }}">
	            <button type="button" class="btn btn-danger" onclick="delete_row({{ $pakage->id }});">Delete</button>
	        </td>
	    </tr>
    @empty
	@endforelse
@empty
@endforelse