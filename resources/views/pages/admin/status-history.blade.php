<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th>Comment</th>
				<th>Date</th>
				<th>Time</th>
				<th>Created Date</th>
				<th>Username</th>
			</tr>
		</thead>
		<tbody>
			@forelse($history as $his)
				<tr>
					<td>{{ $his->addition_info }}</td>
					<td>{{ date('d/m/Y', strtotime($his->status_date)) }}</td>
					<td>{{ $his->status_time }}</td>
					<td>{{ date('d/m/Y', strtotime($his->created_at)) }}</td>
					<td>{{ $his->user }}</td>
				</tr>
			@empty
				<tr>
					<td colspan="6">No History</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</div>