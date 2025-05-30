<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

use App\Mail\MailResetPasswordNotification;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm(){
        return view('auth/passwords/email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request){
        $this->validate($request, ['email' => 'required|email']);

        $user = \App\User::where('email', $request->only('email'))->first();
        if (!$user) {
            return redirect()->back()->with('error', 'You are not registered with ReverseGear');
        }

        $token = Password::broker()->createToken($user); //create reset password token
        
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $response = $this->broker()->sendResetLink(
        //     $request->only('email')
        // );

        try {
            $send = Mail::to($user->email)->send(new MailResetPasswordNotification($user, $token, $request->client_user));

            return redirect()->back()->with('success', 'We have sent an activation link to your registered email Id.');
        } catch (\Swift_TransportException $e) {
            // dd($e->getMessage());
            return redirect()->back()->with('success', 'We have not sent an activation link to your registered email Id.');
        }
        

        // if ($response === Password::RESET_LINK_SENT) {
        //     return back()->with('success', trans($response));
        // }

        // If an error was returned by the password broker, we will get this message
        // translated so we can notify a user of the problem. We'll redirect back
        // to where the users came from so they can attempt this process again.
        // return back()->withErrors(
        //     ['email' => trans($response)]
        // );
    }
}
