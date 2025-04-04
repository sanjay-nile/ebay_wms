<div class="modal-dialog">
    <div class="modal-content">
      <form id="temp-warehouse">
      <div class="modal-header">
        <h4 class="modal-title">Add Warehouse</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="client_id" value="{{ $client_id }}">
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Warehouse</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Warehouse Name" name="warehouse_name">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Contact Person</label>
                  <input type="text" class="form-control form-control-sm" name="contact_person" placeholder="Contact Person">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Email</label>
                  <input type="email" class="form-control form-control-sm" name="email" placeholder="Email">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Phone</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Phone" name="phone">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Address</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Address" name="address">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Country</label>
                  <select class="form-control form-control-sm country-list" name="country">
                    <option value="">Select</option>
                    @forelse($country_list as $country)
                      <option value="{{ $country->id }}" name="{{ $country->name }}">{{ $country->name }}</option>
                    @empty
                    @endforelse
                  </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group state-list">
                  <label for="">State</label>
                  <input type="text" class="form-control form-control-sm" placeholder="State" name="state">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">City</label>
                  <input type="text" class="form-control form-control-sm" placeholder="City" name="city">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Postal Code</label>
                  <input type="text" class="form-control form-control-sm" placeholder="Postal Code" name="postal_code">
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
            let client_id = $('input[name="client_id"]', $(form)).val()
            let country_name = $('.country-list option:selected', $(form)).attr('name');
            let state_name = $('.state-list option:selected', $(form)).attr('name');
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
            if(client_id!='' && client_id!=0){
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

                    show_table_row(increment,warehouse_name,contact_person,email,phone,address,country,state_name,state_code,city,postal_code,full_address,res.id); 

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
              })
            }else{
              show_table_row(increment,warehouse_name,contact_person,email,phone,address,country,state_name,state_code,city,postal_code,full_address,0);
              $('#defaultModal').modal('hide');
              return false;
            }
          }
      });

  function getValueByKey(key,array){
    let ke_value = '';
    array.forEach(function(val,index,arr){
      if(val.name==key) ke_value = val.value;
    });
    return ke_value;
  }

  function show_table_row(increment,warehouse_name,contact_person,email,phone,address,country,state_name,state_code,city,postal_code,full_address,warehouse_id){
      let warehouse = `<tr class="add-${increment}">
        ${(warehouse_id==0)?
          `<input type="hidden"  name="warehouses_arr[]" value="${warehouse_name}">
          <input type="hidden"  name="contact_person_arr[]" value="${contact_person}">
          <input type="hidden"  name="email_arr[]" value="${email}">
          <input type="hidden"  name="phone_arr[]" value="${phone}">
          <input type="hidden"  name="address_arr[]" value="${address}">
          <input type='hidden'  name="country_arr[]" value="${country}">                    
          <input type="hidden"  name="state_arr[]" value="${state_name}">
          <input type="hidden"  name="state_code_arr[]" value="${state_code}">
          <input type="hidden"  name="city_arr[]" value="${city}">
          <input type="hidden"  name="postal_code_arr[]" value="${postal_code}">`:''
        }
        <td>${warehouse_name}</td>
        <td>${contact_person}</td>
        <td>${email}</td>
        <td>${phone}</td>
        <td>${full_address}</td>
        <td>
            ${(warehouse_id!=0)?
              `<button type="button" class="btn btn-view edit-warehouse" data-id="${warehouse_id}" data-row="${increment}"><i class="la la-edit"></i></button>`:''
            }
            <button type="button" class="btn btn-delete btn-danger delete-warehouse" data-row="${increment}" data-id="${warehouse_id}"><i class="la la-trash"></i></button>
        </td>
    </tr>`;
    $('#warehouse-add').append(warehouse);
  }
</script>