@extends('layouts.frontend.new-layout')

@section('content')
    @include('pages.frontend.demo.header')
    <div class="rg-wrapper-content">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
        	<div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.demo.'.$template)
            </div>
        </div>
	</div>
    @include('pages.frontend.demo.footer')
@endsection
