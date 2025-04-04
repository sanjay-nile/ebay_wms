<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

use App\User;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\State;
use App\Models\PalletDeatil;
use App\Models\Meta;
use App\Models\Category;
use App\Models\FormBuilder;
use App\Models\EbayPackage;
use App\Models\StatusHistory;
use App\Models\Option;
use Auth;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Validator;
use PHPExcel_Shared_Date;
use Session;

use Excel;
use App\Exports\ParcelOrdersExport;
use App\Exports\ItemOrdersExport;
use Zip;
use Milon\Barcode\DNS1D;

class OrderController extends Controller
{
    public $perPage = 250;
    protected $guard = 'admin';

    public function __construct() {
        $this->middleware('auth:admin')->except('getOrdersFromEbay', 'getCancelOrdersFromEbay');
    }

    /**
    * manage order meta key and value based
    */
    public function manageAllVendorOrders($get_order){
        $order_data = array();

        if (count($get_order) > 0) {
            foreach ($get_order as $order) {
                $order_postmeta           = array();
                $get_postmeta_by_order_id = $order['post_extras'];

                if(isset($order['package'])){
                    $order_postmeta['packages'] = $order['package'];
                }

                if (count($get_postmeta_by_order_id) > 0) {
                    $date_format                   = new Carbon($order['created_at']);
                    $update_format                   = new Carbon($order['updated_at']);
                    $order_postmeta['_post_id']    = $order['id'];
                    $order_postmeta['_pallet_id']    = $order['pallet_id'] ?? '';
                    $order_postmeta['_order_date'] = $date_format->toDayDateTimeString();
                    $order_postmeta['_update_date'] = $update_format->toDayDateTimeString();
                    $order_postmeta['process_status'] = $order['process_status'] ?? '' ;
                    $order_postmeta['pallet_id']      = $order['pallet_id'] ?? '' ;

                    foreach ($get_postmeta_by_order_id as $postmeta_row) {
                        $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
                    }

                    array_push($order_data, $order_postmeta);
                }
            }
        }

        return $order_data;
    }

    /**
     * warehouse list
     **/
    public function getWarehouseList($id = null){
        $data = array();
        $data['country_list'] = Country::where(['status' => '1'])->get();
        $data['list']         = Warehouse::all();
        $data['clients']         = User::where('user_type_id', 3)->get();

        if (!empty($id)) {
            $data['single_wh']  = Warehouse::find($id);
            $data['state_list'] = State::where(['status' => '1', 'country_id' => $data['single_wh']->country_id])->get();
            $data['assignedcountry'] = explode(',',$data['single_wh']->assigned_country) ;
            // echo '<pre>';
            // print_r($data['assignedcountry']);
            // die;
        }

        return view('pages.admin.warehouse.warehouse', $data);
    }

