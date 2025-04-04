@extends('layouts.frontend.customer')

@section('content')
    @include('pages.frontend.customer.header')
    {{-- @include('pages.frontend.customer.sidebar') --}}
    <div class="page-wrapper">
        <div class="container">
            @include('pages.errors-and-messages')
        </div>
		<div>
            @include('pages.frontend.customer.'.$template)
        </div>
    </div>
@endsection
