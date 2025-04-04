
@extends('layouts.admin.login')

@section('content')

<div class="app-content container center-layout mt-2">
    <div class="content-wrapper">
        <div class="content-header row"> </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <img src="{{ asset('images/mainlogo.png') }}" alt="branding logo" style="height: auto;width: 200px;">
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                    
                                    @include('pages/errors-and-messages')

                                    <h5 class="card-title">Admin Reset Password</h5>
                                    <form class="form-horizontal" method="POST" action="{{ route('admin.password.email') }}">
                                        {{ csrf_field() }}
                                        <label for="email" class="control-label">E-Mail Address</label>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="email" class="form-control" id="user-name" name="email" value="{{ old('email') }}"  autofocus placeholder="Your Email" >
                                            <div class="form-control-position">
                                              <i class="la la-envelope-o"></i>
                                            </div>
                                            @if($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </fieldset>
                                       
                                        <div class="form-group row">
                                            <div class="col-md-6 col-12 text-center text-sm-left">
                                              <fieldset>
                                               
                                              </fieldset>
                                            </div>
                                            <div class="col-md-6 col-12 float-sm-left text-center text-sm-right"><a href="{{ route('admin.login') }}" class="card-link">Back To Login</a></div>
                                        </div>
                                        <button type="submit" class="btn btn-cyan-info btn-block"><i class="ft-unlock"></i> Send Password Reset Link</button>
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

