<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" data-menu="menu-navigation" id="main-menu-navigation">
            <li class="nav-item @if(Request::is('client/dashboard')) active @endif">
                <a href="{{ route('front.client.dashboard') }}">
                    <i class="la la-home">
                    </i>
                    <span class="menu-title" data-i18n="nav.dash.main">
                        Dashboard
                    </span>
                </a>
            </li>

            <li class="nav-item @if(Request::is('client/profile')) active @endif">
                <a href="{{ route('front.client.profile') }}">
                    <i class="la la-user">
                    </i>
                    <span class="menu-title" data-i18n="nav.dash.main">
                        My Profile
                    </span>
                </a>
            </li>

            <li class="nav-item @if(Request::is('client/warehouse') || Request::is('client/*/warehouse')) active @endif">
                <a href="{{ route('client.admin.warehouse') }}">
                    <i class="la la-users" aria-hidden="true"></i> <span class="menu-title" data-i18n=""> Warehouse </span>
                </a>
            </li>

            <li class="nav-item @if(Request::is('client/order-list') || Request::is('client/*/order-details')) active @endif">
                <a href="{{ route('client.order.list') }}">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="">eBay Package List</span>
                </a>
            </li>

            <li class="nav-item @if(Request::is('client/discrepency')) active @endif">
                <a href="{{ route('client.discrepency.list') }}">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="">Discrepency List</span>
                </a>
            </li>

            <li class="nav-item has-sub @if(Request::is('client/*/edit-closed-pallet') || Request::is('client/*/pallet-shipped-edit') || Request::is('client/*/pallet-show') || Request::is('client/pallet-lists') || Request::is('client/pallet-closed-list') || Request::is('client/pallet-shipped-list')) open @endif">
                <a href="#">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="nav.templates.main">View Pallet</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'client.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('client.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">InProcess Pallets</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'client.closedpallet.list') ? 'active' : '' }}">
                        <a href="{{ route('client.closedpallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Closed Pallets</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'client.shipped.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('client.shipped.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Shipped Pallets</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class=" nav-item">
                <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="la la-sign-out">
                    </i>
                    <span class="menu-title" data-i18n="nav.dash.main">
                        Logout
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>