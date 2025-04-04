<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\Shipper;
use App\Models\ShipperAddress;
use App\Models\Charity;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Redirect;
use Session;
use URL;
use Validator;
use DB;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $country = Country::all();
        $charity = Charity::all();
        $shipper = Shipper::orderBy('name', 'ASC')->get();

        return view('home', compact('country', 'shipper', 'charity'));
    }
}
