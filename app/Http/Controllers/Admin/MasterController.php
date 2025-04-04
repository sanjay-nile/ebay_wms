<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\CarrierProduct;
use App\Models\CarrierService;
use App\Models\Country;


use Auth;
use Validator;

class MasterController extends Controller
{
    public $_type = [
        4,5
    ];

	public function __construct(){
		$this->middleware('auth:admin');
	}
    
    public function shippingTypeList(){
    	if(in_array(Auth::user()->user_type_id, $this->_type)){
            return redirect(getDashboardUrl()['dashboard']); 
        }
        $shipping_list = \App\Models\ShippingType::all();
        return view('pages.admin.master.shipping-type-list',compact('shipping_list'));
    }

    /**
    * carrier list
    *
    **/
    public function carrierList($id = null){
    	if(in_array(Auth::user()->user_type_id, $this->_type)){ 
            return redirect(getDashboardUrl()['dashboard']); 
        }

        $carrier_list = Carrier::all();

        $single_c = '';
        if (!empty($id)) {
            $single_c = Carrier::find($id);
        }
        $country_list = Country::where(['status' => '1'])->get();

        return view('pages.admin.master.carrier',compact('carrier_list', 'single_c','country_list'));
    }

    public function otherChargesList(){
        if(in_array(Auth::user()->user_type_id, $this->_type)){ 
            return redirect(getDashboardUrl()['dashboard']); 
        }
        $other_charges_list = \App\Models\OtherCharge::all();
        return view('pages.admin.master.other-charges-list',compact('other_charges_list'));
    }

    public function stateList(Request $request){
        if(Auth::user()->user_type_id != 1){
            return redirect(getDashboardUrl()['dashboard']); 
        }

        $state_list = \App\Models\State::paginate(50);
        if(isset($request->name) && !empty($request->name)){
            $state_list = \App\Models\State::where('name', 'like', '%'.$request->name.'%')->paginate(50);
        }
        
        $country_list = \App\Models\Country::all();
        return view('pages.admin.master.state-list',compact('state_list', 'country_list'));
    }

