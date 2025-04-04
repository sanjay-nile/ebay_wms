@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/create-waywill.js') }}"></script>
<script>
    $(document).ready(function(){
        
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
						<li class="breadcrumb-item active">Add eBay Package</li>
					</ol>
				</div>
			</div>
		</div>
        <!-- Main content -->
        <div class="row">
			<div class="col-md-12">
                @include('pages-message.notify-msg-error')
                @include('pages-message.notify-msg-success')
                @include('pages-message.form-submit')
			</div>
            <div class="col-xs-12 col-md-12 table-responsive">
                <div class="card booking-info-box">
                    <div class="card-header">                        
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
                    <div class="card-body">
                        <section class="list-your-service-section">
                            <div class="list-your-service-content">
                                <div class="list-your-service-form-box">
                                    <div class="row">
                                        <div class="col-md-12"><h5 class="card-title">Add eBay Package</h5></div>
                                    </div>
                                   <form method="post" action="{{ route('admin.order.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="authorized_by" value="{{ Auth::user()->name }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">EVTN Number</label>
                                                    <input type="text" class="form-control" name="evtn_number" placeholder="Enter EVTN Number" value="{{ old('evtn_number') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Label No./ Tracking No.</label>
                                                    <input type="text" class="form-control" name="tracking_number" placeholder="Enter Label No./ Tracking No." value="{{ old('tracking_number') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Customer Name</label>
                                                    <input type="text" class="form-control" name="customer_name" placeholder="Enter Customer Name" value="{{ old('customer_name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">City</label>
                                                    <input type="text" class="form-control" name="customer_city" placeholder="Enter City" value="{{ old('customer_city') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">ZipCode</label>
                                                    <input type="text" class="form-control" name="customer_pincode" placeholder="Enter ZipCode" value="{{ old('customer_pincode') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">State</label>
                                                    <input type="text" class="form-control" name="customer_state" placeholder="Enter State" value="{{ old('customer_state') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <button type="submit" class="btn-Submit1" onClick="this.form.submit(); this.disabled=true; this.value='Sending…'; ">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
                            <thead>
                                <tr>                                         
                                    <th class="ws">Date</th>
                                    <th class="ws">Ref. Number</th>
                                    <th class="ws">EVTN Number</th>
                                    <th class="ws">Name</th>
                                    <th class="ws">Tracking No.</th>
                                    <th class="ws">Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $row)                                        
                                    <tr>                                            
                                        <td class="ws">{!! date('d-m-Y', strtotime($row['_order_date'])) !!}</td>
                                        <td class="ws">
                                            {!! $row['_post_id'] !!}
                                        </td>
                                        <td class="ws">{!! $row['evtn_number'] ?? '' !!}</td>
                                        <td class="ws">{!! $row['customer_name'] ?? '' !!}</td>
                                        <td class="ws">{!! $row['tracking_number'] ?? '' !!}</td>
                                        <td class="ws">{!! $row['customer_address_line_1'] ?? '' !!} {!! $row['customer_address_line_2'] ?? '' !!} {!! $row['customer_city'] ?? '' !!} {!! $row['customer_state'] ?? '' !!} {!! $row['customer_pincode'] ?? '' !!}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection