@extends('layouts.frontend.layout')

@section('content')

<section class="auth-section">
    <div class="container">
        @if(Auth::guest())
            <div class="row d-flex align-items-center justify-content-center">
                <div class="col-md-4">
                    @include('pages/frontend/home/'.$template)
                </div>
            </div>
        @else
            <a class="btn btn-primary" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif

        <div class="auth-copyright-text">@2023 Ecom Global Network all rights reserved. powered by Eq8tor.</div>
    </div>
</section>

@endsection