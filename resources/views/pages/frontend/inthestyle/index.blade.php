@extends('layouts.frontend.inthestyle')

@section('content')
    @include('pages.frontend.inthestyle.header')
    <div class="rg-wrapper-content">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
        	<div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.inthestyle.'.$template)
            </div>
        </div>
	</div>
    @include('pages.frontend.inthestyle.footer')
@endsection
