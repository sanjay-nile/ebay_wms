<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

     /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $credentials =$this->credentials($request);
        $user = \App\User::where('email', $request->only('email'))->first();
        if (!$user) {
            return redirect()->back()->with('error', 'You are not registered with ReverseGear');
        }

        $this->resetPassword($user, $request->password);

        $get_view_data['subject']    =   'Password Changed';
        $get_view_data['view']       =   'mails.resetPasswordMail';
        $get_view_data['user']          =   $user;
        $get_view_data['client_user']   =   $request->client_user;        

        try{
            $mail = Mail::to($user->email)->send(new MainTemplate( $get_view_data ));

            if(!empty($request->client_user)){
                return redirect(route('home.page.user', $request->client_user))->with('success', 'Your password has been successfully changed. You can now login.');
            }

            return redirect(url('/'))->with('success', 'Your password has been successfully changed. You can now login.');
        } catch(\Swift_TransportException $transportExp){
            if(!empty($request->client_user)){
                return redirect(route('home.page.user', $request->client_user))->with('success', 'Your password has been successfully changed. You can now login.');
            }

            return redirect(url('/'))->with('success', 'Your password has been successfully changed. You can now login.');
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        // $this->guard()->login($user);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
