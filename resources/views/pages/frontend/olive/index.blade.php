@extends('layouts.frontend.olive')

@section('content')
    @include('pages.frontend.olive.header')
    <div class="page-wrapper">
        <div class="container">
            @include('pages.errors-and-messages')
        </div>
		<div>
            @include('pages.frontend.olive.'.$template)
        </div>
    </div>
@endsection
