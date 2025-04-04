<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWayBillRequest;

use App\Models\ReverseLogisticWaybill;
use App\Models\PackageDetail;
use App\Models\CarrierProduct;
use App\Models\CarrierService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Auth;
use Validator;

use Exception;
use DB;
use Config;
use Request as PostRequest;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Reader_CSV;

use GuzzleHttp\Client;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

class CommonController extends Controller
{
    public $upload_path;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->upload_path = \Config::get('constants.path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777,true);
    }

    public function getStateByCountryId(Request $request)
    {
        $country_id = $request->country_id;
        $state_list = \App\Models\State::where(['country_id' => $country_id])->get();
        $html       = view('pages.common.state-by-country', compact('state_list'))->render();
        return response()->json(['html' => $html]);
    }

    public function postWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_name' => 'required|max:50|min:2',
            'contact_person' => 'required|max:50|min:2',
            'email'          => 'required|max:50|min:2',
            'phone'          => 'required|max:20|min:8',
            'address'        => 'required|max:50|min:2',
            'country'        => 'required',
            'state'          => 'required|max:50',
            'city'           => 'required|max:50',
            'postal_code'    => 'required|max:15',
            'FromOU'        => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => $validator->errors()->first()]);
        }
        if (isset($request->id) && !empty($request->id)) {
            //echo "yes";
            $msg = 'Warehouse has been updated successfully';
            $id  = \App\Models\Warehouse::where('id', $request->id)->update([
                'name'           => $request->warehouse_name,
                'country_id'     => $request->country,
                'contact_person' => $request->contact_person,
                'email'          => $request->email,
                'state_code'     => $request->state,
                'state'          => get_state_code_by_name($request->state),
                'city'           => $request->city,
                'zip_code'       => $request->postal_code,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'FromOU'         => $request->FromOU,
            ]);
        } else {
            //echo "no";
            $msg                      = 'Warehouse has been saved successfully';
            $ware_obj                 = new \App\Models\Warehouse;
            $ware_obj->user_id        = $request->client_id;
            $ware_obj->name           = $request->warehouse_name;
            $ware_obj->contact_person = $request->contact_person;
            $ware_obj->email          = $request->email;
            $ware_obj->country_id     = $request->country;
            $ware_obj->state          = get_state_code_by_name($request->state);
            $ware_obj->state_code     = $request->state;
            $ware_obj->city           = $request->city;
            $ware_obj->zip_code       = $request->postal_code;
            $ware_obj->address        = $request->address;
            $ware_obj->phone          = $request->phone;
            $ware_obj->status         = '1';
            $ware_obj->FromOU         = $request->FromOU ;
            $ware_obj->save();
            $id = $ware_obj->id;
        }

        if ($id) {
            return response()->json(['status' => true, 'msg' => $msg, 'id' => $id]);
        } else {
            return response()->json(['status' => false, 'msg' => "Please try again, Your request not completed"]);
        }
    }

    public function deleteWarehouse(Request $request, $id)
    {
        $warehouse = \App\Models\Warehouse::find($id);
        if ($warehouse) {
            $warehouse->delete();
            return response()->json(['status' => true, 'msg' => 'Warehouse deleted successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Please try again']);
        }
    }

    public function getWarehouse(Request $request, $id)
    {
        $row          = $request->row;
        $warehouse    = \App\Models\Warehouse::find($id);
        $country_list = \App\Models\Country::where(['status' => '1'])->get();
        if ($warehouse) {
            $state_list = \App\Models\State::where(['status' => '1', 'country_id' => $warehouse->country_id])->get();

            $html = view('pages.admin.client.common.edit-warehouse-modal', compact('country_list', 'state_list', 'warehouse', 'row'))->render();
            return response()->json(['html' => $html, 'status' => true]);
        } else {
            return response()->json(['msg' => "This warehouse not found", 'status' => false]);
        }
    }

    public function warehouseForm(Request $rquest)
    {
        $client_id    = $rquest->client;
        $country_list = \App\Models\Country::where(['status' => '1'])->get();
        $html         = view('pages.admin.client.common.modal', compact('country_list', 'client_id'))->render();
        return response()->json(['html' => $html]);
    }

    /**
     * Code by Sanjay
     * Report of waywill
     **/
    public function getReportData(Request $request)
    {
        $request->request->add(['type' => 'A']);
        $request->request->add(['status'=>'Success']);
        $request->request->add(['return_by'=>'EQTOR_ADMIN']);
        
        $obj  = new ReverseLogisticWaybill;
        $user = Auth::guard(get_guard())->user();

        $lists = $obj->getReverLogisticList($request, $user);
        if ($user->user_type_id == 1) {
            $clients = \App\User::where(['user_type_id' => 3])->get();
        } else if ($user->user_type_id == 2) {
            $obj     = new \App\Models\UserOwnerMapping;
            $clients = $obj->getOwnerClients($user->id);
        } else {
            $clients = array();
        }

        return view('pages.common.report-list', compact('lists', 'clients', 'user'));
    }

    /**
     * code by sanjay
     * uploads orders from the csv file
     */
    public function bulkUploadWaybills(){
        DB::beginTransaction();
        try {
            if (PostRequest::isMethod('post') && PostRequest::ajax()) {                
                $file      = Input::file('csvFileImport')->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                // Valid File Extensions and Check file extension...
                $valid_extension = array("csv", 'xls', 'xlsx');
                if(!in_array(strtolower($extension),$valid_extension)){
                    return (new \Illuminate\Http\Response)->setStatusCode(400,'please upload a file.');
                }

                $inputFileName = Input::file('csvFileImport');
                /*check point*/
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);

                $objPHPExcel = $objReader->load($inputFileName);
                $objPHPExcel->setActiveSheetIndex(0);
                $objWorksheet          = $objPHPExcel->getActiveSheet();
                $CurrentWorkSheetIndex = 0;

                /* row and column*/
                $highestRow    = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();

                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                $headingsArray      = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray      = $headingsArray[1];

                $r              = -1;
                $namedDataArray = $keys = array();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {
                            $key                      = strtolower(str_replace(' ', '_', $columnHeading));
                            $namedDataArray[$r][$key] = $dataRow[$row][$columnKey];
                        }
                    }
                }

                //print_r($namedDataArray); die;
                $waybill_arr = $this->unique_key($namedDataArray, 'customer_order_id');
                //dd($waybill_arr);
                
                $flag = false;
                $failed_way_bill_array = array();
                $success_way_bill = 0;
                $failed_way_bill = 0;
                foreach ($waybill_arr as $key => $value) {
                    $rev_obj = ReverseLogisticWaybill::find($value['customer_order_id']);                    
                    if(!empty($rev_obj)){
                        $failed_way_bill++;
                        array_push($failed_way_bill_array, array('way_bill_number'=>$value['customer_order_id'],'msg'=>"This order already exist."));
                        continue;
                    }

                    $obj = new ReverseLogisticWaybill;                    
                    //$value['client_code'] = 'REVERSEGEAR';
                    //$value['service_code'] = 'ECOMDOCUMENT';
                    $value['customer_code'] = '00000';
                    
                    $value['shipment_id'] = $value['shipment_policy_id'];
                    $value['customer_country'] = $value['customer_country_code'];
                    $value['customer_state'] = $value['customer_state_code'];
                    $value['way_bill_number'] = $value['customer_order_id'];

                    $war_obj = \App\Models\Warehouse::find($value['warehouse_id']);

                    if(!$war_obj){
                        $failed_way_bill++;
                        array_push($failed_way_bill_array, array('way_bill_number'=>$value['customer_order_id'],'msg'=>"Warehouse not found"));
                        continue;
                    }
                    $country_data  = $war_obj->getCountry;
                    $value['type'] = 'A';
                    $value['created_from'] = 'RG';
                    $value['consignee_code'] = '00000';
                    $value['consignee_name'] = $war_obj->name;
                    $value['consignee_phone'] = $war_obj->phone;
                    $value['consignee_address'] = $war_obj->address;
                    $value['consignee_country'] = $country_data->sortname;
                    $value['consignee_country_name'] = $country_data->name;
                    $value['consignee_state'] = $war_obj->state_code;
                    $value['consignee_city'] = $war_obj->city;
                    $value['consignee_pincode'] = $war_obj->zip_code;

                    $shipping_obj   = new \App\Models\ShippingPolicy;
                    $shipment = $shipping_obj->getShipmentCarrierDetailById($value['shipment_policy_id']);
                    if(!$shipment){
                        $failed_way_bill++;
                        array_push($failed_way_bill_array, array('way_bill_number'=>$value['customer_order_id'],'msg'=>"Shipment not found"));
                        continue;
                        
                    }
                    $value['carrier_name'] = $shipment->carrier_name;
                    $value['shipment_name'] = $shipment->shipment_name;
                    $value['rate'] = $shipment->rate;
                    $value['currency'] = $shipment->currency;

                    $meta_array = Arr::only($value, ['customer_name','customer_email', 'customer_address','customer_country','customer_state','customer_city','customer_pincode','customer_phone','service_code','cash_on_pickup','number_of_packages','weight_unit_type','actual_weight','charged_weight','description','consignee_code','consignee_name','consignee_phone','consignee_address','consignee_country','consignee_country_name','consignee_state','consignee_city','consignee_pincode','carrier_name','shipment_name','rate','client_code','customer_code','currency']);

                    $way_bill_id = $obj->store($value); 

                     //set Meta for Waybillnumber
                    $reverse_obj    = ReverseLogisticWaybill::find($way_bill_id);
                    setCustomMeta($reverse_obj,$meta_array);

                    if(isset($value['package_count'])){
                        $packge = new \App\Models\PackageDetail;
                        $packge->createPackageByWayBillId($value,$way_bill_id);
                    }

                    $way_bill_request = $this->generateWayBillRequest($value);

                    $post_url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');

                    $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json','AccessKey'=>Config('constants.AccessKey')]]);
                    $r = $client->post($post_url,['json'=>$way_bill_request]);
                    $response = $r->getBody()->getContents();
                    $json_data = json_decode($response);
                    if($json_data->messageType=='Success'){
                       
                        $label_arr = array(
                            "label_message" => $json_data->message,
                            "label_message_type" => $json_data->messageType,
                            "label_message_status" => $json_data->status,
                            "label_package_sticker_url" => $json_data->packageStickerURL,
                            "label_url" => $json_data->labelURL,
                        );
                        setCustomMeta($reverse_obj,$label_arr);

                        //sendCreatedWaybillMail(array('name'=>$request->customer_name,'label'=>$json_data->labelURL,'email'=>$request->customer_email));
                        DB::commit();

                        $success_way_bill++;
                    }else{
                        $failed_way_bill++;
                        DB::rollback();
                        array_push($failed_way_bill_array, array('way_bill_number'=>$value['way_bill_number'],'msg'=>$json_data->message));
                    }

                }

                return response()->json(['status'=>true,'message'=> 'Successfully saved.','status'=>201,'data'=>$failed_way_bill_array,'success'=>$success_way_bill,'failed'=>$failed_way_bill],201);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status'=>true,'message'=> $e->getMessage(),'status'=>400],400);
        }
    }

    /**
    * code by sanjay
    * create a unique group of data
    */
    public function unique_key($array, $keyname){
        $new_array  = array();
        $login_id = Auth::guard(get_guard())->id();

        foreach ($array as $key => $value) {
            if (!isset($new_array[$value[$keyname]])) {
                $new_array[$value[$keyname]] = $value;
                $new_array[$value[$keyname]]['login_id'] = $login_id;
                $new_array[$value[$keyname]]['bar_code'] = [$value['bar_code']];
                $new_array[$value[$keyname]]['title'] = [$value['title']];
                $new_array[$value[$keyname]]['package_count'] = [$value['package_count']];
                $new_array[$value[$keyname]]['length'] = [$value['length']];
                $new_array[$value[$keyname]]['width'] = [$value['width']];
                $new_array[$value[$keyname]]['height'] = [$value['height']];
                $new_array[$value[$keyname]]['weight'] = [$value['weight']];
            }else{                
                array_push($new_array[$value[$keyname]]['bar_code'], $value['bar_code']);
                array_push($new_array[$value[$keyname]]['title'], $value['title']);
                array_push($new_array[$value[$keyname]]['package_count'], $value['package_count']);
                array_push($new_array[$value[$keyname]]['length'], $value['length']);
                array_push($new_array[$value[$keyname]]['width'], $value['width']);
                array_push($new_array[$value[$keyname]]['height'], $value['height']);
                array_push($new_array[$value[$keyname]]['weight'], $value['weight']);
            }
        }

        $new_array = array_values($new_array);            
        return $new_array;
    }

    /**
    * Create a return order from admin and generate waywill no
    *
    **/
    public function storeWayBill(CreateWayBillRequest $request){
        $login_id = Auth::id();
        $request->request->add(['login_id' => $login_id]);

        if($this->check_validation($request->package_count)){
            return redirect()->back()->with('error', 'Please fill all Package Count');
        }else if($this->check_validation($request->length)){
            return redirect()->back()->with('error', 'Please fill all Package Length');
        }else if($this->check_validation($request->weight)){
            return redirect()->back()->with('error', 'Please fill all Package Weight');
        }else if($this->check_validation($request->charged__weight)){
            return redirect()->back()->with('error', 'Please fill all Package Charged Weight');
        }

        if ($request->warehouse_id != 'discrepency') {
            $request->request->add(['type' => 'A']);
        }else{
            $request->request->add(['type' => 'D']);
            $request->request->add(['warehouse_type' => $request->warehouse_id]);
        }

        $request->request->add(['created_from' => 'RG']);
        $request->request->add(['return_by'=> ReverseLogisticWaybill::RG_ADMIN]);
        $request->request->add(['status' => 'Success']);
        $request->request->add(['consignee_code' => '00000']);

        if ($request->warehouse_id != 'discrepency') {
            # code...
            $war_obj = \App\Models\Warehouse::find($request->warehouse_id);
            if(!$war_obj){
                return redirect()->back()->with('error', 'Warehouse not found.');
            }

            $country_data = $war_obj->getCountry;
            $request->request->add(['consignee_name' => $war_obj->name]);
            $request->request->add(['consignee_phone' => $war_obj->phone]);
            $request->request->add(['consignee_address' => $war_obj->address]);
            $request->request->add(['consignee_country' => $country_data->sortname]);
            $request->request->add(['consignee_country_name' => $country_data->name]);
            $request->request->add(['consignee_state' => $war_obj->state_code]);
            $request->request->add(['consignee_city' => $war_obj->city]);
            $request->request->add(['consignee_pincode' => $war_obj->zip_code]);
        }
        
        $shipping_obj   = new \App\Models\ShippingPolicy;
        $shipment = $shipping_obj->getShipmentCarrierDetailById($request->shipment_id);
        if(!$shipment){
            return redirect()->back()->with('error', 'Shipment not found.');
        }

        $request->request->add(['carrier_name' => $shipment->carrier_name]);
        $request->request->add(['shipment_name' => $shipment->shipment_name]);
        $request->request->add(['rate' => $shipment->rate]);
        $request->request->add(['currency' => $shipment->currency]);
        $request->request->add(['upload_path' => $this->upload_path]);

        // dd($request->all());
        // dd(newReverseLogisticUpdate($request->all()));
        createOrUpdateReturnOrder($request->all());
    }

    /*
    * Update return order and create generate waywill number
    *
    **/
    public function customerWaybillUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_mode' => 'required|max:50',
            'service_code' => 'required|max:50',
            'actual_weight' => 'required|max:50',
            'charged_weight' => 'required|max:50',
            'warehouse_id' => 'required',
            'shipment_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()],422);
        }
        
        if($this->check_validation($request->length)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Length');
        }else if($this->check_validation($request->weight)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Weight');
        }else if($this->check_validation($request->charged__weight)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Charged Weight');
        }

        if ($request->warehouse_id != 'discrepency') {
            $request->request->add(['type' => 'A']);
        }else{
            $request->request->add(['type' => 'D']);
            $request->request->add(['warehouse_type' => $request->warehouse_id]);
        }

        $war_obj = \App\Models\Warehouse::find($request->warehouse_id);
        if(!$war_obj){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Warehouse not found');
        }
        
        if ($request->warehouse_id != 'discrepency') {
            # code...
            $war_obj = \App\Models\Warehouse::find($request->warehouse_id);
            if(!$war_obj){
                return (new \Illuminate\Http\Response)->setStatusCode(406,'Warehouse not found');
            }

            $country_data = $war_obj->getCountry;
            $request->request->add(['consignee_code' => '00000']);
            $request->request->add(['consignee_name' => $war_obj->name]);
            $request->request->add(['consignee_phone' => $war_obj->phone]);
            $request->request->add(['consignee_address' => $war_obj->address]);
            $request->request->add(['consignee_country' => $country_data->sortname]);
            $request->request->add(['consignee_country_name' => $country_data->name]);
            $request->request->add(['consignee_state' => $war_obj->state_code]);
            $request->request->add(['consignee_city' => $war_obj->city]);
            $request->request->add(['consignee_pincode' => $war_obj->zip_code]);
        }        
        
        $shipping_obj   = new \App\Models\ShippingPolicy;
        $shipment = $shipping_obj->getShipmentCarrierDetailById($request->shipment_id);
        if(!$shipment){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Shipment not found');
        }
        
        $request->request->add(['carrier_name' => $shipment->carrier_name]);
        $request->request->add(['shipment_name' => $shipment->shipment_name]);
        $request->request->add(['rate' => $shipment->rate]);
        $request->request->add(['currency' => $shipment->currency]);

       return createOrUpdateReturnOrder($request->all());
    }

    /*
    * Update return bar order to happy returns
    * Code By: Sanjay
    **/
    public function returnBarOrder(Request $request){        
        if($this->check_validation($request->length)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Length');
        }else if($this->check_validation($request->weight)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Weight');
        }else if($this->check_validation($request->charged__weight)){
            return (new \Illuminate\Http\Response)->setStatusCode(406,'Please fill all Package Charged Weight');
        }        
        // dd($request->all());
       return happyReturnsOrder($request->all());
    }

    public function check_validation($request){
        if(isset($request) && count(array_filter($request)) == count($request)){
            return false;
        }else{
           return true;            
        }
        
    }

    /*
    * Generate waybill request response
    *
    */
    public function generateWayBillRequest($request_arr){
        $package_json_string_array = array();
        if(isset($request_arr['package_count']) && is_array($request_arr['package_count']) && count($request_arr['package_count'])>0){
            foreach($request_arr['package_count'] as $key=>$value){
                $bar_code   = $request_arr['bar_code'][$key] ??  "";
                $length     = $request_arr['length'][$key] ??  '0';
                $width      = $request_arr['width'][$key] ??  '0';
                $height     = $request_arr['height'][$key] ??  '0';
                $weight     = $request_arr['weight'][$key] ??  '0';
                $charged_weight  = $request_arr['charged__weight'][$key] ??  '0';
                $data = array(
                    'barCode'=>$bar_code,
                    'packageCount'=>$value,
                    'length'=>$length,
                    'width'=>$width,
                    'height'=>$height,
                    'weight'=>$weight,
                    'chargedWeight'=>$charged_weight,
                    'selectedPackageTypeCode'=>'DOCUMENT'
                );
                array_push($package_json_string_array, $data);
            }
        }

        $array = array(
            "waybillRequestData"=>array(
                "WaybillNumber"=> $request_arr['way_bill_number'] ?? "",
                "DeliveryDate"=> $request_arr['delivery_date']??"",
                "CustomerCode"=> $request_arr['customer_code'],
                "CustomerName"=> $request_arr['customer_name'],
                "CustomerAddress"=> $request_arr['customer_address'],
                "CustomerCity"=> $request_arr['customer_city'],
                "CustomerCountry"=> $request_arr['customer_country'],
                "CustomerPhone"=> $request_arr['customer_phone'],
                "CustomerState"=> $request_arr['customer_state'],
                "CustomerPincode"=> $request_arr['customer_pincode'],
                "ConsigneeCode"=> $request_arr['consignee_code']??"",
                "ConsigneeAddress"=> $request_arr['consignee_address'],
                "ConsigneeCountry"=> $request_arr['consignee_country'],
                "ConsigneeState"=> $request_arr['consignee_state'],
                "ConsigneeCity"=> $request_arr['consignee_city'],
                "ConsigneePincode"=> $request_arr['consignee_pincode'],
                "ConsigneeName"=> $request_arr['consignee_name'],
                "ConsigneePhone"=> $request_arr['consignee_phone'],
                "ClientCode"=> $request_arr['client_code'],
                "NumberOfPackages"=> $request_arr['number_of_packages'],
                "ActualWeight"=> $request_arr['actual_weight'],
                "ChargedWeight"=> $request_arr['charged_weight'],
                "ReferenceNumber"=> $request_arr['reference_number']??"",
                "InvoiceNumber"=> $request_arr['invoice_number']??"",
                "ServiceCode"=> $request_arr['service_code'],
                "WeightUnitType"=> $request_arr['weight_unit_type'],
                "Description"=> $request_arr['description']??"",
                "COD"=> $request_arr['amount']??"",
                "PaymentMode"=> $request_arr['payment_mode'],
                "CODPaymentMode"=> $request_arr['cash_on_pickup']??"",                
                "CreateWaybillWithoutStock"=> "True",
                "packageDetails"=> array(
                    'packageJsonString'=>$package_json_string_array
                )
            )
        );

        return $array;
    }

    public function deleteClientShipmentOtherCharges(Request $request, $id)
    {
        $shipment_other_charges = \App\Models\ShippingPolicy::find($id);
        if ($shipment_other_charges) {
            $shipment_other_charges->delete();
            return response()->json(['status' => true, 'msg' => 'Record deleted successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Please try again']);
        }
    }

    public function sendCustomDutyMail($id){
        if($id){
            $reverse_obj = ReverseLogisticWaybill::where('way_bill_number', $id)->first();
            if(!$reverse_obj){
                return redirect()->back()->with('error', 'Reverse Order not found with #.'.$id. ' waybill number');
            }

            dd($reverse_obj);
        }
    }

    public function ajaxImage(Request $request){
        try {
            $login_id = Auth::user();
            $validator = Validator::make($request->all(),
            [
                'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
            ]);

            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $validator->errors()->first()
                );
            }

            $extension = $request->file('file')->getClientOriginalExtension();
            $dir = 'uploads/';
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $request->file('file')->move(public_path($dir), $filename);
            $imgUrl = 'uploads/'. $filename;

            if(!empty($login_id)){
                $login_id->setMeta('_client_logo' , $imgUrl);
            }

            return $filename;
            
        } catch (\Exception $e) {
            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $e->getMessage()
                );
            }
        }        
    }

    public function packageAjaxImage(Request $request){
        try {
            # image upload code...
            if($request->has('package_id')){
                $pagkage = PackageDetail::find($request->package_id);
                if (!$pagkage) {
                    # code...
                    return array(
                        'fail' => true,
                        'error' => 'Package Detail not found..'
                    );
                }

                if($request->has('status')){
                    $pagkage->where('id', $request->package_id)->update(array('status' => $request->status));
                }

                if($request->has('custom_price')){
                    $pagkage->where('id', $request->package_id)->update(array('custom_price' => $request->custom_price));
                }

                $p_image = [];
                if($pagkage->hasMeta('_package_images')){
                    $p_image = $pagkage->getMeta('_package_images')->toArray();
                }

                if($request->has('images') && $request->TotalImages > 0){
                    $images = $request->file('images');
                    foreach ($images as $key => $value) {
                        $imageName = time().'-'.$key . '.' . $value->getClientOriginalExtension();
                        $value->move($this->upload_path, $imageName);
                        array_push($p_image, $this->upload_path.''.$imageName);
                    }
                    $pagkage->setMeta('_package_images' , $p_image);
                }

                return array(
                    'fail' => true,
                    'error' => 'Action Completed.'
                );
                
            }
            return $filename;
            
        } catch (\Exception $e) {
            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $e->getMessage()
                );
            }
        }
    }

    public function removeImage(Request $request){
        try {
            # image upload code...
            if($request->has('package_id')){
                $pagkage = PackageDetail::find($request->package_id);
                if (!$pagkage) {
                    # code...
                    return array(
                        'fail' => true,
                        'error' => 'Package Detail not found..'
                    );
                }
                
                $images = $pagkage->getMeta('_package_images');
                foreach ($images as $key => $value) {
                    # code...
                    if($value == $request->img_url){
                        unset($images[$key]);
                        // $path = public_path()."/".$request->img_url;
                        // unlink($path);
                        File::delete($request->img_url);
                    }
                }

                $pagkage->setMeta('_package_images' , $images);

                return array(
                    'fail' => true,
                    'error' => 'Action Completed.'
                );
                
            }
            return $filename;
            
        } catch (\Exception $e) {
            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $e->getMessage()
                );
            }
        }
    }

    public function savePallet(Request $request){
        try {
            # image upload code...
            if($request->has('waywill_id')){
                $waywill = ReverseLogisticWaybill::find($request->waywill_id);
                if (!$waywill) {
                    # code...
                    return array(
                        'fail' => true,
                        'error' => 'Return Order not found..'
                    );
                }

                if($request->has('pallet_id')){
                    $waywill->where('id', $request->waywill_id)->update(array('pallet_id' => $request->pallet_id));
                }

                return array(
                    'fail' => true,
                    'error' => 'Action Completed.'
                );
                
            }
            return $filename;
            
        } catch (\Exception $e) {
            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $e->getMessage()
                );
            }
        }
    }

    /**
    * Code By Sanjay
    *
    **/
    public function GetTrackingById($id){
        try {
            $html = '';
            $client    = new Client();
            $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$id;
            // $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.testSecureKey').'&carrierWaybill='.$id;
            $response = $client->get($url);
            $results = json_decode($response->getBody()->getContents());
            if ($results->messageType != 'Success') {
                # code...
                return 'No tracking details available';
            }

            $new_array = $deliverd = $transit = [];
            $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';
            // dd($docket);
            
            if(is_array($docket->docketTrackDetailList) && count($docket->docketTrackDetailList) > 0){
                $trackDetail = reset($docket->docketTrackDetailList);
                // dd($trackDetail);
                if (isset($trackDetail->docketTrackingDetail) && is_array($trackDetail->docketTrackingDetail)) {
                    # code...
                    foreach ($trackDetail->docketTrackingDetail as $value) {
                        # code...
                        $d = date('Y-m-d', strtotime($value->date));
                        /*if (!isset($new_array[$d])) {
                            $new_array[$d] = [$value];
                        } else{
                            array_push($new_array[$d], $value);
                        }*/

                        if(isset($value->waybillStatus) && $value->waybillStatus == 'Delivered'){
                            array_push($deliverd, $value);
                        }

                        if (isset($value->waybillStatus) && in_array($value->waybillStatus, ['In Transit', 'In-Transit'])) {
                            array_push($transit, $value);
                        }
                    }
                }
            }       

            // $deliverd->carrierLabel = '1014';
            // dd($deliverd);
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
                # code...
                $first_transit = reset($transit);
                $first_transit->carrierLabel = '1011';
                $new_array[] = $first_transit;
            }
            if (count($new_array) > 0) {
                # code...
                /*foreach ($new_array as $key => $value) {
                    # code...
                    $html .= '<ul class="progress-tracker progress-tracker--vertical">';
                    $html .= '<div class="dispatch-date">'.date('l, d F', strtotime($key)) .'</div>';
                    foreach ($value as $lt) {
                        # code...
                        $mark = $lt->remarks ?? '';
                        $label = '';
                        if (isset($lt->carrierLabel) && $lt->carrierLabel == '1011') {
                            # code...
                            $mark = 'Received at Post Office';
                            $label = ' | '.$lt->carrierLabel;
                        }
                        if (isset($lt->carrierLabel) && $lt->carrierLabel == '1012') {
                            # code...
                            $mark = 'In Transit to the Hub';
                            $label = ' | '.$lt->carrierLabel;
                        }
                        if (isset($lt->carrierLabel) && $lt->carrierLabel == '1014') {
                            # code...
                            $mark = 'Received Package at our Hub';
                            $label = ' | '.$lt->carrierLabel;
                        }

                        $html .= '<li class="progress-step is-complete">';
                        $html .= '<div class="progress-marker"></div>';
                        $html .= '<div class="progress-text">';
                        $html .= '<h4 class="progress-title">'. $mark. '</h4>';
                        $html .= '<p>'.date('h:i A', strtotime($lt->time)). $label . ' | '. $lt->waybillStatus. '</p>';
                        $html .= '</div></li>';
                    }
                    $html .= '</ul>';
                }*/
                foreach ($new_array as $key => $value) {
                    # code...
                    $html .= '<ul class="progress-tracker progress-tracker--vertical">';
                    $html .= '<div class="dispatch-date">'.date('l, d F', strtotime($value->date)) .'</div>';
                    # code...
                    $mark = $value->remarks ?? '';
                    $label = '';
                    if (isset($value->carrierLabel) && $value->carrierLabel == '1011') {
                        # code...
                        $mark = 'Received at Post Office';
                        $label = ' | '.$value->carrierLabel;
                    }
                    if (isset($value->carrierLabel) && $value->carrierLabel == '1012') {
                        # code...
                        $mark = 'In Transit to the Hub';
                        $label = ' | '.$value->carrierLabel;
                    }
                    if (isset($value->carrierLabel) && $value->carrierLabel == '1014') {
                        # code...
                        $mark = 'Received at Hub';
                        $label = ' | '.$value->carrierLabel;
                    } elseif ($value->waybillStatus == 'Delivered') {
                        # code...
                        $mark = 'Received at Hub';
                        $label = ' | 1014';
                    }

                    $html .= '<li class="progress-step is-complete">';
                    $html .= '<div class="progress-marker"></div>';
                    $html .= '<div class="progress-text">';
                    $html .= '<h4 class="progress-title">'. $mark. '</h4>';
                    $html .= '<p>'.date('h:i A', strtotime($value->time)). $label . ' | '. $value->waybillStatus. '</p>';
                    $html .= '</div></li>';
                    $html .= '</ul>';
                }
            } else{
                $html = 'No tracking details available';
            }

            return $html;   
        } 
        catch (\Exception $e) {
            // return $e->getMessage();
            return 'No tracking details available';
        }        
    }

    /**
    * Code By Sanjay
    *
    **/
    public function getReturnBar(Request $request)
    {
        try {
            $zip = $request->zip;
            // $url = 'https://locations-dev.happyreturns.com/locations?address='.$zip.'&distance=25';
            $url = 'https://locations.happyreturns.com/locations?address='.$zip.'&distance=25';

            $jsonData = json_decode(file_get_contents($url), true);
            if(isset($jsonData['locations']) && count($jsonData['locations']) > 0){
                $address = $jsonData['locations'];
                // $address = reset($jsonData['locations']);
                /*$name = $address['name'];
                $address = $address['address']['address'];
                $city = $address['address']['city'];
                $state = $address['address']['state'];
                $zipcode = $address['address']['zipcode'];
                $openTime = $address['address']['openTime'];
                $closeTime = $address['address']['closeTime'];
                $phoneNumber = $address['address']['phoneNumber'];
                $directions = $address['directions'];
                $hours = $address['hours'];*/
                // dd($address);
                $html = view('pages.frontend.customer.return-bar-address', compact('address'))->render();
                return response()->json(['status' => true, 'html' => $html]);
            }

            return response()->json(['status' => false, 'message' => '<div class="col-lg-7 col-md-12 col-xs-12"><p>No Return Bar™ location found near you. Please select other return options.</p></div>']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => '<div class="col-lg-7 col-md-12 col-xs-12"><p>Error: '.$e->getMessage().'</p></div>']);
        }
    }

    /**
    * Code By Sanjay
    *
    **/
    public function updatePackage(Request $request){
        try {
            # image upload code...
            if($request->has('package_id')){
                $pagkage = PackageDetail::find($request->package_id);
                if (!$pagkage) {
                    # code...
                    return array(
                        'fail' => true,
                        'error' => 'Package Detail not found..'
                    );
                }

                if($request->has('status')){
                    $pagkage->where('id', $request->package_id)->update(array('status' => $request->status));
                }

                if($request->has('estimated_value')){
                    $pagkage->where('id', $request->package_id)->update(array('estimated_value' => $request->estimated_value));
                }

                if($request->has('hs_code')){
                    $pagkage->where('id', $request->package_id)->update(array('hs_code' => $request->hs_code));
                }

                if($request->has('note')){
                    $pagkage->where('id', $request->package_id)->update(array('note' => $request->note));
                }

                if($request->has('refund_status')){
                    $pagkage->where('id', $request->package_id)->update(array('refund_status' => $request->refund_status));
                }                

                return array(
                    'fail' => true,
                    'error' => 'Action Completed.'
                );                
            }            
        } catch (\Exception $e) {
            if ($validator->fails()){
                return array(
                    'fail' => true,
                    'error' => $e->getMessage()
                );
            }
        }
    }

    /**
    *
    * fetch carrier product and services of the carrier
    **/
    public function getCarrierProductAndService(Request $request)
    {
        $carrier_id = $request->carrier_id;
        $cp = CarrierProduct::where(['carrier_id' => $carrier_id])->get();
        $csc = CarrierService::where(['carrier_id' => $carrier_id])->get();

        $cp_htm = view('pages.common.carrier-product', compact('cp'))->render();
        $csc_htm = view('pages.common.carrier-service-code', compact('csc'))->render();

        return response()->json(['cp' => $cp_htm, 'csc' => $csc_htm]);
    }

    /**
    * Change the order return option
    */
    public function changOrderReturnOption(Request $request){
        DB::beginTransaction();
        try {
            $arr = [
                'way_bill_id'=>'required',
                'drop_off'=>'required'
            ];

            $validator = Validator::make($request->all(), $arr);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $reverse_obj = ReverseLogisticWaybill::find($request->way_bill_id);

            # call the happy return api...
            if($request->has('drop_off') && $request->drop_off == 'By_ReturnBar'){                
                $rtn = autoGenerateWaywill($request->all());
                $reverse_obj->setMeta('_drop_off' , 'By_Mail/Courier');
                $reverse_obj->setMeta('_change_return_option' , 'Completed');
                DB::commit();
                if ($rtn) {
                    return redirect()->back()->with('success', 'Your Return request has been successfully processed but some problem with create waywill.');
                } else {
                    return redirect()->back()->with('success', 'Your Return request has been successfully processed.');
                }
            }

            # call the create or generate auto waywill...
            if($request->has('drop_off') && $request->drop_off == 'By_Mail/Courier'){
                $rtn = autoHappyReturnOrder($request->all());
                $reverse_obj->setMeta('_drop_off' , 'By_ReturnBar');
                $reverse_obj->setMeta('_change_return_option' , 'Completed');                
                DB::commit();
                if ($rtn) {
                    return redirect()->back()->with('success', 'Your Return request has been successfully processed but happy return create order not completed.');
                } else {
                    return redirect()->back()->with('success', 'Your Return request has been successfully processed.');
                }
            }            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
    * check mail content here..
    */
    public function checkMailContent(){
        try {
            # send mail to customer...
            $get_view_data['subject']    =   'Missguided Return Confirmation :-7140001615';
            $get_view_data['view']       =   'mails.missguided-order';
            $get_view_data['user']       =   [
                'name' =>  'Rohit Kumar',
                'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                'url' => '',
                'order_no' => '7140001615',
                'track_id' => '71400016157140001615',
                'return_date' => date('d/m/Y'),
                'return_service' => 'Hermes',
                'return_cost' => '20',
            ];

            # sending mail here...
            $to = explode(',', 'vibhuti.sharma@niletechnologies.com,rohit.chauhan@niletechnologies.com,vibhutisharma0605@outlook.com,rohit.chauhan.march@gmail.com');
            
            if(Mail::to($to)->send(new MainTemplate( $get_view_data ))){
                echo 'successfully.';
            } else {
                echo 'Unsuccessfully.';
            }   
        } catch (\Exception $e) {
            echo $e->getMessage();
        }        
    }

    public function updateOrderShipment(Request $request){
        // dd($request->all());
        try {
            $reverse_obj = ReverseLogisticWaybill::find($request->orer_id);
            if(empty($reverse_obj)){
                return response()->json(['msg'=> 'Order not found']);
            }

            if($request->has('shipment_status')){
                $reverse_obj->setMeta('shipment_status' , $request->shipment_status);
            }

            if($request->has('claim_id')){
                $reverse_obj->setMeta('claim_id' , $request->claim_id);
            }

            if($request->has('shipment_date')){
                $reverse_obj->setMeta('shipment_date' , $request->shipment_date);
            }

            return response()->json(['msg'=> 'Action completed. Changes will reflect after page refresh.']);
        } catch (\Exception $e) {
            return response()->json(['msg'=>$e->getMessage()]);
        }
    }

    /**
    * Code By Sanjay
    *
    **/
    public function GetMoreTrackingById($id){
        try {
            $html = '';
            $client    = new Client();
            $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$id;
            // $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.testSecureKey').'&carrierWaybill='.$id;

            $response = $client->get($url);
            $results = json_decode($response->getBody()->getContents());
            if ($results->messageType != 'Success') {
                # code...
                return 'No More tracking details available';
            }

            $new_array = $deliverd = $transit = [];
            $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';
            //dd($docket);
            
            if(is_array($docket->docketTrackDetailList) && count($docket->docketTrackDetailList) > 0){
                $trackDetail = reset($docket->docketTrackDetailList);
                if (isset($trackDetail->docketTrackingDetail) && is_array($trackDetail->docketTrackingDetail)) {
                    foreach ($trackDetail->docketTrackingDetail as $value) {
                        $d = date('Y-m-d', strtotime($value->date));
                        if (!isset($new_array[$d])) {
                            $new_array[$d] = [$value];
                        } else{
                            array_push($new_array[$d], $value);
                        }
                    }
                }
            }

            if (count($new_array) > 0) {
                # code...
                foreach ($new_array as $key => $value) {
                    # code...
                    $html .= '<ul class="progress-tracker progress-tracker--vertical">';
                    $html .= '<div class="dispatch-date">'.date('l, d F', strtotime($key)) .'</div>';
                    foreach ($value as $lt) {
                        $lab = (isset($lt->waybillStatus)) ? $lt->waybillStatus : $lt->actionLabel;
                        # code...
                        $mark = $lt->remarks ?? '';
                        $html .= '<li class="progress-step is-complete">';
                        $html .= '<div class="progress-marker"></div>';
                        $html .= '<div class="progress-text">';
                        $html .= '<h4 class="progress-title">'. $mark. '</h4>';
                        $html .= '<p>'.date('h:i A', strtotime($lt->time)). ' | '. $lab. '</p>';
                        $html .= '</div></li>';
                    }
                    $html .= '</ul>';
                }
            } else{
                $html = 'No More tracking details available';
            }

            return $html;   
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'No tracking details available';
        }        
    }
}
