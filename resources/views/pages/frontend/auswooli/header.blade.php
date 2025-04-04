<header class="header">
	<div class="container">
		<div class="header-navigation">
			<nav class="navbar navbar-expand-lg">
				<div class="navbar-brand mobile-logo">
					<img src="{{  asset('public/images/jaded_logo.jpg') }}" height="40">
				</div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				
				<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="{{ route('auswooli.home') }}">Create a Return</a> </li>
						<li class="nav-item"><a href="{{ route('auswooli.tracking') }}">Track My Return</a> </li>
						<li class="nav-item"><a href="{{ route('auswooli.help') }}">Help</a></li>
					</ul>
				</div>
			</nav>  
		</div>
	</div>
</header>