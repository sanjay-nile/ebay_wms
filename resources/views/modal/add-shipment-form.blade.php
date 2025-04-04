  <form id="save-shipment">
  <!-- Modal Header -->
  <div class="modal-header">
    <h4 class="modal-title">Add Shipment4</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <!-- Modal body -->
  <div class="modal-body">
    <input type="hidden" name="id" id="shipping-id" value="">
      <div class="row">
         <div class="col-md-6">
              <div class="form-group">
                  <label for="">Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Shipment Name">
              </div>
         </div>
         <div class="col-md-6">
              <div class="form-group">
                  <label for="">Status</label>
                  <select name="status" id="" class="form-control">
                      <option value="1">Active</option>
                      <option value="2">InActive</option>
                  </select>
              </div>
         </div>
      </div>
  </div>
  <!-- Modal footer -->
  <div class="modal-footer">
    <button type="button" class="btn btn-blue save-shipment">Save</button>
  </div>
</form>
@php print_r($data) @endphp