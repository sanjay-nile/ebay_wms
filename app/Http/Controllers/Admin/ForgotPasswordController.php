<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
class ForgotPasswordController extends Controller {
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
    public function __construct() {
        $this->middleware('guest:admin');
    }

    public function showLinkRequestForm() {
        return view('pages.admin.passwords.email');
    }

    public function sendResetLinkEmail(Request $request){
        try {
            $this->validateEmail($request);
            
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
        } catch (\Swift_TransportException $e) {
            // dd($e->getMessage());
            return redirect()->back()->with('success', 'We have not sent an activation link to your registered email Id.');
            // return redirect()->back()->with('success', $e->getMessage());
        }
    }
     //defining which password broker to use, in our case its the admins
    protected function broker() {
        return Password::broker('users');
    }
}
