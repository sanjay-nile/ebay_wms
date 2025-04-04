@include('pages.frontend.client.breadcrumb', ['title' => 'Fetch Custom Duty'])

@push('js')
<script>
$(document).ready(function() {
    $('body').on('click','.single-check',function(){
        if($(this).is(':checked')){
            $(".multiple-check").attr('checked',true);
        }else{
            $(".multiple-check").attr('checked',false);
        }
    });

    $('body').on('click','.get-traking-id',function(){
        let self  = $(this);
        let txt   = self.text();
        let array = [];
        if($(".multiple-check").is(':checked')){
            $('.multiple-check:checkbox:checked').each(function(index,item){
                array.push(item.value);
            });

            $.ajax({
                type : 'post',
                url : "{{ route('client.custom-duty.store') }}",
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data : {'ids':array},
                dataType : 'json',
                beforeSend : function(){
                    self.html(txt+` <i class="fa fa-spinner fa-spin"></i>`).attr('disabled',true);
                },
                success : function(response){
                    if(response.status==201){
                        alert(response.message);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }else{
                        self.html(txt).attr('disabled',false);
                        alert(response.message);
                        setTimeout(function(){
                            window.location.reload();
                        },500);
                    }
                },
                error : function(data){
                    self.html(txt).attr('disabled',false);
                    alert(data.statusText);
                }
            })
        }else{
            alert('Please check at least one way bill number');
        }
    });

    //----------------------------------------------------------------------------------------------//
    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });
});
</script>
@endpush

<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <div class="card">
            <div class="card-header avn-card-header">
                <form class="form-horizontal fiter-form ml-1">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="way_bill_number" class="form-control" placeholder="Way Bill Number" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" />
                            </div>
                        </div>                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-content collapse show">   
                <button class="list-right-btn get-traking-id">Get Custom Duties</button>
                <div class="card-body booking-info-box card-dashboard">
                    <table id="client_user_list" class="table table-striped table-bordered nowrap avn-defaults">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="single-check"></th>
                                <th>Way Bill Number</th>
                                <th>Consignee Name</th> 
                                <th>Client Code</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1 @endphp
                            @forelse($lists as $row)
                                <tr>
                                    <td><input type="checkbox" class="multiple-check" name="ids[]" value="{{ $row->way_bill_number }}"></td>
                                    <td>{{ $row->way_bill_number }}</td>
                                    <td>{{ $row->meta->_consignee_name }}</td>
                                    <td>{{ $row->meta->_client_code }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>