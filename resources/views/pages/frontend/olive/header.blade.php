<header class="header">
    <div class="container">
    <nav class="navbar  top-navbar navbar-expand-lg static-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="javascript:void(0)">
                <img src="{{ asset('images/logo.png') }}">
            </a>
            <a class="navbar-brand ml-1" href="javascript:void(0)">
                <img src="{{ asset('images/Olive-logo.jpg') }}" height="60">
            </a>
        </div>           
        <div class="navbar-collapse">
            <ul class="navbar-nav border-btm">
                <li class="nav-item">
                    <a href="{{ route('olive.home') }}">
                        <i class="icon-archive"></i>
                        <span>Request A Return</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('olive.order-list') }}">
                        <i class="icon-archive"></i>
                        <span>My Orders</span>
                    </a>
                </li>                
            </ul>
        </div>
    </nav>
</div>
</header>