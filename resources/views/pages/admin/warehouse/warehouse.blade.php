@extends('layouts.admin.layout')

@section('sidebar')
    @include(getDashboardUrl()['sidebar'])
@stop

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
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
    });
</script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('public/plugins/css/select2.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('public/plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.assigncountry').select2({
              placeholder: '-- Select --',
              allowClear: true
            });
        })
    </script>
@endpush

@section('content')

<div class="app-content content"> 
    <div class="content-wrapper">
        @include('pages-message.notify-msg-error')
        @include('pages-message.notify-msg-success')
        {{-- @include('pages-message.form-submit') --}}
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="box-title">Warehouses</h5>
                        <!-- <a class="btn btn-blue" href="{{ route('admin.warehouse') }}">
                            <i class="fa fa-hand-o-left" aria-hidden="true"></i> {!! trans('admin.back_button') !!}
                        </a> -->
                    </div>
                    <div class="card-body">
                        @if(isset($single_wh))
                            @include('pages.admin.warehouse.edit')
                        @else
                            {{-- @if($list->count() <= 0) --}}
                                @include('pages.admin.warehouse.add')
                            {{-- @endif --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="box-title">Warehouse List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive booking-info-box">
                            <table class="table table-striped table-bordered nowrap avn-defaults table-sm dataTable">
                                <thead>
                                    <tr>
                                        <th>Warehouse ID</th>
                                        <th>Warehouse Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Country Assign</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $wh)
                                    <tr>
                                            <td class="ws">{{ $wh->id }}</td>
                                            <td class="ws">{{ $wh->name }}</td>
                                            <td class="ws">{{ $wh->contact_person }}</td>
                                            <td class="ws">{{ $wh->email }}</td>
                                            <td class="ws">{{ $wh->phone }}</td>
                                            <td class="ws">
                                                {!! $wh->address !!}, {!! $wh->city !!},{!!  $wh->state ?? '' !!},{!! $wh->zip_code !!}, {!! $wh->country->name ?? '' !!}
                                            </td>
                                            <td class="ws">{{ $wh->assigned_country }}</td>
                                            <td class="ws">
                                                <a class="btn btn-view" href="{{ route('admin.warehouse', $wh->id)}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                   @empty
                                       <tr>
                                            <td colspan="12">
                                                <i class="fa fa-exclamation-triangle"></i> There are no data
                                            </td>
                                        </tr>
                                   @endforelse
                                </tbody>
                            </table>            
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="products-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/select2override.css') }}">
@endpush
@push('js')
    <script src="{{ asset('plugins/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.assigncountry').select2({
              placeholder: 'Select Assign Country',
              allowClear: true
            });
        })
    </script>
@endpush