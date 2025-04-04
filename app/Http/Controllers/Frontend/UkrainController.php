<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\State;
use GuzzleHttp\Client;
use App\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MainTemplate;

class UkrainController extends Controller
{
    public $upload_path;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->upload_path = \Config::get('constants.upload_path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777, true, true );
    }

    public function getQuoteNumber(Request $request, $client){
        $cs = User::where('slug', $client)->first();
        if(empty($cs)){
            return redirect(route('home'))->with('error', 'That client user not be found in the our database.');
        }

        $obj = new User;
        $owner = $obj->getOwnerByUserId($cs->id);

        $post = '';
        if ($request->has('reference_number') && $request->filled('reference_number')) {
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')
                            ->where([['pe.key_name','client_ref'],['pe.key_value', '=' , $request->reference_number]]);
            $post = $get_order->where(['posts.post_type' => 'order', 'posts.sub_client_id' => $cs->user_code])->orderBy('posts.id', 'DESC')->first();
        }
        
        return view('pages.frontend.ukrain.quote-number', compact('post', 'cs', 'owner'));
    }

    public function generateLabel(Request $request){
        $postdata = Post::rightJoin('post_extras','post_extras.post_id','=','posts.id')
                    ->select('post_extras.*')
                    ->where('posts.id',$request->post_id)
                    ->get();
        $order_postmeta = array();
        $orderdata = array();
        foreach($postdata as $postmeta_row)
        {
            $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
        }

        $order_postmeta['post'] = Post::find($request->post_id);
        $order_postmeta['id'] = $request->post_id;
        
        $war_obj = getwharehouse($order_postmeta['customer_country']);
        if($war_obj){
            $country = Country::where('id',$war_obj->country_id)->first();
            $st = State::where('id',$war_obj->state)->first();            
            $request->request->add(['warehouse_id' => $war_obj->id]);
            $request->request->add(['consignee_name' => $war_obj->name]);
            $request->request->add(['ConsigneePhone' => $war_obj->phone]);
            $request->request->add(['ConsigneeAddress' => $war_obj->address]);
            $request->request->add(['ConsigneeCountry' => $country->sortname]);
            $request->request->add(['ConsigneeState' => $st->shortname]);
            $request->request->add(['ConsigneeCity' => $war_obj->city]);
            $request->request->add(['ConsigneePincode' => $war_obj->zip_code]);
            $request->request->add(['ConsigneeEmail' => $war_obj->email]);
            $request->request->add(['FromOU' => $war_obj->FromOU]);
        } else{
            return response()->json(['message' => 'No Warehouse found.', 'status' => 200], 200);
        }
        
        // dd($request->all());
        $carrieravailable =  Carrier::where('countrycode', "Like", "%" . $order_postmeta['customer_country'] . "%")->first();
        if ($request->has('carrier') && $request->filled('carrier')) {
            $carrieravailable = Carrier::where('countrycode', "Like", "%" . $order_postmeta['customer_country'] . "%")->where('code', $request->carrier)->first();
        }

        if(!$carrieravailable)
        {
            return response()->json(['message' => 'No Carrier found.', 'status' => 200], 200);
        }

        $request->request->add(['servicecode' => $carrieravailable->code]);
        $request->request->add(['carrier_name' => $carrieravailable->name]);
        $request->request->add(['unit_type' => $carrieravailable->unit_type]);

        $no_of_pakg = 0 ;
        $this->createShopifyMetaData($no_of_pakg, $order_postmeta, $request , $carrieravailable);

        $randomnumber =  rand ( 100 , 999 );
        if (isset($order_postmeta['order_number'])) {
            $flag = false;
            $get_items = json_decode($order_postmeta['items']);
            foreach ($get_items as $key => $value) {
                if(empty($value->item_number)){
                    $flag = true;
                }
            }

            if ($flag) {
                return response()->json(['message' => 'Order does not have serialNumber.', 'status' => 200], 200);
            }

            //  && $order_postmeta['order_status'] == 'Pending'
            if(empty($order_postmeta['sales_order_status'])){
                $res   = $this->createSalesOrder($order_postmeta);
                $response  = $res->getBody()->getContents();
                $response_data = json_decode($response);                
                if ($response_data->messageType == 'Error') {
                    set_post_key_value($order_postmeta['id'], 'sales_order_response', json_encode($response));
                    $rtn_msg = 'Sales Api:- '.$response_data->message;
                    return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                }

                # new line update sales order...
                if(isset($response_data->salesInvoiceNumber)){
                    set_post_key_value($request->post_id, 'sales_order_status', $response);
                } else {
                    $rtn_msg = 'Sales Api:- Sales invoice number not found';
                    return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                }                
            } else {
                $response_data = json_decode($order_postmeta['sales_order_status']);
            }

            $sno = str_replace('/\/', ' ', $response_data->salesInvoiceNumber);

            $sq_rg_no = 'LSC'.$request->post_id. str_replace(' ', '', $order_postmeta['order_number']).'-'.$randomnumber;
            $waybill_array = $this->createOutboundOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable, $sno);
        } else {
            $sq_rg_no = 'LSC'.$request->post_id.$order_postmeta['client_ref'].'-'.$randomnumber;
            $waybill_array = $this->createSopifyOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable);
        }
                
        $js_data = json_encode($waybill_array);
        set_post_key_value($request->post_id, 'create_waywill_request', $js_data);
        $cr_array['create_waywill_request'] = $js_data;
        setCustomMeta($order_postmeta['post'], $cr_array);
        $cr_array['waywill_number'] = $sq_rg_no;

        if(!isset($order_postmeta['create_waybill_status'])){
            $create_response = $this->createShopifyOrderWaywillResponse($js_data);
            $create_data = json_decode($create_response);
            $cr_array['create_waywill_response'] = $create_response;
            // dd($create_data);
            if (empty($create_data)) {
                $rtn_msg = "Create waywill Api:- no response";
                return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
            } elseif (isset($create_data->messageType) && $create_data->messageType == 'Error') {            
                $meta_array['create_waywill_data'] = json_encode($create_data);
                setCustomMeta($order_postmeta['post'], $meta_array);
                $rtn_msg = 'Create waywill Api:- '.$create_data->message;
                set_post_key_value($request->post_id, 'create_waybill_response', $create_response);
                return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
            } else{
                $meta_array['label_message']             = $create_data->message;
                $meta_array['label_message_type']        = $create_data->messageType;
                $meta_array['label_message_status']      = $create_data->status;
                $meta_array['label_package_sticker_url'] = $create_data->packageStickerURL;
                $meta_array['label_url']                 = $create_data->labelURL;
                $meta_array['waybillNumber']             = $create_data->waybillNumber;
                $meta_array['create_waywill_data']       = json_encode($create_data);
                setCustomMeta($order_postmeta['post'], $meta_array);
                set_post_key_value($request->post_id, 'create_waybill_status', $create_response);
                set_post_key_value($request->post_id, 'waybillNumber', $create_data->waybillNumber);
            }
        } else {
            $create_data = json_decode($order_postmeta['create_waybill_status']);
        }

        $g_arr = [
            'waybillNumber' => $create_data->waybillNumber,
            'carrierCode'    => $carrieravailable->code,
            'aggregator'     => '',
            'labelFormat'     => 'PNG',
            'carrierProduct' => ($carrieravailable->product[0]->code) ? ($carrieravailable->product[0]->code): "",
        ];

        if (isset($order_postmeta['order_number'])) {
            $g_arr['labelType'] = '4x6';
        }

        $gr_array['generate_waywill_request'] = json_encode($g_arr);
        $cr_array['generate_waywill_request'] = json_encode($g_arr);
        setCustomMeta($order_postmeta['post'], $gr_array);
        set_post_key_value($request->post_id, 'generate_waybill_request', json_encode($g_arr));

        $gr_response = $this->generateShopifyOrderWaywillResponse($g_arr);            
        $gr_json = json_decode($gr_response);
        
        // dd($gr_response);

        if (empty($gr_json)) {                
            $rtn_msg = "Generate waywill Api:- no response";
            return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
        } elseif (isset($gr_json->messageType) && $gr_json->messageType == 'Error') {
            $meta_array['generate_waywill_status'] = json_encode($gr_json);
            setCustomMeta($order_postmeta['post'], $meta_array);
            set_post_key_value($request->post_id, 'generate_waybill_response', $gr_response);

            $rtn_msg = 'Generate waywill Api:- This return method is currently unavailable. Please try again later.';
            // $rtn_msg = 'Generate waywill Api:- '.$gr_json->message;
            return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
        }

        $label = reset($gr_json->labelDetailList);
        $meta_array['generate_waywill_status'] = json_encode($gr_json);
        $meta_array['tracking_id'] = $gr_json->carrierWaybill;
        $meta_array['label_url'] = $label->artifactUrl;
        setCustomMeta($order_postmeta['post'], $meta_array);

        set_post_key_value($request->post_id, 'generate_waybill_status', $gr_response);
        set_post_key_value($request->post_id, 'order_reference_number', $waybill_array['waybillRequestData']['WaybillNumber']);
        set_post_key_value($request->post_id, 'tracking_id', $gr_json->carrierWaybill);
        set_post_key_value($request->post_id, 'label_url', $label->artifactUrl);
        set_post_key_value($request->post_id, 'order_status', 'Completed');

        # send mail to customer...
        if (in_array($order_postmeta['client_id'], ['RG00000008']) && in_array($order_postmeta['subclient_id'], ['RG00000037', 'RG00000030'])) {
            $get_view_data['subject']    =   'Virginia Mileage Choice Program - OBD Return Label';
            $get_view_data['view'] = 'mails.ims-order';
        } else {
            if (isset($order_postmeta['order_number'])) {
                $get_view_data['subject']    =   'LinkShipcycle :-'.$order_postmeta['order_number'] ?? $request->post_id;
            } else {
                $get_view_data['subject']    =   'LinkShipcycle :-'.$order_postmeta['client_ref'] ?? $request->post_id;
            }
            $get_view_data['view'] = 'mails.jaded-order';
        }
        $get_view_data['attach_pdf'] = '';
        $get_view_data['pdf_filename'] = '';

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
        $result = file_put_contents('public/'.$path_upload, $pdf_data);
        if ($result) {
            $get_view_data['attach_pdf'] = public_path($path_upload);
            $get_view_data['pdf_filename'] = $fileName;
            $pdf_url = asset('public/'.$path_upload);
            set_post_key_value($request->post_id, 'attachment_pdf', $path_upload);
        }

        $get_view_data['user']       =   [
            'name' =>  $order_postmeta['customer_name'],
            'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
            'url' => $pdf_url,
            'order_no' => $order_postmeta['client_ref'] ?? $request->post_id,
            'track_id' => $gr_json->carrierWaybill ?? '',
            'return_date' => date('d/m/Y'),
            'return_service' => 'Postal Service',
            'description' => $order_postmeta['description'] ?? '',
        ];

        try{
            $m = $order_postmeta['customer_email_id'] ?? 'vibhuti.mca@gmail.com';
            // $m = 'vibhuti.mca@gmail.com';
            if (isset($order_postmeta['client_ref'])) {
                $mail = Mail::to($m)->send(new MainTemplate( $get_view_data ));
            }
            return response()->json(['pdf_url' => $label->artifactUrl, 'message' => 'Label generated successfully.', 'track_id' => $gr_json->carrierWaybill, 'status' => 201], 201);
        }catch(\Swift_TransportException $transportExp){                
            return response()->json(['pdf_url' => $label->artifactUrl, 'message' => 'Mail not send but Label generated successfully.', 'track_id' => $gr_json->carrierWaybill, 'status' => 201], 201);
        }
    }


    public function createShopifyMetaData($no_of_pakg, $order_postmeta, $request , $carrieravailable){
        # set Meta for Waybillnumber...        
        $meta_array = [
            'customer_name' => $order_postmeta['customer_name'],
            'customer_email' => $order_postmeta['customer_email_id'] ?? 'customer@example.com',
            'customer_order_email' => $order_postmeta['customer_email_id'] ?? 'customer@example.com',
            'customer_address' => $order_postmeta['customer_address_line_1'],
            'customer_country' => $order_postmeta['customer_country'],
            'customer_state' => $order_postmeta['customer_state'] ?? '',
            'customer_city' => $order_postmeta['customer_city'],
            'customer_pincode' => $order_postmeta['customer_pincode'],
            'customer_phone' => $order_postmeta['customer_phone'],
            'service_code' => $request->servicecode,
            'warehouse_id' => $request->warehouse_id,
            'number_of_packages' => $no_of_pakg,
            'actual_weight' => 0,
            'charged_weight' => 0,
            'client_code' => 'REVERSEGEAR',
            'customer_code' => '0',
            'label_url' => '',
            'label_package_sticker_url' => '',
            'drop_off' => 'By_Courier',
            'source' => 'CUSTOMER',
            'source_name' => 'N/A',
            'waiver' => 'N/A',
            'order_type' => 'N/A',
            'consignee_name' => $request->consignee_name,
            'ConsigneeContactPerson'=> '',
            'shipment_name' => '',
            'carrier_name' => $carrieravailable->name,
            'unit_type' => $carrieravailable->unit_type,
            'rtn_total' => '',
            'currency' => '',
            'payment_mode' => $order_postmeta['payment_mode'],
            'shipping_charges' => '',
            'return_charges' => '',
            'env_amount' => $request->env_amount,
            'curated_id' => '',
            'remark' => '',
            'all_request' => json_encode($request),
        ];
        // dd($meta_array);
        setCustomMeta($order_postmeta['post'],$meta_array);
    }


    public function createSopifyOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable){
        # create package array...
        $package_array = array();
        $no_of_pakg = 0;

        $package = array(
            'barCode' => '',
            'packageCount' => 1,
            'length' => $request->length ?? '10',
            'width' => $request->width ?? '8',
            'height' => $request->height ?? '3',
            'weight' => $request->weight ?? '0.5',
            'chargedWeight' => $request->weight ?? '0.5',
            'selectedPackageTypeCode'=>'BOX',
            'itemCount' => 1
        );
        array_push($package_array, $package);

        $phone = $order_postmeta['customer_phone'];
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        if (isset($order_postmeta['order_number'])) {
            $ref = $order_postmeta['client'].' '.$order_postmeta['subclient'].' '.$order_postmeta['order_number'];
        } else {
            $ref = $order_postmeta['client'].' '.$order_postmeta['subclient'].' '.$order_postmeta['client_ref'];
        }

        $waybill_array = array(
            "waybillRequestData" => array(
                "consigneeGeoLocation" => "",
                "FromOU" => $request->FromOU,
                "DeliveryDate" => "",
                "WaybillNumber" => $sq_rg_no,
                "CustomerCountry" => $order_postmeta['customer_country'],
                "CustomerState" => $order_postmeta['customer_state'] ?? $order_postmeta['customer_city'],
                "CustomerCity" => $order_postmeta['customer_city'],
                "CustomerPincode" => $order_postmeta['customer_pincode'],
                "CustomerName" => $order_postmeta['customer_name'],
                "CustomerAddress" => $order_postmeta['customer_address_line_1'],
                "CustomerEmail" => $order_postmeta['customer_email_id'] ?? 'customer@example.com',
                "CustomerPhone" => $order_postmeta['customer_phone'],
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
                "ConsigneeContactPerson" => '',
                "ConsigneeWhat3Words" => "",
                "CreateWaybillWithoutStock" => "true",
                "stockIn" => true,
                "StartLocation" => "",
                "EndLocation" => "",
                "ClientCode" => $carrieravailable->ClientCode,
                "NumberOfPackages" => 1,
                "ActualWeight" => $request->weight ?? 0.5,
                "ChargedWeight" => $request->weight ?? 0.5,
                "CargoValue" => 5,
                "ReferenceNumber" => $order_postmeta['client_ref'] ?? $request->post_id,
                "InvoiceNumber" => $sq_rg_no,
                "PaymentMode" => "TBB",
                "ServiceCode" => $carrieravailable->service[0]->code,                
                "WeightUnitType" => $carrieravailable->unit_type,
                "Description" => "Client return order",
                "COD" => "",
                "Currency" => $order_postmeta['currency'],
                "salesInvoiceNumber" => "",
                "CODPaymentMode" => "",
                "skipCityStateValidation" => true,
                "packageDetails" => array(
                    'packageJsonString' => $package_array
                    )                        
                )
        );
        // dd($waybill_array);
        return $waybill_array;
    }

    public function createOutboundOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable, $sno){
        # create package array...
        $package_array = array();
        $no_of_pakg = 0;

        $package = array(
            'barCode' => '',
            'packageCount' => 1,
            'length' => $request->length ?? '10',
            'width' => $request->width ?? '8',
            'height' => $request->height ?? '3',
            'weight' => $request->weight ?? '0.5',
            'chargedWeight' => $request->weight ?? '0.5',
            'selectedPackageTypeCode'=>'BOX',
            'itemCount' => 1
        );
        array_push($package_array, $package);

        $phone = $order_postmeta['customer_phone'];
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        if (isset($order_postmeta['order_number'])) {
            $ref = $order_postmeta['client'].' '.$order_postmeta['subclient'].' '.$order_postmeta['order_number'];
        } else {
            $ref = $order_postmeta['client'].' '.$order_postmeta['subclient'].' '.$order_postmeta['client_ref'];
        }

        $waybill_array = array(
            "waybillRequestData" => array(
                "consigneeGeoLocation" => "",
                "FromOU" => $request->FromOU,
                "DeliveryDate" => "",
                "WaybillNumber" => $sq_rg_no,                
                "CustomerName" => $request->consignee_name,
                "CustomerEmail" => $request->ConsigneeEmail,
                "CustomerPhone" => $request->ConsigneePhone,
                "CustomerAddress" => $request->ConsigneeAddress,
                "CustomerCity" => $request->ConsigneeCity,
                "CustomerCountry" => $request->ConsigneeCountry,
                "CustomerState" => $request->ConsigneeState,
                "CustomerPincode" => $request->ConsigneePincode,                        
                "CustomerCode" => "00000",
                "ConsigneeCode" => "00000",
                "ConsigneeName" => $order_postmeta['customer_name'],
                "ConsigneePhone" => $order_postmeta['customer_phone'],
                "ConsigneeAddress" => $order_postmeta['customer_address_line_1'],
                "ConsigneeCountry" => $order_postmeta['customer_country'],
                "ConsigneeState" => $order_postmeta['customer_state'] ?? $order_postmeta['customer_city'],
                "ConsigneeCity" => $order_postmeta['customer_city'],
                "ConsigneePincode" => $order_postmeta['customer_pincode'],
                "ConsigneeEmail" => $order_postmeta['customer_email_id'] ?? 'customer@example.com',
                "ConsigneeContactPerson" => '',
                "ConsigneeWhat3Words" => "",
                "CreateWaybillWithoutStock" => "true",
                "stockIn" => true,
                "StartLocation" => "",
                "EndLocation" => "",
                "ClientCode" => $carrieravailable->ClientCode,
                "NumberOfPackages" => 1,
                "ActualWeight" => $request->weight ?? 0.5,
                "ChargedWeight" => $request->weight ?? 0.5,
                "CargoValue" => 5,
                "ReferenceNumber" => $order_postmeta['client_ref'] ?? $request->post_id,
                "InvoiceNumber" => $sq_rg_no,
                "PaymentMode" => "TBB",
                "ServiceCode" => $carrieravailable->service[0]->code,                
                "WeightUnitType" => $carrieravailable->unit_type,
                "Description" => "Client LinkShipcycle Order",
                "COD" => "",
                "Currency" => $order_postmeta['currency'],
                "salesInvoiceNumber" => $sno,
                "CODPaymentMode" => "",
                "skipCityStateValidation" => true,
                "packageDetails" => array(
                    'packageJsonString' => $package_array
                    )                        
                )
        );
        // dd($waybill_array);
        return $waybill_array;
    }


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
            Log::channel('shopify_order')->info($str);
            return null;
        }       
    }

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
            Log::channel('shopify_order')->info($str);
            return null;
        }
    }

    /*
    * scan data 
    */
    public function syncOldTrackingData(){
        try {
            $client    = new Client();

            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
            $insert_data = $get_order->where(['posts.post_type' => 'order', 'posts.cron_status' => '0'])->orderBy('posts.id', 'DESC')->get();

            Post::join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']])->where(['posts.post_type' => 'order', 'posts.cron_status' => '0'])->orderBy('posts.id', 'DESC')->chunk(10, function ($records) {
                dd($records);
            });

            dd($insert_data);
            foreach ($insert_data as $key => $account) {
                $tracking_id = $account->meta->_waybillNumber ?? '';                
                if (empty($tracking_id)) {
                    continue;
                }
                $url = 'https://api.logixplatform.com/webservice/v2/MultipleWaybillTracking?SecureKey='.Config('constants.secureKey').'&WaybillNumber='.$tracking_id;
                $response = $client->get($url);
                $results = json_decode($response->getBody()->getContents());

                if(isset($results->messageType) && $results->messageType == 'Error' && !empty($waybill_obj)){
                    continue;
                } else {
                    if(isset($results->waybillTrackDetailList) && !empty($results->waybillTrackDetailList)){                        
                        $deliverd = $fscan = $transit = [];
                        foreach($results->waybillTrackDetailList as $trackDetails){
                            if (isset($trackDetails->waybillTrackingDetail) && is_array($trackDetails->waybillTrackingDetail)) {
                                foreach ($trackDetails->waybillTrackingDetail as $value) {
                                    if(isset($value->waybillStatus) && $value->waybillStatus == 'Delivered'){
                                        array_push($deliverd, $value);
                                    }

                                    if (isset($value->waybillStatus) && in_array($value->waybillStatus, ['In Transit', 'In-Transit'])) {
                                        array_push($transit, $value);
                                    }

                                    if (isset($value->carrierLabel) && in_array($value->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])) {
                                        array_push($fscan, $value);
                                    }

                                    if (isset($value->remarks) && in_array($value->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])) {
                                        array_push($fscan, $value);
                                    }
                                }
                            }
                        }

                        if (count($fscan) <= 0) {
                            continue;
                        }

                        set_post_key_value($account->id, 'order_waywill_data', json_encode($trackDetails->waybillTrackingDetail));
                        set_post_key_value($account->id, 'order_waywill_status', $trackDetails->currentStatus);
                        // $carrier_name = $account->meta->_carrier_name ?? 'None';

                        # for inpost code...
                        if(count($transit) > 1){
                            $first_transit = reset($transit);
                            $last_transit = end($transit);

                            if (count($fscan) > 0){
                                $first_transit = reset($fscan);
                                set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'First Scan';
                            }
                            
                            if(isset($first_transit->carrierLabel) && !in_array($first_transit->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])){
                                set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'In Transit';
                            }
                            
                            if(isset($first_transit->remarks) && !in_array($first_transit->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])){
                                set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'In Transit';
                            }

                            /**/
                            /*else {
                                $first_transit = end($transit);
                                set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'First Scan';   
                            }*/
                        } elseif (count($transit) == 1) {
                            # code...
                            if (count($fscan) > 0) {
                                $first_transit = reset($fscan);
                                set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'First Scan';
                            } else {
                                $first_transit = reset($transit);
                                set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                $account->inscan_status = 'First Scan';
                            }
                        }

                        if(count($deliverd) > 1){
                            $last_d = reset($deliverd);
                            set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));                                
                            $account->inscan_status = 'Delivered';
                            $account->cron_status = 2;
                        } elseif (count($deliverd) == 1) {
                            $last_d = reset($deliverd);
                            set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));
                            $account->inscan_status = 'Delivered';
                            $account->cron_status = 2;
                        }
                        
                        $account->save();
                    }
                }
            }

            Log::channel('shopify_order')->info('Cron Status:- '. $account->id. ' is Success');
            return 'Success';
        } catch (\Exception $e) {
            Log::channel('shopify_order')->info('Cron Status:- '. $e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncTrackingData(){
        try {
            Post::join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']])->where(['posts.post_type' => 'order', 'posts.cron_status' => '0'])->orderBy('posts.id', 'DESC')->chunk(10, function ($records) {
                // dd($records);
                $rg_reference_number = [];
                foreach ($records as $record) {
                    array_push($rg_reference_number, $record->meta->_waybillNumber ?? '');
                }

                $waywill_num = implode(',', $rg_reference_number);
                $client    = new Client();
                $url = 'https://api.logixplatform.com/webservice/v2/MultipleWaybillTracking?SecureKey=DB7FACCA8A3640648D918B4A4818178A&WaybillNumber='.$waywill_num;
                $response = $client->get($url);
                $results = json_decode($response->getBody()->getContents());
                // dd($results);
                if(isset($results->waybillTrackDetailList) && count($results->waybillTrackDetailList) > 0){
                    $count = count($results->waybillTrackDetailList) - 1;
                    foreach($results->waybillTrackDetailList as $k => $trackDetails){
                        $deliverd = $fscan = $transit = [];
                        if (isset($trackDetails->waybillTrackingDetail) && is_array($trackDetails->waybillTrackingDetail)) {
                            foreach ($trackDetails->waybillTrackingDetail as $value) {

                                if(isset($value->carrierLabel) && $value->carrierLabel == '114'){
                                    array_push($deliverd, $value);
                                } elseif(isset($value->waybillStatus) && $value->waybillStatus == 'Delivered'){
                                    array_push($deliverd, $value);
                                }
                                
                                if (isset($value->waybillStatus) && in_array($value->waybillStatus, ['In Transit', 'In-Transit'])) {
                                    array_push($transit, $value);
                                }

                                if (isset($value->carrierLabel) && in_array($value->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])) {
                                    array_push($fscan, $value);
                                }

                                if (isset($value->remarks) && in_array($value->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])) {
                                    array_push($fscan, $value);
                                }
                            }
                        }

                        if (count($fscan) <= 0) {
                            continue;
                        }

                        $account = Post::join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where('pes.key_name', 'waybillNumber')->where('pes.key_value', $trackDetails->waybillNumber)->first();
                        if (!empty($account)) {
                            set_post_key_value($account->id, 'order_waywill_data', json_encode($trackDetails->waybillTrackingDetail));
                            set_post_key_value($account->id, 'order_waywill_status', $trackDetails->currentStatus);

                            # for inpost code...
                            if(count($transit) > 1){
                                $first_transit = reset($transit);
                                $last_transit = end($transit);

                                if (count($fscan) > 0){
                                    $first_transit = reset($fscan);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                }
                                
                                if(isset($first_transit->carrierLabel) && !in_array($first_transit->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])){
                                    set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'In Transit';
                                }
                                
                                if(isset($first_transit->remarks) && !in_array($first_transit->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])){
                                    set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'In Transit';
                                }

                                /**/
                                /*else {
                                    $first_transit = end($transit);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';   
                                }*/
                            } elseif (count($transit) == 1) {
                                # code...
                                if (count($fscan) > 0) {
                                    $first_transit = reset($fscan);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                } else {
                                    $first_transit = reset($transit);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                }
                            }

                            if(count($deliverd) > 1){
                                $last_d = reset($deliverd);
                                set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));                                
                                $account->inscan_status = 'Delivered';
                                $account->cron_status = 2;
                            } elseif (count($deliverd) == 1) {
                                $last_d = reset($deliverd);
                                set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));
                                $account->inscan_status = 'Delivered';
                                $account->cron_status = 2;
                            }
                            
                            $account->save();
                        }
                    }
                }
            });

            return 'Success';
        } catch (\Exception $e) {
            Log::channel('shopify_order')->info('Cron Status:- '. $e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncOutboundTrackingData(){
        try {
            $client    = new Client();

            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['Completed', 'Shipped']);
            $insert_data = $get_order->where(['posts.post_type' => 'outbound_order', 'posts.cron_status' => '0'])->orderBy('posts.id', 'DESC')->get();

            if ($insert_data->isNotEmpty()) {
                foreach ($insert_data as $key => $account) {
                    $tracking_id = $account->meta->_waybillNumber ?? '';
                    if (empty($tracking_id)) {
                        continue;
                    }
                    $url = 'https://api.logixplatform.com/webservice/v2/MultipleWaybillTracking?SecureKey='.Config('constants.secureKey').'&WaybillNumber='.$tracking_id;
                    $response = $client->get($url);
                    $results = json_decode($response->getBody()->getContents());

                    if(isset($results->messageType) && $results->messageType == 'Error' && !empty($waybill_obj)){
                        continue;
                    } else {
                        if(isset($results->waybillTrackDetailList) && !empty($results->waybillTrackDetailList)){                        
                            $deliverd = $fscan = $transit = [];
                            foreach($results->waybillTrackDetailList as $trackDetails){
                                if (isset($trackDetails->waybillTrackingDetail) && is_array($trackDetails->waybillTrackingDetail)) {
                                    foreach ($trackDetails->waybillTrackingDetail as $value) {
                                        if(isset($value->waybillStatus) && $value->waybillStatus == 'Delivered'){
                                            array_push($deliverd, $value);
                                        }

                                        if (isset($value->waybillStatus) && in_array($value->waybillStatus, ['In Transit', 'In-Transit'])) {
                                            array_push($transit, $value);
                                        }

                                        if (isset($value->carrierLabel) && in_array($value->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])) {
                                            array_push($fscan, $value);
                                        }

                                        if (isset($value->remarks) && in_array($value->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])) {
                                            array_push($fscan, $value);
                                        }
                                    }
                                }
                            }

                            /*if (count($fscan) <= 0) {
                                continue;
                            }*/

                            set_post_key_value($account->id, 'order_waywill_data', json_encode($trackDetails->waybillTrackingDetail));
                            set_post_key_value($account->id, 'order_waywill_status', $trackDetails->currentStatus);
                            // $carrier_name = $account->meta->_carrier_name ?? 'None';

                            # for inpost code...
                            if(count($transit) > 1){
                                $first_transit = reset($transit);
                                $last_transit = end($transit);

                                if (count($fscan) > 0){
                                    $first_transit = reset($fscan);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                }
                                
                                if(isset($first_transit->carrierLabel) && !in_array($first_transit->carrierLabel, ['PPU', 'OR', 'Received by Australia Post'])){
                                    set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'In Transit';
                                }
                                
                                if(isset($first_transit->remarks) && !in_array($first_transit->remarks, ['PROCESSED THROUGH USPS FACILITY', 'Sorting done at departure depot', 'FirstReceipt'])){
                                    set_post_key_value($account->id, 'order_waywill_in_transit', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'In Transit';
                                }

                                /**/
                                /*else {
                                    $first_transit = end($transit);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';   
                                }*/
                            } elseif (count($transit) == 1) {
                                # code...
                                if (count($fscan) > 0) {
                                    $first_transit = reset($fscan);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                } else {
                                    $first_transit = reset($transit);
                                    set_post_key_value($account->id, 'order_waywill_status_date', date('Y-m-d', strtotime($first_transit->date)));
                                    $account->inscan_status = 'First Scan';
                                }
                            }

                            if(count($deliverd) > 1){
                                $last_d = reset($deliverd);
                                set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));                                
                                $account->inscan_status = 'Delivered';
                                $account->cron_status = 2;
                            } elseif (count($deliverd) == 1) {
                                $last_d = reset($deliverd);
                                set_post_key_value($account->id, 'order_waywill_deliverd', date('Y-m-d', strtotime($last_d->date)));
                                $account->inscan_status = 'Delivered';
                                $account->cron_status = 2;
                            }
                            
                            $account->save();
                        }
                    }
                }
            }

            Log::channel('shopify_order')->info('Cron Status:- '. $account->id. ' is Success');
            return 'Success';
        } catch (\Exception $e) {
            Log::channel('shopify_order')->info('Cron Status:- '. $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Create sales order
     * @param array
     */
    public function createSalesOrder($order){
        $client    = new Client();
        $products  = [];
        $i         = 0;
        $get_items = json_decode($order['items']);
        foreach ($get_items as $key => $value) {
            $sku_items = findGoodSKu($value->sku);
            if (!empty($sku_items)) {
                foreach ($sku_items as $k => $v) {
                    if ($v['component_type'] == 'Device') {
                        $products[$k]['serialNumbers'] = $value->item_number;
                    } else {
                        $products[$k]['serialNumbers'] = "";
                    }
                    $products[$k]['product_sku']    = str_replace(' ', '', $v['component_pn']);
                    $products[$k]['price']          = $value->price;
                    $products[$k]['quantity']       = $value->quantity ?? 1;
                    $products[$k]['measurmentUnit'] = 'Pieces';
                }
            } else {
                $sku_url = 'https://api.logixplatform.com/webservice/v2/GetStockProductWise?secureKey=DB7FACCA8A3640648D918B4A4818178A&warehouseCode=CALGARY&productCode='.str_replace(' ', '', $value->sku);
                $jsonData = json_decode(file_get_contents($sku_url), true);
                if(isset($jsonData['stock'][0]['partNumber'])){
                    $products[$i]['product_sku']    = $jsonData['stock'][0]['partNumber'];
                } else {
                    $products[$i]['product_sku']    = str_replace(' ', '', $value->sku);
                }
                $products[$i]['price']          = $value->price;
                $products[$i]['quantity']       = $value->quantity ?? 1;
                $products[$i]['measurmentUnit'] = 'Pieces';
                $products[$i]['serialNumbers'] = $value->item_number;
                
                $i++;
            }
        }

        # shipping address...
        $shipperName     = $order['customer_name'];
        $shippingAddress = $order['customer_address_line_1'] . ' ' . $order['customer_address_line_2'];
        $shippingCity    = $order['customer_city'];
        $shippingState   = $order['customer_state'];
        $shippingCountry = $order['customer_country'];
        $shippingPincode = $order['customer_pincode'];
        $shippingPhone = $order['customer_phone'];

        # customer address...
        $customerName    = '';
        $customerAddress = '';
        $customerCity    = '';
        $customerState   = '';
        $customerCountry = '';
        $customerPincode = '';
        $customerPhone   = '';
        $customerEmail   = '';

        $form_data = [
            'salesOrderNumber'      => 'LSC-'.date('his').'-'.$order['id'],
            'warehouse'             => 'CALGARY',
            'SecureKey'             => Config('constants.secureKey'),
            'products'              => json_encode($products),
            'customerCode'          => 'IMS',
            'receiverName'          => $shipperName,
            'shipperName'           => $shipperName,
            'shippingAddress'       => $shippingAddress,
            'shippingCity'          => $shippingCity,
            'shippingState'         => $shippingState,
            'shippingCountry'       => $shippingCountry,
            'shippingPincode'       => $shippingPincode,
            'shippingPhone'         => $shippingPhone,
            'customerName'          => $customerName,
            'customerAddress'       => $customerAddress,
            'customerCity'          => $customerCity,
            'customerState'         => $customerState,
            'customerCountry'       => $customerCountry,
            'customerPincode'       => $customerPincode,
            'customerPhone'         => $customerPhone,
            'customerEmail'         => $customerEmail,
            'remarks'               => 'Order from Client',
            'createInvoice'         => 'true',
            'username'              => 'ecomglobal',
            'paymentType'           => 'PREPAID',
            'eCommerceSite'         => 'LinkShipcycle',
            'carrierCode'           => '',
            'carrierProductCode'    => '',
            'dutyPaidBy'            => 'SENDER',
        ];

        // dd($form_data);
        set_post_key_value($order['id'], 'sales_order_request', json_encode($form_data));
        $res = $client->post(Config('constants.salesOrderUrl'), [
            'form_params' => $form_data,
        ]);

        return $res;
    }
}
