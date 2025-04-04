<div class="info-list-section">
    <div class="info-list-inner">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">First Name</label>
                <input type="text" class="form-control" name="first_name" placeholder="Enter name" value="{{ ($data)?$data->name : old('first_name') }}">
                @if($errors->has('first_name'))
                <span class="text-danger">{{ $errors->first('first_name') }}</span>
                @endif
            </div>
        </div>                                                
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Last Name</label>
                <input type="text" class="form-control" name="last_name" placeholder="Contact Person Name" value="{{ ($data)?$data->last_name : old('last_name') }}">
                @if($errors->has('last_name'))
                <span class="text-danger">{{ $errors->first('last_name') }}</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Email Id</label>
                 @if($data)
                     <input type="text" class="form-control" readonly="" value="{{ ($data)?$data->email:'' }}">
                @else
                <input type="text" class="form-control" name="email" placeholder="Email Id" value="{{ ($data)?$data->email:'' }}">
                @endif

                @if($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
            </div>
        </div>                                                
        <div class="col-md-6">
             <div class="form-group">
                 <label for="">Phone No.</label>
                 <input type="text" class="form-control" name="phone" placeholder="Enter phone" value="{{ ($data)? $data->phone : old('phone') }}">
                 @if($errors->has('phone'))
                 <span class="text-danger">{{ $errors->first('phone') }}</span>
                 @endif
             </div>
         </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Address</label>
                <input type="text" class="form-control" name="address" placeholder="Enter address" value="{{ ($data)? $data->address : old('address') }}">
                @if($errors->has('address'))
                <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="">Status</label>
                @php $status = ($data)?$data->status : old('status');  @endphp
                <select name="status" class="form-control">
                    <option value="">Select</option>
                    <option value="1" @if($status==1) {{ 'selected' }} @endif>Active</option>
                    <option value="2" @if($status==2) {{ 'selected' }} @endif>Inactive</option>
                </select>
                @if($errors->has('status'))
                <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
            </div>
        </div>
        
        @if(empty($data))
            <div class="col-md-12">
                <div class="upload-btn-wrapper">
                  <button class="btn-upload"><i class="la la-upload mr-1"></i>Upload Contract</button>
                  <input type="file" name="myfile" id="profile_image">
                </div>
            </div>
        @endif
    </div>
    </div>
</div>