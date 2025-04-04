@extends('layouts.frontend.missguided')

@section('content')
    @include('pages.frontend.missguided.header')
    <div class="main-wrapper">
    	<div class="container">
	            @include('pages.errors-and-messages')
	            @include('pages.frontend.missguided.'.$template)
	        </div>
    	</div>
    </div>
    @include('pages.frontend.missguided.footer')
@endsection
