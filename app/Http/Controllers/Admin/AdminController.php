<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Mail\defaultMail;
use Illuminate\Http\Request;
use App\User;
use App\Models\UserOwnerMapping;
use App\Models\ReverseLogisticWaybill;
use App\Models\PalletDeatil;
use App\Models\PackageDetail;
use App\Models\Category;
use App\Models\Post;

use Request as RequestsUrl;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Input;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use PHPExcel_Shared_Date;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Config;
use Validator;
use Mail;
use Auth;
use DB;

class AdminController extends Controller {
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
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $users = User::where('user_type_id',2)->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        return view('pages.admin.sub-admin.list',compact('users'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request) {
        /*if(Auth::user()->user_type_id!=1 || Auth::user()->user_type_id!=2){
            return redirect(getDashboardUrl()['dashboard']); 
        }*/
        
        $hourAgo = Carbon::now()->subHour();

        $total_sub_admin = User::where(['user_type_id'=>2])->count();
        $total_opreator = User::where(['user_type_id'=>7])->count();

        $total_scan_in = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')
                    ->where([['pes.key_name','order_status']])
                    ->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->count();

        $total_scan_in_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')
                    ->where([['pes.key_name','order_status']])
                    ->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->where('posts.created_at', '>=', $hourAgo)->count();

        $total_scan_out = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->count();

        $total_scan_out_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->where('posts.created_at', '>=', $hourAgo)->count();

        $total_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->count();

        $total_dispatch_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->where('posts.created_at', '>=', $hourAgo)->count();

        $pending_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03'])->where(['posts.post_type' => 'scan'])->count();

        $cancelled = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-06'])->where(['posts.post_type' => 'scan'])->count();

        $move_pacakge = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','location_move']])->whereIn('pes.key_value', ['move'])->where(['posts.post_type' => 'scan'])->count();

        return view('pages.admin.dashboard', compact(
            'total_sub_admin', 'total_opreator', 'total_scan_in', 'total_scan_out', 'total_dispatch', 'pending_dispatch', 'cancelled', 'move_pacakge', 'total_scan_in_hr', 'total_scan_out_hr', 'total_dispatch_hr'
        ));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function metricDashboard(Request $request) {
        $hourAgo = Carbon::now()->subHour();

        $total_sub_admin = User::where(['user_type_id'=>2])->count();
        $total_opreator = User::where(['user_type_id'=>7])->count();

        # scan in data...
        $total_scan_in = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->count();

        $total_scan_in_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->where('posts.created_at', '>=', $hourAgo)->count();

        # scan out data...
        $total_scan_out = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->count();

        $total_scan_out_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->where('posts.updated_at', '>=', $hourAgo)->count();

        # dispatch data...
        $total_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->count();

        $total_dispatch_hr = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->where('posts.updated_at', '>=', $hourAgo)->count();

        # pending for dispatch...
        $pending_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03'])->where(['posts.post_type' => 'scan'])->count();

        $total_op_orders = 0;

        return view('pages.admin.matric-dashboard', compact(
            'total_sub_admin', 'total_opreator', 'total_scan_in', 'total_scan_out', 'total_dispatch', 'pending_dispatch', 'total_scan_in_hr', 'total_scan_out_hr', 'total_dispatch_hr', 'total_op_orders'
        ));
    }

    /**
     * display the run time dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function runtimeDashboard(Request $request) {
        $total_sub_admin = User::where(['user_type_id'=>2])->count();
        $total_opreator = User::where(['user_type_id'=>7])->count();

        $scan_in = (new Post)->newQuery();
        $scan_in->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date);
            $end_t = Carbon::parse($request->end_date);
            $scan_in->whereDate('posts.created_at', '>=', $end_f->format('Y-m-d'))->whereDate('posts.created_at', '<=', $end_t->format('Y-m-d'));
        } else {
            $end_f = Carbon::now()->startOfDay();
            $end_t =  Carbon::now()->endOfDay();
            $scan_in->whereDate('posts.created_at', '>=', $end_f->format('Y-m-d'))->whereDate('posts.created_at', '<=', $end_t->format('Y-m-d'));
        }
        $total_scan_in = $scan_in->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->count();

        $scan_out = (new Post)->newQuery();
        $scan_out->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date);
            $end_t = Carbon::parse($request->end_date);
            $scan_out->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $scan_out->where([['dt.key_name','scan_out_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        } else {
            $end_f = Carbon::now()->startOfDay();
            $end_t =  Carbon::now()->endOfDay();
            $scan_out->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $scan_out->where([['dt.key_name','scan_out_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        }
        $total_scan_out = $scan_out->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->count();


        $dispatch = (new Post)->newQuery();
        $dispatch->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date);
            $end_t = Carbon::parse($request->end_date);
            $dispatch->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $dispatch->where([['dt.key_name','scan_dispatch_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        } else {
            $end_f = Carbon::now()->startOfDay();
            $end_t =  Carbon::now()->endOfDay();
            $dispatch->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $dispatch->where([['dt.key_name','scan_dispatch_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        }
        $total_dispatch = $dispatch->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->count();


        $pen_dis = (new Post)->newQuery();
        $pen_dis->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date);
            $end_t = Carbon::parse($request->end_date);
            $pen_dis->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $pen_dis->where([['dt.key_name','scan_out_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        } else {
            $end_f = Carbon::now()->startOfDay();
            $end_t =  Carbon::now()->endOfDay();
            $pen_dis->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
            $pen_dis->where([['dt.key_name','scan_out_date']])->whereDate('dt.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('dt.key_value', '<=', $end_t->format('Y-m-d'));
        }
        $pending_dispatch = $pen_dis->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03'])->where(['posts.post_type' => 'scan'])->count();

        # per hour data....
        $scan_in_hr = (new Post)->newQuery();
        $scan_in_hr->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        $total_scan_in_hr = $scan_in_hr->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01'])->whereBetween('posts.created_at', [now()->subHour()->subMinute(), now()->subHour()])->count();

        $scan_out_hr = (new Post)->newQuery();
        $scan_out_hr->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        $scan_out_hr->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
        $scan_out_hr->where([['dt.key_name','scan_out_time']])->whereBetween('dt.created_at', [now()->subHour()->subMinute(), now()->subHour()]);
        // $scan_out_hr->where([['dt.key_name','scan_out_time']])->where('dt.updated_at', '>=', Carbon::now()->subHour());
        $total_scan_out_hr = $scan_out_hr->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->count();

        $dis_hr = (new Post)->newQuery();
        $dis_hr->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        $dis_hr->leftJoin('post_extras AS dt', 'posts.id', '=', 'dt.post_id');
        $dis_hr->where([['dt.key_name','scan_dispatch_time']])->whereBetween('dt.created_at', [now()->subHour()->subMinute(), now()->subHour()]);
        // $dis_hr->where([['dt.key_name','scan_dispatch_time']])->where('dt.updated_at', '>=', Carbon::now()->subHour());
        $total_dispatch_hr = $dis_hr->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->count();

        $today = Carbon::today();
        $pending_pick = (new Post)->newQuery();
        $pending_pick->leftJoin('post_extras AS sd', 'posts.id', '=', 'sd.post_id');
        $pending_pick->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id');
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date);
            $end_t = Carbon::parse($request->end_date);
            $pending_pick->where([['sd.key_name','sale_date']])->whereDate('sd.key_value', '>=', $end_f->format('Y-m-d'))->whereDate('sd.key_value', '<=', $end_t->format('Y-m-d'));
        } else {
            $pending_pick->where([['sd.key_name','sale_date']])->whereRaw('DATEDIFF(NOW(), sd.key_value) > ?', [3]);   
        }
        $total_op_orders = $pending_pick->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->count();

        return response()->json([
            'total_sub_admin' => $total_sub_admin,
            'total_opreator' => $total_opreator,
            'total_scan_in' => $total_scan_in,
            'total_scan_out' => $total_scan_out,
            'total_dispatch' => $total_dispatch,
            'pending_dispatch' => $pending_dispatch,
            'total_scan_in_hr' => $total_scan_in_hr,
            'total_scan_out_hr' => $total_scan_out_hr,
            'total_dispatch_hr' => $total_dispatch_hr,
            'total_op_orders' => $total_op_orders,
            'status' => 201
        ], 201);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function graphDashboard(Request $request) {
        return view('pages.admin.graph-dashboard');
    }

    /**
     * display the live 24 hr data...
     * 
     * @return \Illuminate\Http\Response
     */
    public function liveGraphDashboard(Request $request) {
        $end_f = Carbon::now()->startOfDay()->format('Y-m-d');
        $end_t =  Carbon::now()->endOfDay()->format('Y-m-d');

        // Filter by Date Range...
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_t = Carbon::parse($request->end_date)->format('Y-m-d');
        }

        # operator data...
        $get_order = (new User)->newQuery();
        $get_order->where('user_type_id', 7);
        $get_order->select(
            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-01') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as scan_in"),

            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-02', 'IS-07') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as scan_out"),

            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-03') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as picked"),

            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-04', 'IS-05') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as dispatch"),

            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-06') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as cancelled"),

            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-02', 'IS-07','IS-01','IS-03','IS-04', 'IS-05') and pe.updated_at BETWEEN '".$end_f." 00:00:00' AND '".$end_t." 23:59:59') as total_package"),
            'users.name'
        );

        // dd(Str::replaceArray('?', $get_order->getBindings(), $get_order->toSql()));
        $opreator = $get_order->orderBy('users.name', 'ASC')->paginate(100);
        $opreator_view = view('pages.common.operator-html',array('opreator'=>$opreator))->render();

        return response()->json([
            'opreator' => $opreator_view,
            'status' => 201
        ], 201);
    }

    /**
     * get the chart data
     * 
     * @return \Illuminate\Http\Response
     */
    public function chartData(Request $request){
        $end_f = Carbon::now()->startOfDay()->format('Y-m-d');
        $end_t =  Carbon::now()->endOfDay()->format('Y-m-d');

        // Filter by Date Range...
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $end_f = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_t = Carbon::parse($request->end_date)->format('Y-m-d');
        }

        $get_order = (new User)->newQuery();
        $get_order->select(
            DB::raw("(select count(*) from posts INNER JOIN post_extras as pe ON posts.id = pe.post_id INNER JOIN post_extras as pes ON posts.id = pes.post_id where posts.post_author_id = users.id and pe.key_name = 'order_status' and pe.key_value IN ('IS-04', 'IS-05') and pes.key_name = 'scan_dispatch_date' and pes.key_value BETWEEN '".$end_f."' AND '".$end_t."') as dispatch"),
            'users.name'
        );
        $opreator = $get_order->where('user_type_id', 7)->orderBy('users.name', 'ASC')->get();

        $labels = $opreator->pluck('name')->map(function ($name) {
            return $name;
        })->toArray();

        $data = $opreator->pluck('dispatch')->toArray();

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function createForm(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        return view('pages.admin.sub-admin.add');
    }

    public function storeSubAdmin(Request $request){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
         $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:20|min:2',
            'last_name' => 'required|max:20|min:2',
            'email' => 'required|max:50|min:2|email|unique:users',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
            'department' => 'required|max:191',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.sub-admin.create'))->withErrors($validator)->withInput();
        }

        try{
            $pass_word = randomPassword();
            $user = new User;
            $user->first_name = ucfirst($request->first_name);
            $user->last_name = ucfirst($request->last_name);
            $user->name = ucwords($request->first_name.' '.$request->last_name);
            $user->email = strtolower($request->email);
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->department = $request->department ?? '';
            $user->status = $request->status;
            $user->password = bcrypt('password');
            $user->is_assigned = 'N';
            $user->user_type_id = 2;
            $user->created_by = Auth::id();
            $user->save();

            $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
            $user->save();

            # send mail...
            $get_view_data['subject']    =   'Create Account!';
            $get_view_data['view']       =   'mails.account';
            $get_view_data['user']       =   $user;
            $get_view_data['password']   =   $pass_word;

            try {
                Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
                return redirect(route('admin.sub-admin'))->with('success','Admin Rep has been created successfully');
            } catch (\Swift_TransportException $e) {
                return redirect(route('admin.sub-admin'))->with('success','Admin Rep has been created successfully');
            }
            
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
    }

    public function editSubAdmin(User $user){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        return view('pages.admin.sub-admin.edit',compact('user'));
    }

    public function updateSubAdmin(User $user){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $this->validate(request(), [
            'first_name' => 'required|max:20|min:2',
            'last_name' => 'required|max:20|min:2',
            'phone' => 'max:15',
            'address' => 'max:191',
            'status' => 'required',
        ]);
        
        try{
            $user->first_name = ucfirst(request('first_name'));
            $user->last_name = ucfirst(request('last_name'));
            $user->name = ucwords(request('first_name').' '.request('last_name'));
            $user->phone = request('phone');
            $user->address = request('address');
            $user->department = request('department');
            $user->status = request('status');
            $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
            $user->save();
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }

        return redirect(route('admin.sub-admin'))->with('success','Admin Rep has been updated successfully');
    }    

    public function assignClientToSubAdminList(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $new = new User;
        $users = $new->getAssignUserByTypeId(3);
        return view('pages.admin.assign-client-to-subadmin-list',compact('users'));
    }

    public function assignClientToSubAdminCreate(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $sub_admins = User::where('user_type_id',2)->get();
        $clients = User::where(['user_type_id'=>3])->get();
        // $clients = User::where(['user_type_id'=>3,'is_assigned'=>'N'])->get();
        return view('pages.admin.assign-client-to-subadmin',compact('sub_admins','clients'));
    }

    public function assignClientToSubAdminStore(Request $request){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $this->validate($request, [
            'customer_rep' => 'required',
            'client' => 'required|array',
        ]);
        try{
            $sub_admin_id = $request->customer_rep;
           if(is_array($request->client)){
            foreach($request->client as $client_id){
                $u_o_m = new UserOwnerMapping;
                $u_o_m->user_id = $client_id;
                $u_o_m->owner_id = $sub_admin_id;
                $result = $u_o_m->save();
                if($result){
                    $user = User::find($client_id);
                    $user->is_assigned = 'Y';
                    $user->save();
                }

            }
           }
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('client.to.subadmin'))->with('success','Client has been assigned successfully');
       
    }

    public function assignClientUserToClientStore(Request $request){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $this->validate($request, [
            'client' => 'required',
            'client_user' => 'required|array',
        ]);
        try{
            $client_id = $request->client;
           if(is_array($request->client_user)){
            foreach($request->client_user as $client_user_id){
                $u_o_m = new UserOwnerMapping;
                $u_o_m->user_id = $client_user_id;
                $u_o_m->owner_id = $client_id;
                $result = $u_o_m->save();
                if($result){
                    $user = User::find($client_user_id);
                    $user->is_assigned = 'Y';
                    $user->save();
                }

            }
           }
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('client-user.to.client'))->with('success','Client User has been assigned successfully');
    }

    public function assignClientUserToClientCreate(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $client_users = User::where(['user_type_id'=>4,'is_assigned'=>'N'])->get();
        $clients = User::where('user_type_id',3)->get();
        return view('pages.admin.assign-client-user-to-client',compact('clients','client_users'));
    }

    public function assignClientUserToClientList(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $new = new User;
        $users = $new->getAssignUserByTypeId(4);
        return view('pages.admin.assign-client-user-to-client-list',compact('users'));
    }

    public function assignClientUserToClientDestory($id){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $mapping = UserOwnerMapping::find($id);
        if($mapping){
            $user_id = $mapping->user_id;
            $delete = $mapping->delete();
            if($delete){
                $user = User::find($user_id);
                $user->is_assigned = 'N';
                $user->save();
                return back()->with('success','Client User has been unassigned successfully');
            }
        }
        return back()->with('error','Record not found');
    }

    public function assignClientToSubAdminDestory($id){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        DB::beginTransaction();
        try{
            $mapping = UserOwnerMapping::find($id);
            if($mapping){
                $user_id = $mapping->user_id;
                $user = User::find($user_id);
                $user->is_assigned = 'N';
                $save = $user->save();
                if($save && $mapping->delete()){
                   DB::commit();
                    return back()->with('success','Client has been unassigned successfully');
                }
            }else{
                return back()->with('error','Record not found');
            }

        }catch(Exception $e){
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
        
    }

    public function subAdminDestory($id){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        DB::beginTransaction();
        try{
            $sub_admin = User::where(['id'=>$id,'user_type_id'=>'2']);
            if($sub_admin->first()){
                $map_data = UserOwnerMapping::where(['owner_id'=>$id])->get();
                if($map_data->count()>0){
                    foreach($map_data as $row){
                        $user = User::find($row->user_id);
                        $user->is_assigned = 'N';
                        $user->save();
                    }
                    
                    $delete = UserOwnerMapping::where(['owner_id'=>$id])->delete();
                    if($delete && $sub_admin->delete()){
                        DB::commit();
                        return back()->with('success','Customer Rep has been deleted successfully');    
                    }else{
                        DB::rollback();
                        return back()->with('error','Please try again, Something is wrong');
                    }
                    
                }else{
                    $sub_admin->delete();
                    DB::commit();
                    return back()->with('success','Customer Rep has been deleted successfully');
                }
            }
            return back()->with('error','Record not found');
        }catch(Exception $e){
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
        
    }

    public function changePassword(){
        return view('pages.admin.change-password');
    }

    public function changePasswordUpdate(Request $request,User $user){
        $validator = Validator::make($request->all(), [
            'password' => 'required|max:15|min:2',
            'confirm_password' => 'required|max:15|min:2|same:password',
        ]);
        if ($validator->fails()) {
            return redirect(route('admin.change-password'))->withErrors($validator)->withInput();
        }
        try{
            $user->password = bcrypt($request->password);
            $user->save();
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('admin.change-password'))->with('success','Password has been changed successfully.');
    }

    public function profileShow(){
        $user = Auth::user();
        return view('pages.admin.profile',compact('user'));
    }

    public function profileUpdate(Request $request,User $user){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return redirect(route('admin.profile'))->withErrors($validator)->withInput();
        }
        try{
            $full_name = explode(" ", $request->name);
            $first_name =  $full_name[0];
            array_shift($full_name);
            $user->first_name = ucfirst($first_name);
            $user->last_name = ucfirst(join(" ",$full_name));
            $user->name = ucwords($request->name);
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->save();
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('admin.profile'))->with('success','Profile has been updated successfully.');
    }

    public function sendMail(){
        Mail::to('kamleshwar.yadav@niletechnologies.com')->send(new defaultMail(array('content'=>"Hi, How are you")));
    }

    /**
    * Display waywil does not have custom duty
    * Code by: sanjay
    **/
    public function getCustomDuty(Request $request){        
        $ob = new ReverseLogisticWaybill;
        $lists = $ob->getCustomDuty($request);

        return view('pages.admin.custom-duty', compact('lists'));
    }

    public function postCustomDuty(Request $request){
        if(is_array($request->ids)  && RequestsUrl::ajax()){

            if(empty($request->ids) && count($request->ids)==0){
                return response()->json(array('status'=>false,'msg'=>'Please check at least one way bill number'));
            }

            $way_bill_number = $request->ids;            
            DB::beginTransaction();
            try{
                $status = false;
                foreach ($way_bill_number as $value) {
                    # code...
                    $post_url = Config('constants.customDutyUrl').'getDatInvoiceDetail?secureKey='.Config('constants.secureKey').'&waybillNumber='.$value;
                    $client = new \GuzzleHttp\Client();
                    $r = $client->get($post_url);
                    $response = $r->getBody()->getContents();
                    $json_data = json_decode($response);                    
                    if($json_data->messageType=='Error'){
                        continue;
                    }
                    $status = true;
                    $waybill = ReverseLogisticWaybill::where('way_bill_number', $value)->first();
                    $waybill->setMeta('_custom_duties' , $response);
                    DB::commit();
                }
                if($status){
                    return response()->json(['message' => 'Duties and taxes genereted Successfully', 'status' => 201], 201);
                }else{
                    return response()->json(['message'=> "Duties and taxes not found. Please try again",'status'=>200],200);
                }
            }catch (\Exception $e) {
                DB::rollback();
                return (new \Illuminate\Http\Response)->setStatusCode(400,$e->getMessage());
            }
        }
    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function addPallet(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $lists = [];
        if($request->all()){
            $obj = new ReverseLogisticWaybill;
            $lists = $obj->getProcessedList($request);
        }

        return view('pages.admin.pallet.add', compact('client_list', 'lists'));
    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function palletStore(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required',
                // 'tracking_id' => 'required',
                // 'fright_charges' => 'required',
                // 'custom_duty' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $pallet = new PalletDeatil();
            $pallet->type = 'S';
            $pallet->pallet_id = $request->pallet_id;
            $pallet->client_id = $request->client_id;
            $pallet->warehouse_id = $request->warehouse_id;
            $pallet->shipping_type_id = $request->shipping_type_id;
            $pallet->rate = $request->rate;
            $pallet->carrier = $request->carrier;
            $pallet->tracking_id = $request->tracking_id;
            $pallet->fright_charges = $request->fright_charges;
            $pallet->custom_duty = $request->custom_duty;
            $pallet->save();

            return redirect(route('admin.pallet.list'))->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        
    }

    public function palletUpdate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required',
                // 'tracking_id' => 'required',
                // 'fright_charges' => 'required',
                // 'custom_duty' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $pallet = PalletDeatil::find($request->p_id);
            $pallet->pallet_id = $request->pallet_id;
            $pallet->client_id = $request->client_id;
            $pallet->warehouse_id = $request->warehouse_id;
            $pallet->shipping_type_id = $request->shipping_type_id;
            $pallet->rate = $request->rate;
            $pallet->carrier = $request->carrier;
            $pallet->tracking_id = $request->tracking_id;
            $pallet->fright_charges = $request->fright_charges;
            $pallet->custom_duty = $request->custom_duty;
            $pallet->custom_vat = $request->custom_vat;
            $pallet->export_vat_number = $request->export_vat_number;
            $pallet->import_duty_paid = $request->import_duty_paid;
            $pallet->import_vat_paid = $request->import_vat_paid;
            $pallet->import_vat_number = $request->import_vat_number;
            $pallet->weight_unit_type = $request->weight_unit_type;
            $pallet->weight_of_shipment = $request->weight_of_shipment;
            $pallet->hawb_number = $request->hawb_number;
            $pallet->mawb_number = $request->mawb_number;
            $pallet->manifest_number = $request->manifest_number;
            $pallet->return_type = $request->return_type;
            $pallet->rtn_import_entry_number = $request->rtn_import_entry_number;
            $pallet->rtn_import_entry_date = $request->rtn_import_entry_date;
            $pallet->export_declaration_number = $request->export_declaration_number;
            $pallet->export_declaration_date = $request->export_declaration_date;
            $pallet->exchange_rate = $request->exchange_rate;
            $pallet->flight_number = $request->flight_number;
            $pallet->flight_date = $request->flight_date;
            $pallet->pallet_type = $request->pallet_type;
            $pallet->save();

            return redirect(route('admin.pallet.list'))->with('success', 'Action Completed');
        } catch (Exception $e) {
             return redirect()->back()->with('error', $e->getMessage());
        }
        
    }

    public function palletEdit(PalletDeatil $pallet){
        $obj = new \App\Models\ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = \App\User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $items = $pallet->items;
        
        return view('pages.admin.pallet.edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'items', 'pallet'));
    }

    /**
    * Display Inprocess pallet list data
    * Code by: sanjay
    **/
    public function palletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        $lists = $list->where(['type' => 'S', 'pallet_type' => 'InProcess'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.list', compact('lists', 'client_list'));
    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function palletShow(PalletDeatil $pallet){        
        return view('pages.admin.pallet.show', compact('pallet'));
    }

    /*
    * Get order list by barcode or tracking id...
    *
    **/
    public function getOrderList(Request $request){
        $lists = '';
        if($request->all()){
            $obj = new ReverseLogisticWaybill;
            $lists = $obj->getProcessedList($request);
        }
        
        $html = '';
        if(!empty($lists)){
            $html = view('pages.admin.pallet.add-pallet',compact('lists'))->render();
        }
        echo json_encode(array('htm'=>$html));
    }

    /*
    * create pallet and update on orders...
    *
    **/
    public function palletByOrders(Request $request){
        try {            
            if ($request->has('pallet_name')) {
                # code...
                $ex_pall = PalletDeatil::where('pallet_id', $request->pallet_name)->first();
                if ($ex_pall) {
                    # code...
                    $pallet = $ex_pall;
                } else {
                    $pallet = new PalletDeatil();
                    $pallet->pallet_id = $request->pallet_name;
                    $pallet->type = 'S';
                    $pallet->return_type = $request->return_type;
                    $pallet->save();
                }

                if ($pallet->id && count($request->pallet_orders) > 0) {
                    # code...
                    foreach ($request->pallet_orders as $key => $value) {
                        # code...
                        // $waywill = ReverseLogisticWaybill::find($value);
                        // $waywill->where('id', $value)->update(array('pallet_id' => $request->pallet_name));

                        $pkg = PackageDetail::find($value);
                        $pkg->where('id', $value)->update(array('pallet_id' => $request->pallet_name));
                    }

                    return redirect(route('admin.pallet.list'))->with('succes', 'Create Pallet Name succcessfully');
                }
            }

            return redirect()->back()->with('error', 'Something wrong.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
    * add multiple pallet data
    */
    public function addMultiPallet(){
        return view('pages.admin.pallet.multi-pallet-add');
    }

    /**
    * add multiple pallet data
    */
    public function multiPalletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        // $lists = PalletDeatil::where('type', 'M')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        $lists = $list->where('type', 'M')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.multi-pallet-list', compact('lists', 'client_list'));
    }

    /**
    * store multiple pallet data
    * Code by: sanjay
    **/
    public function multiPalletStore(Request $request){
        try {
            if (!$request->has('m_pallet_id') && empty($request->m_pallet_id)) {
                # code...
                return redirect()->back()->with('error', 'Multi Pallet ID is empty.');
            }
            $pallet = new PalletDeatil();
            $pallet->pallet_id = $request->m_pallet_id;
            $pallet->type = 'M';
            $pallet->save();
            $p_id = $pallet->id;

            if(count($request->pallet_ids) > 0){
                foreach ($request->pallet_ids as $key => $value) {
                    # code...
                    $p_object = PalletDeatil::where('pallet_id',$value)->first();
                    if ($p_object) {
                        # code...
                        $p_object->parent = $p_id;
                        $p_object->save();
                    }
                }
            }

            return redirect(route('admin.multi.pallet.list'))->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function multiPalletEdit(PalletDeatil $pallet){
        $obj = new \App\Models\ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = \App\User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        return view('pages.admin.pallet.multi-pallet-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list'));

    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function multiPalletShow(PalletDeatil $pallet){        
        return view('pages.admin.pallet.multi-pallet-show', compact('pallet'));
    }

    /**
    * autocomplete search
    * Code by: sanjay
    **/
    public function search(Request $request){
        $search = $request->get('term');          
        $result = PalletDeatil::where('type', 'S')
                ->where('parent', '=', null)
                ->where('pallet_id', 'LIKE', '%'. $search. '%')->get();
        return response()->json($result);   
    }

    /**
    * generate csv
    * Code by: sanjay
    **/
    public function generateFile(){
        $data = Input::all();
        if (isset($data['pallet_id']) && !empty($data['pallet_id'])) {
            # code...
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=Manifest_" . time() . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0",
            );
            
            $pallet = PalletDeatil::find($data['pallet_id']);
            if(!$pallet){
                return redirect()->back();
            }

            $rows = $columnNames = [];
            # for canda manifest data...
            if($data['country'] == 'Canada'){
                $columnNames = ['Manifest Date','Pallet ID', 'MAWB Number', 'HAWB#','Manifest #', 'Flight Date', 'Flight Number', 'Return Import Entry Number', 'Return Import Entry Date', 'Export Declaration Number', 'Export Declaration Date', 'Exchange Rate', 'Import VAT Number', 'Import VAT Paid','Import Duty Paid', 'Export VAT Number','Custom Duty Paid', 'Custom VAT Paid','Batch No.',"Shipper's Code","Shipper's Name", 'Order No.', 'Date', 'Customer', 'Address', 'City', 'Province', 'Postal Code', 'Sku No.', 'HS Code', 'Ctry', 'Qty', 'GBP', 'CAD', 'Exchange Rate'];
                if(count($pallet->items) > 0){
                    foreach ($pallet->items as $key => $pkg) {
                        # code...
                        // foreach ($order->packages as $key => $pkg) {
                            # code...
                            $data_ar = [date('Y-m-d', strtotime($pallet->created_at)), $pallet->pallet_id, $pallet->mawb_number, $pallet->hawb_number, $pallet->manifest_number, date('Y-m-d', strtotime($pallet->flight_date)), $pallet->flight_number, $pallet->rtn_import_entry_number, date('Y-m-d', strtotime($pallet->rtn_import_entry_date)), $pallet->export_declaration_number, date('Y-m-d', strtotime($pallet->export_declaration_date)), $pallet->exchange_rate, $pallet->import_vat_number, $pallet->import_vat_paid, $pallet->import_duty_paid, $pallet->export_vat_number, $pallet->custom_duty, $pallet->custom_vat, '','', '', $pkg->order->way_bill_number, date('Y-m-d', strtotime($pkg->created_at)),$pkg->order->meta->_customer_name, $pkg->order->meta->_customer_address, $pkg->order->meta->_customer_city, $pkg->order->meta->_customer_state, $pkg->order->meta->_customer_pincode,  $pkg->bar_code, $pkg->hs_code, 'CA', $pkg->package_count, '', '', ''];
                            array_push($rows, $data_ar);
                        // }
                    }
                }
            }
            
            # for UK manifest data...
            if ($data['country'] == 'UK') {
                # code...
                $columnNames = $this->getColoumnName();
                if(count($pallet->items) > 0){
                    foreach ($pallet->items as $key => $pkg) {
                        # code...
                        // foreach ($order->packages as $key => $pkg) {
                            # code...                           
                            $data_ar = [date('Y-m-d', strtotime($pallet->created_at)), $pallet->pallet_id, $pallet->mawb_number, $pallet->hawb_number, $pallet->manifest_number, date('Y-m-d', strtotime($pallet->flight_date)), $pallet->flight_number, $pallet->rtn_import_entry_number, date('Y-m-d', strtotime($pallet->rtn_import_entry_date)), $pallet->export_declaration_number, date('Y-m-d', strtotime($pallet->export_declaration_date)), $pallet->exchange_rate, $pallet->import_vat_number, $pallet->import_vat_paid, $pallet->import_duty_paid, $pallet->export_vat_number, $pallet->custom_duty, $pallet->custom_vat, '', $pkg->order->way_bill_number, $pkg->title, $pkg->bar_code,$pkg->hs_code,'US','GB','GB','','',$pkg->weight,'','1',$pkg->package_count,'','GBP',$pkg->price,'','','','','','','','','','','','','','','','',$pkg->order->meta->_customer_name,$pkg->order->meta->_customer_state,$pkg->order->meta->_customer_city,$pkg->order->meta->_customer_pincode,$pkg->order->meta->_customer_country,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''];

                            array_push($rows, $data_ar);
                        // }
                    }
                }
            }

            # create csv...
            $callback = function () use ($columnNames, $rows) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columnNames);
                foreach ($rows as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } else {
            # code...
            return redirect()->back();
        }
    }

    public function getColoumnName(){
        $clmn_arr = [
            'Manifest Date',
            'Pallet ID',
            'MAWB Number',
            'HAWB#',
            'Manifest #',
            'Flight Date',
            'Flight Number',
            'Return Import Entry Number',
            'Return Import Entry Date',
            'Export Declaration Number',
            'Export Declaration Date',
            'Exchange Rate',
            'Import VAT Number',
            'Import VAT Paid',
            'Import Duty Paid',
            'Export VAT Number',
            'Custom Duty Paid',
            'Custom VAT Paid',
            'Line Number',
            'Order Number',
            'Product Code',
            'SKU',
            'HS Code',
            'Dest Cntry',
            'Disp Cntry',
            'Orig Cntry',
            'Goods Description',
            'Commodity Code',
            'Item Gross Mass',
            'CPC',
            'Item Net Mass',
            'Quantity',
            'Value',
            'Currency',
            'Selling Price',
            'Item Third Quantity',
            'Item Stat Val.',
            'UN Dangerous Goods Code',
            'Packages Number',
            'Packages Kind',
            'Packages Marks and Numbers',
            'Document Code',
            'Document Reference',
            'Document Status',
            'Consignor ID',
            'Consignor Name',
            'Consignor Street',
            'Consignor City',
            'Consignor Postcode',
            'Consignor Country',
            'Consignee ID',
            'Consignee Name',
            'Consignee Street',
            'Consignee City',
            'Consignee Postcode',
            'Consignee Country',
            'AI Statement Code',
            'AI Statement Text',
            'AI Statement Code 2',
            'AI Statement Text 2',
            'AI Statement Code 3',
            'AI Statement Text 3',
            'AI Statement Code 4',
            'AI Statement Text 4',
            'AI Statement Code 5',
            'AI Statement Text 5',
            'Prev Doc Class 1',
            'Prev Doc Type 1',
            'Prev Doc Reference 1',
            'Prev Doc Class 2',
            'Prev Doc Type 2',
            'Prev Doc Reference 2',
            'Prev Doc Class 3',
            'Prev Doc Type 3',
            'Prev Doc Reference 3',
            'Commodity Add Code',
            'Serial no.',
            'Purchase Order',
            'Invoice Number',
            'Customer Defined 1',
            'Customer Defined 2',
            'Document Code 2',
            'Document Reference 2',
            'Document Status 2',
            'Document Code 3',
            'Document Reference 3',
            'Document Status 3',
            'Document Code 4',
            'Document Reference 4',
            'Document Status 4',
            'Document Code 5',
            'Document Reference 5',
            'Document Status 5',
        ];

        return $clmn_arr;
    }


    /*
    * create pallet and update on orders...
    **/
    public function addPalletToOrders(Request $request){
        DB::beginTransaction();
        try {
            if ($request->has('pallet_name') && !empty($request->pallet_name)) {
                $ex_pall = PalletDeatil::where('pallet_id', $request->pallet_name)->first();
                if ($ex_pall) {
                    $pallet = $ex_pall;
                } else {
                    $pallet = new PalletDeatil();
                    $pallet->pallet_id = $request->pallet_name;
                    $pallet->type = 'S';
                    $pallet->return_type = $request->return_type;
                    $pallet->save();
                }

                if ($pallet->id && count($request->pallet_orders) > 0) {
                    foreach ($request->pallet_orders as $key => $value) {
                        # code...
                        // $waywill = ReverseLogisticWaybill::find($value);
                        // $waywill->where('id', $value)->update(array('pallet_id' => $request->pallet_name));

                        $pkg = PackageDetail::find($value);
                        $pkg->where('id', $value)->update(array('pallet_id' => $request->pallet_name));
                    }

                    DB::commit();
                    // return redirect(route('admin.pallet.list'))->with('succes', 'Action succcessfully');
                    return redirect()->back()->with('succes', 'Action succcessfully');
                }
            } else {
                DB::rollback();
                return redirect()->back()->with('error', 'Please select one of the Pallet action.');
            }            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
    * Display Closed pallet list data
    * Code by: sanjay
    **/
    public function closedPalletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        $lists = $list->where(['type' => 'S', 'pallet_type' => 'Closed'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.list', compact('lists', 'client_list'));
    }

    /**
    * Display Closed pallet list data
    * Code by: sanjay
    **/
    public function shippedPalletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        $lists = $list->where(['type' => 'S', 'pallet_type' => 'Shipped'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.list', compact('lists', 'client_list'));
    }

    public function getCategory(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }

        return view('pages.admin.categories');
    }

    public function categoryStore(Request $request){
        try {
            $file      = $request->file('cat_file')->getClientOriginalName();
            $baseFilename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv') {
                $inputFileName = $request->file('cat_file');
                
                /*check point*/
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load($inputFileName);
                $objPHPExcel->setActiveSheetIndex(0);
                $objWorksheet          = $objPHPExcel->getActiveSheet();
                $CurrentWorkSheetIndex = 0;
                /* row and column*/
                // $sheet = $objPHPExcel->getSheet(0);
                $highestRow    = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();

                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                $headingsArray      = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray      = $headingsArray[1];

                $r              = -1;
                $namedDataArray = $keys = array();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '') || empty($dataRow[$row]['A'])) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {
                            $key                      = strtolower(str_replace(' ', '_', $columnHeading));
                            $namedDataArray[$r][$key] = $dataRow[$row][$columnKey];
                            array_push($keys,$key);
                        }
                    }
                }

                // dd($namedDataArray);

                $order_arr = $this->duplicteCategory($namedDataArray, 'category');
                if (!is_array($order_arr)) {
                    return redirect()->back()->with('error', 'Not a array.');
                }

                // dd($order_arr);
                foreach($order_arr as $key => $value){
                    if (empty($value)) {
                        continue;
                    }
                    
                    $slug = createSlug($value['parent']);
                    $cat = Category::where('slug', $slug)->first();
                    // dd($cat);
                    
                    if (empty($cat)) {
                        $cat = new Category;
                        $cat->name = $value['parent'];
                        $cat->parent_id = 0;
                        $cat->slug = $slug;
                        if (!empty($value['parent_code'])) {
                            $cat->code = $value['parent_code'];
                        }
                        $cat->save();
                    }                    
                    
                    $id = $cat->id;

                    foreach ($value['sub']['category'] as $ke => $sub) {
                        $sub_slug = createSlug($sub['name']);
                        $sub_cat = Category::where('slug', $sub_slug)->first();
                        if (empty($sub_cat)) {
                            $sub_cat = new Category;
                            $sub_cat->name =  $sub['name'];
                            $sub_cat->code =  $sub['code'];
                            $sub_cat->conditions =  json_encode($value['sub']['condition'][$ke]);
                            $sub_cat->slug = createSlug($sub_slug);
                            $sub_cat->parent_id = $id;
                            $sub_cat->save();
                        }
                    }
                }

                return redirect()->back()->with('success', 'Action succcessfully');
            } else {
                return redirect()->back()->with('error', 'wrong extension');
            }
        }catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function duplicteCategory($array, $keyname){
        try {
            $new_array  = array();
            foreach ($array as $key => $value) {
                if (!isset($new_array[$value[$keyname]])) {
                    $new_array[$value[$keyname]]['parent'] = $value['category'];
                    $new_array[$value[$keyname]]['parent_code'] = $value['categorycode'] ?? '';
                    $new_array[$value[$keyname]]['sub']['category'] = [];
                    $new_array[$value[$keyname]]['sub']['condition'] = [];
                    array_push($new_array[$value[$keyname]]['sub']['category'], ['name' => $value['sub-category'], 'code' => $value['subcategorycode']]);
                    $con = [];
                    if (!empty($value['conditoin-1'])) {
                        array_push($con, trim($value['conditoin-1']));
                    }
                    if (!empty($value['conditoin-2'])) {
                        array_push($con, trim($value['conditoin-2']));
                    }
                    if (!empty($value['conditoin-3'])) {
                        array_push($con, trim($value['conditoin-3']));
                    }
                    if (!empty($value['conditoin-4'])) {
                        array_push($con, trim($value['conditoin-4']));
                    }
                    if (!empty($value['conditoin-5'])) {
                        array_push($con, trim($value['conditoin-5']));
                    }
                    if (!empty($value['conditoin-6'])) {
                        array_push($con, trim($value['conditoin-6']));
                    }
                    if (!empty($value['conditoin-7'])) {
                        array_push($con, trim($value['conditoin-7']));
                    }
                    if (!empty($value['conditoin-8'])) {
                        array_push($con, trim($value['conditoin-8']));
                    }
                    if (!empty($value['conditoin-9'])) {
                        array_push($con, trim($value['conditoin-9']));
                    }
                    array_push($new_array[$value[$keyname]]['sub']['condition'], $con);
                } else {
                    array_push($new_array[$value[$keyname]]['sub']['category'], ['name' => $value['sub-category'], 'code' => $value['subcategorycode']]);
                    $con = [];
                    if (!empty($value['conditoin-1'])) {
                        array_push($con, trim($value['conditoin-1']));
                    }
                    if (!empty($value['conditoin-2'])) {
                        array_push($con, trim($value['conditoin-2']));
                    }
                    if (!empty($value['conditoin-3'])) {
                        array_push($con, trim($value['conditoin-3']));
                    }
                    if (!empty($value['conditoin-4'])) {
                        array_push($con, trim($value['conditoin-4']));
                    }
                    if (!empty($value['conditoin-5'])) {
                        array_push($con, trim($value['conditoin-5']));
                    }
                    if (!empty($value['conditoin-6'])) {
                        array_push($con, trim($value['conditoin-6']));
                    }
                    if (!empty($value['conditoin-7'])) {
                        array_push($con, trim($value['conditoin-7']));
                    }
                    if (!empty($value['conditoin-8'])) {
                        array_push($con, trim($value['conditoin-8']));
                    }
                    if (!empty($value['conditoin-9'])) {
                        array_push($con, trim($value['conditoin-9']));
                    }
                    array_push($new_array[$value[$keyname]]['sub']['condition'], $con);
                }
            }

            $new_array = array_values($new_array);
            
            return $new_array;
        } catch (Exception $e) {
            return true;
        }
    }

    public function getSubCategory(Request $request){
        $cat_id = $request->cat_id;
        $level = $request->level;
        $cat_list = Category::where(['parent_id' => $cat_id])->get();
        $html       = view('pages.common.sub-category', compact('cat_list', 'level'))->render();
        return response()->json(['html' => $html]);
    }

    public function getFillterSubCategory(Request $request){
        $cat_id = $request->cat_id;
        $level = $request->level;
        $cat_list = Category::where(['parent_id' => $cat_id])->get();
        $html       = view('pages.common.fillter-sub-category', compact('cat_list', 'level'))->render();
        return response()->json(['html' => $html]);
    }

    public function getFillterSubCandition(Request $request){
        $cat_id = $request->cat_id;
        $sub_cat_id = $request->sub_cat_id;
        $list = Category::where(['id' => $sub_cat_id, 'parent_id' => $cat_id])->first();
        $cat_list = [];
        if (!empty($list->conditions)) {
            // code...
            $cat_list = json_decode($list->conditions);
        }
        $html = view('pages.common.fillter-condition', compact('cat_list'))->render();
        return response()->json(['html' => $html]);
    }

    public function getForm(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }

        return view('pages.admin.form-build');
    }


    public function operator(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $users = User::where('user_type_id',7)->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        return view('pages.admin.operator.list',compact('users'));
    }

    public function operatorCreateForm(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        return view('pages.admin.operator.add');
    }

    public function storeOperator(Request $request){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
         $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:20|min:2',
            'last_name' => 'required|max:20|min:2',
            'email' => 'required|max:50|min:2|email|unique:users',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
            'department' => 'required|max:191',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.operator.create'))->withErrors($validator)->withInput();
        }

        try{
            $pass_word = randomPassword();
            $user = new User;
            $user->first_name = ucfirst($request->first_name);
            $user->last_name = ucfirst($request->last_name);
            $user->name = ucwords($request->first_name.' '.$request->last_name);
            $user->email = strtolower($request->email);
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->department = $request->department ?? '';
            $user->status = $request->status;
            $user->password = bcrypt('password');
            $user->is_assigned = 'N';
            $user->user_type_id = 7;
            $user->created_by = Auth::id();
            $user->save();

            $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
            $user->save();

            # send mail...
            $get_view_data['subject']    =   'Create Account!';
            $get_view_data['view']       =   'mails.account';
            $get_view_data['user']       =   $user;
            $get_view_data['password']   =   $pass_word;

            try {
                Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
                return redirect(route('admin.operator'))->with('success','Operator has been created successfully');
            } catch (\Swift_TransportException $e) {
                return redirect(route('admin.operator'))->with('success','Operator has been created successfully');
            }
            
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }
    }

    public function editOperator(User $user){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        return view('pages.admin.operator.edit',compact('user'));
    }

    public function updateOperator(User $user){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $this->validate(request(), [
            'first_name' => 'required|max:20|min:2',
            'last_name' => 'required|max:20|min:2',
            'phone' => 'max:15',
            'address' => 'max:191',
            'status' => 'required',
        ]);
        
        try{
            $user->first_name = ucfirst(request('first_name'));
            $user->last_name = ucfirst(request('last_name'));
            $user->name = ucwords(request('first_name').' '.request('last_name'));
            $user->phone = request('phone');
            $user->address = request('address');
            $user->department = request('department');
            $user->status = request('status');
            $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
            $user->save();
        }catch(Exception $e){
            return back()->withError($e->getMessage())->withInput();
        }

        return redirect(route('admin.operator'))->with('success','Operator has been updated successfully');
    }

    public function operatorDestory($id){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        DB::beginTransaction();
        try{
            $sub_admin = User::where(['id'=>$id,'user_type_id'=>'7']);
            if($sub_admin->first()){
                $map_data = UserOwnerMapping::where(['owner_id'=>$id])->get();
                if($map_data->count()>0){
                    foreach($map_data as $row){
                        $user = User::find($row->user_id);
                        $user->is_assigned = 'N';
                        $user->save();
                    }
                    
                    $delete = UserOwnerMapping::where(['owner_id'=>$id])->delete();
                    if($delete && $sub_admin->delete()){
                        DB::commit();
                        return back()->with('success','Operator has been deleted successfully');    
                    }else{
                        DB::rollback();
                        return back()->with('error','Please try again, Something is wrong');
                    }
                    
                }else{
                    $sub_admin->delete();
                    DB::commit();
                    return back()->with('success','Operator has been deleted successfully');
                }
            }
            return back()->with('error','Record not found');
        }catch(Exception $e){
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
        
    }
}
