<div class="header-flag-info @if(!empty($owner) && $owner->slug == 'ims') virginia-header @endif">
    <div class="container">
        <div class="row">
            <!-- <div class="col-6">
                <div class="header-logo-1">
                    <img src="{{  asset('public/images/Lonsdale_logo.jpg') }}">
                </div>
            </div> -->
            <div class="col-6">
                <div class="header-logo-1">
                    {{-- <img src="{{  asset('public/images/stanley-black-decker.png') }}"> --}}
                    @if(!empty($cs) && $cs->hasMeta('_client_logo'))
                        <img src="{{ asset('public/'.$cs->getMeta('_client_logo')) }}">
                    @else
                        <h2>{{ $cs->name ?? '' }}</h2>
                    @endif
                </div>
            </div>
            <div class="col-6 justify-content-end d-flex">
                @php
                    // dd($owner);
                @endphp
                @if(!empty($owner) && $owner->hasMeta('_client_logo'))
                    <img src="{{ asset('public/'.$owner->getMeta('_client_logo')) }}" height="36px;">
                @else
                    {{-- <img src="{{URL::asset('public/images/ecom-global-colour-logo.png')}}" height="36px;"> --}}
                    <img src="{{  asset('public/images/Lonsdale-Logo.png') }}" height="36px;">
                @endif
            </div>
        </div>
    </div>
</div>
<header class="header mt-3">
    <div class="container">
        <div class="header-navigation">
            <nav class="navbar navbar-expand-lg">  
                <!-- <div class="navbar-brand mobile-logo">                 
                    <img src="{{  asset('public/images/Huboo_Logo.png') }}" height="40">
                </div> -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>        
                
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <!-- <li class="nav-item"><a href="">I have a quote number</a> </li>
                        <li class="nav-item"><a href="">Create Return</a> </li> -->
                        <li class="nav-item"><a href="">Track My Package</a> </li>
                        <li class="nav-item"><a href="">Help</a></li>
                        <li class="nav-item pr-0"><a target="_blank" href="https://www.ecomglobalsystems.com/contact/">Contact</a></li>
                    </ul>
                </div>
            </nav>  
        </div>
    </div>
</header>