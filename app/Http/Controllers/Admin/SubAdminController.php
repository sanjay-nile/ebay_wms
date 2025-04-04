<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\User;
use App\Models\UserOwnerMapping;
use App\Models\ReverseLogisticWaybill;
use App\Models\PalletDeatil;
use App\Models\PackageDetail;
use App\Models\Category;
use App\Models\Post;


class SubAdminController extends Controller
{
    protected $guard = 'admin';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashboard(Request $request){
        /*if(Auth::user()->user_type_id!=1 || Auth::user()->user_type_id!=2){
            return redirect(getDashboardUrl()['dashboard']); 
        }*/
                

        $total_sub_admin     = User::where(['user_type_id'=>2])->count();
        $total_client        = User::where(['user_type_id'=>3])->count();
        $total_opreator        = User::where(['user_type_id'=>7])->count();
        $total_client_user   = User::where(['user_type_id'=>4])->count();
        $total_user          = User::where(['user_type_id'=>5])->count();
        $total_scan_in = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01'])->where(['posts.post_type' => 'scan'])->count();

        $total_scan_out = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02', 'IS-07'])->where(['posts.post_type' => 'scan'])->count();

        $total_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05'])->where(['posts.post_type' => 'scan'])->count();
        $pending_dispatch = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03'])->where(['posts.post_type' => 'scan'])->count();
        $cancelled = Post::leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-06'])->where(['posts.post_type' => 'scan'])->count();

        $inprocess          = PalletDeatil::where('pallet_type', 'InProcess')->count();
        $close          = PalletDeatil::where('pallet_type', 'Closed')->count();
        $shipped          = PalletDeatil::where('pallet_type', 'Shipped')->count();

        $obj = new ReverseLogisticWaybill;
        $total_reverse_order = $obj->whereNotIn('status',['Deleted'])->withTrashed()->count();        
        # actual failed order...
        $actual_failed = 0;
        $repeated = $obj->repeatedReturnOrders($request);

        return view('pages.admin.dashboard',compact(
            'total_reverse_order',
            'total_sub_admin',
            'total_client',
            'total_client_user',
            'actual_failed',
            'repeated', 'inprocess', 'close', 'shipped', 'total_opreator', 'total_scan_in', 'total_scan_out', 'total_dispatch', 'pending_dispatch', 'cancelled'
        ));

        // return view('pages.admin.sub-admin.dashboard', compact('total_reverse_order', 'total_client', 'total_user', 'total_order', 'lists','total_client_user','total_user', 'users','actual_failed','failed'));
    }
}
