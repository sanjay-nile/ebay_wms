<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;
use Config;
use Exception;
use DB;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function __construct(){
		$this->middleware('auth:admin')->except(['getCountryList','getCarrierList','getShipmentList','getchargesList']);
	}

    /*Client Dahsboard*/
    public function dashboard(){
        $total_reverse_order = \App\Models\ReverseLogisticWaybill::where(['client_id'=>Auth::id()])->count();
        $total_client_user        =\App\Models\UserOwnerMapping::where(['owner_id'=>Auth::id()])->count();
        return view('pages.admin.client.client-dashboard',compact('total_reverse_order','total_client_user'));
    }

    public function index(){
        if(Auth::user()->user_type_id!=1 && Auth::user()->user_type_id!=2){ 
            return redirect(getDashboardUrl()['dashboard']); 
        }

        if(Auth::user()->user_type_id==1){
            $ob = new User;
            $users = $ob->getClientUser(3);
        }else{            
            $ob = new User;
            $users = $ob->getUserWithOwnerByTypeId(null,Auth::id());
        }

    	return view('pages.admin.client.list',compact('users'));
    }

    public function createForm(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }

        $carrier_list  = \App\Models\Carrier::where(['status'=>'1'])->get(); 
        $shipping_list = \App\Models\ShippingType::where(['status'=>'1'])->get();
        $country_list  = \App\Models\Country::where(['status'=>'1'])->get();
        $charges_list  = \App\Models\OtherCharge::where(['status'=>'1'])->get();
    	return view('pages.admin.client.create',compact('carrier_list','shipping_list','country_list', 'charges_list'));
    }

    public function ClientStore(Request $request){
        DB::beginTransaction();
        try{
            # code for add or edit client...
            if ($request->create_type == 'add_client') {
                # code...
                $validator_arr = [
                    'first_name' => 'required|max:50|min:2',
                    'last_name' => 'required|max:50|min:2',
                    'email' => 'required|max:50|min:2|email|unique:users',
                    'phone' => 'required|max:15|min:8',
                    'status' => 'required',
                ];
            } elseif ($request->create_type == 'edit_client') {
                # code...
                $validator_arr = [
                    'first_name' => 'required|max:50|min:2',
                    'last_name' => 'required|max:50|min:2',
                    'phone' => 'required|max:15|min:8',
                ];
            }

            if($request->hasFile('company_logo')){
                $validator_arr['company_logo'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
            }

            $validator = Validator::make($request->all(), $validator_arr);

            if ($validator->fails()) {
                return (new \Illuminate\Http\Response)->setStatusCode(422,$validator->errors()->first());
            }

            if ($request->create_type == 'add_client') {
                # code here...
                // $pass_word = randomPassword();
                $pass_word = 'password';

                $user = new User;
                $user->first_name = ucfirst($request->first_name);
                $user->last_name = ucfirst($request->last_name);
                $user->name = ucwords($request->first_name.' '.$request->last_name);
                $user->email = strtolower($request->email);
                $user->phone = $request->phone;
                $user->address = $request->address;
                $user->status = $request->status;
                $user->password = bcrypt($pass_word);
                $user->is_assigned = 'N';
                $user->user_type_id = 3;
                $user->created_by = Auth::id();
                $user->save();
                $id = $user->id;

                $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $id), '0', STR_PAD_LEFT) . $id;
                $user->save();

                # client logo...
                if($request->hasFile('company_logo')){
                    $extension = $request->file('company_logo')->getClientOriginalExtension();
                    $dir = 'uploads/';
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $request->file('company_logo')->move(public_path($dir), $filename);
                    $imgUrl = 'uploads/'. $filename;
                    $user->setMeta('_client_logo' , $imgUrl);
                }

                # send mail...
                $get_view_data['subject']    =   'Create Account!';
                $get_view_data['view']       =   'mails.account';
                $get_view_data['user']       =   $user;
                $get_view_data['password']   =   $pass_word;

                DB::commit();
                try {
                    Mail::to($user->email)->send(new MainTemplate( $get_view_data ));
                    return response()->json(['message'=>"Record has been created successfully.",'status'=>201],201);
                } catch (\Swift_TransportException $e) {
                    return response()->json(['message'=>"Record has been created successfully.",'status'=>201],201);
                }
            } elseif ($request->create_type == 'edit_client') {
                # code here...                
                $user = User::findOrFail($request->client_id);
                $user->first_name = ucfirst($request->first_name);
                $user->last_name = ucfirst($request->last_name);
                $user->name = ucwords($request->first_name.' '.$request->last_name);
                $user->address = $request->address;
                $user->phone = $request->phone;
                $user->status = $request->status;
                $user->save();
                $id = $user->id;

                $user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $user->id), '0', STR_PAD_LEFT) . $user->id;
                $user->save();
                
                # client logo...
                if($request->hasFile('company_logo')){
                    $extension = $request->file('company_logo')->getClientOriginalExtension();
                    $dir = 'uploads/';
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $request->file('company_logo')->move(public_path($dir), $filename);
                    $imgUrl = 'uploads/'. $filename;
                    $user->setMeta('_client_logo' , $imgUrl);
                }

                DB::commit();
                return response()->json(['message'=>"Record has been updated successfully.",'status'=>200],200);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['message'=>$e->getMessage(),'status'=>400],400);
        }
    }

    public function storeClient(Request $request){
    	$validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
            'email' => 'required|max:50|min:2|email|unique:users',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect(route('client.create'))->withErrors($validator)->withInput();
        }
        try{
            $full_name = explode(" ", $request->name);
            $first_name =  $full_name[0];
            array_shift($full_name);
        	$user = new User;
	        $user->first_name = ucfirst($first_name);
	        $user->last_name = ucfirst(join(" ",$full_name));
	        $user->name = ucwords($request->name);
	        $user->email = strtolower($request->email);
	        $user->phone = $request->phone;
	        $user->address = $request->address;
	        $user->status = $request->status;
	        $user->password = bcrypt(Config('constants.defaultPassword'));
	        $user->is_assigned = 'N';
	        $user->user_type_id = 3;
	        $user->created_by = Auth::id();
	        $user->save();
        }catch(Exception $e){
        	return back()->withError($e->getMessage())->withInput();
        }
        return redirect(route('admin.client'))->with('success','Client has been created successfully');
    }

    public function editClient(User $user){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $address_details = $user->getMeta('address_details');
        $warehouses = \App\Models\Warehouse::where(['user_id'=>$user->id])->get();
        $shipments = \App\Models\ShippingPolicy::where(['user_id'=>$user->id, 'type' => 'shipment'])->get();
        $charges = \App\Models\ShippingPolicy::where(['user_id'=>$user->id, 'type' => 'charges'])->get();

        $carrier_list  = \App\Models\Carrier::where(['status'=>'1'])->get(); 
        $shipping_list = \App\Models\ShippingType::where(['status'=>'1'])->get();
        $country_list  = \App\Models\Country::where(['status'=>'1'])->get();
        $charges_list  = \App\Models\OtherCharge::where(['status'=>'1'])->get();
        $currency_list = available_currency();
        
        return view('pages.admin.client.edit-client',compact('user','address_details','warehouses','shipments','carrier_list','shipping_list','country_list', 'charges_list', 'charges','currency_list'));
    }

    public function updateClient(User $user){
    	$this->validate(request(), [
            'name' => 'required|max:50|min:2',
            'email' => 'required|max:50|min:2|email',
            'phone' => 'required|max:15|min:8',
            'address' => 'required|max:191',
            'status' => 'required',
        ]);
        
        try{
            $full_name = explode(" ", request('name'));
            $first_name =  $full_name[0];
            array_shift($full_name);
	        $user->first_name = ucfirst($first_name);
            $user->last_name = ucfirst(join(" ",$full_name));
	        $user->name = ucwords(request('name'));
	        $user->email = strtolower(request('email'));
	        $user->phone = request('phone');
	        $user->address = request('address');
	        $user->status = request('status');
	        $user->save();
        }catch(Exception $e){
        	return back()->withError($e->getMessage())->withInput();
        }
        return back()->with('success','Client has been updated successfully');
    }

    public function clientDestory($id){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        DB::beginTransaction();
        try{
            $client = User::where(['id'=>$id,'user_type_id'=>3]);
            $user_row = $client->first();
            if($user_row){
                $mapping = \App\Models\UserOwnerMapping::Where(['owner_id'=>$user_row->id])->get();

                \App\Models\UserOwnerMapping::where(['user_id'=>$user_row->id])->orWhere(['owner_id'=>$user_row->id])->delete();
                if($mapping->count()){
                    foreach($mapping as $row){
                        $user = User::find($row->user_id);
                        $user->is_assigned = 'N';
                        $user->save();
                    }
                }

                if($client->delete()){
                   DB::commit();
                    return back()->with('success','Client has been deleted successfully');
                }
            }else{
                return back()->with('error','Record not found');
            }

        }catch(Exception $e){
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
        
    }

    public function getClientOrders(){
        return view('pages.admin.client.client-orders');
    }

    public function getClientProfile(){
        $user = User::find(Auth::id());
        $address_details = $user->getMeta('address_details');
        $warehouses = \App\Models\Warehouse::where(['user_id'=>$user->id])->get();
        $shipments = \App\Models\ShippingPolicy::where(['user_id'=>$user->id, 'type' => 'shipment'])->get();
        $charges = \App\Models\ShippingPolicy::where(['user_id'=>$user->id, 'type' => 'charges'])->get();

        $carrier_list  = \App\Models\Carrier::where(['status'=>'1'])->get(); 
        $shipping_list = \App\Models\ShippingType::where(['status'=>'1'])->get();
        $country_list  = \App\Models\Country::where(['status'=>'1'])->get();
        $charges_list  = \App\Models\OtherCharge::where(['status'=>'1'])->get();
        return view('pages.admin.client.profile',compact('user','address_details','warehouses','shipments','carrier_list','shipping_list','country_list', 'charges_list', 'charges'));   
    }

    public function getCountryList()
    {
        # code...
        $country_list  = \App\Models\Country::where(['status'=>'1'])->get();
        $html = '';        
        if(count($country_list) > 0){
            $html .= '<option value="">Select</option>';
            foreach ($country_list as $country) {
                # code...                
                $html .= '<option value="'.$country->id.'">'.$country->name.'</option>';
            }
        }else{
            $html = '<option value="">Please add country</option>';
        }

        return $html;
    }

    public function getCarrierList()
    {
        # code...
        $carrier_list  = \App\Models\Carrier::where(['status'=>'1'])->get();
        $html = '';        
        if(count($carrier_list) > 0){
            $html .= '<option value="">Select</option>';
            foreach ($carrier_list as $carrier) {
                # code...                
                $html .= '<option value="'.$carrier->id.'">'.$carrier->name.'</option>';
            }
        }else{
            $html = '<option value="">Please add carrier</option>';
        }

        return $html;
    }

    public function getShipmentList()
    {
        # code...
        $shipping_list  = \App\Models\ShippingType::where(['status'=>'1'])->get();
        $html = '';        
        if(count($shipping_list) > 0){
            $html .= '<option value="">Select</option>';
            foreach ($shipping_list as $shipments) {
                # code...                
                $html .= '<option value="'.$shipments->id.'">'.$shipments->name.'</option>';
            }
        }else{
            $html = '<option value="">Please add shipments</option>';
        }

        return $html;
    }

    public function getchargesList()
    {
        # code...
        $charges_list  = \App\Models\OtherCharge::where(['status'=>'1'])->get();
        $html = '';        
        if(count($charges_list) > 0){
            $html .= '<option value="">Select</option>';
            foreach ($charges_list as $charges) {
                # code...                
                $html .= '<option value="'.$charges->id.'">'.$charges->name.'</option>';
            }
        }else{
            $html = '<option value="">Please add other charges</option>';
        }

        return $html;
    }

}
