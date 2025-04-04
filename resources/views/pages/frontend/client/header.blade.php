@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        $('#image').click(function(){
            $('#logo-file').click();
        });

        $('#logo-file').change(function () {
            if ($(this).val() != '') {
                upload(this);

            }
        });
    });    

    function upload(img) {
        var form_data = new FormData();
        form_data.append('file', img.files[0]);
        form_data.append('_token', '{{csrf_token()}}');
        $.ajax({
            url: "{{route('ajax-image-upload')}}",
            data: form_data,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.fail) {
                    alert(data.error);
                }else{
                    location.reload();
                }

                location.reaload                
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>
@endpush

<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto">
                    <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
                        <i class="ft-menu font-large-1"></i>
                    </a>
                </li>
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="{{ getDashboardUrl()['dashboard'] }}">
                        <img alt="modern admin logo" class="" height="40" src="{{ asset('public/images/mainlogo.png') }}"></img>
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-target="#navbar-mobile" data-toggle="collapse">
                        <i class="la la-ellipsis-v"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li>
                        @if(Auth::user()->getMeta('_client_logo'))
                            <img class="img-responsive rounded-circle" id="image" src="{{ asset('public/'.Auth::user()->getMeta('_client_logo')) }}" style="width: 50px;margin-top: 10px;"></img>
                        @else
                            <img class="img-responsive" id="image" src="{{ asset('public/images/no_logo.jpg') }}" style="width: 55px;margin-top: 1px;"></img>
                        @endif

                        <input type="file" id="logo-file" style="display: none"/>
                        <input type="hidden" id="file_name"/>
                    </li>
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" data-toggle="dropdown" href="#">
                            <span class="mr-1">
                                <span class="user-name text-bold-700">
                                    {{ Auth::user()->name }}
                                </span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('front.client.profile') }}">
                                <i class="ft-user"></i>My Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('client.change-password') }}">
                                <i class="ft-mail"></i>Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ft-power"></i>Logout
                            </a>
                        </div>
                    </li>                    
                </ul>
            </div>
        </div>
    </div>
    <form action="{{ route('logout') }}" id="logout-form" method="POST" style="display: none;">
        @csrf
    </form>
</nav>

{{-- <header class="navbar header-nav-content navbar-header-fixed">
    <div class="navbar-brand">
        <a href="{{ getDashboardUrl()['dashboard'] }}" class="df-logo">
            <img class="" height="40" src="{{ asset('public/images/mainlogo.png') }}"></img>
        </a>
    </div>
    <div id="navbarMenu" class="navbar-menu-wrapper">
        <ul class="nav nav-tabs nav-tabs-list header-menu">
            <li class="nav-item @if(Request::is('client/dashboard')) active @endif">
                <a href="{{ route('front.client.dashboard') }}" class="nav-link"> Dashboard</a>
            </li>

            @if(Auth::user()->sub_client_permission=='Y')
                <li class="nav-item with-sub {{ (Request::is('client/edit/*/sub-client') || Request::is('client/create/sub-client')) ? 'active' : '' }}">
                    <a href="" class="nav-link"><i data-feather="package"></i> Sub Client</a>
                    <ul class="navbar-menu-sub">
                        <li class="nav-sub-item {{ (Request::is('client/create/sub-client')) ? 'active' : '' }}">
                            <a href="{{ route('create.sub-client') }}" class="nav-sub-link">Add Sub Client</a>
                        </li>
                        <li class="nav-sub-item {{ (Request::is('client/create/sub-client-list')) ? 'active' : '' }}">
                            <a href="{{ route('create.sub-client-list') }}" class="nav-sub-link">List Sub Client</a>
                        </li>
                    </ul>
                </li>
            @endif

            <li class="nav-item with-sub {{ (Request::is('client/*/client-user-edit')) ? 'active' : '' }}">
                <a href="" class="nav-link"><i data-feather="package"></i>Client User</a>
                <ul class="navbar-menu-sub">
                    <li class="nav-sub-item {{ (Request::is('client/create/client-user-form')) ? 'active' : '' }}">
                        <a href="{{ route('create.client-user') }}" class="nav-sub-link">Add Client User</a>
                    </li>
                    <li class="nav-sub-item {{ (Request::is('client/client-user-list')) ? 'active' : '' }}">
                        <a href="{{ route('client-user-list') }}" class="nav-sub-link">List Client User</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item with-sub">
                <a href="" class="nav-link"><i data-feather="package"></i>Return Orders</a>
                <ul class="navbar-menu-sub">
                    <li class="nav-sub-item @if(Request::is('client/reverse-logistic-new')) active @endif">
                        <a href="{{ route('client.new-eqtor-reverse-logistic') }}" class="nav-sub-link">New Return Orders</a>
                    </li>
                    <li class="nav-sub-item @if(Request::is('client/intransit-orders')) active @endif">
                        <a href="{{ route('client.intransit-orders') }}" class="nav-sub-link">Intransit Orders</a>
                    </li>
                    <li class="nav-sub-item @if(Request::is('client/reverse-logistic-list')) active @endif">
                        <a href="{{ route('client.reverse-logistic-list') }}" class="nav-sub-link">Received at Hub</a>
                    </li>                    
                    <li class="nav-sub-item @if(Request::is('client/reverse-logistic-create')) active @endif">
                        <a href="{{ route('client.reverse-logistic.create') }}" class="nav-sub-link">Create Return Order</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if(Request::is('client/report')) active @endif">
                <a href="{{ route('client.report') }}" class="nav-link"> Report</a>
            </li>
        </ul>
    </div>

    <div class="navbar-right">
        <div class="dropdown profile-info">
            <div class="profile-image">
                @if(Auth::user()->getMeta('_client_logo'))
                    <img class="rounded-circle" id="image" src="{{ asset(Auth::user()->getMeta('_client_logo')) }}"></img>
                @else
                    <img class="rounded-circle" id="image" src="{{ asset('images/no_logo.jpg') }}"></img>
                @endif

                <input type="file" id="logo-file" style="display: none"/>
                <input type="hidden" id="file_name"/>
            </div>
            <a href="javascript:void(0);" class="dropdown-link" data-toggle="dropdown">
                <div class="avatar avatar-sm">                    
                    {{ Auth::user()->name }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right tx-13">
                <a href="{{ route('front.client.profile') }}" class="dropdown-item"><i data-feather="edit-3"></i>My Profile</a>
                <a href="{{ route('client.change-password') }}" class="dropdown-item"><i data-feather="user"></i>Change Password</a>
                <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item"><i data-feather="log-out"></i>Sign Out</a>
            </div>
        </div>
    </div>
    <form action="{{ route('logout') }}" id="logout-form" method="POST" style="display: none;">
        @csrf
    </form>
</header> --}}