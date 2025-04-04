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
						<img class="" alt="modern admin logo" height="40" src="{{ asset('public/images/mainlogo.png') }}">
					</a>
				</li>
				<li class="nav-item d-md-none">
					<a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile">
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
					<li class="dropdown dropdown-user nav-item">
						<a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
							<span class="mr-1">
                                <span class="user-name text-bold-700">
                                    {{ Auth::user()->name }}
                                </span>
                            </span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" href="{{ route('front.client-user.profile') }}"><i class="ft-user"></i> My Profile</a>
							<a class="dropdown-item" href="{{ route('client-user.change-password') }}"><i class="ft-mail"></i> Change Password</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
								<i class="ft-power"></i> Logout
							</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
            <li class="nav-item @if(Request::is('client-user/dashboard')) active @endif">
                <a href="{!! route('front.client-user.dashboard') !!}" class="nav-link"> Dashboard</a>
            </li>
            <li class="nav-item with-sub">
                <a href="" class="nav-link"><i data-feather="package"></i>Return Orders</a>
                <ul class="navbar-menu-sub">
                    <li class="nav-sub-item @if(Request::is('client-user/reverse-logistic-new')) active @endif">
                        <a href="{{ route('client-user.reverse-logistic-new') }}" class="nav-sub-link">New Return Orders</a>
                    </li>
                    <li class="nav-sub-item @if(Request::is('client-user/intransit-orders')) active @endif">
                        <a href="{{ route('client-user.intransit-orders') }}" class="nav-sub-link">Intransit Orders</a>
                    </li>
                    <li class="nav-sub-item @if(Request::is('client-user/reverse-logistic-list')) active @endif">
                        <a href="{{ route('client-user.reverse-logistic-list') }}" class="nav-sub-link">Received at Hub</a>
                    </li>
                    <li class="nav-sub-item @if(Request::is('client-user/reverse-logistic-create')) active @endif">
                        <a href="{{ route('client-user.reverse-logistic.create') }}" class="nav-sub-link">Create Return Order</a>
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
            <a href="javascript:void(0);" class="dropdown-link" data-toggle="dropdown">
                <div class="avatar avatar-sm">                    
                    {{ Auth::user()->name }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right tx-13">
                <a href="{{ route('front.client-user.profile') }}" class="dropdown-item"><i data-feather="edit-3"></i>My Profile</a>
                <a href="{{ route('client-user.change-password') }}" class="dropdown-item"><i data-feather="user"></i>Change Password</a>
                <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
                	<i data-feather="log-out"></i>Sign Out
                </a>
            </div>
        </div>
    </div>
    <form action="{{ route('logout') }}" id="logout-form" method="POST" style="display: none;">
        @csrf
    </form>
</header> --}}
