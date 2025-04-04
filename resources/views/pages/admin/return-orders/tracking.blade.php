@extends('layouts.admin.layout')

@section('sidebar')

    @include(getDashboardUrl()['sidebar'])

@stop

@section('content')

<div class="app-content content">

    <div class="content-wrapper">

        <div class="we-page-title">

            <div class="row">

				<div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">

					<h3 class="content-header-title mb-0 d-inline-block">Get Tracking ID</h3>

					<div class="row breadcrumbs-top d-inline-block">

					  <div class="breadcrumb-wrapper col-12">

						<ol class="breadcrumb">

							<li class="breadcrumb-item"><a href="{{ getDashboardUrl()['dashboard'] }}">Home</a></li>

							<li class="breadcrumb-item active">Get Tracking ID</li>

						</ol>

					  </div>

					</div>

				</div>

            </div>

        </div>

        <div class="row">

			<div class="col-xs-12 col-md-12 ">

				@include('includes/admin/notify')

                <div class="card">

                    <div class="card-header avn-card-header">

                        <form class="form-horizontal fiter-form ml-1">

                            <div class="row">

                                <div class="col-md-3">

                                    <div class="input-group">

                                        <input type="text" name="way_bill_number" class="form-control" placeholder="Way Bill Number" value="{{ app('request')->input('way_bill_number') }}" autocomplete="off" />

                                    </div>

                                    

                                </div>

                                <div class="col-md-3">

                                    <div class="input-group">

                                        <input type="text" name="name" class="form-control" placeholder="Consignee Name" value="{{ app('request')->input('name') }}" autocomplete="off" />

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="input-group">

                                        <input type="text" name="client_code" class="form-control" placeholder="Client Code" value="{{ app('request')->input('client_code') }}" autocomplete="off" />

                                    </div>

                                </div>

                                                                    

                                <div class="col-md-2">

                                    <button type="submit" class="btn btn-cyan" id="search-btn"><i class="la la-search"></i></button>

                                    <a href="{{ route('tracking') }}" class="btn-refresh reset"><i class="la la-refresh"></i></a>

                                </div>

                            </div>

                        </form>

                        <div class="heading-elements">



                            <ul class="list-inline mb-0">

                               <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>

                               <li><a data-action="expand"><i class="ft-maximize"></i></a></li>

                           </ul>

                        </div>

                    </div>



                    <div class="card-content collapse show">   

                        <button class="list-right-btn get-traking-id">Get Tracking ID</button>                 		

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

                        <!-- /.col -->

                    </div>

                </div>

            </div>

        </div>



        <!-- /.content -->

    </div>

    <!-- /.content-wrapper -->

</div>

	

@endsection

@push('css')

<link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatable/css/datatables.min.css') }}">

@endpush

@push('scripts')

<script src="{{ asset('plugins/datatable/js/datatables.min.js') }}"></script>

<script>

$(document).ready(function() {

    var defaults= {

        dom: 'Bfrtip', buttons: [

            {

                extend:'excel', text: '<i class="la la-file-excel-o"></i> Excel', exportOptions: {

                    columns: ':not(:last-child)'

                }

            }

        ],

        "searching": false, "ordering": true, "bPaginate": false, "bInfo": false,

        "columnDefs": [

            { 

                "targets": [0], //first column / numbering column

                "orderable": false, //set not orderable

            },

        ],

    };



    $('.avn-defaults').dataTable($.extend(true, {}, defaults, {}));



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

                url : "{{ route('tracking.store') }}",

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

