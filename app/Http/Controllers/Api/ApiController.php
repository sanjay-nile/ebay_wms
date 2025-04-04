<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendPushNotificationController as PushNotification;

use App\Models\ReverseLogisticWaybill;
use App\Models\PackageDetail;
use App\Models\StatusHistory;
use App\Models\Warehouse;
use App\Models\ShippingPolicy;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\PvxWebhook;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

use Input;
use DB;
use Config;
use App\User;
use Auth;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

use App\Helpers\ProcessMessageHelper;

class ApiController extends Controller
{
    protected $notification;
    public $upload_path;

    public const HEADER_KEY = 'RG-ASDF2365410';
    public const HEADER_KEY_PROD = 'RGP-JFKUFYNFKYTY898765QWEY';
    public const HEADER_NAME = 'api-key';
    public const AUTH = 'logixerp';

    public function __construct()
    {
        # code...
        $this->notification = new PushNotification;

        $this->upload_path = \Config::get('constants.upload_path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777, true, true );
    }

    /**
    * Update tracking id from the logixerp
    *
    **/
    public function updateOrderTrakingId(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                if(isset($data['waybillNumber']) && !empty($data['waybillNumber'])){
                    $order_id = $data['waybillNumber'];
                    $waybill_obj = ReverseLogisticWaybill::where(['id' => $order_id])->first();
                    // $waybill_obj = ReverseLogisticWaybill::where(['way_bill_number' => $data['waybillNumber']])->first();

                    if(!$waybill_obj){                        
                        return response()->json(['success'=>false,'data'=>[],'message'=>'Not a valid way bill number'],400);
                    }                    
                    $old_value = [];
                    if($waybill_obj->hasMeta('_order_tracking_id')){
                        $old_value = json_decode($waybill_obj->getMeta('_order_tracking_id'));
                        array_push($old_value, $data);                        
                    }else{
                    	array_push($old_value, $data); 
                    }
                    
                    if ($waybill_obj->setMeta('_order_tracking_id', json_encode($old_value))) {
                        $waybill_obj->setMeta('_order_current_status', $data['status']);

                        # send notification...
                        $message = 'ALERT: Dear '.\Config::get('app.name').' Customer your waybill number:#'.$waybill_obj->way_bill_number.'tracking status has been chnaged in to:'.$data['status'];
                        $mail = $waybill_obj->getMeta('_customer_email');
                        $eqtor_response = array();
                        
                        return response()->json(['success'=>true,'data'=>[],'message'=>'Traking status update successfully','eqtor_response'=>$eqtor_response], 200);
                    }                    
                } else{
                    return response()->json(['success'=>false,'data'=>[],'message'=>'Parameter Invalid'],400);
                }
            }else{
                
                return response()->json(['success'=>false,'data'=>[],'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {
            
            return response()->json(['success'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }

    public function getOrderTracking(Request $request){
        $validator = Validator::make($request->all(), [
            'way_bill_number' => 'required|max:200',
        ]);

        if ($validator->fails()) {
            return response() ->json(['message'=>$validator->errors()->first(),'status'=>false,'data'=>[]],422);
        }

        if(Config('constants.reverseGearSecureKey')!=$request->secureKey){
            return response()->json(['status'=>false,'data'=>[],'message'=>"Secure Key is not valid"],400);
        }

        $data = ReverseLogisticWaybill::where(['way_bill_number'=>$request->way_bill_number])->first();
        if(!$data){
            return response()->json(['status'=>false,'data'=>[],'message'=>"way bill number is not valid"],400);
        }

        if(!$data->hasMeta('_order_tracking_id')){
            return response()->json(['status'=>false,'data'=>[],'message'=>"Your tracking not generated yet"],200);
        }
        
        $tracking_detail = [];
        $track = json_decode($data->getMeta('_order_tracking_id'));
        $new_array = [];
        $track_id = '';
        foreach($track as $t){
            $d = date('Y-m-d', strtotime($t->modifiedOn));
            $track_id = $t->carrierWaybillNumber;
            if (!isset($new_array[$d])) {
                $at = ['modifiedOn' => $t->modifiedOn, 'status' => $t->status, 'remark' => $t->remarks];
                $new_array[$d]['cnt'] = [$at];
                $new_array[$d]['dt'] = date('l, d F', strtotime($t->modifiedOn));
            } else{
                $at = ['modifiedOn' => $t->modifiedOn, 'status' => $t->status, 'remark' => $t->remarks];
                array_push($new_array[$d]['cnt'], $at);
            }
        }
        $new_array = array_values($new_array);
        $tracking_detail['_order_tracking_id'] = $track_id;
        $tracking_detail['_order_tracking_content'] = $new_array;
            

        return response()->json(['status'=>true,'data'=>$tracking_detail,'message'=>'Success'],200);
    }

    /*
    * Happy returns package status
    **/
    public function updatePackageStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }
                
                if($headers == self::HEADER_KEY || $headers == self::HEADER_KEY_PROD){
                    if(isset($data['order_number']) && !empty($data['order_number'])){
                        $user_obj = User::where(['email' => $data['email']])->first();
                        if(!$user_obj){
                            return response()->json(['success'=>false,'message'=>'Not a valid user email address'],400);
                        }

                        $waybill_obj = ReverseLogisticWaybill::where(['way_bill_number' => $data['order_number']])->first();
                        if(!$waybill_obj){
                            return response()->json(['success'=>false,'message'=>'Not a valid way order number'],400);
                        }

                        if(!isset($data['returning']) && count($data['returning']) <= 0){
                            return response()->json(['success'=>false,'message'=>'Not a valid returning data'],400);
                        }

                        $status = false;
                        $msg = '';
                        foreach ($data['returning'] as $key => $value) {
                            # code...
                            $p_id = explode('-', $value['package_id']);
                            $package = PackageDetail::where(['id' => reset($p_id)])->first();
                            if(!$package){
                                $status = true;
                                $msg = 'Not a valid package id:'.$value['package_id'];
                                goto end;
                            }
                            $count = $package->hiting_count + 1;
                            $dt = date('Y-m-d');
                            $package_up = PackageDetail::where(['id' => $value['package_id']])->update(['return_status' => $value['return_status'], 'hiting_count' => $count, 'package_count' => $count, 'rcvd_date_at_returnbar' => $dt]);
                        }

                        end:
                        if($status){
                            return response()->json(['success'=>false,'message'=>$msg],400);
                        }

                        return response()->json(['success'=>true,'message'=>'Return Status saved successfully.'], 200);
                    } else{
                        return response()->json(['success'=>false,'message'=>'Parameter Invalid'],400);
                    }
                } else {
                	return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }                
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /*
    * olive get receive at hub orders
    **/
    public function receiveAtHubOrder(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                if(isset($data['client_id']) && !empty($data['client_id'])){
                    $request->request->add(['status'=>'Success']);
                    $request->request->add(['process_status'=>'processed']);

                    $obj = new ReverseLogisticWaybill;
                    $lists = $obj->getOrdersForApi($request);

                    $lists->map(function ($product) {
                        $product->metadata = $product->getMetas();
                        return $product;
                    });

                    // dd($product->getMetas()->toArray());
                    // dd($lists);

                    return response()->json(['success'=>true, 'data' => $lists->toArray()], 200);
                } else{
                    return response()->json(['success'=>false,'message'=>'Parameter Invalid'],400);
                }
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /*
    * Olive returns package status
    **/
    public function updateOrderStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                if(isset($data['way_bill_number']) && !empty($data['way_bill_number'])){
                    
                    $waybill_obj = ReverseLogisticWaybill::where(['way_bill_number' => $data['way_bill_number']])->first();
                    if(!$waybill_obj){
                        return response()->json(['success'=>false,'message'=>'Not a valid way order number'],400);
                    }

                    if(!isset($data['returning']) && count($data['returning']) <= 0){
                        return response()->json(['success'=>false,'message'=>'Not a valid returning data'],400);
                    }

                    $status = false;
                    $msg = '';
                    foreach ($data['returning'] as $key => $value) {
                        # code...
                        $package = PackageDetail::where(['id' => $value['package_id']])->first();
                        if(!$package){
                            $status = true;
                            $msg = 'Not a valid package id:'.$value['package_id'];
                            goto end;
                        }
                        $package_up = PackageDetail::where(['id' => $value['package_id']])->update(['return_status' => $value['return_status']]);
                    }

                    end:
                    if($status){
                        return response()->json(['success'=>false,'message'=>$msg],400);
                    }

                    return response()->json(['success'=>true,'message'=>'Return Status saved successfully.'], 200);
                } else{
                    return response()->json(['success'=>false,'message'=>'Parameter Invalid'],400);
                }
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /**
    * Update waywill status from the logixerp
    *
    **/
    public function updateWaywillStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $order = $request->all();
                // dd($order);
                    
                if (!isset($order['waybillNumber'])) {
                    return response()->json(['success'=>false,'data'=>[],'message'=>'Not a valid way bill number'],400);
                }

                $waywill = str_replace('MG', '', $order['waybillNumber']);
                $waybill_obj = ReverseLogisticWaybill::where(['rg_reference_number' => $order['waybillNumber']])->first();
                if(!$waybill_obj){
                    $waybill_obj = ReverseLogisticWaybill::where(['id' => $waywill])->first();
                }

                if(!$waybill_obj){
                    return response()->json(['success'=>false,'data'=>[],'message'=>'Not a valid way bill number'],400);
                }

                if(!$waybill_obj->hasMeta('_order_waywill_status') && !empty($order['carrierWaybill'])){
                    $final_dt = [$order];
                    $dt = date('Y-m-d');
                    $status = '';
                    $waybill_obj->setMeta([
                        '_order_waywill_status' => $order['docketStatusType'],
                        '_order_waywill_data' => json_encode($final_dt),
                        '_order_waywill_date' => $order['date'],
                        '_order_waywill_time' => $order['time']
                    ]);

                    /*if(!empty($waybill_obj->getMeta('_order_waywill_status_date')) && $waybill_obj->getMeta('_order_waywill_date') != $order['date'] && $waybill_obj->getMeta('_order_waywill_time') != $order['time'] && in_array($order['docketStatusType'], ['In Transit', 'INTRANSIT', 'InTransit'])){
                        $waybill_obj->setMeta([
                            '_order_waywill_in_transit' => $order['date']
                        ]);
                        $status = 'In Transit';
                    }

                    if(empty($waybill_obj->getMeta('_order_waywill_status_date')) && in_array($order['docketStatusType'], ['In Transit', 'INTRANSIT', 'InTransit'])){
                        $waybill_obj->setMeta([
                            '_order_waywill_status_date' => $order['date']
                        ]);
                        $status = 'First Scan';
                    }*/

                    // if(empty($waybill_obj->getMeta('_order_waywill_first_scan')) && $order['docketStatusType'] == 'At Origin'){
                    //     $waybill_obj->setMeta([
                    //         '_order_waywill_first_scan' => $dt
                    //     ]);
                    // }

                    /*if(empty($waybill_obj->getMeta('_order_waywill_deliverd')) && $order['docketStatusType'] == 'DELIVERED'){
                        $waybill_obj->setMeta([
                            '_order_waywill_deliverd' => $order['date']
                        ]);
                        $status = 'Delivered';
                    }*/

                    // $waybill_obj->cancel_return_status = $order['status'];
                    /*$waybill_obj->inscan_status = $status;
                    $waybill_obj->save();*/
                } else {
                    $status_data = json_decode($waybill_obj->getMeta('_order_waywill_data'));
                    // dd($status_data);
                    array_push($status_data, $order);
                    $status = '';
                    $dt = date('Y-m-d');
                    $waybill_obj->setMeta([
                        '_order_waywill_status' => $order['docketStatusType'],
                        '_order_waywill_data' => json_encode($status_data),
                        '_order_waywill_date' => $order['date'],
                        '_order_waywill_time' => $order['time']
                    ]);

                    /*if(!empty($waybill_obj->getMeta('_order_waywill_status_date')) && $waybill_obj->getMeta('_order_waywill_date') != $order['date'] && $waybill_obj->getMeta('_order_waywill_time') != $order['time'] && in_array($order['docketStatusType'], ['In Transit', 'INTRANSIT', 'InTransit'])){
                        $waybill_obj->setMeta([
                            '_order_waywill_in_transit' => $order['date']
                        ]);
                        $status = 'In Transit';
                    }

                    if(empty($waybill_obj->getMeta('_order_waywill_status_date')) && in_array($order['docketStatusType'], ['In Transit', 'INTRANSIT', 'InTransit'])){
                        $waybill_obj->setMeta([
                            '_order_waywill_status_date' => $order['date']
                        ]);
                        $status = 'First Scan';
                    }
                    
                    if(empty($waybill_obj->getMeta('_order_waywill_deliverd')) && $order['docketStatusType'] == 'DELIVERED'){
                        $waybill_obj->setMeta([
                            '_order_waywill_deliverd' => $order['date']
                        ]);

                        $status = 'Delivered';
                    }*/

                    // $waybill_obj->cancel_return_status = $order['status'];
                    // $waybill_obj->inscan_status = $status;
                    // $waybill_obj->save();
                }

                return response()->json(['success'=>true,'data'=>[],'message'=>'Waywill status update successfully'], 200);
            }else{                
                return response()->json(['success'=>false,'data'=>[],'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }

    /*public function updateWaywillStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                // dd($data);
                if(isset($data['auth']) && !empty($data['auth']) && $data['auth'] == 'logixerp'){
                    # frist index from array...
                    $order = reset($data['waybilldetail']);
                    
                    if (!isset($order['waybillnumber'])) {
                        # code...
                        return response()->json(['success'=>false,'data'=>[],'message'=>'Not a valid way bill number'],400);
                    }

                    $waywill = str_replace('MG', '', $order['waybillnumber']);
                    // $arr_str = explode('-', $waywill);
                    // $waywill_id = end($arr_str);
                    
                    $waybill_obj = ReverseLogisticWaybill::where(['rg_reference_number' => $order['waybillnumber']])->first();

                    if(!$waybill_obj){
                        $waybill_obj = ReverseLogisticWaybill::where(['id' => $waywill])->first();
                    }

                    if(!$waybill_obj){
                        return response()->json(['success'=>false,'data'=>[],'message'=>'Not a valid way bill number'],400);
                    }

                    # for olive case..
                    if ($waybill_obj->hasMeta('_order_type') && $waybill_obj->meta->_order_type == 'Olive') {
                        return response()->json(['success'=>true,'data'=>[],'message'=>'Waywill status update successfully'], 200);
                    }

                    if(!$waybill_obj->hasMeta('_order_waywill_status') && !empty($order['carrierLabel']) && $order['status'] != 'At Origin'){
                        $waybill_obj->setMeta('_order_waywill_status', $order['carrierLabel']);
                        $waybill_obj->setMeta('_order_waywill_data', json_encode($data));
                        $dt = date('Y-m-d');
                        $waybill_obj->setMeta('_order_waywill_status_date', $dt);
                        $waybill_obj->setMeta('_order_waywill_status_response', json_encode($order));

                        // $waybill_obj->cancel_return_status = $order['status'];
                        $waybill_obj->inscan_status = $order['carrierLabel'];
                        $waybill_obj->save();

                        # send mail to customer...
                        $get_view_data['subject']    =   'Missguided Return Update :-'.$waybill_obj->way_bill_number;
                        $get_view_data['view']       =   'mails.missguided-status-order';
                        $get_view_data['user']       =   [
                            'name' =>  $waybill_obj->meta->_customer_name,
                            'order_no' => $waybill_obj->way_bill_number,
                            'track_id' => $waybill_obj->tracking_id ?? 'N/A',
                            'return_date' => date('d/m/Y', strtotime($waybill_obj->created_at)),
                            'return_service' => $waybill_obj->meta->_carrier_name ?? 'Hermes',
                            'return_cost' => $waybill_obj->meta->_rtn_total ?? 0,
                            'status' => $order['status'],
                        ];                        

                        # send in to order return sqs...
                        (new ProcessMessageHelper())->returnOrderStaus($waybill_obj->way_bill_number);

                        # sending mail here...
                        try{
                            $mail = Mail::to($waybill_obj->meta->_customer_email)->send(new MainTemplate( $get_view_data ));
                            return response()->json(['success'=>true,'data'=>[],'message'=>'Waywill status update successfully'], 200);
                        } catch(\Swift_TransportException $transportExp){
                            //$transportExp->getMessage();
                            return response()->json(['success'=>true,'data'=>[],'message'=>'Waywill status update successfully'], 200);
                        }                        
                    } else {
                        return response()->json(['success'=>false,'data'=>[],'message'=>'May be carrier label not found or status already updated.'],400);
                    }
                } else{
                    return response()->json(['success'=>false,'data'=>[],'message'=>'Parameter Invalid'],400);
                }
            }else{
                
                return response()->json(['success'=>false,'data'=>[],'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }*/

    /**
    * All return orders
    **/
    public function getAllReturnsOrders(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();                
                if(isset($data['from']) && !empty($data['from'])){
                    $obj = new ReverseLogisticWaybill;
                    $lists = $obj->getApiAllReturnOrders($request);
                    $final_data = [];
                    if($lists->count() > 0){
                        foreach ($lists as $key => $row) {
                            # code...
                            foreach ($row->packages as $pakage) {
                                # code...
                                $arr = [
                                    'OrderId' => $row->id,
                                    'ReturnId' => $row->way_bill_number,
                                    'ReturnDate' => date('Y-M-d',strtotime($row->created_at)).'T'.date('H:m:sZ',strtotime($row->created_at)),
                                    'SKU' => $pakage->bar_code,
                                    'SKUDescription' => $pakage->title,
                                    'ReturnQty' => $pakage->package_count,
                                    'ItemCountryOfOrigin' => '',
                                    'ReasonID' => $pakage->return_reason,
                                    'Reason' => $pakage->note,
                                    'Comments' => '',
                                    'TranslatedComments' => '',
                                    'ReturningCountry' => $row->meta->_customer_country,
                                ];

                                array_push($final_data, $arr);
                            }
                        }

                        return response()->json(['status'=>true,'data'=> $final_data, 'message'=>'Successfully'], 200);
                    } else{
                        return response()->json(['status'=>false,'data'=>[],'message'=>'No order found.'],200);
                    }
                } else {
                    return response()->json(['status'=>false,'data'=>[],'message'=>'Please enter the From Date.'],400);
                }
            }else{                
                return response()->json(['status'=>false,'data'=>[],'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {
            
            return response()->json(['status'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }   
    }


    /**
     * code by sanjay - 11-nov-2021
     * added warehouse to the client admin
     **/
    public function addWarehouse(Request $request){
        try {
            if ($request->isMethod('post')) {

                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                $validator_array = [
                    'client_id' => 'required',
                    'warehouse_name' => 'required|max:50|min:2',
                    'contact_person' => 'required|max:50|min:2',
                    'email' => 'required|max:50|min:2',
                    'phone' => 'required|max:20|min:8',
                    'address' => 'required|max:50|min:2',
                    'country_id' => 'required',
                    'state_code' => 'required|max:50',
                    'city' => 'required|max:50',
                    'postal_code' => 'required|max:15',
                ];
                
                $validator = Validator::make($request->all(), $validator_array);

                if ($validator->fails()) {
                    return response()->json(['status'=>false,'msg'=>$validator->errors()->first()],400);
                }

                $status = 201;
                $msg = 'Warehouse has been created successfully';
                $ware_obj = new \App\Models\Warehouse; 
                $ware_obj->user_id = $request->client_id;
                $ware_obj->name = $request->warehouse_name;
                $ware_obj->contact_person = $request->contact_person;
                $ware_obj->email = $request->email;
                $ware_obj->country_id = $request->country_id;
                $ware_obj->state = get_state_code_by_name($request->state_code);
                $ware_obj->state_code = $request->state_code;
                $ware_obj->city = $request->city;
                $ware_obj->zip_code = $request->postal_code;
                $ware_obj->address = $request->address;
                $ware_obj->phone = $request->phone;
                $ware_obj->status = '1';
                $ware_obj->save();
                $id = $ware_obj->id;

                $ship_obj = new \App\Models\ShippingPolicy;
                $ship_obj->shipping_type_id = 1;
                $ship_obj->user_id = $request->client_id;
                $ship_obj->carrier_id = 1;
                $ship_obj->rate = 0;
                $ship_obj->currency = 'USD';
                $ship_obj->status = '1';
                $ship_obj->is_default = 1;
                $ship_obj->type = 'shipment';
                $ship_obj->save();

                if($id){
                    return response()->json(['status'=>true,'msg'=>$msg,'id'=>$id],$status);
                }else{
                    return response()->json(['status'=>false,'msg'=>"Please try again, Your request not completed"]);
                }
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }

    /**
     * code by sanjay - 11-nov-2021
     * add client orders here
     **/
    public function createReturnOrders(Request $request){
        try {
            if ($request->isMethod('post')) {
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                $validator_array = [
                    'client_id' => 'required',
                    'way_bill_number' => 'required',
                    'order_email' => 'required',
                    'customer_name' => 'required',
                    'customer_email' => 'required',
                    'customer_phone' => 'required',
                    'customer_address' => 'required',
                    'customer_country_code' => 'required',
                    'customer_state_code' => 'required',
                    'customer_city' => 'required',
                    'customer_postcode' => 'required',
                ];
                
                $validator = Validator::make($request->all(), $validator_array);

                if ($validator->fails()) {
                    return response()->json(['status'=>false,'msg'=>$validator->errors()->first()],400);
                }

                $request->request->add(['login_id' => 1]);
                $request->request->add(['type' => 'I']);
                $request->request->add(['created_from' => 'RG']);
                $request->request->add(['return_by' => ReverseLogisticWaybill::RG_CUSTOMER]);
                $request->request->add(['way_bill_number' => $request->way_bill_number]);
                $request->request->add(['consignee_name' => '']);
                $request->request->add(['shipment_name' => '']);
                $request->request->add(['carrier_name' => '']);
                $request->request->add(['unit_type' => 'KGS']);
                $request->request->add(['warehouse_id' => $request->warehouse_id]);
                $request->request->add(['shipment_id' => '']);
                $request->request->add(['client_code' => '00000']);
                $request->request->add(['customer_code' => '00000']);
                $request->request->add(['actual_weight' => '1']);
                $request->request->add(['charged_weight' => '1']);

                # warehouse...
                $war_obj = Warehouse::where('assigned_country', "Like", "%" . $request->customer_country_code . "%")->where('user_id', $request->client_id)->first();
                if($war_obj){
                    $country = Country::where('id',$war_obj->country_id)->first();
                    $request->request->add(['warehouse_id' => $war_obj->id]);
                    $request->request->add(['consignee_name' => $war_obj->name]);
                    $request->request->add(['ConsigneePhone' => $war_obj->phone]);
                    $request->request->add(['ConsigneeAddress' => $war_obj->address]);
                    $request->request->add(['ConsigneeCountry' => $country->sortname]);
                    $request->request->add(['ConsigneeState' => $war_obj->state_code]);
                    $request->request->add(['ConsigneeCity' => $war_obj->city]);
                    $request->request->add(['ConsigneePincode' => $war_obj->zip_code]);
                    $request->request->add(['ConsigneeEmail' => $war_obj->email]);
                    $request->request->add(['FromOU' => $war_obj->FromOU]);
                } else{
                    return response()->json(['message' => 'No Warehouse found.', 'status' => 200], 200);
                }

                # shipment..
                $ship_obj = ShippingPolicy::where(['user_id' => $request->client_id, 'type' => 'shipment', 'is_default' => 1])->first();
                if ($ship_obj) {
                    # code...
                    $request->request->add(['shipment_id' => $ship_obj->id]);
                    $request->request->add(['shipment_name' => $ship_obj->shippingType->name]);
                    $request->request->add(['carrier_name' => $ship_obj->carrier->name]);
                } else {
                    return response()->json(['message' => 'No Shipment found.', 'status' => 200], 200);
                }

                # carrier...
                if($request->customer_country_code == 'AU'){
                    $carrier = Carrier::where('countrycode', "Like", "%" . $request->customer_country_code . "%")->where('name', "Like" , "%" .'AUSPOST'. "%")->first();
                    $request->request->add(['ConsigneeContactPerson' => 'Returns Department']);
                } elseif($request->customer_country_code == 'CA') {
                    $carrier = Carrier::where('countrycode', "Like", "%" . $request->customer_country_code . "%")->where('name', "Like" , "%" .'CanadaPost'. "%")->first();
                } else{
                    $carrier = Carrier::where('countrycode', "Like", "%" . $request->customer_country_code . "%")->where('name', $request->carrier)->first();
                }

                if(! $carrier){
                    return response()->json(['message' => 'No Carrier found.', 'status' => 200], 200);
                }

                if($carrier){
                    $request->request->add(['servicecode' => $carrier->code]);
                    $request->request->add(['carrier_name' => $carrier->name]);
                    $request->request->add(['unit_type' => $carrier->unit_type]);
                }

                // dd($request->all());
                # store data in to db...
                $al_pkg = ReverseLogisticWaybill::where('way_bill_number', $request->way_bill_number)->where('status', 'Success')->first();
                if(!empty($al_pkg)){
                    return response()->json(['message' => 'The waybillnumber is already exists.', 'status' => 200], 200);
                }

                $obj = new ReverseLogisticWaybill;
                $way_bill_id = $obj->store($request->all());

                # set Meta for Waybillnumber...
                $meta_array = [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->order_email,
                    'customer_order_email' => $request->order_email ?? '',
                    'customer_address' => $request->customer_address.' '.$request->customer_address2 ?? '',
                    'customer_country' => $request->customer_country_code,
                    'customer_state' => $request->customer_state_code,
                    'customer_city' => $request->customer_city,
                    'customer_pincode' => $request->customer_postcode,
                    'customer_phone' => $request->customer_phone,
                    'service_code' => $request->service_code ?? '',
                    'number_of_packages' => $request->number_of_packages,
                    'actual_weight' => $request->actual_weight,
                    'charged_weight' => $request->charged_weight,
                    'client_code' => $request->client_code,
                    'customer_code' => $request->customer_code,
                    'label_url' => '',
                    'label_package_sticker_url' => '',
                    'drop_off' => 'By_Courier',
                    'source' => ReverseLogisticWaybill::CUSTOMER,
                    'source_name' => 'N/A',
                    'waiver' => $request->waiver ?? 'N/A',
                    'order_type' => ReverseLogisticWaybill::MISS_TYPE,
                    'consignee_name' => $request->consignee_name,
                    'shipment_name' => $request->shipment_name,
                    'carrier_name' => $request->carrier_name,
                    'unit_type' => $request->unit_type,
                    'rtn_total' => $request->amount,
                    'currency' => $request->currency,
                    'all_data' => json_encode($request->all())
                ];

                $reverse_obj    = ReverseLogisticWaybill::find($way_bill_id);
                setCustomMeta($reverse_obj,$meta_array);

                $data = $request->all();
                if (isset($data['items']) && is_array($data['items'])) {
                    foreach($data['items'] as $item){
                        $package_obj = new PackageDetail();
                        $package_obj->bar_code                      = $item['sku'] ?? null;
                        $package_obj->title                         = $item['name'] ?? null;
                        $package_obj->price                         = $item['price'] ?? 0;
                        $package_obj->package_count                 = $item['quantity'] ?? '0';
                        $package_obj->reverse_logistic_waybill_id   = $way_bill_id;
                        $package_obj->length                        = $item['length'] ?? '1';
                        $package_obj->width                         = $item['width'] ?? '1';
                        $package_obj->height                        = $item['height'] ?? '1';
                        $package_obj->weight                        = $item['weight'] ?? '1';
                        $package_obj->charged_weight                =  '1';
                        $package_obj->custom_price                  = $item['price'] ?? '0';
                        $package_obj->color                         = $item['color'] ?? 'N/A';
                        $package_obj->size                          = $item['size'] ?? 'N/A';
                        $package_obj->estimated_value               = '';
                        $package_obj->hs_code                       = $item['hs_code'] ?? '';
                        $package_obj->country_of_origin             = $item['country_of_origin'] ?? '';
                        $package_obj->status                        = '';
                        $package_obj->selected_package_type_code    = 'DOCUMENT';
                        $package_obj->weight_unit_type              = 'KGS';
                        $package_obj->dimension                     = 'IN';
                        $package_obj->note                          = '';
                        $package_obj->return_reason                 = '';
                        $package_obj->image_url                     = '';
                        $package_obj->save();
                    }
                }

                if(isset($data['carrier']) && !empty($data['carrier']) && !empty($carrier)){
                    $randomnumber =  rand ( 100000000 , 9999999999 );
                    $sq_rg_no = 'SC'.str_replace('#', '', $request->way_bill_number).$way_bill_id;

                    $waybill_array = $this->createSopifyOrderWaywillRequest($sq_rg_no, $data, $request, $carrier,$way_bill_id);

                    $js_data = json_encode($waybill_array);
                    $cr_array['create_waywill_request'] = $js_data;
                    setCustomMeta($reverse_obj, $cr_array);
                    $meta_array['waywill_number'] = $sq_rg_no;

                    # response create waywill...
                    $create_response = $this->createShopifyOrderWaywillResponse($js_data);    
                    $create_data = json_decode($create_response);
                    $meta_array['create_waywill_response'] = $create_response;

                    if (empty($create_data)) {
                        $rtn_msg = "Create waywill Api:- no response";
                        return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                    } elseif (isset($create_data->messageType) && $create_data->messageType == 'Error') {                
                        $meta_array['create_waywill_data'] = json_encode($create_data);
                        setCustomMeta($reverse_obj, $meta_array);
                        $rtn_msg = 'Create waywill Api:- '.$create_data->message;
                        return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                    } else{
                        $meta_array['label_message']             = $create_data->message;
                        $meta_array['label_message_type']        = $create_data->messageType;
                        $meta_array['label_message_status']      = $create_data->status;
                        $meta_array['label_package_sticker_url'] = $create_data->packageStickerURL;
                        $meta_array['label_url']                 = $create_data->labelURL;
                        $meta_array['waybillNumber']             = $create_data->waybillNumber;
                        $meta_array['create_waywill_data']       = json_encode($create_data);
                    }

                    setCustomMeta($reverse_obj, $meta_array);

                    # generate waywill response...
                    $g_arr = [
                        'waybillNumber' => $create_data->waybillNumber,
                        'carrierCode'    => $carrier->code,
                        'aggregator'     => '',
                        'carrierProduct' => ($carrier->product[0]->code) ? ($carrier->product[0]->code): "",
                        'reportName' => $carrier->reportName
                    ];

                    if($request->client_id == '757'){
                        $g_arr['labelFormat'] = 'ZPL';
                    }
                    
                    $gr_array['generate_waywill_request'] = json_encode($g_arr);
                    setCustomMeta($reverse_obj, $gr_array);

                    $gr_response = $this->generateShopifyOrderWaywillResponse($g_arr);            
                    $gr_json = json_decode($gr_response);                    
                    if (empty($gr_json)) {                
                        $rtn_msg = "Generate waywill Api:- no response";
                        return response()->json(['message' => 'This return method is currently unavailable. Please try again later.', 'status' => 200], 200);
                    } elseif (isset($gr_json->messageType) && $gr_json->messageType == 'Error') {
                        $meta_array['generate_waywill_status'] = json_encode($gr_json);
                        setCustomMeta($reverse_obj, $meta_array);
                        $rtn_msg = $rtn_msg = 'Generate waywill Api:- '.$gr_json->message;
                        return response()->json(['message' => 'This return method is currently unavailable. Please try again later.', 'status' => 200], 200);
                    }

                    $label = reset($gr_json->labelDetailList);
                    $meta_array['generate_waywill_status'] = json_encode($gr_json);
                    setCustomMeta($reverse_obj, $meta_array);

                    # create cn22 level...
                    $reverse_obj->warehouse_id          = $request->warehouse_id;
                    $reverse_obj->shipping_policy_id    = $request->shipment_id;
                    $reverse_obj->cod_payment_mode      = "Cash";            
                    $reverse_obj->status                = 'Success';
                    $reverse_obj->tracking_id           = $gr_json->carrierWaybill;
                    $reverse_obj->rg_reference_number   = $sq_rg_no;
                    $reverse_obj->save();

                    $get_view_data['subject']    =   'Return Confirmation :-'.$request->way_bill_number;
                    $get_view_data['view']       =   'mails.jaded-order';
                    $get_view_data['attach_pdf'] = '';

                    # save pdf and send to mail...
                    $imagePath = public_path($this->upload_path);
                    if(!File::exists($imagePath)) {
                        File::makeDirectory($imagePath, 0777, true, true );
                    }

                    $pdf_url  = $label->artifactUrl;
                    $filename  = basename($pdf_url);
                    $fileName  = $filename;
                    $path_upload = $this->upload_path.$fileName;
                    $ch = curl_init($pdf_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $pdf_data = curl_exec($ch);
                    curl_close($ch);
                    $result = file_put_contents($path_upload, $pdf_data);
                    if ($result) {
                        $get_view_data['attach_pdf'] = public_path($path_upload);
                        $get_view_data['pdf_filename'] = $fileName;
                        $pdf_url = asset('public/'.$path_upload);

                        $pdf_arr['attachment_pdf'] = $path_upload;
                        setCustomMeta($reverse_obj, $pdf_arr);
                    }

                    $get_view_data['user'] = [
                        'name' =>  $data['customer_name'],
                        'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                        'url' => $pdf_url,
                        'order_no' => $request->way_bill_number,
                        'track_id' => $gr_json->carrierWaybill ?? '',
                        'return_date' => date('d/m/Y', strtotime($reverse_obj->created_at)),
                        'return_service' => $request->carrier ?? $request->carrier_name,
                        'return_cost' => $request->rtn_total ?? 0,
                        'return_charges' => 0,
                        'currency' => $request->currency,
                        'emailurl' => '',
                        'europecountrycheck' => '',
                    ];            
                    
                    # sending mail here...
                    try{                
                        $mail = Mail::to($request->customer_email)->send(new MainTemplate( $get_view_data ));
                        return response()->json([
                            'message' => 'Label generated successfully for order id:'.$request->way_bill_number ,
                            'url' => $label->artifactUrl,
                            'code' => $fr->code ?? '',
                            'status' => 201,
                            'id'=>$way_bill_id
                        ], 201);
                    }catch(\Swift_TransportException $transportExp){                
                        return response()->json([
                            'message' => 'Label generated successfully for order id:'.$request->way_bill_number ,
                            'url' => $label->artifactUrl,
                            'code' => $fr->code ?? '',
                            'status' => 201,
                            'id'=>$way_bill_id
                        ], 201);
                    }
                }

                $status = 201;
                $msg = 'Return Order has been created successfully';

                return response()->json(['status'=>true,'msg'=>$msg,'id'=>$way_bill_id], $status);                
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['status'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }

    /**
     * Create waywill array
     */
    public function createSopifyOrderWaywillRequest($sq_rg_no, $data, $request, $carrieravailable, $way_bill_id){
        $packagedetail = PackageDetail::where('reverse_logistic_waybill_id',$way_bill_id)->get();
        $referenceNumber = array();
        $hscode          = array();
        $packagetitle    = array();
        $sku             = array();
        $coo = '';
        $no_of_pakg = 0;
        foreach($packagedetail as $key => $package){
            $coo = $package->country_of_origin;
            array_push($referenceNumber,$package->country_of_origin);
            array_push($hscode,$package->hs_code);
            array_push($packagetitle,$package->title);
            array_push($sku,$package->bar_code);
            $no_of_pakg += $package->package_count;
        }
        
        $implodedreferencenumber = implode(',' , $referenceNumber) ;
        $implodedhscode          = implode(',' , $hscode) ;
        $implodedpackagetitle         = implode(',' , $packagetitle) ;
        $implodedsku       = implode(',' , $sku) ;
        if(in_array($request->customer_country_code, countryEU())){
            if($request->customer_country_code == 'AU')
            {
                $request->currency = 'AUD';
            }
            elseif($request->customer_country_code == 'CA')
            {
                $request->currency = 'CAD';
            }
            else{
                $request->currency = 'EUR';
            }
        }

        # create package array...
        $package_array = array();        
        $package = array(
            'barCode'       => '',
            'packageCount'  => 1,
            'length'        => '10',
            'width'         => '8',
            'height'        => '3',
            'weight'        => '1',
            'chargedWeight' => '1',
            'selectedPackageTypeCode' => ($request->customer_country_code == 'AU') ? '3J05' : 'BOX',
            'itemCount'     => $no_of_pakg
        );
        array_push($package_array, $package);

        $phone = $request->customer_phone;
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $user = User::where('id', $data['client_id'])->first();
        $refrence_no = $coo.''.$user->name.''.$request->way_bill_number;
        $waybill_array = array(
            "waybillRequestData" => array(
                "consigneeGeoLocation" => "",
                "FromOU" => $request->FromOU,
                "DeliveryDate" => "",
                "WaybillNumber" => $sq_rg_no,
                "CustomerCountry" => $request->customer_country_code,
                "CustomerState" => $request->customer_state_code,
                "CustomerCity" => $request->customer_city,
                "CustomerPincode" => $request->customer_postcode,
                "CustomerName" => $request->customer_name,
                "CustomerAddress" => $request->customer_address,
                "CustomerEmail" => $request->customer_email,
                "CustomerPhone" => $request->customer_phone,
                "CustomerCode" => "00000",
                "ConsigneeCode" => "00000",
                "ConsigneeName" => $request->consignee_name,
                "ConsigneePhone" => $request->ConsigneePhone,
                "ConsigneeAddress" => $request->ConsigneeAddress,
                "ConsigneeCountry" => $request->ConsigneeCountry,
                "ConsigneeState" => $request->ConsigneeState,
                "ConsigneeCity" => $request->ConsigneeCity,
                "ConsigneePincode" => $request->ConsigneePincode,
                "ConsigneeEmail" => $request->ConsigneeEmail,
                "ConsigneeContactPerson" => 'Returns Department',
                "ConsigneeWhat3Words" => "",
                "CreateWaybillWithoutStock" => "true",
                "stockIn" => true,
                "StartLocation" => "",
                "EndLocation" => "",
                "ClientCode" => $carrieravailable->ClientCode,
                "NumberOfPackages" => 1,
                "ActualWeight" => 1,
                "ChargedWeight" => 1,
                "CargoValue" => $request->amount,
                "ReferenceNumber" => $refrence_no,
                "InvoiceNumber" => $request->way_bill_number,
                "PaymentMode" => "TBB",
                "ServiceCode" => $carrieravailable->service[0]->code,
                "WeightUnitType" => $carrieravailable->unit_type,
                "Description" => "ShipCycle Client return order",
                "COD" => "",
                "Currency" => $request->currency,
                "salesInvoiceNumber" => "",
                "CODPaymentMode" => "",
                "skipCityStateValidation" => true,
                "packageDetails" => array(
                    'packageJsonString' => $package_array
                )                        
            )
        );

        return $waybill_array;
    }

    /**
    * Create waywill response
    **/
    public function createShopifyOrderWaywillResponse($js_data){
        try {
            // $url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');
            $url = Config('constants.cuactiveUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>  $js_data,
                CURLOPT_HTTPHEADER => array(
                    "AccessKey: logixerp",
                    "Content-Type: application/json"
                ),
            ));
            $create_response = curl_exec($curl);
            curl_close($curl);

            return $create_response;    
        } catch (\Exception $e) {
            $str = 'Create Waybill Api Error:- '.$e->getMessage();
            return null;
        }       
    }

    /**
    * Generate waywill response
    **/
    public function generateShopifyOrderWaywillResponse($g_arr){
        try {
            // $url = Config('constants.activeUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');
            $url = Config('constants.cuactiveUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');
            $g_client = new Client(['headers'=>['AccessKey'=> Config('constants.AccessKey'), 'Content-Type' => 'application/json']]);
            $rg = $g_client->post($url,['form_params' => $g_arr]);
            $g_response = $rg->getBody()->getContents();
            return $g_response;
        } catch (\Exception $e) {
            $str = 'Generate Api Error:- '.$e->getMessage();
            return null;
        }
    }


    /*
    * Order Tracking Details
    **/
    public function orderTrackingDetail(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                $data = $request->all();

                if(!isset($data['way_bill_number'])){
                    return response()->json(['success'=>false,'message'=>'Not a valid way will number'],400);
                }

                if(isset($data['way_bill_number']) && empty($data['way_bill_number'])){
                    return response()->json(['success'=>false,'message'=>'way will number is required.'],400);
                }

                $post = ReverseLogisticWaybill::where(['way_bill_number'=>$request->way_bill_number, 'status' => 'Success'])->first();
                if(!$post){
                    return response()->json(['status'=>false,'data'=>[],'message'=>"way bill number is not valid"],400);
                }

                if ($post) {
                    if (!empty($post->tracking_id)) {
                        // code...
                        $html = '';
                        $client    = new Client();
                        $id = $post->tracking_id;
                        $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$id;
                        $response = $client->get($url);
                        $results = json_decode($response->getBody()->getContents());
                        if ($results->messageType != 'Success') {
                            return response()->json(['success'=>true,'data'=>[],'message'=>'Action Completed'], 200);
                        }

                        $new_array = $deliverd = $transit = [];
                        $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';
                        
                        if(is_array($docket->docketTrackDetailList) && count($docket->docketTrackDetailList) > 0){
                            $trackDetail = reset($docket->docketTrackDetailList);
                            if (isset($trackDetail->docketTrackingDetail) && is_array($trackDetail->docketTrackingDetail)) {
                                foreach ($trackDetail->docketTrackingDetail as $value) {
                                    
                                    $d = date('Y-m-d', strtotime($value->date));
                                    if($value->waybillStatus == 'Delivered'){
                                        array_push($deliverd, $value);
                                    }

                                    if ($value->waybillStatus == 'In Transit') {
                                        array_push($transit, $value);
                                    }
                                }
                            }
                        }

                        $new_array = array_merge($new_array, $deliverd);

                        $first_transit = $last_transit = [];
                        if(count($transit) > 1){
                            $first_transit = reset($transit);
                            $first_transit->carrierLabel = '1012';

                            $last_transit = end($transit);
                            $last_transit->carrierLabel = '1011';

                            $new_array[] = $first_transit;
                            $new_array[] = $last_transit;
                        } elseif (count($transit) == 1) {
                            $first_transit = reset($transit);
                            $first_transit->carrierLabel = '1011';
                            $new_array[] = $first_transit;
                        }

                        return response()->json(['success'=>true,'data'=>$docket, 'tracking_id' => $id, 'message'=>'Action Completed'], 200);
                    }
                }

                return response()->json(['success'=>false,'message'=>'Tracking Detail not found.'],400);
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /*
    * Order Tracking Details
    **/
    public function orderTrackingStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                $data = $request->all();

                if(!isset($data['way_bill_number'])){
                    return response()->json(['success'=>false,'message'=>'Not a valid way will number'],400);
                }

                if(isset($data['way_bill_number']) && empty($data['way_bill_number'])){
                    return response()->json(['success'=>false,'message'=>'way will number is required.'],400);
                }

                $post = ReverseLogisticWaybill::where(['way_bill_number'=>$request->way_bill_number, 'status' => 'Success'])->first();
                if(!$post){
                    return response()->json(['status'=>false,'data'=>[],'message'=>"way bill number is not valid"],400);
                }

                if ($post) {
                    if (!empty($post->tracking_id)) {
                        // code...
                        $html = '';
                        $client    = new Client();
                        $id = $post->tracking_id;
                        $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$id;
                        $response = $client->get($url);
                        $results = json_decode($response->getBody()->getContents());
                        if ($results->messageType != 'Success') {
                            return response()->json(['success'=>true,'data'=>[],'message'=>'Action Completed'], 200);
                        }

                        $new_array = $deliverd = [];
                        $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';
                        
                        if(is_array($docket->docketTrackDetailList) && count($docket->docketTrackDetailList) > 0){
                            $trackDetail = reset($docket->docketTrackDetailList);
                            if (isset($trackDetail->docketTrackingDetail) && is_array($trackDetail->docketTrackingDetail)) {
                                foreach ($trackDetail->docketTrackingDetail as $value) {                                    
                                    array_push($deliverd, $value);
                                }
                            }
                        }

                        $history = StatusHistory::where('post_id', $post->id)->orderBy('status_date', 'DESC')->get();
                        if ($history->isNotEmpty()){
                            foreach($history as $his){
                                $arr = [
                                    "actionLabel" => getStatusValue($his->status_id),
                                    "carrierLabel" => $his->status_id,
                                    "date" => $his->status_date."T00:00:00",
                                    "remarks" => $his->addition_info,
                                    "time" => "1970-01-01T".$his->status_time,
                                    "updateBy" => $his->user,
                                    "updatedOU" => "",
                                    "waybillStatus" => getStatusValue($his->status_id)
                                ];
                                array_push($deliverd, $arr);
                            }
                        }

                        $new_array = array_merge($new_array, $deliverd);
                        // dd($new_array);

                        return response()->json(['success'=>true,'data'=>$new_array, 'tracking_id' => $id, 'message'=>'Action Completed'], 200);
                    }
                }

                return response()->json(['success'=>false,'message'=>'Tracking Detail not found.'],400);
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /*
    * Order Tracking Details List
    **/
    public function orderTrackingStatusList(Request $request){
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
                $headers = $request->header(self::HEADER_NAME);
                if($headers == null){
                    return response()->json(['success'=>false,'message'=>'Not a valid header'],400);
                }

                if($headers != self::HEADER_KEY){
                    return response()->json(['success'=>false,'message'=>'Not a valid Api Key'],400);
                }

                $data = $request->all();

                if(!isset($data['client_id'])){
                    return response()->json(['success'=>false,'message'=>'Not a valid client id'],400);
                }

                if(isset($data['client_id']) && empty($data['client_id'])){
                    return response()->json(['success'=>false,'message'=>'client id is required.'],400);
                }

                $query = (new ReverseLogisticWaybill)->newQuery();

                if($request->has('start_date') && $request->filled('start_date')  && $request->has('end_date') && $request->filled('end_date')){
                    $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start_date,$request->end_date));
                }

                if($request->has('start_date') && $request->filled('start_date')){
                    $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start_date);
                }

                if($request->has('end_date') && $request->filled('end_date')){
                    $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end_date);
                }

                $orders = $query->where('client_id', $data['client_id'])->paginate(10);
                if(count($orders) < 0){
                    return response()->json(['status'=>false,'data'=>[],'message'=>"data not valid"],400);
                }

                $new_array = [];
                if ($orders) {
                    foreach ($orders as $key => $post) {
                        if (!empty($post->tracking_id)) {
                            $client    = new Client();
                            $id = $post->tracking_id;
                            $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$id;
                            $response = $client->get($url);
                            $results = json_decode($response->getBody()->getContents());
                            /*if ($results->messageType != 'Success') {
                                continue;
                            }*/

                            $deliverd = [];
                            $arr_data = [
                                'tracking_id' => $post->tracking_id,
                                'order_id' => $post->way_bill_number,
                                'client' => $post->client->name ?? 'N/A'
                            ];
                            $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';                            
                            if(isset($docket->docketTrackDetailList) && is_array($docket->docketTrackDetailList) && count($docket->docketTrackDetailList) > 0){
                                $trackDetail = reset($docket->docketTrackDetailList);
                                if (isset($trackDetail->docketTrackingDetail) && is_array($trackDetail->docketTrackingDetail)) {
                                    foreach ($trackDetail->docketTrackingDetail as $value) {
                                        array_push($deliverd, $value);
                                    }
                                }
                            }

                            $history = StatusHistory::where('post_id', $post->id)->orderBy('status_date', 'DESC')->get();
                            if ($history->isNotEmpty()){
                                foreach($history as $his){
                                    $arr = [
                                        "actionLabel" => getStatusValue($his->status_id),
                                        "carrierLabel" => $his->status_id,
                                        "date" => $his->status_date."T00:00:00",
                                        "remarks" => $his->addition_info,
                                        "time" => "1970-01-01T".$his->status_time,
                                        "updateBy" => $his->user,
                                        "updatedOU" => "",
                                        "waybillStatus" => getStatusValue($his->status_id)
                                    ];
                                    array_push($deliverd, $arr);
                                }
                            }

                            $arr_data['status_list'] = $deliverd;
                            array_push($new_array, $arr_data);
                        }
                    }

                    return response()->json([
                        'success'=>true,
                        'total'=> $orders->total(),
                        'currentPage'=> $orders->currentPage(),
                        // 'firstItem'=> $orders->firstItem(),
                        // 'lastItem'=> $orders->lastItem(),
                        // 'lastPage'=> $orders->lastPage(),
                        'nextPageUrl'=> $orders->nextPageUrl(),
                        'perPage'=> $orders->perPage(),
                        'previousPageUrl'=> $orders->previousPageUrl(),
                        'data'=>$new_array,
                        'message'=>'Action Completed'
                    ], 200);
                }

                return response()->json(['success'=>false,'message'=>'Tracking Detail not found.'],400);
            }else{
                return response()->json(['success'=>false,'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }


    public function updateTrackingStatus(Request $request){
        try {
            if ($request->isMethod('post')) {
                $results = $request->all();
                // dd($results['waybillTrackDetailList']);

                if(isset($results['waybillTrackDetailList']) && count($results['waybillTrackDetailList']) > 0){
                    foreach($results['waybillTrackDetailList'] as $k => $trackDetails){
                        $deliverd = $fscan = $transit = [];
                        $waybill_obj = ReverseLogisticWaybill::where('rg_reference_number', $trackDetails['waybillNumber'])->first();
                        if (isset($trackDetails['waybillTrackingDetail']) && is_array($trackDetails['waybillTrackingDetail'])) {
                            foreach ($trackDetails['waybillTrackingDetail'] as $value) {

                                if(isset($value['carrierLabel']) && $value['carrierLabel'] == '114'){
                                    array_push($deliverd, $value);
                                } elseif(isset($value['waybillStatus']) && $value['waybillStatus'] == 'Delivered'){
                                    array_push($deliverd, $value);
                                }
                                

                                if (isset($value['waybillStatus']) && in_array($value['waybillStatus'], ['In Transit', 'In-Transit'])) {
                                    array_push($transit, $value);
                                }

                                if (isset($value['waybillStatus']) && in_array($value['waybillStatus'], ['PPU', 'OR', 'Received by Australia Post'])) {
                                    array_push($fscan, $value);
                                }

                                if (isset($value['remarks']) && in_array($value['remarks'], ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])) {
                                    array_push($fscan, $value);
                                }
                            }
                        }

                        // dd([$deliverd , $fscan , $transit]);

                        if (!empty($waybill_obj)) {
                            if($waybill_obj->hasMeta('_order_waywill_status')){
                                $waybill_obj->updateMeta('_order_waywill_status' , $trackDetails->currentStatus);
                            } else {
                                $waybill_obj->setMeta('_order_waywill_status' , $trackDetails->currentStatus);
                            }

                            if($waybill_obj->hasMeta('_order_waywill_data')){
                                $waybill_obj->updateMeta('_order_waywill_data' , json_encode($trackDetails->waybillTrackingDetail));
                            } else {
                                $waybill_obj->setMeta('_order_waywill_data' , json_encode($trackDetails->waybillTrackingDetail));
                            }

                            # for inpost code...
                            $carrier_name = $waybill_obj->meta->_carrier_name ?? 'None';
                            // if (isset($waybill_obj->meta->_carrier_name) && $waybill_obj->meta->_carrier_name == 'InPost') {}
                            if (count($transit) > 1) {
                                $first_transit = reset($transit);
                                $last_transit = end($transit);
                                if (in_array($carrier_name, ['InPost', 'AUSPOST', 'UPS'])) {
                                    if(isset($first_transit->carrierLabel) && !in_array($first_transit->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])){
                                        $waybill_obj->setMeta([
                                            '_order_waywill_in_transit' => date('Y-m-d', strtotime($first_transit->date))
                                        ]);
                                        $waybill_obj->inscan_status = 'In Transit';
                                    }
                                } elseif (in_array($carrier_name, ['Asda', 'USPS'])) {
                                    if(isset($first_transit->remarks) && !in_array($first_transit->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])){
                                        $waybill_obj->setMeta([
                                            '_order_waywill_in_transit' => date('Y-m-d', strtotime($first_transit->date))
                                        ]);
                                        $waybill_obj->inscan_status = 'In Transit';
                                    }
                                }

                                if (in_array($carrier_name, ['Postal Services'])) {
                                    $waybill_obj->setMeta([
                                        '_order_waywill_in_transit' => date('Y-m-d', strtotime($first_transit->date))
                                    ]);
                                    $waybill_obj->inscan_status = 'In Transit';

                                    $waybill_obj->setMeta([
                                        '_order_waywill_status_date' => date('Y-m-d', strtotime($last_transit->date))
                                    ]);
                                }

                                if (count($fscan) > 0){
                                    $first_transit = reset($fscan);
                                    $waybill_obj->setMeta([
                                        '_order_waywill_status_date' => date('Y-m-d', strtotime($first_transit->date))
                                    ]);
                                    $waybill_obj->inscan_status = 'First Scan';
                                }
                            } elseif (count($transit) == 1) {
                                $first_transit = reset($transit);
                                if (in_array($carrier_name, ['Postal Services'])) {
                                    $waybill_obj->setMeta([
                                        '_order_waywill_status_date' => date('Y-m-d', strtotime($first_transit->date))
                                    ]);
                                    $waybill_obj->inscan_status = 'First Scan';
                                }
                            }

                            if(count($fscan) > 0){
                                $first_transit = reset($fscan);
                                $waybill_obj->setMeta([
                                    '_order_waywill_status_date' => date('Y-m-d', strtotime($first_transit->date))
                                ]);
                                $waybill_obj->inscan_status = 'First Scan';
                            }

                            if(count($deliverd) > 1){
                                $last_d = reset($deliverd);
                                $waybill_obj->setMeta([
                                    '_order_waywill_deliverd' => date('Y-m-d', strtotime($last_d->date))
                                ]);
                                $waybill_obj->inscan_status = 'Delivered';
                                $waybill_obj->cron_status = 2;
                            } elseif (count($deliverd) == 1) {
                                $last_d = reset($deliverd);
                                $waybill_obj->setMeta([
                                    '_order_waywill_deliverd' => date('Y-m-d', strtotime($last_d->date))
                                ]);
                                $waybill_obj->inscan_status = 'Delivered';
                                $waybill_obj->cron_status = 2;
                            }
                            
                            $waybill_obj->save();
                        }
                    }
                }

                return response()->json(['success'=>true,'data'=>[],'message'=>'Waywill status update successfully'], 200);
            }else{                
                return response()->json(['success'=>false,'data'=>[],'message'=>'Invalid Request'],400);
            }
        } catch (\Exception $e) {            
            return response()->json(['success'=>false,'data'=>[],'message'=>$e->getMessage()],400);
        }
    }


    /**
     * Return webhook call
     */
    public function prentaWebhook(Request $request){
        try {
            $data = $request->all();
            if(is_array($data)){
                $data = json_encode($data, true);
            }

            // $data = '{"RMA":"1170|date=02-09-2024 15:22:17|returnCode=RET9|ReturnReasonDesc=|ReturnCond=|TrackingNo=PR-117090051|Reasoncode=|ReturnCondCode=|Itemcode=PRE.C.ATL.B.ROS.M.5.F|quantityReturned=1|itemComments=|barcode=5060912370481|itemDesc=Atlas Rose Gold Metallic Shoe 5F|itemWeight=0.20|itemHeight=7.50|itemWidth=17.00|itemDeptht=16.00|itemName=Atlas Rose Gold Metallic Shoe 5F"}';
            $d_data = json_decode($data);
            $w_data = explode('|', $d_data->RMA);
            // dd($w_data);

            $MerchantOrderNo = str_replace('Itemcode=', '', $w_data[8]);
            $Quantity = str_replace('quantityReturned=', '', $w_data[9]);
            $way_bill_id = reset($w_data);
            $reverse_obj = ReverseLogisticWaybill::where(['way_bill_number' => $way_bill_id, 'status' => 'Success'])->first();

            # return merchant api json data....
            if ($reverse_obj->hasMeta('_orderData')) {
                $orders = json_decode($reverse_obj->meta->_orderData);
                $linesItem = [];
                $reason = 'WRONG_SIZE';
                $ChannelProductNo = [];
                $inc_price = 0;
                $exl_price = 0;
                foreach ($reverse_obj->packages as $key => $pkg) {
                    $ChannelProductNo[] = $pkg->bar_code;
                }

                foreach ($orders->Lines as $key => $variants) {
                    /*if (in_array($variants->ChannelProductNo, $ChannelProductNo)) {
                        $a = [
                            "MerchantProductNo" => $variants->MerchantProductNo,
                            "Quantity" => $variants->Quantity
                        ];
                        array_push($linesItem, $a);
                        $inc_price += $variants->OriginalLineTotalInclVat;
                        $exl_price += $variants->OriginalLineTotalExclVat;
                    }*/

                    if ($MerchantOrderNo == $variants->MerchantProductNo) {
                        $a = [
                            "MerchantProductNo" => $variants->MerchantProductNo,
                            "Quantity" => $Quantity
                        ];
                        array_push($linesItem, $a);
                        $inc_price += $variants->OriginalLineTotalInclVat * $Quantity;
                        $exl_price += $variants->OriginalLineTotalExclVat * $Quantity;
                    }
                }

                $dt = date('Y-m-d')."T".date('h.i.s').".821Z";
                $inc_price = $inc_price - 2.99;
                $exl_price = $exl_price - 2.99;
                
                $merchantArr = array(
                    "MerchantOrderNo" => $orders->MerchantOrderNo,
                    "MerchantReturnNo" => $reverse_obj->meta->_waybillNumber,
                    "Lines" => $linesItem,
                    "Id" => $orders->Id,
                    "Reason" => $reason,
                    "CustomerComment" => "Description on website was not accurate",
                    "MerchantComment" => "Description on website was not accurate",
                    "RefundInclVat" => $inc_price,
                    "RefundExclVat" => $exl_price,
                    // "ReturnDate" => $dt
                );

                // dd($merchantArr);
                $mr_data = json_encode($merchantArr);
                $cr_array['return_merchant_request'] = $mr_data;
                $rtn_response = $this->returnMerchantResponse($mr_data);    
                $cr_array['return_merchant_response'] = $rtn_response;
                setCustomMeta($reverse_obj, $cr_array);
            }

            $webhookdata = new PvxWebhook();
            $webhookdata->payload = $data;
            $webhookdata->waybill_id = $way_bill_id;
            $webhookdata->save();
            
            return response()->json(['success'=>true, 'message'=>'Action Completed'], 200); 
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],400);
        }
    }

    /**
     * Return merchant api
     */
    public function returnMerchantResponse($js_data){
        try {
            $url = 'https://prentashoes.channelengine.net/api/v2/returns/merchant?apikey=e7c987109d0f0ab4c27dc950c2d41597d46799bb';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>  $js_data,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
                ),
            ));
            $create_response = curl_exec($curl);
            curl_close($curl);

            return $create_response;    
        } catch (\Exception $e) {
            $str = 'Return Merchant Api Error:- '.$e->getMessage();
            return null;
        }       
    }
}
