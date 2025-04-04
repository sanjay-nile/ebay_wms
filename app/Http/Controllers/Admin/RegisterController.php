<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class RegisterController extends Controller {
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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    public function create(Request $request) {
        $this->validate($request, [
            'name'     => 'required|max:50|min:2|alpha',
            'email'    => 'required|email|max:50|min:5',
            'password' => 'required|max:50',
        ]);
        Admin::firstOrCreate(
            [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'status'   => 1,
                'slug'      => generateSlug($request->name),
            ]);
        return redirect(route('admin.register'))->with('success', 'Admin has been created successfully');
    }

    public function showRegistrationForm() {
        return view('pages.admin.register');
    }
}
