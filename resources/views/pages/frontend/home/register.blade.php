<!-- <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link @if(Request::get('tab') != 'partner') active @endif" id="customer-tab" data-toggle="tab" href="#customer" role="tab" aria-controls="customer" aria-selected="true">Customer</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(Request::get('tab') == 'partner') active @endif" id="partner-tab" data-toggle="tab" href="#partner" role="tab" aria-controls="partner" aria-selected="false">Partner</a>
    </li>
</ul> -->
<!-- tab panel -->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
        <div class="auth-form-card">
            <h3 class="register-heading">Register as a Customer</h3>
            <div class="register-form">
                <form method="POST" action="{{ route('register') }}">
                    <div class="row register-form">            
                        @csrf
                        <input type="hidden" name="user_type_id" value="5">
                        <input type="hidden" name="client_user" value="@if(isset($user)) {{ $user }} @endif">
                        <div class="col-md-6">
                            <div class="form-group group">
                                <label>First name</label>
                                <input id="first_name"  type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>
                                <span class="bar"></span>
                                @error('first_name')
                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group group">
                                <label>Last name</label>
                                <input id="last_name" type="text" class="form-control  @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name">
                                <span class="bar"></span>
                                @error('last_name')
                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group group">
                                <label>Email</label>
                                <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                <span class="bar"></span>
                                @error('email')
                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group group">
                                <label>Phone</label>
                                <input id="phone" type="text" class="form-control  @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                                <span class="bar"></span>
                                @error('phone')
                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group group">
                                <label>Password</label>
                                <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                <span class="bar"></span>
                                

                                @error('password')
                                    <span class="error text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group group">
                                <label>Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control " name="password_confirmation" required autocomplete="new-password">
                                <span class="bar"></span>
                                
                            </div>                
                        </div>
                        <div class="clear"></div>
                        <div class="col-md-12">
                            <div class="signIn_form_button text-center">
                                <button type="submit" class="btn btn-signIn">
                                    {{ __('Register') }} <span> <i class="fa fa-sign-in"></i></span>
                                </button>
                            </div>
                            <div class="forgotPassword d-flex justify-content-center links">
                                Have an account? <a href="{{ URL::to('login') }}"> Login</a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @include('pages/errors-and-messages')
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>