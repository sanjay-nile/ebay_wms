<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item @if(Request::is('admin/admin-dashboard') || Request::is('admin/customer-rep-dashboard')) active @endif">
                <a href="{{ getDashboardUrl()['dashboard'] }}">
                    <i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span>
                </a>
            </li>

            <li class="nav-item has-sub">
                <a href="#"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Metric Dashboard</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.metric.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.metric.dashboard') }}"><i class="la la-bars"></i><span class="menu-title" data-i18n="nav.dash.main">Live Summary Data</span></a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.graph.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.graph.dashboard') }}"><i class="la la-bars"></i><span class="menu-title" data-i18n="nav.dash.main">Live Detailed Data</span></a>
                    </li>
                </ul>
            </li>

            {{-- super admin --}}
            @if(Auth::user()->user_type_id == 1)
                <li class="nav-item has-sub">
                    <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main"> Admin Rep</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.sub-admin.create') ? 'active' : '' }}">
                          <a href="{{ route('admin.sub-admin.create') }}"><i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">Add Admin Rep</span></a>
                        </li>
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.sub-admin') ? 'active' : '' }}">
                            <a href="{{ route('admin.sub-admin') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Admin Rep</span></a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-sub">
                    <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main"> Operator Rep</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.operator.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.operator.create') }}"><i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">Add Operator Rep</span></a>
                        </li>
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.operator') ? 'active' : '' }}">
                            <a href="{{ route('admin.operator') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Operator Rep</span></a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-sub">
                    <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Client</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.client.create') ? 'active' : '' }}">
                          <a href="{{ route('admin.client.create') }}"><i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">Add Client Admin</span></a>
                        </li>
                        <li class="nav-item {{ (\Request::route()->getName() == 'admin.client') ? 'active' : '' }} @if(Request::is('admin/client/*/edit')) active @endif">
                            <a href="{{ route('admin.client') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Client Admin</span></a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- sub admin --}}
            @if(Auth::user()->user_type_id == 2)
                <li class="nav-item has-sub">
                    <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Client</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ (\Request::route()->getName() == 'client') ? 'active' : '' }}">
                            <a href="{{ route('admin.client') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Client</span></a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(in_array(Auth::user()->user_type_id, [1,2]))
                <li class="nav-item @if(Request::is('admin/warehouse') || Request::is('admin/warehouse/*')) active @endif">
                    <a href="{{ route('admin.warehouse') }}"><i class="la la-building" aria-hidden="true"></i> <span class="menu-title" data-i18n=""> Warehouse </span></a>
                </li>

                <li class="nav-item {{ (\Request::route()->getName() == 'admin.carrier') ? 'active' : '' }}">
                    <a href="{{ route('admin.carrier') }}"><i class="la la-envelope"></i><span class="menu-title" data-i18n="nav.dash.main">Carriers</span></a>
                </li>

                <li class="nav-item {{ (\Request::route()->getName() == 'admin.add.rack') ? 'active' : '' }}">
                    <a href="{{ route('admin.add.rack') }}"><i class="la la-plus"></i><span class="menu-title" data-i18n="nav.dash.main">Add Rack</span></a>
                </li>
            @endif

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.rack.list') ? 'active' : '' }}">
                <a href="{{ route('admin.rack.list') }}"><i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Rack List</span></a>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.all.scan.data') ? 'active' : '' }}">
                <a href="{{ route('admin.all.scan.data') }}"><i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">All Scan Data List</span></a>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.add.scan.in') ? 'active' : '' }}">
                <a href="{{ route('admin.add.scan.in') }}"><i class="la la-qrcode"></i><span class="menu-title" data-i18n="nav.dash.main">Scan In List</span></a>
            </li>

            <li class="nav-item has-sub">
                <a href="#"><i class="la la-database"></i><span class="menu-title" data-i18n="nav.templates.main">Scan Out Data</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.add.scan.out') ? 'active' : '' }}">
                        <a href="{{ route('admin.add.scan.out') }}"><i class="la la-qrcode"></i><span class="menu-title" data-i18n="nav.dash.main">Single Scan Out</span></a>
                    </li>

                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.combined.scan.out') ? 'active' : '' }}">
                        <a href="{{ route('admin.combined.scan.out') }}"><i class="la la-qrcode"></i><span class="menu-title" data-i18n="nav.dash.main">Combined Scan Out</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/ebay/new-orders/details/*')) open @endif">
                <a href="#"><i class="la la-database"></i><span class="menu-title" data-i18n="nav.templates.main">Dispatch Data</span></a>
                <ul class="menu-content">
                    <li class="nav-item @if(Request::is('admin/dispatch/new') || Request::is('admin/ebay/new-orders/details/*')) active @endif">
                        <a href="{{ route('admin.dispatch.list', 'new') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Ready For Dispatch</span></a>
                    </li>
                    <li class="nav-item @if(Request::is('admin/dispatch/view')) active @endif">
                        <a href="{{ route('admin.dispatch.list', 'view') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Processed Packages</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.cancelled.list') ? 'active' : '' }}">
                <a href="{{ route('admin.cancelled.list') }}"><i class="la la-times"></i><span class="menu-title" data-i18n="nav.dash.main">Cancelled Packages</span></a>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.move.location.list') ? 'active' : '' }}">
                <a href="{{ route('admin.move.location.list') }}"><i class="la la-exchange"></i><span class="menu-title" data-i18n="nav.dash.main">Move To Location</span></a>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.move.to.dispatch') ? 'active' : '' }}">
                <a href="{{ route('admin.move.to.dispatch') }}"><i class="la la-exchange"></i><span class="menu-title" data-i18n="nav.dash.main">Move To Dispatch</span></a>
            </li>

            @if(Auth::user()->user_type_id == 1)
                <li class="nav-item {{ (\Request::route()->getName() == 'admin.change.package.data') ? 'active' : '' }}">
                    <a href="{{ route('admin.change.package.data') }}"><i class="la la-bars"></i><span class="menu-title" data-i18n="nav.dash.main">Change Package Data</span></a>
                </li>
            @endif

            {{-- <li class="nav-item has-sub">
                <a href="#"><i class="la la-database"></i><span class="menu-title" data-i18n="nav.templates.main">Master Data</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.carrier') ? 'active' : '' }}">
                        <a href="{{ route('admin.carrier') }}"><i class="la la-eye"></i><span class="menu-title" data-i18n="nav.dash.main">Carrier</span></a>
                    </li>
                </ul>
            </li> --}}

            <li class=" nav-item">
                <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="la la-sign-out"></i><span class="menu-title" data-i18n="nav.dash.main">Logout</span></a>
            </li>
        </ul>
    </div>
</div>