@include('pages.frontend.client.breadcrumb', ['title' => 'Add Warehouse'])

@push('js')
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


        
{{-- <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="box-title">Warehouses</h5>
            </div>
            <div class="card-body">
                @if(isset($single_wh))
                    @include('pages.frontend.client.warehouse.edit')
                @else
                    @include('pages.frontend.client.warehouse.add')
                @endif
            </div>
        </div>
    </div>
</div> --}}

<div class="row">
    <div class="col-12">
        <div class="card Warehouses-card">
            <div class="card-header">
                <h5 class="box-title">Warehouse List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive booking-info-box">
                    <table class="table table-striped table-bordered table-hover admin-data-table admin-data-list table-sm avn-defaults">
                        <thead>
                            <tr>
                                <th>Warehouse Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <!-- <th>Phone</th>
                                <th>Address</th>
                                <th>Country Assign</th>
                                <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($list as $wh)
                            <tr>
                                    <td class="ws">{{ $wh->name }}</td>
                                    <td class="ws">{{ $wh->contact_person }}</td>
                                    <td class="ws">{{ $wh->email }}</td>
                                    <!-- <td class="ws">{{ $wh->phone }}</td>
                                    <td class="ws">
                                        {!! $wh->address !!}, {!! $wh->city !!},{!!  $wh->getstates->name ?? '' !!},{!! $wh->zip_code !!}, {!! $wh->country->name ?? '' !!}
                                    </td>
                                    <td class="ws">{{ $wh->assigned_country }}</td>
                                    <td class="ws">
                                        <a class="btn btn-view" href="{{ route('admin.warehouse', $wh->id)}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td> -->
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