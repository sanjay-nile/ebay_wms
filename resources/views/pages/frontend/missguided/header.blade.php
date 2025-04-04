<header class="header">
	<div class="container">
		<div class="header-navigation">
			<nav class="navbar navbar-expand-lg">  
				<div class="navbar-brand logo">
					{{-- <a href="{{ route('missguided.home') }}" class="custom-logo-link"> --}}
					{{-- <a href="javascript:void(0)" class="custom-logo-link"> --}}
						<img src="{{  asset('public/images/miss-logo.svg') }}" height="40">
					{{-- </a> --}}
				</div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>        
				
				<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="{{ route('missguided.home') }}">Create a Return</a> </li>
						<li class="nav-item"><a href="{{ route('missguided.tracking') }}">Track My Return</a> </li>
						<li class="nav-item"><a href="{{ route('missguided.help') }}">Help</a></li>
						<!-- <li class="nav-item"><a href="https://www.missguided.eu/help#help-returns-container">Help</a></li> -->
						{{-- <li class="nav-item"> <a href="#">English</a> </li> --}}
					</ul>					
				</div>
			</nav>  
		</div>
	</div>
</header>