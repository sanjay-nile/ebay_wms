<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item @if(Request::is('admin/customer-rep-dashboard')) active @endif">
                <a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>
            </li>                 
      
            <li class="nav-item has-sub">
                <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Client</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'client') ? 'active' : '' }}">
                        <a href="{{ route('client') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Client</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if(Request::is('admin/all-return-orders')) active @endif">
                <a href="{{ route('all.return.orders') }}">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="">All Returns</span>
                </a>
            </li>

            <li class="nav-item @if(Request::is('admin/actual-statistics')) active @endif">
                <a href="{{ route('actual.statistics') }}">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="">Actual Statistics</span>
                </a>
            </li>

            {{-- <li class="nav-item {{ (\Request::route()->getName() == 'process.list') ? 'active' : '' }} @if(Request::is('admin/process/*/show')) active @endif">
                <a href="{{ route('process.list') }}">
                    <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Process Orders</span>
                </a>
            </li> --}}

            <li class="nav-item {{ (\Request::route()->getName() == 'process.list') ? 'active' : '' }} @if(Request::is('admin/process/*/show')) active @endif">
                <a href="{{ route('process.list') }}">
                    <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Warehouse Inventory</span>
                </a>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/pallet/closed-edit/*') || Request::is('admin/pallet/shipped-edit/*') ||Request::is('admin/pallet/edit/*') || Request::is('admin/pallet/show/*')) open @endif">
                <a href="#">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="nav.templates.main">View Pallet</span>
                </a>
                <ul class="menu-content">
                    {{-- <li class="nav-item {{ (\Request::route()->getName() == 'admin.pallet.add') ? 'active' : '' }}">
                        <a href="{{ route('admin.pallet.add') }}">
                          <i class="la la-plus"></i><span class="menu-title" data-i18n="nav.dash.main">Add Pallet</span>
                        </a>
                    </li> --}}
                    
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('admin.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">InProcess Pallets</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.closed.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('admin.closed.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Closed Pallets</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.shipped.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('admin.shipped.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Shipped Pallets</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/manifest/export-uk/edit/*') || Request::is('admin/manifest/customer-broker/edit/*') || Request::is('admin/manifest/import-uk/edit/*') || Request::is('admin/manifest/vat-return/edit/*') || Request::is('admin/manifest/export-europe/edit/*') || Request::is('admin/manifest/export-uk/show/*') || Request::is('admin/manifest/customs-broker/show/*') || Request::is('admin/manifest/import-uk/show/*') || Request::is('admin/manifest/vat-return/show/*') || Request::is('admin/manifest/export-europe/show/*')) open @endif">
                <a href="#">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="nav.templates.main">Generate Manifests</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.export.uk') ? 'active' : '' }}">
                        <a href="{{ route('admin.export.uk') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Export Out of Uk Manifest</span>
                        </a>
                    </li>

                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.import.uk') ? 'active' : '' }}">
                        <a href="{{ route('admin.import.uk') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Import Back to Uk Manifest</span>
                        </a>
                    </li>

                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.export.europe') ? 'active' : '' }}">
                        <a href="{{ route('admin.export.europe') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Export Out of Europe</span>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.cust.broker') ? 'active' : '' }}">
                        <a href="{{ route('admin.cust.broker') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Customs Broker -LCA</span>
                        </a>
                    </li>

                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.vat.return') ? 'active' : '' }}">
                        <a href="{{ route('admin.vat.return') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">Vat Return</span>
                        </a>
                    </li>                    
                </ul>
            </li>

            <li class="nav-item has-sub @if(Request::is('admin/restock-orders') || Request::is('admin/resell-orders') || Request::is('admin/return-orders') || Request::is('admin/redirect-orders') || Request::is('admin/recycle-orders')) open @endif">
                <a href="#">
                    <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.templates.main">Discrepancies Orders</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item @if(Request::is('admin/restock-orders')) active @endif">
                      <a href="{{ route('restock.orders') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Restock Orders</span></a>
                    </li>
                    <li class="nav-item @if(Request::is('admin/resell-orders')) active @endif">
                        <a href="{{ route('resell.orders') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Resell Orders</span></a>
                    </li>
                    <li class="nav-item @if(Request::is('admin/return-orders')) active @endif">
                        <a href="{{ route('return.orders') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Return Orders</span></a>
                    </li>
                    <li class="nav-item @if(Request::is('admin/redirect-orders')) active @endif">
                        <a href="{{ route('redirect.orders') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Redirect Orders</span>
                        </a>
                    </li>
                    <li class="nav-item @if(Request::is('admin/recycle-orders')) active @endif">
                        <a href="{{ route('recycle.orders') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Recycle Orders</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'other.orders') ? 'active' : '' }}">
                        <a href="{{ route('other.orders') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Other Orders</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'discrepency.list') ? 'active' : '' }}">
                        <a href="{{ route('discrepency.list') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Discrepency Orders</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'short.shipment.orders') ? 'active' : '' }}">
                        <a href="{{ route('short.shipment.orders') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Short Shipment Orders</span>
                        </a>
                    </li>
                </ul>
            </li>
          
            {{-- <li class="nav-item has-sub">
                <a href="#"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.templates.main">Return Orders</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'reverse-logistic.create') ? 'active' : '' }}">
                      <a href="{{ route('reverse-logistic.create') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Create Return Order</span></a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'reverse-logistic.new') ? 'active' : '' }}">
                        <a href="{{ route('reverse-logistic.new') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">New Return Orders</span></a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'reverse-logistic') ? 'active' : '' }}">
                        <a href="{{ route('reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">View Return Orders</span></a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'process.list') ? 'active' : '' }}">
                        <a href="{{ route('process.list') }}">
                            <i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Process Orders</span>
                        </a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'tracking') ? 'active' : '' }}">
                        <a href="{{ route('tracking') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Get Tracking ID</span></a>
                    </li>
                </ul>
            </li> --}}

            {{-- <li class="nav-item has-sub @if(Request::is('admin/pallet/edit/*') || Request::is('admin/pallet/show/*')) open @endif">
                <a href="#">
                    <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="nav.templates.main">Pallet Data</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.pallet.add') ? 'active' : '' }}">
                        <a href="{{ route('admin.pallet.add') }}">
                          <i class="la la-plus"></i><span class="menu-title" data-i18n="nav.dash.main">Add Pallet</span>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ (\Request::route()->getName() == 'admin.pallet.list') ? 'active' : '' }}">
                        <a href="{{ route('admin.pallet.list') }}">
                            <i class="la la-list"></i><span class="menu-title" data-i18n="nav.dash.main">View Pallet</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            
            {{-- <li class="nav-item has-sub">
                <a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Cust Reverse Logistic</span></a>
                <ul class="menu-content">
                    <li class="nav-item {{ (\Request::route()->getName() == 'new-reverse-logistic') ? 'active' : '' }}">
                        <a href="{{ route('new-reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">New Reverse Logistic</span></a>
                    </li>
                    <li class="nav-item {{ (\Request::route()->getName() == 'customer-reverse-logistic') ? 'active' : '' }}">
                        <a href="{{ route('customer-reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">View Reverse Logistic</span></a>
                    </li>
                </ul>
            </li> --}}
            <li class=" nav-item">
                <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="la la-sign-out"></i><span class="menu-title" data-i18n="nav.dash.main">Logout</span></a>
            </li>
        </ul>
    </div>
</div>