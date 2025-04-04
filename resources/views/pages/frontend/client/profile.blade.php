@include('pages.frontend.client.breadcrumb', ['title' => 'My Profile'])

@push('js')
<script src="{{ asset('plugins/js/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function(){
    //------------------------------------------------------------------------------------------------------
    $(document).on('click','.add-warehouse',function(){
        let client = $(this).data('client');
        $.ajax({
            url : "{{ route('warehouse.create') }}",
            data : {'client':client},
            dataType : 'json',
            success : function(data){
                $('#defaultModal').html(data.html);
                $('#defaultModal').modal({
                    backdrop:'static',
                    keyboard:false,
                    show:true
                });

            }
        })
    });

    //-------------------------------------------------------------------------
    $('body').on('click','.delete-warehouse',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        var url = '{{ route("warehouse.delete", ":id") }}';
        url = url.replace(':id', id);
        if(id && confirm('Are you sure, you want to delete this warehouse')){
            $.ajax({
                type:'delete',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success : function(res){
                    if(res.status==true){
                        $('.add-'+row).remove();
                    }else{
                        alert(res.msg)
                    }
                }
            })
        }

    });
    //------------------------------------------------------------------------------------------------------
    $(document).on('change','.country-list',function(){
        let id = $(this).val();
        $.ajax({
            type:'get',
            url : "{{ route('country.state') }}",
            data:{country_id:id},
            dataType : 'json',
            success : function(data){
                $(".state-list").replaceWith(data.html);
            }
        })
    });
    //------------------------------------------------------------------------------------------------------
    $(document).on('click','.edit-warehouse',function(){
        let id = $(this).data('id');
        let row = $(this).data('row');
        let url = "{{ route('warehouse.show',":id") }}";
        url = url.replace(":id",id);
        if(id){
            $.ajax({
                url : url,
                dataType:'json',
                data:{'row':row},
                success : function(res){
                    if(res.status==true){
                        $('#defaultModal').html(res.html);
                        $('#defaultModal').modal('show');
                    }else{
                        alert(res.msg);
                    }
                }
            })
        }
    });

    //----------------------------------------------------------------------------------------------        
    $('body').on('submit','#edit-client',function(e){
        e.preventDefault();
        let form = $(this); 
        $.ajax({
            type : 'post',
            url : form.attr('action'),
            data : form.serialize(),
             headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType : 'json',
            beforeSend : function(){
                $(".save-client").html(`Submit <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
            },
            success : function(res){
                $('.error-msg').html(`<div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.message}    
                            </div>`);
                $(".save-client").html(`Submit`).attr('disabled',false);
                setTimeout(function(){
                    window.location.href = "{{ route('front.client.profile') }}"
                },2000);
                return false;
            },
            error:function(res){
                $('.error-msg').html(`<div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                ${res.statusText}    
                            </div>`);
                $(".save-client").html(`Submit`).attr('disabled',false);
                return false;
            }
        })
    });
});
</script>
@endpush

<style type="text/css">
    .booking-info-box .info-list-inner{border-radius: 4px; padding: 10px; background-image: linear-gradient(#bbbdbf, #e7e9e9); }
</style>

<div class="row">    
    <div class="col-xs-12 col-md-12">
        @if($user->user_code)
            {{-- <span class="badge badge-success mb-1">UNIQUE CODE ->  <strong>{{ $user->user_code }}</strong></span> --}}
        @endif
        <div class="card Client-card">            
            <div class="card-content">
                <div class="card-body">
                    <div class="card-heading-title"><h5 class="card-title">Client Admin Info</h5></div>
                    
                    <form action="{{ route('client.update.profile') }}" method="post" id="edit-client" class="" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $user->id }}">
                        <div class="tab-content">
                            @include('pages.frontend.client.common.client',array('data'=>$user))

                            <div class="row mt-1">
                                <div class="col-md-10 error-msg"></div>
                                <div class="col-md-2">
                                    <button class="btn btn-info btn-red pull-right save-client" type="submit" style="margin-bottom: 0;">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>