    /**
     * Add or edit warehose
     */
    public function addWarehouse(Request $request){
        try {
            $data  = $request->all();
            $rules = [
                'name'           => 'required',
                'contact_person' => 'required',
                'email'          => 'required',
                'phone'          => 'required',
                'address'        => 'required',
                'country'        => 'required',
                'city'           => 'required',
                'zip_code'       => 'required',
            ];

            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            
            $assign = null;
            if($request->has('assign_to') && $request->has('assign_to')){
                $assign = implode(',', $request->assign_to);
            }
            if ($request->wh_id) {
                $id = Warehouse::where('id', $request->wh_id)->update([
                    'name'           => $request->name,
                    'country_id'     => $request->country,
                    'contact_person' => $request->contact_person,
                    'email'          => $request->email,
                    'state'       => $request->state_id,
                    'city'           => $request->city,
                    'zip_code'       => $request->zip_code,
                    'address'        => $request->address,
                    'phone'          => $request->phone,
                    'user_id'      => $request->client_id ?? Auth::id(),
                    // 'carrier_id'      => $request->carrier_id,
                    // 'code'      => $request->client_code,
                    'assigned_country'      => $assign,
                ]);
                $msg = 'Warehouse has been updated successfully';
            } else {
                $ware_obj                 = new Warehouse;
                $ware_obj->name           = $request->name;
                $ware_obj->contact_person = $request->contact_person;
                $ware_obj->email          = $request->email;
                $ware_obj->country_id     = $request->country;
                $ware_obj->state          = $request->state_id ?? $request->state;
                $ware_obj->city           = $request->city;
                $ware_obj->zip_code       = $request->zip_code;
                $ware_obj->address        = $request->address;
                $ware_obj->phone          = $request->phone;
                $ware_obj->status         = '1';
                $ware_obj->assigned_country      = $assign;
                $ware_obj->user_id      = $request->client_id ?? Auth::id();
                $ware_obj->save();
                $id  = $ware_obj->id;
                $msg = 'Warehouse has been saved successfully';
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

    /**
     * Remove orders
     */
    public function removeOrderPackage($id){
        $dlt = EbayPackage::where('post_id', $id)->delete();
        $dlt_1 = PostExtra::where('post_id', $id)->delete();
        $dlt_2 = Post::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Action completed.');
    }


    /**
     * status update code
     */
    public function orderStatusHistory(Request $request){
        $history = StatusHistory::where('post_id', $request->post_id)->whereNotNull('addition_info')->where('type', $request->type)->orderBy('status_date', 'DESC')->get();
        $html = view('pages.admin.status-history', compact('history'))->render();

        return response()->json(['history' => $html]);
    }

    /**
    * check the ebay configration for the refresh token
    **/
    public function checkEbayConfigration(){
        $new     = true;
        $refresh = false;
        $data    = '';

        $mode = 'live'; // change here to live
        $api_url  = 'https://api.sandbox.ebay.com';
        $keys     = base64_encode('EcomGlob-ShipCycl-SBX-6fceab9cc-9f93134d:SBX-fceab9cce5ff-e0d1-488f-867f-8145');
        $redirect = 'Ecom_Global_Inc-EcomGlob-ShipCy-suldkrs';
        $code     = "v%5E1.1%23i%5E1%23f%5E0%23r%5E1%23I%5E3%23p%5E3%23t%5EUl41XzU6NDUxNDlFMzU3NzM0QzlEMDRGNzU1QkUzN0NBRENDNkJfMl8xI0VeMTI4NA%3D%3D";

        if ($mode == 'live') {
            $api_url  = 'https://api.ebay.com';
            $keys     = base64_encode('EcomGlob-ShipCycl-PRD-5fceab9cc-9d920685:PRD-fceab9cc3566-3510-4a39-a6f5-f1a0');
            $redirect = 'Ecom_Global_Inc-EcomGlob-ShipCy-zbhhz';
            $code     = "v%5E1.1%23i%5E1%23f%5E0%23p%5E3%23r%5E1%23I%5E3%23t%5EUl41XzEwOjFFMTlENUMwODc0Mjg5RUQyRDYyMzA0REQ0MkI4RTY1XzFfMSNFXjI2MA%3D%3D";
        }

        # check option data is non empty...
        $pro_option_data = Option::where('option_name', 'ebay_production_auth_data')->first();
        if ($pro_option_data) {
            $data    = json_decode($pro_option_data->option_value);
            $refresh = true; // change after here is true
            $new     = false; // uncomment
        }


        # check is sandbox enable or not...
        if ($mode == 'sandbox') {
            $test_option_data = Option::where('option_name', 'ebay_sandbox_auth_data')->first();
            if ($test_option_data) {
                $data    = json_decode($test_option_data->option_value);
                $refresh = true; // change after here is true
                $new     = false; // uncomment
            }
        }


        # check refresh is false and code is empty...
        if (!$refresh && empty($code)) {
            return ['type' => 'error', 'msg' => 'Check your Ebay configration and set the Authorization code.'];
        }


        // dd([$new, $refresh]);
        # check if auth code is first time...
        if ($new) {            
            $url        = $api_url . "/identity/v1/oauth2/token?grant_type=authorization_code&redirect_uri=" . $redirect . "&code=" . $code;
            $post_field = '';
        }

        # check if auth code is refresh...
        if ($refresh) {
            $url        = $api_url . '/identity/v1/oauth2/token';
            $post_field = "grant_type=refresh_token&refresh_token=" . $data->refresh_token . "&scope=https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.payment.dispute";
        }

        # setup curl to fetch access token...
        $oauth = 'Basic ' . $keys;
        $curl  = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $post_field,
            CURLOPT_HTTPHEADER     => array(
                "authorization: " . $oauth,
                "cache-control: no-cache",
                "charset: UTF_8",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        # check error...
        if ($err) {
            return ['type' => 'error', 'msg' => "cURL Error #:" . $err];
        }

        $result = json_decode($response);
        // dd($result);
        # check some error...
        if (isset($result->error)) {
            if ($mode == 'sandbox') {
                $key = 'ebay_sandbox_auth_data';
            } else {
                $key = 'ebay_production_auth_data';
            }
            // Option::where('option_name', $key)->delete();
            return ['type' => 'error', 'msg' => $result->error_description];
        }

        if (!isset($result->access_token) || empty($result->access_token)) {                
            return ['type' => 'error', 'msg' => 'Access Token not found'];
        }

        # check if is new or access token valid...
        if ($new) {
            if ($mode == 'sandbox') {
                $opt_data = Option::where('option_name', 'ebay_sandbox_auth_data')->first();
                $key = 'ebay_sandbox_auth_data';
            } else {
                $opt_data = Option::where('option_name', 'ebay_production_auth_data')->first();
                $key = 'ebay_production_auth_data';
            }

            if($opt_data){
                $opt_value = ['option_value' => $response];
                $opt_update = Option::where('option_name', $key)->update($opt_value);
            }else{
                Option::insert(array(
                    'option_name'  => $key,
                    'option_value' => $response,
                    'created_at'   => date("y-m-d H:i:s", strtotime('now')),
                    'updated_at'   => date("y-m-d H:i:s", strtotime('now')),
                ));
            }
        }

        return ['response' => $response, 'api_url' => $api_url];
    }

    /**
     * curl get function
    */
    public function getCurlResponse($api_url, $access_token){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$access_token,
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }

    /**
     * Post curl funciton
     **/
    public function postCurlResponse($api_url, $access_token, $json_data, $method = 'POST'){
        $in_curl = curl_init();
        curl_setopt_array($in_curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$access_token,
                "Cache-Control: no-cache",
                'Content-Language: en-US'
            ),
        ));

        $in_response = curl_exec($in_curl);
        curl_close($in_curl);
        $off_res = json_decode($in_response);
        return $off_res;
    }


    /**
     * Get the orders from the ebay based on configration
     */
    public function getOrdersFromEbay($offset = 0, $next_url = ''){
        try {
            # curl response...
            $response = $this->checkEbayConfigration();
            if(isset($response['type'])){
                return response()->json(['message' => $response['msg'], 'status' => 200], 200);
            }

            $result = json_decode($response['response']);
            # code...
            if (isset($result->access_token)) {
                $currentTimePST = Carbon::now()->setTimezone('America/Los_Angeles');
                $f_d = date("Y-m-d", strtotime("-15 day"));
                $t_d = $currentTimePST->format('Y-m-d');
                $ct_t = $currentTimePST->format('H:i:s');
                // $ct_t = date('H:i:s', time());
                // dd([$f_d, $t_d, $ct_t]);

                # check all the orders from ebay...
                if ($offset > 0) {
                    Log::channel('shopify_order')->info('Cron Tab Start count is:- '.$offset);
                } else {
                    Log::channel('shopify_order')->info('Cron Tab Start count is:- '.$offset);
                }

                if (!empty($next_url)) {
                    Log::channel('shopify_order')->info('Calling the next url getting form the ebay api response.');
                    $url = $next_url;
                } else {
                    $url = $response['api_url']."/sell/fulfillment/v1/order?filter=creationdate:%5B".$f_d."T07:00:00.000Z..".$t_d."T".$ct_t.".999Z%5D&limit=200&offset=".$offset;
                    // $url = $response['api_url']."/sell/fulfillment/v1/order?filter=creationdate:%5B".$f_d."T00:00:00.000Z..".$t_d."T23:59:59.999Z%5D&limit=200&offset=".$offset;
                    // $url = $response['api_url']."/sell/fulfillment/v1/order?filter=creationdate:%5B".$f_d."T00:00:00.000Z..".$t_d."T".$ct_t.".999Z%5D&limit=50&offset=".$offset;
                    // $url = $response['api_url']."/sell/fulfillment/v1/order?filter=creationdate:%5B".$f_d."T07:00:00.000Z..".$t_d."T23:59:59.999Z%5D";
                    // $url = $response['api_url']."/sell/fulfillment/v1/order?orderIds=11-12873-79509";
                }

                Log::channel('shopify_order')->info($url);
                $all_order = $this->getAllOrdersWithToken($result->access_token, $url, $offset);
                $status = 201;
                if ($all_order['status'] == 'error') {
                    $status = 200;
                }

                Log::channel('shopify_order')->info($all_order['msg']);
                return response()->json(['message' => $all_order['msg'], 'status' => $status], $status);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * get orders with user access token
     * @param string, string
     */
    public function getAllOrdersWithToken($token, $api_url, $offset){
        DB::beginTransaction();
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Authorization: Bearer " . $token,
                    "Cache-Control: no-cache",
                    "Content-Type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return ['status' => 'error', 'msg' => $err];
            }

            $response = json_decode($response);
            // echo '<pre>'; print_r($response); die;

            if(isset($response->errors) && is_array($response->errors)){
                $err = $response->errors;
                return ['status' => 'error', 'msg' => $err[0]->message];
            }

            $flag = false;
            $count = 0;
            if (isset($response->orders) && count($response->orders) > 0) {
                foreach ($response->orders as $key => $value) {
                    #check the ebay order id..
                    if (empty($value->orderId)) {
                        continue;
                    }

                    if (isset($value->orderPaymentStatus) && $value->orderPaymentStatus == 'FULLY_REFUNDED') {
                        continue;
                    }

                    # check the line item..
                    if (is_array($value->lineItems)) {
                        $status = 'IS-02';
                        $typ = 'single';
                        if (count($value->lineItems) > 1) {
                            $status = 'IS-07';
                            $typ = 'combined';
                        }

                        foreach ($value->lineItems as $key => $item) {
                            $sku = $item->sku ?? '';
                            $postExtra = [
                                'order_number' => $value->orderId,
                                'ebay_legacy_order_id' => $value->legacyOrderId ?? '',
                                'sale_date' => $value->creationDate ?? '',
                                'ebay_order_modify_date' => $value->lastModifiedDate ?? '',
                                'ebay_order_fulfillment_status' => $value->orderFulfillmentStatus ?? '',
                                'ebay_order_payment_status' => $value->orderPaymentStatus ?? '',
                                'ebay_order_seller_id' => $value->sellerId ?? '',
                                'buyer_username' => $value->buyer->username ?? '',
                                'ebay_order_currency' => $value->pricingSummary->total->currency ?? '',
                                'shipping_and_handling' => $value->pricingSummary->deliveryCost->value ?? 0,
                                'seller_collected_tax' => 0,
                                'ebay_collected_tax' => 0,
                                'total_price' => $value->pricingSummary->total->value ?? '',
                                'discount' => $value->pricingSummary->deliveryDiscount->value ?? 0,
                                'payment_method' => $value->paymentSummary->payments[0]->paymentMethod ?? 'None',
                                'shipping_service' => $value->orderPaymentStatus ?? '',
                                'ebay_order_content' => json_encode($value),
                                'sales_order_status' => null,
                                'ebay_return_order_status' => null,
                                'ebay_cancel_order_status' => null,
                                'ebay_exchange_order_status' => null,
                                'order_status' => $status,
                                'order_type' => $typ,
                                'item_number' => $item->lineItemId ?? '',
                                'legacyItemId'      => $item->legacyItemId,
                                'legacyVariationId' => $item->legacyVariationId ?? '',
                                'item_title'        => $item->title ?? '',
                                'item_quantity'     => $item->quantity ?? '',
                                'item_price'        => $item->lineItemCost->value ?? '',
                                'shippingCost'      => $item->deliveryCost->shippingCost->value ?? 0,
                                'handlingCost'      => $item->deliveryCost->handlingCost->value ?? 0,
                                'discountAmount'    => $item->deliveryCost->discountAmount->value ?? 0
                            ];

                            # shipping address....
                            if (is_array($value->fulfillmentStartInstructions)) {
                                foreach ($value->fulfillmentStartInstructions as $key => $address) {
                                    if ($address->fulfillmentInstructionsType == 'SHIP_TO' && isset($address->shippingStep)) {
                                        $postExtra['ship_to_name']        = $address->shippingStep->shipTo->fullName;
                                        $postExtra['ship_to_email']       = $address->shippingStep->shipTo->email ?? 'None';
                                        $postExtra['ship_to_phone']       = $address->shippingStep->shipTo->primaryPhone->phoneNumber ?? 'None';
                                        $postExtra['ship_to_address_1']   = $address->shippingStep->shipTo->contactAddress->addressLine1;
                                        $postExtra['ship_to_address_2']   = $address->shippingStep->shipTo->contactAddress->addressLine2 ?? 'None';
                                        $postExtra['ship_to_city']          = $address->shippingStep->shipTo->contactAddress->city;
                                        $postExtra['ship_to_state']         = $address->shippingStep->shipTo->contactAddress->stateOrProvince ?? '';
                                        $postExtra['ship_to_zip']           = $address->shippingStep->shipTo->contactAddress->postalCode;
                                        $postExtra['ship_to_country']       = $address->shippingStep->shipTo->contactAddress->countryCode;
                                        $postExtra['shipping_carrier_code'] = $address->shippingStep->shippingCarrierCode ?? '';
                                        $postExtra['shipping_service_code'] = $address->shippingStep->shippingServiceCode ?? '';
                                    }
                                }
                            }

                            # check the cancel status...
                            if (isset($value->cancelStatus->cancelState) && $value->cancelStatus->cancelState == 'CANCELED') {
                                $postExtra['order_status'] = 'IS-06';
                                $postExtra['ebay_cancel_order_status'] = $value->cancelStatus->cancelState;
                                $postExtra['cancle_date'] = $value->cancelStatus->cancelledDate ?? '';
                                $postExtra['cancelRequests'] = json_encode($value->cancelStatus->cancelRequests);
                            }

                            # chek alreday dispatch or not...
                            $chk = (new Post)->newQuery();
                            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $sku]]);
                            $chk->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $value->orderId]]);
                            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-04', 'IS-05', 'IS-03']);
                            $Check_Dis = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
                            if ($Check_Dis->isNotEmpty()) {
                                continue;
                            }

                            sleep(5); // Waits for 2 seconds

                            $order = (new Post)->newQuery();
                            $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $sku]]);
                            $order->select(
                                DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
                                DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date') as scan_dispatch_date"),
                                DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number') as tracking_number"),
                                DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
                                DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
                                'posts.id'
                            );
                            $Check = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();

