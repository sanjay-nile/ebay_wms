@extends('layouts.frontend.night-store')

@section('content')
    @include('pages.frontend.night-store.header')
    <div class="rg-wrapper-content night-store-wrapper">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
        	<div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.night-store.'.$template)
            </div>
        </div>
	</div>
    @include('pages.frontend.night-store.footer')
@endsection
