@extends('layouts.frontend.layout')

@section('content')
    <div class="page-wrapper">
        <div class="container">
            @include('pages.'.$template)
        </div>
    </div>
@endsection
