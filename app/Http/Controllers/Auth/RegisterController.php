<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\UserActivate;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;
use Config;
use Auth;

class RegisterController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    protected $redirectTo = '/';

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
     * code by sanjay
     * custom registration form
     */
    public function showRegistrationForm()
    {
        return view('pages.frontend.home.index', array('template' => 'register'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => 'required|max:15|min:8',
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Get a validator for an incoming partner request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function partnerValidator(array $data)
    {
        return Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => 'required|max:15|min:8',
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name'         => $data['first_name'] . ' ' . $data['last_name'],
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'email'        => $data['email'],
            'user_type_id' => $data['user_type_id'],
            'token'        => str_random(40) . time(),
            'status'       => User::ACTIVE,
            'password'     => Hash::make($data['password']),
            'phone'        => $data['phone'],
        ]);

        $get_view_data['subject']    =   'Activate Account!';
        $get_view_data['view']       =   'mails.main';
        $get_view_data['user']       =   $user;
        $get_view_data['client_user'] =  $data['client_user'];

        $user->slug = generateSlug($user->name);
        $user->save();

        // Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
        // $user->notify(new UserActivate($user));

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            // return redirect(Url('register/?tab=customer'))->withErrors($validator)->withInput();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        event(new Registered($user = $this->create($request->all())));

        # new code auto login..
        // $this->guard()->login($user);

        if(!empty($request->client_user)){
            return redirect()->route('home.page.user', $request->client_user)
            ->with(['success' => 'Congratulations! You have registered your account successfully.']);
        }

        return redirect()->route('login')
            ->with(['success' => 'Congratulations! You have registered your account successfully.']);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createPartner(Request $request)
    {
        $validator = $this->partnerValidator($request->all());

        if ($validator->fails()) {
            return redirect(Url('register/?tab=partner'))->withErrors($validator)->withInput();
        }

        $full_name = explode(" ", $request->company_name);
        $first_name =  $full_name[0];
        array_shift($full_name);
        $user = new User;
        $user->first_name = ucfirst($first_name);
        $user->last_name = ucfirst(join(" ",$full_name));
        $user->name = ucwords($request->company_name);
        $user->slug = generateSlug($request->company_name);
        $user->email = strtolower($request->email);
        $user->phone = $request->phone;
        $user->token = str_random(40) . time();
        $user->status = User::INACTIVE;
        $user->is_assigned = 'Y';
        $user->contact_person_name = $request->contact_person_name;
        $user->password = bcrypt($request->password);
        $user->user_type_id = $request->user_type_id;

        if($user->save()){
            $get_view_data['subject']    =   'Activate Account!';
            $get_view_data['view']       =   'mails.main';
            $get_view_data['user']       =   $user;

            $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
            $user->save();

            try {
                $mail = Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
                return redirect()->route('login')->with(['success' => 'Congratulations! You have registered your account successfully, shortly you will receive an email to activate your account.']);   
            } catch (\Swift_TransportException $e) {
                return redirect()->route('login')->with(['success' => 'Congratulations! You have registered your account successfully, shortly you will receive an email to activate your account.']);
            }            
        }
        
        return redirect()->back();
    }

    /**
     * @param $token
     */
    public function activate(Request $request, $token = null)
    {
        $user = User::where('token', $token)->first();
        if (empty($user)) {
            return redirect()->to('/')
                ->with(['error' => 'Your activation code is either expired or invalid.']);
        }

        $user->update(['token' => null, 'status' => User::ACTIVE]);

        $client = $request->input('client');

        if(isset($client) && !empty($client)){
            return redirect()->route('home.page.user', $client)
            ->with(['success' => 'Congratulations! your account is now activated.']);
        }
        
        return redirect()->route('login')
            ->with(['success' => 'Congratulations! your account is now activated.']);
    }

}
