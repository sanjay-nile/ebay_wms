<div class="modal-dialog">
    <div class="modal-content">
      <form id="temp-warehouse">
      <div class="modal-header">
        <h4 class="modal-title">Edit Warehouse</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Warehouse</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Warehouse Name" name="warehouse_name" value="{{ $warehouse->name }}"> 
                  <input type="hidden" name="id" value="{{ $warehouse->id }}">
                  <input type="hidden" name="row" value="{{ $row }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Contact Person</label>
                  <input type="text" class="form-control form-control-sm" name="contact_person" placeholder="Contact Person" value="{{ $warehouse->contact_person }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Email</label>
                  <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" value="{{ $warehouse->email }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Phone</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Phone" name="phone" value="{{ $warehouse->phone}}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Address</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Address" name="address" value="{{ $warehouse->address }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Country</label>
                  <select class="form-control form-control-sm country-list" name="country">
                    <option value="">Select</option>
                    @forelse($country_list as $country)
                      <option value="{{ $country->id }}" name="{{ $country->name }}" {{ ($country->id==$warehouse->country_id)?'selected':'' }}>{{ $country->name }}</option>
                    @empty
                    @endforelse
                  </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group state-list">
                  <label for="">State</label>
                  @if(isset($state_list) && $state_list)
                    <select class="form-control" name="state">
                      <option value="">Select</option>
                      @forelse($state_list as $state)
                        <option value="{{ ($state->shortname)??$state->name }}" name="{{ $state->name }}" {{ ($state->name==$warehouse->state || $state->shortname==$warehouse->state_code)?'selected':'' }} >{{ $state->name }}</option>
                      @empty
                      @endforelse
                    </select>
                  @else
                    <input type="text" class="form-control form-control-sm" placeholder="State" name="state">
                  @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">City</label>
                  <input type="text" class="form-control form-control-sm" placeholder="City" name="city" value="{{ $warehouse->city }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Postal Code</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Postal Code" name="postal_code" value="{{ $warehouse->zip_code }}">
                </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <div class="form-error"></div>
        <button type="button" class="btn-red" data-dismiss="modal">Close</button>
        <button type="submit" class="btn-blue" >Save</button>
      </div>
      </form>
    </div>
</div>
<script>
  $("#temp-warehouse").validate({
          rules: {
              warehouse_name: {required: true,maxlength: 100},
              contact_person:{required:true,maxlength:50},
              email:{required:true,email: true,maxlength:50},
              phone:{required:true,maxlength:20},
              address:{required:true,maxlength:100},
              country:{required:true},
              state:{required:true,maxlength:50},
              city:{required:true,maxlength:50},
              postal_code:{required:true,maxlength:15},
          },
          errorElement: "small",
          errorPlacement: function(error, element) {
              error.addClass("text-danger");
              error.insertAfter(element);
          },
          success: function() {
              return false;
          },
          submitHandler: function(form) {
            var lt = 'USD';
            let country_name = $('.country-list option:selected', $(form)).attr('name');
            let state_name = $('.state-list option:selected', $(form)).attr('name');
            let row = $('input[name="row"]',$(form)).val();
            let data = $(form).serializeArray();
            let increment =  $('#warehouse-add tr').length+1;
            let warehouse_name = getValueByKey('warehouse_name',data);
            let contact_person = getValueByKey('contact_person',data);
            let email = getValueByKey('email',data);
            let phone = getValueByKey('phone',data);
            let address = getValueByKey('address',data);
            let country = getValueByKey('country',data);
            let state_code = getValueByKey('state',data);
            let city = getValueByKey('city',data);
            let postal_code = getValueByKey('postal_code',data);
            let full_address = address+", "+city+", "+state_name+", "+postal_code+", "+country_name;

            $.ajax({
                type: 'post',
                url : "{{ route('warehouse.store') }}",
                data : $(form).serialize(),
                dataType : 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend : function(){
                  $(form).find('.btn-blue').html('Save <i class="fa fa-spinner fa-spin"></i>').attr('disabled',true);
                },
                success : function(res){
                  if(res.status==true){
                    $('.add-'+row+' td').eq(0).text(warehouse_name);
                    $('.add-'+row+' td').eq(1).text(contact_person);
                    $('.add-'+row+' td').eq(2).text(email);
                    $('.add-'+row+' td').eq(3).text(phone);
                    $('.add-'+row+' td').eq(4).text(full_address);
                    $('.form-error').html(`<div class="alert alert-success alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          ${res.msg}    
                      </div>`);
                    setTimeout(function(){
                      $('#defaultModal').modal('hide');
                    },1000)
                    
                    return false;
                  }else{
                     $('.form-error').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.msg}    
                            </div>`);
                     $(form).find('.btn-blue').html('Save').attr('disabled',false);
                     return false;
                  }
                }
              });
            return false;
          }
      });

  function getValueByKey(key,array){
    let ke_value = '';
    array.forEach(function(val,index,arr){
      if(val.name==key) ke_value = val.value;
    });
    return ke_value;
  }
</script>