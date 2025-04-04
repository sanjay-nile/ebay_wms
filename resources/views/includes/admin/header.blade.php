<!-- fixed-top-->
  <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
      <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
          <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
          <li class="nav-item mr-auto">
            <a class="navbar-brand" href="{{ getDashboardUrl()['dashboard'] }}">
              <img class="" alt="modern admin logo" height="40" src="{{ asset('public/images/mainlogo.png') }}">
              
            </a>
          </li>
          <li class="nav-item d-md-none">
            <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a>
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
                  <span class="user-name text-bold-700">{{ Auth::user()->name }}</span>
                </span>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
              @if(Auth::user()->user_type_id==3)
                <a class="dropdown-item" href="{{ route('admin.client.profile') }}"><i class="ft-user"></i> My Profile</a>
              @else
                 <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ft-user"></i> My Profile</a>
              @endif

                <a class="dropdown-item" href="{{ route('admin.change-password') }}"><i class="ft-mail"></i> Change Password</a>

                <div class="dropdown-divider"></div><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ft-power"></i> Logout</a>
              </div>
            </li>                    
          </ul>
        </div>
      </div>
    </div>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
  </nav>