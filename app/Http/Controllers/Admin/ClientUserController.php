<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;
use Exception;
use Config;
use DB;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

class ClientUserController extends Controller
{
	public function __construct(){
		$this->middleware('auth:admin');
	}
    
    public function index(){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }

        if(Auth::user()->user_type_id==1){
            $ob = new User;
            $users = $ob->getUserWithOwnerByTypeId(4);
        }else{
            $ob = new User;
            $users = $ob->getUserWithOwnerByTypeId(NULL,Auth::id());
        }

    	return view('pages.admin.client-user.list',compact('users'));
    }

    public function createForm(){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }
    	return view('pages.admin.client-user.add');
    }

    public function storeClientUser(Request $request){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }
    	 $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:20|min:2',
            'last_name' => 'required|max:20|min:2',
            'email' => 'required|max:50|min:2|email|unique:users',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect(route('client-user.create'))->withErrors($validator)->withInput();
        }
        try{
            $pass_word = randomPassword();
            $is_assigned = (Auth::user()->user_type_id==3) ? "Y" : "N";
        	$user = new User;
	        $user->first_name = ucfirst($request->first_name);
	        $user->last_name = ucfirst($request->last_name);
	        $user->name = ucwords($request->first_name.' '.$request->last_name);
            $user->slug = generateSlug($request->first_name.' '.$request->last_name);
	        $user->email = strtolower($request->email);
	        $user->phone = $request->phone;
	        $user->address = $request->address;
	        $user->status = $request->status;
	        $user->password = bcrypt($pass_word);
	        $user->is_assigned = $is_assigned;
	        $user->user_type_id = 4;
	        $user->created_by = Auth::id();
	        $user->save();
            if(Auth::user()->user_type_id==3){
                $u_o_m = new  \App\Models\UserOwnerMapping;
                $u_o_m->user_id = $user->id;
                $u_o_m->owner_id = Auth::id();
                $u_o_m->save();
            }

            # send mail...
            $get_view_data['subject']    =   'Welcome Email';
            $get_view_data['view']       =   'mails.account';
            $get_view_data['user']       =   $user;
            $get_view_data['password']   =   $pass_word;

            Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
        }catch(Exception $e){
        	return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('client-user'))->with('success','Client User has been created successfully');
    }

    public function editClientUser(User $user){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }
    	return view('pages.admin.client-user.edit',compact('user'));
    }

    public function updateClientUser(User $user){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }
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
	        $user->status = request('status');
	        $user->save();
        }catch(Exception $e){
        	return back()->withError($e->getMessage())->withInput();
        }
        return back()->with('success','Client User has been updated successfully');
    }

    public function clientUserDestory($id){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=3){ return redirect(getDashboardUrl()['dashboard']); }
        DB::beginTransaction();
        try{
            $client = User::where(['id'=>$id,'user_type_id'=>4]);
            $user_row = $client->first();
            if($user_row){
                
                \App\Models\UserOwnerMapping::where(['user_id'=>$user_row->id])->delete();
                
                if($client->delete()){
                   DB::commit();
                    return back()->with('success','Client User has been deleted successfully');
                }
            }else{
                return back()->with('error','Record not found');
            }

        }catch(Exception $e){
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
        
    }

    public function dashboard(){
        $total_reverse_order = \App\Models\ReverseLogisticWaybill::where(['created_by'=>Auth::id()])->count();
        return view('pages.admin.client-user.client-user-dashboard',compact('total_reverse_order'));
    }
}
