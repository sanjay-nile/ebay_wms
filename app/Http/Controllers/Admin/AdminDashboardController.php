<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use Config;
use Validator;
use Mail;
use Auth;
use DB;

class AdminDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $guard = 'admin';

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index(){
        return view('pages.admin.dashboard');
    }
}
