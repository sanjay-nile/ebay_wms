<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Session;
use Cookie;

class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;



    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // protected $redirectTo = 'client-user/dashboard';

    protected function redirectTo( ) {
        if (Auth::check() && in_array(Auth::user()->user_type_id, [3,4,5,6])) {
            return route('front.client.dashboard');
        } else{
            \Session::flash('error', 'You are not authorized to access this page'); 
            Auth::logout();
            return route('login');
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * code by sanjay
     * custom login form
     */

    public function showLoginForm()
    {
        return view('pages.frontend.home.index', array('template' => 'login'));
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = $this->field($request);
        Session::put('client_user', $request->get('client_user'));
        
        return [
            $field         => $request->get($this->username()),
            'password'     => $request->get('password'),
            'status'       => User::ACTIVE
        ];
    }

    /**
     * Determine if the request field is email or username.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function field(Request $request)
    {
        $email = $this->username();

        return filter_var($request->get($email), FILTER_VALIDATE_EMAIL) ? $email : 'username';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $field = $this->field($request);

        $messages = ["{$this->username()}.exists" => 'The account you are trying to login is not activated or it has been disabled.'];

        $this->validate($request, [
            $this->username() => "required|exists:users,{$field},status," . User::ACTIVE,
            'password'        => 'required',
        ], $messages);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $client_user = Session::get('client_user');
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        if (isset($client_user) && !empty($client_user)) {
            # code...
            return redirect(route('home.page.user', $client_user));
        }
        return redirect(route('home'));
    }

    public function set_admin_lang() {
		// $get_current_default_lang = get_default_languages_data();
		// if (count($get_current_default_lang) > 0) {
		// 	App::setLocale($get_current_default_lang['lang_code']);
		// }
        if(\Session::get('locale') == 'en') 
            \App::setLocale('en');
        else                                
            \App::setLocale('en');
	}

    public function goToAdminLoginPage()
    {
        $user_view = '';
        $pass_view = '';
        $this->set_admin_lang();
        if (Cookie::has('remember_me_data')) {
            $get_cookie = Cookie::get('remember_me_data');
            $cookie_parse = explode('#', $get_cookie);
            if (is_array($cookie_parse) && count($cookie_parse) > 0) {
                $userDetails = User::find($cookie_parse[0]);
                $password = bcrypt(base64_decode($cookie_parse[1]));
                if (Hash::check(base64_decode($cookie_parse[1]), $password) && Hash::check(base64_decode($cookie_parse[1]), $userDetails['password'])) {
                    $user_view = $userDetails['email'];
                    $pass_view = base64_decode($cookie_parse[1]);
                }
            }
        }

        $data = array(
            'user'                => $user_view,
            'pass'                => $pass_view,
        );

        return view('pages.auth.admin-login')->with('data', $data);
    }

    public function postAdminLogin(){
        if (Request::isMethod('post') && Session::token() == Request::get('_token')) {
            
         } else {
            return redirect()->back();
        }
    }
}
