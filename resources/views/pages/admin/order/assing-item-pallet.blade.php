@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        toastr.options ={
           "closeButton" : true,
           "progressBar" : true,
           "disableTimeOut" : true,
        }

        let explt =`<div class="col-md-4">
                <label>Pallet Id</label>
                <div class="form-group" id="exist_pallet_list">
                    <input type="text" name="pallet_name" value="" class="form-control">
                </div>
            </div>

            <div class="col-md-3">
                <input type="hidden" name="form_type" value="close">
                <button type="button" id="add-to-old-pallet" class="btn add-to-pallet mt-2">Submit</button>
            </div>`;


        let crplt =`<div class="col-md-3">
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

        <div class="col-md-3">
            <input type="hidden" name="form_type" value="add">
            <button type="button" id="add-to-pallet" class="btn add-to-pallet mt-2 mb-2">Submit</button>
        </div>`;

        $("#create-pallet").click(function () {
            $("#ex-pallet").html(crplt);
        });

        $(document).on('click','#existing-pallet',function(){
            $("#ex-pallet").html(explt);
        });

        $(document).on('click',"#add-to-pallet", function(){            
            $("#pallet-save").submit();            
        });

        $(document).on('click',"#add-to-old-pallet", function(){           
            $("#pallet-save").submit();
        });
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
						<li class="breadcrumb-item active">Assign Item  Ref. To Pallet</li>
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
                    <div class="card-body">
                        <section class="list-your-service-section">
                            <div class="list-your-service-content">
                                <div class="list-your-service-form-box">
                                    <div class="row">
                                        <div class="col-md-12"><h5 class="card-title">Assign Item To Pallet</h5></div>
                                    </div>
                                   <form method="post" action="{{ route('admin.assign.pallet.item') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Item Ref. Number</label>
                                                    <input type="text" class="form-control" name="package_id" placeholder="Enter Item Ref Number" value="{{ old('package_id') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group" id="exist_pallet_list">
                                                    <label>Existing Pallet</label>
                                                    <input type="text" class="form-control" name="pallet_name" placeholder="Enter Pallet Id" value="{{ old('pallet_name') }}">
                                                    {{-- <select name="pallet_name" id="" class="form-control">
                                                        @forelse($pallets as $pallet)
                                                            <option value="{{ $pallet->pallet_id }}" @if($pallet->pallet_type == 'InProcess') style="color:green" @else style="color:red" @endif>{{ $pallet->pallet_id }}</option>
                                                        @empty
                                                            <option value="">No Existing pallet exists</option>
                                                        @endforelse
                                                    </select> --}}
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
                </div>
            </div>

            <div class="col-xs-12 col-md-12 ">
                <div class="card booking-info-box">            
                    <div class="card-header">
                        <div class="pallet-div">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" id="create-pallet" class="btn btn-blue">Create New Pallet</button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="existing-pallet" class="btn btn-blue">Close Pallet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="rg-pack-table">
                            <div class="booking-info-box">
                                <form action="{{ route('admin.create.pallet') }}" method="post" id="pallet-save">
                                    @csrf
                                    <div class="row ml-1" id="ex-pallet"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div>

@endsection