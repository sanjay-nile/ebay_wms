@extends('layouts.frontend.auswooli-new-layout')

@section('content')
    @include('pages.frontend.auswooli.header')
    <div class="rg-wrapper-content">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
        	<div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.auswooli.'.$template)
            </div>
        </div>
	</div>
    @include('pages.frontend.auswooli.footer')
@endsection
