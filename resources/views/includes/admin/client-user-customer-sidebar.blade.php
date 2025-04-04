<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      <li class="nav-item"><a href="{{ getDashboardUrl()['dashboard'] }}"><i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span></a>
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
      <li class=" nav-item"><a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="la la-sign-out"></i><span class="menu-title" data-i18n="nav.dash.main">Logout</span></a></li>
    </ul>
  </div>
</div>