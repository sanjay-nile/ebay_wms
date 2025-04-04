@extends('layouts.admin.layout')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">Assign Client To Admin Rep</li>
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
                            <a href="{{ route('sub-admin') }}" class="btn btn-outline-primary btn-sm"><i class="la la-arrow-left"></i> Back</a>
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
                                        <div class="list-your-service-form-box" style="width: 85%;">
                                            <div class="row">
                                                <div class="col-md-12"><h5 class="card-title">Assign Client To Admin Rep</h5></div>
                                            </div>
                                            <form action="{{ route('client.to.subadmin.store') }}" method="post">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Admin Rep</label>
                                                            <select name="customer_rep" id="" class="form-control">
                                                                <option value="">Select</option>
                                                                @forelse($sub_admins as $sub_admin)
                                                                <option value="{{ $sub_admin->id }}" @php if($sub_admin->id == old('customer_rep')){ echo 'selected'; } @endphp >{{ $sub_admin->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                            @if($errors->has('customer_rep'))
                                                            <span class="text-danger">{{ $errors->first('customer_rep') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            @php 
                                                                $client_arr = old('client')?? array();
                                                            @endphp
                                                            <label for="">Clients</label>
                                                            <select name="client[]" id="clients" class="form-control" multiple >
                                                                <option value="">Select</option>
                                                                @forelse($clients as $client)
                                                                <option value="{{ $client->id }}" @php if(in_array($client->id,$client_arr)){ echo 'selected'; } @endphp>{{ $client->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                            @if($errors->has('client'))
                                                            <span class="text-danger">{{ $errors->first('client') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-red pull-right" onclick="this.disabled=true; this.innerText='Sending…';this.form.submit();">Assign</button>
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
    <link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/select2override.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#clients').select2({
              placeholder: 'Select Clients',
              allowClear: true
            });
        })
    </script>
@endpush