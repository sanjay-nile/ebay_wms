@push('css')
<link href="{{ asset('admin/css/new-admin-app.css') }}" rel="stylesheet">
<style type="text/css">
	.booking-profile-info {
    width: 40%;
    margin: 0 auto;
}
</style>
@endpush
@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        $("#save-profile-form").find('input').attr('disabled',true);
        $(document).on('click','.edit-button',function(){
            let self = $(this);
            let text = $(this).text();
            if(text.trim()=='Edit'){
                $("#save-profile-form").find('input').attr('disabled',false);
                $('.save-button').html(`<button class='btn btn-red' type="submit">Update</button>`);
                self.text('Cancel');
            }else{
                self.text('Edit');
                $("#save-profile-form").find('input').attr('disabled',true);
                $('.save-button').html('');
            }
        })
    })
</script>
@endpush

<section class="tips-section booking-profile-info">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">                
                <div class="card-content collapse show">
                    <div class="card-body booking-info-box card-dashboard">
                        <div>
                            <h2 style="display: inline-block;">My Profile</h2> <span class="pull-right btn btn-info btn-sm edit-button">Edit </span>
                        </div>
                        
                        <form method="post" action="{!! route('customer.save.detail') !!}" id="save-profile-form">
                            @csrf
                            <input type="hidden" name="id" value="{!! Auth::user()->id !!}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputFristName">First Name</label>
                                        <input type="text" class="form-control" id="exampleInputFristName" value="{{Auth::user()->first_name}}" name="first_name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputLastName">Last Name</label>
                                        <input type="text" class="form-control" id="exampleInputLastName" value="{{Auth::user()->last_name}}" name="last_name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                     <div class="form-group">
                                        <label for="exampleInputEmail">Email Id</label>
                                        <input type="text" class="form-control" id="exampleInputEmail" value="{{Auth::user()->email}}" name="email" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPhone">Phone</label>
                                        <input type="text" class="form-control" id="exampleInputPhone" value="{{Auth::user()->phone}}" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group save-button">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</section>