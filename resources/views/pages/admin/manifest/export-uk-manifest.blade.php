@extends('layouts.admin.layout')
@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

<style type="text/css">
    .nav.nav-tabs.nav-underline {
        border-bottom: 1px solid #ffdfe4 !important;
        margin-bottom: 26px !important;
        background: #fff1f3;
    }
    .align-col .row .col-md-2, .col-md-1{padding: 0px 5px;}
</style>

@section('content')
<div class="app-content content"> 
    <div class="content-wrapper">
      <div class="we-page-title">
         <div class="row">
            <div class="col-md-8 align-self-left">
               <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                  <li class="breadcrumb-item active">Export Out of UK Manifest</li>
               </ol>
            </div>
         </div>
      </div>

        <!-- Main content -->
        <div class="row">
         <div class="col-xs-12 col-md-12">
            @include('pages/errors-and-messages')
         </div>

            <div class="col-xs-12 col-md-12">
                <div class="card booking-info-box">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal fiter-form ml-2 mt-1 align-col">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Client</label>
                                        <div class="form-group">
                                            <select name="client" id="" class="form-control">
                                                <option value="">Select Client</option>
                                                @forelse($client_list as $client)
                                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Pallet ID</label>
                                        <div class="form-group">
                                            <input type="text" name="pallet_id" class="form-control" placeholder="Pallet ID" value="" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Return Type</label>
                                        <div class="form-group">
                                            <select name="return_type" id="" class="form-control">
                                                <option value="" >-- Select Return Type --</option>
                                                <option value="Charity">Charity</option>
                                                <option value="Discrepency">Discrepency</option>
                                                <option value="Restock">Restock</option>
                                                <option value="Resell">Resell</option>
                                                <option value="Return">Return</option>
                                                <option value="Redirect">Redirect</option>
                                                <option value="Recycle">Recycle</option>
                                                <option value="Other">Other</option>
                                                <option value="Short Shipment">Short Shipment</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-1">
                                        <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i> Search</button>
                                        <a href="{{ route('admin.export.uk') }}" class="btn-refresh reset"><i class="la la-refresh"></i> Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                @php $i=1 @endphp
                @forelse($lists as $row)
                    <section class="card table-card-section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-table-4">
                                    <div class="list-table">
                                        <div class="list">
                                            <p class="s-no-table-1">S No. {{ $i++ }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Date</label>
                                            <p>{{ date('d/m/Y',strtotime($row->created_at)) }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Pallet ID</label>
                                            <p>{{ $row->pallet_id }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Client Name</label>
                                            <p>{{ $row->client->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list">
                                            <label>From Warehouse</label>
                                            @php
                                                // dd($row->meta);
                                                // $row->getMetas();
                                                $fr = $row->getMeta('fr_warehouse_id' , '');
                                                $w_fr = (!empty($fr)) ? getWareHouseName($fr) : 'N/A';
                                            @endphp
                                            <p>{{ $w_fr }}</p>
                                        </div>
                                        <div class="list">
                                            <label>To Warehouse</label>
                                            <p>{{ $row->warehouse->name ?? 'N/A' }}</p>
                                        </div>
                                        {{-- <div class="list">
                                            <label>Shipment Type</label>
                                            <p>{{ $row->shipmentType->name ?? 'N/A'}}</p>
                                        </div> --}}
                                        <div class="list">
                                            <label>Pallet Type</label>
                                            <p>{{ $row->return_type ?? 'N/A'}}</p>
                                        </div>
                                        <div class="list collapse">
                                            <label>Sell Rate</label>
                                            <p>{{ $row->rate ?? 0}}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="list-table">
                                        <div class="list">
                                            <label>Carrier</label>
                                            <p>{{ $row->carrier ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Tracking ID</label>
                                            <p>{{ $row->tracking_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Freight Charges</label>
                                            <p>{{ $row->fright_charges ?? 0 }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Custom Duty</label>
                                            <p>{{ $row->custom_duty ?? 0 }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Taxes</label>
                                            <p>{{ $row->custom_vat ?? 0 }}</p>
                                        </div>
                                        <div class="list">
                                            <label>MAWB #</label>
                                            <p>{{ $row->mawb_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list">
                                            <label>HAWB #</label>
                                            <p>{{ $row->hawb_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list">
                                            <label>Manifest#</label>
                                            <p>{{ $row->manifest_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="list-1 pull-right">
                                            <a class="btn view-btn-ic" href="{{ route('admin.export.uk.show',$row) }}" title="View">
                                                View
                                            </a>
                                        </div>
                                        <div class="list-1 pull-right mb-2">
                                            <a class="btn edit-btn-ic" href="{{ route('admin.export.uk.edit',$row) }}" title="Edit">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection