<div class="auth-form-card">
    <div class="auth-form-logo">        
        @if($user && $user->hasMeta('_client_logo'))
            <img src="{{ asset('public/'.$user->getMeta('_client_logo')) }}">
        @else
            <img src="{{URL::asset('public/images/ecom-global-colour-logo.png')}}">
        @endif
    </div>
    <div class="register-form-card">
        <div class="register-form-card-body">
            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="client_user" value="{{ $user->slug ?? '' }}">
                <div class="icon-form-group form-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('E-Mail') }}" autofocus="">
                    <span class="form-control-position">
                      <i class="la la-envelope-o"></i>
                    </span>
                </div>                    
                <div class="icon-form-group form-group">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
                    <span class="form-control-position">
                      <i class="la la-key"></i>
                    </span>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="remembercheckbox remember">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">{{ __('Remember Me') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="forgotPassword d-flex justify-content-center">
                                @if (Route::has('password.request'))
                                    <a class="btn-forgot" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>    
                </div>                
                <div class="form-group signIn_form_button">
                    <button type="submit" class="btn btn-signIn">
                        {{ __('Sign-in') }}<span> <i class="fa fa-sign-in"></i></span>
                    </button>
                </div>
                <div class="form-group">
                    @include('pages/errors-and-messages')
                </div>
            </form>
        </div>
    </div>
</div>

