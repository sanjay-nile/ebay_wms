@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        <div class="we-page-title">
            <div class="row">
                <div class="col-md-8 align-self-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                        <li class="breadcrumb-item active">Import Order</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card box-info">
                    <div class="card-body">
                        <form enctype="multipart/form-data" method="POST" action="{{route('admin.import.order.invoice')}}" id="formid">
                            @csrf
                            <div class="rg-pack-card">
                                <h5>Import Order Data</h5>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="file" name="postdata_file" id="postdata_file" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-blue save-client">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection