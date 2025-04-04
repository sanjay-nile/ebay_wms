@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('css')
<style type="text/css">
    .nav.nav-tabs.nav-underline {
        border-bottom: 1px solid #ffdfe4 !important;
        margin-bottom: 26px !important;
        background: #fff1f3;
    }
    .align-col .row .col-md-2, .col-md-1{padding: 0px 5px;}
</style>
@endpush

@section('content')

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="we-page-title">
                <div class="row">
                    <div class="col-md-8 align-self-left">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-dashboard"></i> Home</a></li>
                            <li class="breadcrumb-item active">Pallet Layout</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.content-wrapper -->

        <div class="content-wrapper">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    @include('pages/errors-and-messages')
                    <div class="card booking-info-box">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="info-list-section">
                                    <form method="post" action="{{ route('admin.pallet.store') }}" autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Pallet ID </label>
                                                <div class="form-group">
                                                    <input type="text" name="pallet_name" value="{{ generateUniquePalletNames() }}" class="form-control" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Condition <span class="required-field"></span></label>
                                                <div class="form-group">
                                                    <select name="condition" id="" class="form-control" required>
                                                        <option value="">-- Select --</option>
                                                        @foreach(conditionCode() as $code)
                                                            <option value="{{ $code }}">{{ $code }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Original Sales Incoterm <span class="required-field"></span></label>
                                                <div class="form-group">
                                                    <select name="sales_incoterm" id="" class="form-control" required>
                                                        <option value="">-- Select --</option>
                                                        <option value="DDU">EXPORTS DDU</option>
                                                        <option value="DDP">EXPORTS DDP</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Pallet Type <span class="required-field"></span></label>
                                                <div class="form-group">
                                                    <select name="pallet_type" id="" class="form-control" required>
                                                        <option value="">-- Select --</option>
                                                        <option value="Closed">Closed</option>
                                                        <option value="Shipped">Shipped</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Reselling Grade</label>
                                                <div class="form-group">
                                                    <select name="reselling_grade" id="" class="form-control" required>
                                                        <option value="">-- Select --</option>
                                                        @foreach(getResellingGrade() as $code)
                                                            <option value="{{ $code }}">{{ $code }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>From Warehouse <span class="required-field"></span></label>
                                                <div class="form-group">
                                                    <select name="fr_warehouse_id" id="to_client_warehouse_list" class="form-control">
                                                        <option value="">-- Select --</option>
                                                            @forelse($warehouse_list as $warehouse)
                                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                            @empty
                                                                <option value="">Warehouse not added yet</option>
                                                            @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>To Warehouse </label>
                                                <div class="form-group">
                                                    <select name="warehouse_id" id="client_warehouse_list" class="form-control">
                                                        <option value="">-- Select --</option>
                                                            @forelse($warehouse_list as $warehouse)
                                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                            @empty
                                                                <option value="">Warehouse not added yet</option>
                                                            @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>SC Main Category <span class="required-field"></span></label>
                                                <div class="form-group">
                                                    <select name="main_category" class="form-control">
                                                        <option value="">--- Select Category ---</option>
                                                        @forelse($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Category Tier 1</label>
                                                <div class="form-group">
                                                    <select name="category_tier_1" class="form-control">
                                                        <option value="">-- Select --</option>
                                                        @forelse($sub_categories as $cat)
                                                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Category Tier 2</label>
                                                <div class="form-group">
                                                    <select name="category_tier_2" class="form-control">
                                                       <option value="">-- Select --</option>
                                                        @forelse($sub_categories_2_tier as $cat)
                                                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Category Tier 3</label>
                                                <div class="form-group">
                                                    <select name="category_tier_3" class="form-control">
                                                        <option value="">-- Select --</option>
                                                        @forelse($sub_categories_3_tier as $cat)
                                                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Preferred Listing Price</label>
                                                <input type="text" class="form-control" name="pl_price" value="">
                                                @error('pl_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Preferred Listing Price %</label>
                                                <input type="text" class="form-control" name="ppl_price" value="">
                                                @error('ppl_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Authorised by</label>
                                                <input type="text" class="form-control" name="authorised_by" value="">
                                                @error('authorised_by')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Actual Sold Price</label>
                                                <input type="text" class="form-control" name="as_price" value="">
                                                @error('as_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Actual Sold Price %</label>
                                                <input type="text" class="form-control" name="asp_price" value="">
                                                @error('asp_price')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="">Date Auctioned</label>
                                                <input type="text" class="form-control" name="date_a" value="">
                                                @error('date_a')
                                                    <small class="error">The field is required</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Pallet File</label>
                                                    <input type="file" name="cat_file" class="form-control" value="" required>
                                                </div>

                                                <a href="{{ asset('public/uploads/Closed_Shipped_Pallets.csv') }}" download><u> Download Sample </u></a>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-red">Submit</button>
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
        </div>
    </div>

@endsection