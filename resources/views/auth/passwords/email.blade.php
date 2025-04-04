@extends('layouts.frontend.layout')
@section('content')
<section class="">
    <div class="view-intro register">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 register-left">
                    <div class="banner-content">
                        <h1 class="h1-responsive-heading">Welcome to Ship Cycle </h1>
                        <hr class="hr-light">
                        <h6 class="h1-responsive-heading_description">Ship Cycle supports global businesses to efficiently manage, track and save money on returned items wherever they operate in the world.</h6>
                        @if(Auth::guest())
                            @if(Request::is('/') || Request::is('login'))                            
                                <a href="{{ URL::to('register/?tab=customer') }}" class="btn btn-sm btn-warning btn-register-rt" id="login-form-link">
                                    <span><i class="fa fa fa-user"></i></span> Register
                                </a>
                            @endif

                            @if(Request::is('register') || Request::is('password/reset/*') || Request::is('password/reset') || Request::is('reset/password/*'))
                                @if(isset($user))
                                    <a href="{{ route('home.page.user', $user) }}" class="btn btn-sm btn-warning btn-login-lf" id="login-form-link">
                                        <span><i class="fa fa fa-user"></i></span> LogIn
                                    </a>
                                @else                                
                                    <a href="{{ URL::to('/') }}" class="btn btn-sm btn-warning btn-login-lf" id="login-form-link">
                                        <span><i class="fa fa fa-user"></i></span> LogIn
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>                    
                </div>
                @if(Auth::guest())
                    <div class="col-md-8 register-right">                        
                        <!-- tab panel -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                                <h3 class="register-heading">{{ __('Reset Password') }}</h3>
                                <div class="row register-form">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <form method="POST" action="{{ route('password.email') }}">
                                            @csrf
                                            <input type="hidden" name="client_user" value="@if(isset($user)){{ trim($user) }} @endif">
                                            <div class="form-group group">
                                                <i class="fa fa-envelope prefix"></i>
                                                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                <span class="bar"></span>
                                                <label>{{ __('E-Mail') }}</label>

                                                @error('email')
                                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="signIn_form_button text-center">
                                                <button type="submit" class="btn btn-signIn">
                                                    {{ __('Submit') }}
                                                </button>
                                            </div>                                            
                                        </form>                                        
                                        @include('pages/errors-and-messages')
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div style="margin-top: 158px;"></div>
    </div>
</section>
@endsection