                            // dd($Check);
                            Log::channel('shopify_order')->info('fetch data from db:- '. json_encode($Check->toArray()));
                            if ($Check->isNotEmpty()) {
                                Log::channel('shopify_order')->info('Already Inserted item ref:- '.$sku);
                                foreach ($Check as $ck => $cv) {
                                    if (empty($cv->scan_dispatch_date) && empty($cv->tracking_number) && !empty($cv->order_number) && $cv->order_number == $value->orderId && in_array($cv->order_status, ['IS-01', 'IS-02', 'IS-07'])) {
                                        foreach ($postExtra as $p_key => $p_value) {
                                            set_post_key_value($cv->id, $p_key, $p_value);
                                        }

                                        Post::where('id', $cv->id)->update([
                                            'ebay_id' => $value->orderId,
                                            'ebay_date' => date('Y-m-d H:i:s', strtotime($value->creationDate))
                                        ]);

                                        # store the user log...
                                        $his = new StatusHistory;
                                        $his->post_id = $cv->id;
                                        $his->addition_info = 'Cron Move into Scan out detail.';
                                        $his->type = 'cron-scan-out';
                                        $his->status_date = date('Y-m-d');
                                        $his->status_time = date('H:i:s');
                                        $his->user = 'Ecom-Cron';
                                        $his->save();
                                    } elseif (empty($cv->scan_dispatch_date) && empty($cv->tracking_number) && empty($cv->order_number) && in_array($cv->order_status, ['IS-01', 'IS-02', 'IS-07'])) {
                                        foreach ($postExtra as $p_key => $p_value) {
                                            set_post_key_value($cv->id, $p_key, $p_value);
                                        }

                                        Post::where('id', $cv->id)->update([
                                            'ebay_id' => $value->orderId,
                                            'ebay_date' => date('Y-m-d H:i:s', strtotime($value->creationDate))
                                        ]);

                                        # store the user log...
                                        $his = new StatusHistory;
                                        $his->post_id = $cv->id;
                                        $his->addition_info = 'Cron Move into Scan out detail.';
                                        $his->type = 'cron-scan-out';
                                        $his->status_date = date('Y-m-d');
                                        $his->status_time = date('H:i:s');
                                        $his->user = 'Ecom-Cron';
                                        $his->save();

                                        DB::commit();
                                    } elseif (!empty($cv->order_number) && $cv->order_number != $value->orderId) {
                                        $aa_order = (new Post)->newQuery();
                                        $aa_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','order_number'],['p2.key_value', '=' , $value->orderId]]);
                                        $aa_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $sku]]);
                                        $Check_aa = $aa_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();

