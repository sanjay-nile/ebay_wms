@extends('layouts.frontend.motel-rocks')

@section('content')
    @include('pages.frontend.motel-rocks.header')
    <div class="rg-wrapper-content">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
        	<div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.motel-rocks.'.$template)
            </div>
        </div>
	</div>
    @include('pages.frontend.motel-rocks.footer')
@endsection
