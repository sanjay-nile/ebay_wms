<aside class="left-sidebar">
    <nav id="sidebar" class="sidebar-wrapper">
        <div class="sidebar-content">
            <!-- sidebar-search  -->
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="{{ route('customer.dashboard') }}">
                            <i class="icon-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.return-request') }}">
                            <i class="icon-archive"></i>
                            <span>Request A Return</span>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="{{ route('customer.track-order') }}">
                            <i class="icon-admin"></i>
                            <span>Track Order</span>
                        </a>
                    </li> -->

                    <!-- <li>
                        <a href="{{ route('customer.my-order') }}">
                            <i class="icon-prospects"></i>
                            <span>My Order</span>
                        </a>
                    </li> -->

                    <li>
                        <a href="{{ route('customer.my-profile') }}">
                            <i class="icon-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            <i class="icon-logout"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</aside>
