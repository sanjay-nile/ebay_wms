@extends('layouts.frontend.new-layout')

@section('content')
    @include('pages.frontend.jaded.header')
    <div class="rg-wrapper-content">
        <div class="rg-wrapper-body tab-pane1" id="tabs">
            <div class="container">
                @include('pages.errors-and-messages')
                @include('pages.frontend.jaded.'.$template)
            </div>
        </div>
    </div>
    @include('pages.frontend.jaded.footer')
@endsection
