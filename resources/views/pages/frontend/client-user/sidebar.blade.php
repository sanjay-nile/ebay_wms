<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
  	<div class="main-menu-content">
    	<ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      		<li class="nav-item @if(Request::is('client-user/dashboard')) active @endif">
		        <a href="{{ route('front.client-user.dashboard') }}">
		          	<i class="la la-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span>
		        </a>
      		</li>      
	      	<li class="nav-item @if(Request::is('client-user/profile')) active @endif">
		        <a href="{{ route('front.client-user.profile') }}">
		          	<i class="la la-user"></i><span class="menu-title" data-i18n="nav.dash.main">My Profile</span>
		        </a>
	      	</li>
	      	@php
		        $prmission = json_decode(Auth::user()->user_permissions);
		         // dd($prmission);
	      	@endphp
	      	
	      	<li class="nav-item @if(Request::is('client-user/create-return-order')) active @endif">
            	<a href="{{ route('client-user.create-return-order') }}" class="nav-sub-link">
              		<i class="la la-truck"></i> Create New Return
            	</a>
          	</li>

            <li class="nav-item @if(Request::is('client-user/return-orders')) active @endif">
                <a class="nav-sub-link" href="{{ route('client-user.return.orders') }}">
                    <i class="la la-truck"></i> All Returns
                </a>
            </li>
          	
          	<!-- <li class="nav-item @if(Request::is('client-user/intransit-orders')) active @endif">
	            <a href="{{ route('client-user.intransit-orders') }}" class="nav-sub-link">
	              	<i class="la la-truck"></i> Processed Returns
	            </a>
          	</li>

          	<li class="nav-item @if(Request::is('client-user/new-orders-list')) active @endif">
              	<a href="{{ route('client-user.new-return-order') }}" class="nav-sub-link">
                	<i class="la la-truck"></i> UnProcessed Returns
              	</a>
          	</li>

          	<li class="nav-item @if(Request::is('client-user/inscan-return-order')) active @endif">
              	<a href="{{ route('client-user.inscan.return.order') }}" class="nav-sub-link">
                	<i class="la la-truck"></i> InScan Returns
              	</a>
          	</li>

          	<li class="nav-item @if(Request::is('client-user/cancel-return-order')) active @endif">
              	<a href="{{ route('client-user.cancel.return.order') }}" class="nav-sub-link">
                	<i class="la la-truck"></i> Cancelled Returns
              	</a>
          	</li>

          	<li class="nav-item @if(Request::is('client-user/receive-at-hub')) active @endif">
              	<a href="{{ route('client-user.receive-at-hub') }}" class="nav-sub-link">
                	<i class="la la-truck"></i> Received at Hub
              	</a>
          	</li> -->
          	

	      	@if(Auth::user()->client_type=='1')
		      	<li class="nav-item @if(Request::is('client-user/rma/rma-code-list') || Request::is('client-user/rma-code/*/rma-code-list')) active @endif">
			        <a href="{{ route('client-user.code.list') }}">
			          	<i class="la la-code"></i><span class="menu-title" data-i18n="nav.dash.mains">RMA Codes</span>
			        </a>
		      	</li>
      		@endif
	      	<li class=" nav-item">
		        <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
		          	<i class="la la-sign-out"></i><span class="menu-title" data-i18n="nav.dash.main">Logout</span>
		        </a>
	      	</li>
    	</ul>
  	</div>
</div>