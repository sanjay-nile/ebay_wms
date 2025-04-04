<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      
      <li class="nav-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>
      </li>                 
      <li class="nav-item @if(Request::is('admin/client-profile')) active @endif">
        <a href="{{ route('client.profile') }}">
          <i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">My Profile</span>
        </a>
      </li>
      <li class="nav-item has-sub"><a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Client User</span></a>
            <ul class="menu-content">
                <li class="nav-item {{ (\Request::route()->getName() == 'client-user.create') ? 'active' : '' }}">
                  <a href="{{ route('client-user.create') }}"><i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">Add Client User</span></a>
                </li>
                <li class="nav-item {{ (\Request::route()->getName() == 'client-user') ? 'active' : '' }}">
                    <a href="{{ route('client-user') }}"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.dash.main">View Client User</span></a>
                </li>
            </ul>
      </li>
      
      <li class="nav-item has-sub"><a href="#"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.templates.main">Reverse Logistic</span></a>
            <ul class="menu-content">
                <li class="nav-item {{ (\Request::route()->getName() == 'reverse-logistic.create') ? 'active' : '' }}">
                  <a href="{{ route('reverse-logistic.create') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Create Reverse Logistic</span></a>
                </li>
                <li class="nav-item {{ (\Request::route()->getName() == 'reverse-logistic') ? 'active' : '' }}">
                    <a href="{{ route('reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">View Reverse Logistic</span></a>
                </li>
                                
                 <li class="nav-item {{ (\Request::route()->getName() == 'tracking') ? 'active' : '' }}">
                    <a href="{{ route('tracking') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">Get Tracking ID</span></a>
                </li>
            </ul>
      </li>
      <li class="nav-item has-sub"><a href="#"><i class="la la-users"></i><span class="menu-title" data-i18n="nav.templates.main">Cust Reverse Logistic</span></a>
            <ul class="menu-content">
                <li class="nav-item {{ (\Request::route()->getName() == 'new-reverse-logistic') ? 'active' : '' }}">
                    <a href="{{ route('new-reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">New Reverse Logistic</span></a>
                </li>
                <li class="nav-item {{ (\Request::route()->getName() == 'customer-reverse-logistic') ? 'active' : '' }}">
                    <a href="{{ route('customer-reverse-logistic') }}"><i class="la la-truck"></i><span class="menu-title" data-i18n="nav.dash.main">View Reverse Logistic</span></a>
                </li>
            </ul>
      </li>
      <li class="nav-item has-sub">
        <a href="#"><i class="la la-database"></i><span class="menu-title" data-i18n="nav.templates.main">Master Data</span></a>
        <ul class="menu-content">
            <li class="nav-item {{ (\Request::route()->getName() == 'admin.shipping-type') ? 'active' : '' }}">
              <a href="{{ route('admin.shipping-type') }}"><i class="la la-eye"></i><span class="menu-title" data-i18n="nav.dash.main">Shipment Type</span></a>
            </li>
            
            <li class="nav-item {{ (\Request::route()->getName() == 'admin.carrier') ? 'active' : '' }}">
                <a href="{{ route('admin.carrier') }}"><i class="la la-eye"></i><span class="menu-title" data-i18n="nav.dash.main">Carrier</span></a>
            </li>

            <li class="nav-item {{ (\Request::route()->getName() == 'admin.other-charges') ? 'active' : '' }}">
                <a href="{{ route('admin.other-charges') }}">
                  <i class="la la-eye"></i><span class="menu-title" data-i18n="nav.dash.main">Other Charges</span>
                </a>
            </li>
        </ul>
      </li>
      <li class="nav-item {{ (\Request::route()->getName() == 'admin.report') ? 'active' : '' }}">
        <a href="{{ route('admin.report') }}">
          <i class="la la-bar-chart-o"></i><span class="menu-title" data-i18n="nav.dash.main">Report</span>
        </a>
      </li>
      <li class=" nav-item"><a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="la la-sign-out"></i><span class="menu-title" data-i18n="nav.dash.main">Logout</span></a></li>
    </ul>
  </div>
</div>