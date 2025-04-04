@if (Session::has('error'))
  	<div class="alert alert-danger alert-dismissible">
  		<button type="button" class="close" data-dismiss="alert">&times;</button>
    	{{ Session::get('error') }}
 	 </div>
@elseif(Session::has('success'))
 	<div class="alert alert-success alert-dismissible">
	  	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    {{ Session::get('success') }}
  	</div>
@elseif(Session::has('warning'))
	<div class="alert alert-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{{ Session::get('warning') }}
	</div>
@elseif(Session::has('info'))
	<div class="alert alert-info alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{{ Session::get('info') }}
	</div>
@endif