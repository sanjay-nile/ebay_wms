@push('css')
<link href="{{ asset('admin/css/new-admin-app.css') }}" rel="stylesheet">
@endpush

@push('js')
<script>
    $(document).ready(function(){
        $('#country-select').on('change', function() {
            let cd = $(this).find(":selected").attr('data-code');
            $('#phonecode').val('+'+cd);
        });        
    });
</script>
@endpush

<section class="detail-section return-request-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12">                        
                <div class="tips-card return-form-item">
                    <div class="card-content">
                        <div class="card-header info-Detail-section">
                            <h4>Addresses <small>(You can add multiple addresses)</small></h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('customer.save.address') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                @if(!empty($single))
                                    <input type="hidden" name="id" value="{{ $single->id }}">
                                @endif
                                <div class="info-list-inner">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Type <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control">
                                                    @if(isset($single->type))
                                                        <option value="Office" @if($single->type == 'Office') selecte @endif>Office</option>
                                                        <option value="Home" @if($single->type == 'Home') selecte @endif>Home</option>
                                                    @else
                                                        <option value="Office">Office</option>
                                                        <option value="Home">Home</option>
                                                    @endif
                                                </select>
                                                @error('type')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Name<span class="text-danger">*</span></label>
                                                @if(isset($single->name))
                                                    <input type="text" class="form-control" name="name" value="{!! $single->name !!}">
                                                @else
                                                    <input type="text" class="form-control" name="name" value="">
                                                @endif
                                                @error('name')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>                                
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street Address 1<span class="text-danger">*</span></label>
                                                @if(isset($single->address_1))
                                                    <input type="text" class="form-control" name="address_1" value="{!! $single->address_1 !!}">
                                                @else
                                                    <input type="text" class="form-control" name="address_1" value="">
                                                @endif
                                                @error('address_1')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street Address 2</label>
                                                @if(isset($single->address_2))
                                                    <input type="text" class="form-control" name="address_2" value="{!! $single->address_2 !!}">
                                                @else
                                                    <input type="text" class="form-control" name="address_2" value="">
                                                @endif
                                                @error('address_2')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Country <span class="text-danger">*</span></label>
                                                <select name="country" id="country-select" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    @forelse($country_list as $country)
                                                        @if(isset($single->country_id))
                                                            <option value="{{ $country->id }}" @if($single->country_id == $country->id) selected @endif data-code="{{ $country->phonecode }}">
                                                                {{ $country->name }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $country->id }}" data-code="{{ $country->phonecode }}">{{ $country->name }}</option>
                                                        @endif
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @error('country')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">State <span class="text-danger">*</span></label>
                                                @if(isset($single->state))
                                                    <input type="text" class="form-control" name="state" value="{!! $single->state !!}">
                                                @else
                                                    <input type="text" class="form-control" name="state" placeholder="Enter State" value="">
                                                @endif
                                                @error('state')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">City <span class="text-danger">*</span></label>
                                                @if(isset($single->city))
                                                    <input type="text" class="form-control" name="city" value="{!! $single->city !!}">
                                                @else
                                                    <input type="text" class="form-control" name="city" placeholder="Enter City" value="">
                                                @endif
                                                @error('city')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Zip code <span class="text-danger">*</span></label>
                                                @if(isset($single->zip))
                                                    <input type="text" class="form-control" name="zip" value="{!! $single->zip !!}">
                                                @else
                                                    <input type="text" class="form-control" name="zip" placeholder="Enter Pincode" value="">
                                                @endif
                                                @error('zip')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-md-1">
                                                    <label for="">Code<span class="text-danger">*</span></label>
                                                    @if(isset($single->country_id))
                                                        <input type="tel" class="form-control" id="phonecode" name= "phonecode" placeholder="e.g. +1" value="+{{ $single->country->phonecode}}">
                                                    @else
                                                        <input type="tel" class="form-control" id="phonecode" name= "phonecode" placeholder="e.g. +1">
                                                    @endif
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Phone<span class="text-danger">*</span></label>
                                                    @if(isset($single->phone))
                                                        <input type="text" class="form-control" name="phone" value="{!! $single->phone !!}">
                                                    @else
                                                        <input type="text" class="form-control" name="phone" value="">
                                                    @endif
                                                    @error('phone')
                                                        <span class="error text-danger" role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn-red btn-sm btnSubmitMargin save-waybill">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tips-card return-form-item">
                    <div class="card-content">
                        <div class="card-header info-Detail-section">
                            <h4>Your Addresses</h4>
                        </div>
                        <div class="card-body">                         
                            <div class="row">
                                @forelse($address as $addr)
                                    <div class="col-md-4">
                                        <div class="address-info-card address-info-card-bg ">
                                            <div class="address-info-card-content">
                                                <div class="address-info-text">
                                                    <span>Type:</span>{!! $addr->type !!}
                                                </div>
                                                <div class="address-info-text">
                                                    <span>Name:</span>{!! $addr->name !!}
                                                </div>
                                                 <div class="address-info-text">
                                                    <span>Address:</span> {!! $addr->address_1 !!} {!! $addr->address_2 !!}, {!! $addr->city !!} {!! $addr->state !!} , {!! get_country_name_by_id($addr->country_id) !!}, {!! $addr->zip !!}
                                                </div>
                                                <div class="address-info-text">
                                                    <span>Phone:</span>+{{ $addr->country->phonecode}}{!! $addr->phone !!}
                                                </div>
                                            </div>
                                            <div class="address-info-btn">
                                                 <a href="{!! route('customer.edi.address', $addr->id) !!}" class="btn-edit">
                                                <i class="fa fa-pencil"></i> Edit
                                                </a>
                                                <a href="{!! route('customer.delete.address', $addr->id) !!}" class="btn-delete">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>                                   
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>