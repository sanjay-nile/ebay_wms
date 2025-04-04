@extends('layouts.admin.login')

@section('content')
<div class="app-content container center-layout mt-2">
    <div class="content-wrapper">
        <div class="content-header row"> </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        @include('includes/admin/notify')
                        <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <img src="{{ asset('public/images/mainlogo.png') }}" alt="branding logo" style="height: auto;width: 200px;">
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="{{ route('admin.login.submit') }}" method="post">
                                        {{ csrf_field() }}
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="email" class="form-control" id="user-name" name="email" value="{{ old('email') }}"  autofocus placeholder="Your Email"  >
                                            <div class="form-control-position">
                                              <i class="la la-envelope-o"></i>
                                            </div>
                                            @if($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" class="form-control" id="user-password" name="password" value="" placeholder="Enter Password" >
                                            <div class="form-control-position">
                                              <i class="la la-key"></i>
                                            </div>
                                             @if($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                            @endif
                                        </fieldset>
                                        <div class="form-group row">
                                            <div class="col-md-6 col-12 text-center text-sm-left">
                                              <fieldset>
                                                <input type="checkbox" id="remember" class="chk-remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember-me"> Remember Me</label>
                                              </fieldset>
                                            </div>
                                            <div class="col-md-6 col-12 float-sm-left text-center text-sm-right"><a href="{{ route('admin.password.request') }}" class="card-link">Forgot Password?</a></div>
                                        </div>
                                        <button type="submit" class="btn btn-cyan-info btn-block"><i class="ft-unlock"></i> Login</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
  </div>
@endsection
