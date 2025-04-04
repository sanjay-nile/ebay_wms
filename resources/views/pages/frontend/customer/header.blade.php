<header class="header">
    <div class="container">
    <nav class="navbar  top-navbar navbar-expand-lg static-top">
        <div class="navbar-header">
            {{-- <a class="navbar-brand" href="{{ route('customer.dashboard') }}"> --}}
            <a class="navbar-brand" href="javascript:void(0)">
                <img src="{{ asset('images/logo.png') }}">
            </a>
            <a class="navbar-brand ml-1" href="javascript:void(0)">
                <img src="{{ asset('images/Olive-logo.jpg') }}" height="60">
            </a>
        </div>
           
        <div class="navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <!-- <form class="search-input">
                        <div class="input-group">
                            <i class="icon-search" aria-hidden="true"></i>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                    </form> -->
                </li>
            </ul>
          
            <ul class="navbar-nav border-btm">
                <li class="nav-item">
                    <a href="{{ route('customer.return-request') }}">
                        <i class="icon-archive"></i>
                        <span>Request A Return</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a id="myorder" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-dashboard"></i>
                        <span>My Orders</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('customer.dashboard.new') }}">
                            {{ __('New Orders') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                            {{ __('Previous Orders') }}
                        </a>
                    </div>
                </li>                
                <li class="nav-item">
                    <a href="{{ route('customer.my-profile') }}">
                        <i class="icon-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
               
                <li class="nav-item dropdown">                   
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <img src="{{ asset('images/profile.png') }}" alt="user" class="profile-pic">
                        <b>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</b> <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('customer.address') }}">
                            {{ __('Manage Addresses') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('customer.change-password') }}">
                            {{ __('Change Password') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
</header>