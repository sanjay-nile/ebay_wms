@include('pages.frontend.client.breadcrumb', ['title' => 'RMA Code List'])

<div class="row">
    <div class="col-xs-12 col-md-12 ">
        <div class="card">
            <div class="card-content collapse show">
                <div class="card-body booking-info-box card-dashboard table-responsive">
                    <form class="form-horizontal" method="post" action="{{ route('client.code.store') }}">
                        @csrf
                        <div class="row">
                            @if(isset($code))
                                <input type="hidden" name="code_id" value="{{ $code->id }}">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="code" class="form-control" placeholder="RMA Code" value="{{ $code->code }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="order_id" class="form-control" placeholder="Order Id" value="{{ $code->order_id }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="email_id" class="form-control" placeholder="Order Email Id" value="{{ $code->email_id }}" autocomplete="off" />
                                    </div>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="code" class="form-control" placeholder="RMA Code" value="" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="order_id" class="form-control" placeholder="Order Id" value="" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="text" name="email_id" class="form-control" placeholder="Order Email Id" value="" autocomplete="off" />
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-cyan"><i class="la la-upload"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <table id="client_user_list" class="table table-striped table-hover nowrap table-sm mt-3">
                        <thead>
                            <tr>
                                <th>RMA Code</th>
                                <th>Order ID</th>
                                <th>Email ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lists as $list)
                                <tr>
                                    <td>{{ $list->code }}</td>
                                    <td>{{ $list->order_id }}</td>
                                    <td>{{ $list->email_id }}</td>
                                    <td>
                                        <a href="{{ route('client.code.list.edit', $list->id) }}" class="btn btn-sm btn-primary"><i class="la la-edit"></i></a>
                                        <a href="{{ route('client.code.delete', $list->id) }}" class="btn btn-sm btn-danger"><i class="la la-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    {{-- <div class="col-md-12">{{ $lists->appends(Request::except('page'))->onEachSide(2)->links() }}</div> --}}
                </div>
            </div>
        </div>
    </div>
</div>