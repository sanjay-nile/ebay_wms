@extends('layouts.frontend.missguided')

@section('content')
    @include('pages.frontend.shopify.header')
    <div class="main-wrapper">
    	<div class="container">
	            @include('pages.errors-and-messages')
	            @include('pages.frontend.shopify.'.$template)
	        </div>
    	</div>
    </div>
    @include('pages.frontend.shopify.footer')
@endsection