                                        $ab_order = (new Post)->newQuery();
                                        $ab_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','order_number'],['p2.key_value', '=' , $cv->order_number]]);
                                        $ab_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $sku]]);
                                        $Check_ab = $ab_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
                                        // dd([$Check_aa, $Check_ab]);

                                        if ($Check_aa->isEmpty() && !$Check_ab->isEmpty()) {
                                            $post                   = new Post;
                                            $post->post_author_id   = 1;
                                            $post->post_content     = 'Scan order';
                                            $post->post_title       = 'Scan order';
                                            $post->post_slug        = Str::slug('Scan order', '-');
                                            $post->parent_id        = 0;
                                            $post->post_status      = 1;
                                            $post->post_type        = 'scan';
                                            $post->package_id       = $sku;
                                            $post->ebay_id          = $value->orderId;
                                            $post->location_id      = $cv->scan_i_location_id;
                                            $post->ebay_date        = date('Y-m-d H:i:s', strtotime($value->creationDate));
                                            
                                            $postExtra['scan_i_location_id'] = $cv->scan_i_location_id;
                                            $postExtra['authorized_by']     = 'Ecom';
                                            $postExtra['scan_out_user']     = 'Ecom';
                                            $postExtra['scan_out_date']     = date('Y-m-d');
                                            $postExtra['scan_out_time']     = date('H:i:s');
                                            $postExtra['scan_i_package_id'] = $sku;
                                            if ($post->save()) {
                                                Log::channel('shopify_order')->info('Insert item ref:- '.$sku);
                                                foreach ($postExtra as $p_key => $p_value) {
                                                    // updateOrCreatePostMeta($post->id, $p_key, $p_value);
                                                    set_post_key_value($post->id, $p_key, $p_value);
                                                }

                                                $count += 1;
                                                $flag = true;
                                            }
                                        }
                                    }

                                    DB::commit();
                                    sleep(1); // Waits for 2 seconds
                                }

                                continue;
                            }
                            
                            if ($Check->isEmpty()) {
                                # insert the data...
                                $post                   = new Post;
                                $post->post_author_id   = 1;
                                $post->post_content     = 'Scan order';
                                $post->post_title       = 'Scan order';
                                $post->post_slug        = Str::slug('Scan order', '-');
                                $post->parent_id        = 0;
                                $post->post_status      = 1;
                                $post->post_type        = 'scan';
                                $post->package_id       = $sku;
                                $post->ebay_id          = $value->orderId;
                                $post->ebay_date        = date('Y-m-d H:i:s', strtotime($value->creationDate));
                                
                                $postExtra['scan_i_location_id'] = '';
                                $postExtra['authorized_by']     = 'Ecom';
                                $postExtra['scan_out_user']     = 'Ecom';
                                $postExtra['scan_out_date']     = date('Y-m-d');
                                $postExtra['scan_out_time']     = date('H:i:s');
                                $postExtra['scan_i_package_id'] = $sku;
                                
                                if ($post->save()) {
                                    Log::channel('shopify_order')->info('Insert item ref:- '.$sku);
                                    foreach ($postExtra as $p_key => $p_value) {
                                        updateOrCreatePostMeta($post->id, $p_key, $p_value);
                                    }

                                    # store the user log...
                                    $his = new StatusHistory;
                                    $his->post_id = $post->id;
                                    $his->addition_info = 'Cron insert in to scan out.';
                                    $his->type = 'cron-ins-scan-out';
                                    $his->status_date = date('Y-m-d');
                                    $his->status_time = date('H:i:s');
                                    $his->user = 'Ecom-Cron';
                                    $his->save();

                                    DB::commit();
                                    $count += 1;
                                    $flag = true;
                                }
                            }
                        }
                    }
                }

                // Pause execution for 5 seconds
                sleep(2);
                if ($offset <= 10) {
                    $this->getOrdersFromEbay($offset + 1, $response->next ?? '');
                }
            }

            if ($flag) {
                return ['status' => 'success', 'msg' => $count.' Ebay orders saved successfully.'];                    
            }

            return ['status' => 'error', 'msg' => 'No order found from Ebay'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }

    /**
     * Get the orders from the ebay based on configration
     */
    public function getCancelOrdersFromEbay($offset = 0, $next_url = ''){
        try {
            $response = $this->checkEbayConfigration();
            if(isset($response['type'])){
                return response()->json(['message' => $response['msg'], 'status' => 200], 200);
            }

            $result = json_decode($response['response']);
            if (isset($result->access_token)) {
                $currentTimePST = Carbon::now()->subDay()->setTimezone('America/Los_Angeles');
                $f_d = date("Y-m-d", strtotime("-20 day"));
                $t_d = $currentTimePST->format('Y-m-d');
                $ct_t = $currentTimePST->format('H:i:s');
                
                # check all the orders from ebay...
                if ($offset > 0) {
                    Log::channel('shopify_order')->info('Cancel Cron Tab Start count is:- '.$offset);
                } else {
                    Log::channel('shopify_order')->info('Cancel Cron Tab Start count is:- '.$offset);
                }

                if (!empty($next_url)) {
                    $url = $next_url;
                } else {
                    $url = $response['api_url']."/sell/fulfillment/v1/order?filter=creationdate:%5B".$f_d."T00:00:00.000Z..".$t_d."T23:59:59.999Z%5D&limit=200&offset=".$offset;
                    // $url = $response['api_url']."/sell/fulfillment/v1/order?orderIds=01-12843-07150,25-12815-07281,14-12829-82522,03-12847-18639,18-12840-81882,09-12852-53000,14-12847-03600";
                }

                Log::channel('shopify_order')->info('Cancel Url:- '.$url);
                $all_order = $this->getCancellationOrdersFromEbay($result->access_token, $url, $offset);
                $status = 201;
                if ($all_order['status'] == 'error') {
                    $status = 200;
                }

                Log::channel('shopify_order')->info($all_order['msg']);
                return response()->json(['message' => $all_order['msg'], 'status' => $status], $status);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }


    /**
     * get cancellation orders with user access token
     *
     */
    public function getCancellationOrdersFromEbay($token, $api_url, $offset){
        DB::beginTransaction();
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => array(
                    "Authorization: Bearer " . $token,
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return ['status' => 'error', 'msg' => $err];
            }

            $orders = json_decode($response, true);
            // echo '<pre>'; print_r($orders); die;
            $flag = false;
            $count = 0;
            if (!empty($orders['orders'])) {
                // Filter orders where orderStatus = CANCELLED
                $canceledOrders = array_filter($orders['orders'], function ($order) {
                    return isset($order['cancelStatus']['cancelState']) && $order['cancelStatus']['cancelState'] === 'CANCELED';
                });

                if (count($canceledOrders) > 0) {
                    foreach ($canceledOrders as $key => $order) {
                        $postExtra['order_status'] = 'IS-06';
                        $postExtra['ebay_cancel_order_status'] = $order['cancelStatus']['cancelState'];
                        $postExtra['cancelRequests'] = json_encode($order['cancelStatus']['cancelRequests']);

                        $chk = (new Post)->newQuery();
                        $chk->leftJoin('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $order['orderId']]]);
                        $chk->leftJoin('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','order_status']])->whereIn('pe.key_value', ['IS-01','IS-07', 'IS-02', 'IS-03']);
                        $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
                        if (!$Check->isEmpty()) {
                            foreach ($Check as $ck => $cv) {
                                foreach ($postExtra as $p_key => $p_value) {
                                    updateOrCreatePostMeta($cv->id, $p_key, $p_value);
                                }

                                DB::commit();
                                $count += 1;
                                $flag = true;
                            }
                        }
                    }
                }

                // dd($canceledOrders);
                // Pause execution for 5 seconds
                sleep(2);
                if ($offset <= 20) {
                    $this->getCancelOrdersFromEbay($offset + 1, $orders['next'] ?? '');
                }
            } else {
                return ['status' => 'error', 'msg' => 'No cancel order found from Ebay'];
            }

            if ($flag) {
                return ['status' => 'success', 'msg' => $count.' Cancel Ebay orders saved successfully.'];                    
            }

            return ['status' => 'error', 'msg' => 'No cancel order found from Ebay'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }


    /**
     * fetch and cancel ebay order from the api
     */
    public function fetchOrCancelEbayOrder(Request $request){
        try {
            $response = $this->checkEbayConfigration();
            if(isset($response['type'])){
                return response()->json(['message' => $response['msg'], 'status' => 200], 200);
            }

            $result = json_decode($response['response']);
            if (isset($result->access_token)) {
                $offset = 25;
                $url = $response['api_url']."/sell/fulfillment/v1/order?orderIds=".$request->ebay_id;
                if ($request->order_type == 'fetch') {
                    $all_order = $this->getAllOrdersWithToken($result->access_token, $url, $offset);
                } else {
                    $all_order = $this->getCancellationOrdersFromEbay($result->access_token, $url, $offset);
                }

                $status = 201;
                if ($all_order['status'] == 'error') {
                    $status = 200;
                }

                Log::channel('shopify_order')->info('ebay order id:- '.$request->ebay_id);
                Log::channel('shopify_order')->info($all_order['msg']);
                return response()->json(['message' => $all_order['msg'], 'status' => $status], $status);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }
}