    public function shippingTypePost(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first('name')]);
        }
        
        if(isset($request->id) && !empty($request->id)){
            $id = \App\Models\ShippingType::where('id', $request->id)->update(['name' => $request->name, 'status' => $request->status]);
        } else{
            $shipment = new \App\Models\ShippingType;
            $shipment->name = $request->name;
            $shipment->status = $request->status;
            $id = $shipment->save();
        }

        if($id){
            return response()->json(['status'=>true,'msg'=>"Shipment has been created successfully"]);
        }else{
            return response()->json(['status'=>false,'msg'=>"Please try agian, Your request not completed"]);
        }
    }

    public function carrierPost(Request $request){
        /*$validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first('name')]);
        }
        
        if(isset($request->id) && !empty($request->id)){
            $id = \App\Models\Carrier::where('id', $request->id)->update(['name' => $request->name, 'status' => $request->status]);
        } else{
            $carrier = new \App\Models\Carrier;
            $carrier->name = $request->name;
            $carrier->status = $request->status;
            $id = $carrier->save();
        }

        if($id){
            return response()->json(['status'=>true,'msg'=>"Carrier has been created successfully"]);
        }else{
            return response()->json(['status'=>false,'msg'=>"Please try agian, Your request not completed"]);
        }*/

        try {
            $data  = $request->all();
            // dd($data);
            if (isset($request->id) && !empty($request->id)) {
                $rules = [
                    'name' => 'required',
                    'code' => 'required'
                ];

                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator);
                }

                $arr = [
                    'name' => $request->name,
                    'code' => $request->code,
                ];
                if ($request->has('status') && $request->filled('status')) {
                    $arr['status'] = $request->status;
                }
                $id = Carrier::where('id', $request->id)->update($arr);

                if($request->has('cp_name') && !empty($data['cp_name'][0])){
                    foreach ($data['cp_name'] as $key => $value) {
                        # code...
                        $cp_obj = CarrierProduct::where('id', $data['cp_id'][$key])->update(['name' => $data['cp_name'][$key], 'code' => $data['cp_code'][$key]]);
                    }
                }

                if($request->has('csc_name') && !empty($data['csc_name'][0])){
                    foreach ($data['csc_name'] as $key => $value) {
                        # code...
                        $csc_obj = CarrierService::where('id', $data['csc_id'][$key])->update(['name' => $data['csc_name'][$key], 'code' => $data['csc_code'][$key]]);
                    }
                }

                $msg = 'Carrier has been updated successfully';
            } else {
                $rules = [
                    'name' => 'required',
                    'code' => 'required',
                    'selectcountry'=> 'required',
                    'unit_type'=>'required',
                    'client_code'=>'required'
                ];

                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator);
                }

                $country = null;
                if($request->has('selectcountry') && $request->has('selectcountry')){
                    $country = implode(',', $request->selectcountry);
                }
                $carrier_obj       = new Carrier;
                $carrier_obj->name = $request->name;
                $carrier_obj->code = $request->code;
                $carrier_obj->countrycode = $country;
                $carrier_obj->unit_type = $request->unit_type;
                $carrier_obj->ClientCode = $request->client_code;
                if ($request->has('status') && $request->filled('status')) {
                    $carrier_obj->status = $request->status;
                }
                $carrier_obj->save();
                $id  = $carrier_obj->id;

                if($request->has('cp_name') && !empty($data['cp_name'][0])){
                    foreach ($data['cp_name'] as $key => $value) {
                        # code...
                        $cp_obj = new CarrierProduct;
                        $cp_obj->name = $data['cp_name'][$key];
                        $cp_obj->code = $data['cp_code'][$key];
                        $cp_obj->carrier_id = $id;
                        $cp_obj->save();
                    }
                }

                if($request->has('csc_name') && !empty($data['csc_name'][0])){
                    foreach ($data['csc_name'] as $key => $value) {
                        # code...
                        $csc_obj = new CarrierService;
                        $csc_obj->name = $data['csc_name'][$key];
                        $csc_obj->code = $data['csc_code'][$key];
                        $csc_obj->carrier_id = $id;
                        $csc_obj->save();
                    }
                }
                
                $msg = 'Carrier has been saved successfully';
            }

            if ($id) {
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Please try again, Your request not completed");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function otherChargesPost(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first('name')]);
        }
        
        if(isset($request->id) && !empty($request->id)){
            $id = \App\Models\OtherCharge::where('id', $request->id)->update(['name' => $request->name, 'status' => $request->status]);
        } else{
            $shipment = new \App\Models\OtherCharge;
            $shipment->name = $request->name;
            $shipment->status = $request->status;
            $id = $shipment->save();
        }

        if($id){
            return response()->json(['status'=>true,'msg'=>"Other Charge has been created successfully"]);
        }else{
            return response()->json(['status'=>false,'msg'=>"Please try agian, Your request not completed"]);
        }
    }

    public function statePost(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:2',
            'shortname' => 'required|max:50|min:2',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first('name')]);
        }
        
        if(isset($request->id) && !empty($request->id)){
            $id = \App\Models\State::where('id', $request->id)->update([
                'name' => $request->name,
                'shortname' => $request->shortname,
                'country_id' => $request->country_id,
                'status' => $request->status
            ]);
        } else{
            $state = new \App\Models\State;
            $state->name = $request->name;
            $state->shortname = $request->shortname;
            $state->country_id = $request->country_id;
            $state->status = $request->status;
            $id = $state->save();
        }

        if($id){
            return response()->json(['status'=>true,'msg'=>"State has been saved successfully"]);
        }else{
            return response()->json(['status'=>false,'msg'=>"Please try agian, Your request not completed"]);
        }
    }

    public function shippingDestroy($id){
        if($id){
            $dlt = \App\Models\ShippingType::find($id)->delete();
            if($dlt){
                return redirect()->back()->with('success', 'successfully deleted');
            }
        }

        return redirect()->back();
    }

    public function carrierDestroy($id){
        if($id){
            $dlt = \App\Models\Carrier::find($id)->delete();
            if($dlt){
                return redirect()->back()->with('success', 'successfully deleted');
            }
        }

        return redirect()->back();
    }

    public function otherChargesDestroy($id){
        if($id){
            $dlt = \App\Models\OtherCharge::find($id)->delete();
            if($dlt){
                return redirect()->back()->with('success', 'successfully deleted');
            }
        }

        return redirect()->back();
    }

    public function stateDestroy($id){
        if($id){
            $dlt = \App\Models\State::find($id)->delete();
            if($dlt){
                return redirect()->back()->with('success', 'successfully deleted');
            }
        }

        return redirect()->back();
    }

    public function shippingTypeCreate(Request $request){
        $view = view('modal.add-shipment-form',array('data'=>$request->all()))->render();
        print_r($view);
    }
}
