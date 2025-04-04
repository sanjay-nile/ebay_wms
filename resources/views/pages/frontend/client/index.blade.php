@extends('layouts.frontend.client')

@section('content')
    @include('pages.frontend.client.header')
    @include('pages.frontend.client.sidebar')

    <div class="app-content content">
        <div class="content-wrapper">
            @include('pages.errors-and-messages')

            @include('pages.frontend.client.'.$template)
        </div>
    </div>

    {{-- <div class="wrapper-body">
        <div class="container">
            @include('pages.errors-and-messages')

            @include('pages.frontend.client.'.$template)
        </div>
    </div> --}}

    @include('pages.frontend.client.footer')
@endsection
