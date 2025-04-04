@extends('layouts.frontend.client')

@section('content')
    @include('pages.frontend.client-user.header')
    @include('pages.frontend.client-user.sidebar')
    <div class="app-content content">
        <div class="content-wrapper">
            @include('pages.errors-and-messages')
            @include('pages.frontend.client-user.'.$template)
        </div>
    </div>
    @include('pages.frontend.client-user.footer')

   {{--  @include('pages.frontend.client-user.header')
    <div class="wrapper-body">
        <div class="container">
            @include('pages.errors-and-messages')

            @include('pages.frontend.client-user.'.$template)
        </div>
    </div>
    @include('pages.frontend.client-user.footer') --}}
    
@endsection
