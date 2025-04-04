@if($opreator->count() > 0)
    <table class="table table-striped table-bordered table-sm">
        <tr>
            <th>Name</th>
            <th>Scan In</th>
            <th>Scan Out</th>
            <th>Pending Picked</th>
            <th>Dispatched</th>
            <th>Cancelled</th>
            <th>Total Handled</th>
        </tr>
        @forelse($opreator as $op)
            <tr>
                <td>{{ $op->name ?? '' }}</td>
                <td>{{ $op->scan_in ?? '' }}</td>
                <td>{{ $op->scan_out ?? '' }}</td>
                <td>{{ $op->picked ?? '' }}</td>
                <td>{{ $op->dispatch ?? '' }}</td>
                <td>{{ $op->cancelled ?? '' }}</td>
                <td>{{ $op->total_package ?? '' }}</td>
            </tr>
        @empty
        @endforelse
    </table>

    <div class="pagination">
        {!! $opreator->links() !!}
    </div>
@endif