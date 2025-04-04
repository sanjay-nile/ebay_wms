<?php

use App\Library\GetFunction;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Models\ReverseLogisticWaybill;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Warehouse;
use App\Models\OrderData;
use App\Models\OrderItem;
use App\Models\PackageDetail;
use App\Models\PalletDeatil;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Models\Meta as MetaData;
use DB as DBS;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\Category;


/**
* Auth Guard
*
*/
function get_guard()
{
    if (Auth::guard('admin')->check()) {return "admin";} else {return "web";}
}

/**
* Auth Login DashBoard
*/
function getDashboardUrl()
{
    if (Auth::user()->user_type_id == 1) {
        return ['dashboard' => route('admin.dashboard'), 'sidebar' => 'includes/admin/sidebar'];
    } else if (Auth::user()->user_type_id == 2) {
        return ['dashboard' => route('admin.sub-admin.dashboard'), 'sidebar' => 'includes/admin/sidebar'];
    } else if (Auth::user()->user_type_id == 3) {
        return ['dashboard' => route('front.client.dashboard')];
    } else if (Auth::user()->user_type_id == 4) {
        return ['dashboard' => route('front.client-user.dashboard')];
    } else if (Auth::user()->user_type_id == 5) {
        return ['dashboard' => route('customer.dashboard'), 'sidebar' => 'includes/admin/client-user-customer-sidebar'];
    } else {
        return ['dashboard' => route('admin.login'), 'sidebar' => 'includes/admin/sidebar'];
    }
}

/**
* Client Type
*/
function getClientType(){
    return \Config::get('constants.client_user');
}

function displayClientType($key = null){
    $tp = getClientType();

    if (!empty($key)) {
        # code...
        return $tp[$key] ?? '';
    }

    return '';
}


function available_currency(){
    return array(
        'AED' => 'United Arab Emirates Dirham (د.إ)',
        'ARS' => 'Argentine Peso ($)',
        'AUD' => 'Australian Dollars ($)',
        'BDT' => 'Bangladeshi Taka (৳)',
        'BRL' => 'Brazilian Real (R$)',
        'BGN' => 'Bulgarian Lev (лв.)',
        'CAD' => 'Canadian Dollars ($)',
        'CLP' => 'Chilean Peso ($)',
        'CNY' => 'Chinese Yuan (¥)',
        'COP' => 'Colombian Peso ($)',
        'DKK' => 'Danish Krone (DKK)',
        'DOP' => 'Dominican Peso (RD$)',
        'EUR' => 'Euros (€)',
        'HKD' => 'Hong Kong Dollar ($)',
        'HRK' => 'Croatia kuna (Kn)',
        'HUF' => 'Hungarian Forint (Ft)',
        'ISK' => 'Icelandic krona (Kr.)',
        'IDR' => 'Indonesia Rupiah (Rp)',
        'INR' => 'Indian Rupee (Rs.)',
        'NPR' => 'Nepali Rupee (Rs.)',
        'JPY' => 'Japanese Yen (¥)',
        'KRW' => 'South Korean Won (₩)',
        'MYR' => 'Malaysian Ringgits (RM)',
        'MXN' => 'Mexican Peso ($)',
        'NGN' => 'Nigerian Naira (₦)',
        'NZD' => 'New Zealand Dollar ($)',
        'PHP' => 'Philippine Pesos (₱)',
        'GBP' => 'Pounds Sterling (£)',
        'RON' => 'Romanian Leu (lei)',
        'RUB' => 'Russian Ruble (руб.)',
        'SGD' => 'Singapore Dollar ($)',
        'ZAR' => 'South African rand (R)',
        'SEK' => 'Swedish Krona (kr)',
        'CHF' => 'Swiss Franc (CHF)',
        'TWD' => 'Taiwan New Dollars (NT$)',
        'THB' => 'Thai Baht (฿)',
        'UAH' => 'Ukrainian Hryvnia (₴)',
        'USD' => 'US Dollars ($)',
        'VND' => 'Vietnamese Dong (₫)',
        'EGP' => 'Egyptian Pound (EGP)',
    );
}

function get_currency_symbol($code = ''){
    switch ($code) {
        case 'AED':
            $currency_symbol = 'د.إ';
            break;
        case 'AUD':
        case 'ARS':
        case 'CAD':
        case 'CLP':
        case 'COP':
        case 'HKD':
        case 'MXN':
        case 'NZD':
        case 'SGD':
        case 'USD':
            $currency_symbol = '&#36;';
            break;
        case 'BDT':
            $currency_symbol = '&#2547;&nbsp;';
            break;
        case 'BGN':
            $currency_symbol = '&#1083;&#1074;.';
            break;
        case 'BRL':
            $currency_symbol = '&#82;&#36;';
            break;
        case 'CHF':
            $currency_symbol = '&#67;&#72;&#70;';
            break;
        case 'CNY':
        case 'JPY':
        case 'RMB':
            $currency_symbol = '&yen;';
            break;
        case 'CZK':
            $currency_symbol = '&#75;&#269;';
            break;
        case 'DKK':
            $currency_symbol = 'DKK';
            break;
        case 'DOP':
            $currency_symbol = 'RD&#36;';
            break;
        case 'EGP':
            $currency_symbol = 'EGP';
            break;
        case 'EUR':
            $currency_symbol = '&euro;';
            break;
        case 'GBP':
            $currency_symbol = '&pound;';
            break;
        case 'HRK':
            $currency_symbol = 'Kn';
            break;
        case 'HUF':
            $currency_symbol = '&#70;&#116;';
            break;
        case 'IDR':
            $currency_symbol = 'Rp';
            break;
        case 'ILS':
            $currency_symbol = '&#8362;';
            break;
        case 'INR':
            $currency_symbol = 'Rs.';
            break;
        case 'ISK':
            $currency_symbol = 'Kr.';
            break;
        case 'KIP':
            $currency_symbol = '&#8365;';
            break;
        case 'KRW':
            $currency_symbol = '&#8361;';
            break;
        case 'MYR':
            $currency_symbol = '&#82;&#77;';
            break;
        case 'NGN':
            $currency_symbol = '&#8358;';
            break;
        case 'NOK':
            $currency_symbol = '&#107;&#114;';
            break;
        case 'NPR':
            $currency_symbol = 'Rs.';
            break;
        case 'PHP':
            $currency_symbol = '&#8369;';
            break;
        case 'PLN':
            $currency_symbol = '&#122;&#322;';
            break;
        case 'PYG':
            $currency_symbol = '&#8370;';
            break;
        case 'RON':
            $currency_symbol = 'lei';
            break;
        case 'RUB':
            $currency_symbol = '&#1088;&#1091;&#1073;.';
            break;
        case 'SEK':
            $currency_symbol = '&#107;&#114;';
            break;
        case 'THB':
            $currency_symbol = '&#3647;';
            break;
        case 'TRY':
            $currency_symbol = '&#8378;';
            break;
        case 'TWD':
            $currency_symbol = '&#78;&#84;&#36;';
            break;
        case 'UAH':
            $currency_symbol = '&#8372;';
            break;
        case 'VND':
            $currency_symbol = '&#8363;';
            break;
        case 'ZAR':
            $currency_symbol = '&#82;';
            break;
        default:
            $currency_symbol = '';
            break;
    }

    return $currency_symbol;
}

function setCustomMeta($obj,$array){
    // DB::enableQueryLog();
    if(is_array($array) && !empty($array) && count($array)>0){
        foreach($array as $key => $value){
            if($key == 'customer_pincode'){
                $val = strval($value);
                $v = (string) $val;
                $ke = "_".$key;
                $obj->setMeta('_'.$key, $v, 'string');
                // dd(DB::getQueryLog());
            } else{
                if (is_float($value)) {
                    $obj->setMeta('_'.$key, $value, 'float');
                } else {
                    $obj->setMeta('_'.$key, $value);
                }
            }
        }
    }
}

/**
 * Get function for country name
 *
 * @param country id
 * @return string
 */
function get_country_name_by_id($id = ''){
    return GetFunction::getCountryNameById($id);
}

/**
 * Get function for phone code
 *
 * @param country id
 * @return string
 */
function get_phone_code_by_cid($id = ''){
    return GetFunction::getPhoneCodeByCid($id);
}

/**
 * Get function for state code
 *
 * @param state name
 * @return string
 */
function get_state_code_by_name($name = ''){
    return GetFunction::getStateCodeByName($name);
}

/**
* Get reason list
*
*/
function get_order_refund_list($key = ''){
    return GetFunction::getOrderRefendList($key);
}

/**
* Country list
**/
function get_country_list(){
    return GetFunction::get_all_countries();
    // return GetFunction::getCountryList();
}

/**
 * Get function for country 
 *
 * @param Country code
 * @return string
 */
function get_country_by_code($code = '') {
  return GetFunction::get_country_name_by_code( $code );
}

/**
* carrier list
**/
function get_carrier_list(){
    return GetFunction::getCarrierList();
}

/**
* shipment list
**/
function get_shipment_list(){
    return GetFunction::getShipmentList();
}

/**
* charges list
**/
function get_charges_list(){
    return GetFunction::getChargesList();
}

function sendCreatedWaybillMail($array){
    $msg = "<p></p>";
    $msg .= "<p>Dear ".$array['name'].",</p>";
    $msg .= "<p>Your return request has been submitted successfully. Your details are as follows: </p>";
    $msg .= "<p></p>";
    $msg .= "<p><b>Return Instructions</b></p>";
    $msg .= "<p>1) Place the products in the original packaging: the products must be returned in the same conditions as they were received, that is, in a perfect state of conservation, complete with all their unused components, with the original packaging and any included manuals, labels and tags still attached to the product.</p>";

    $msg .= "<p>2) The returned items will be checked by our warehouse to make sure they were returned intact; we will charge you any depreciation, reimbursing you only the residual value of the Product.</p>";
    $msg .= "<p>3) Please download the package label by clicking on the button given below, that contains the return number and address of our warehouse, paste it outside the box containing the product(s) that you wish to return.</p>";
    $msg .= "<p><a href=".$array['label']." style='color: #fff; background: #b51f38; border-radius: 40px; padding: 13px 52px; border:none; margin-bottom: 25px; display: inline-block;'>Download Package Label</a></b></p>";
    $msg .= "<p></p>";
    $msg .= "<p>Best Regards</p>";
    $msg .= "<p></p>";
    $msg .= "<p>The ReverseGear Team</p>";

    $data['from_email'] = 'info@reversegear.net';
    $data['site_title'] = 'Reverse Gear';
    $data['subject']    = 'Return Request';
    $data['view']       = 'email-templates.defaultEmail';
    $data['mail_to']    = $array['email'];
    $data['content']    = $msg;
    Mail::to($data['mail_to'])->send(new MainTemplate( $data ));
}


function get_client_list(){
    return GetFunction::getEtailerList();
}

function check_validation($request){       
    if(isset($request) && count(array_filter($request)) == count($request)){
        return false;
    }else{
       return true;            
    }
}

if(! function_exists('randomPassword')){
    function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}

if(!function_exists('generateUniqueWaybillNumber')){
    function generateUniqueWaybillNumber(){
        $way_bill_number = '';

        do {
           $way_bill_number = 'SC-Chic'.mt_rand( 10000000, 99999999 );
        } while ( DBS::table( 'reverse_logistic_waybills' )->where( 'way_bill_number', $way_bill_number )->exists() );
        
        return $way_bill_number;
    }
}

if(!function_exists('generateUniquePalletName')){
    function generateUniquePalletName(){
        $pallet_id = '';

        do {
           $pallet_id = 'SC-Chic'.'_'.date('mdY').'_'.mt_rand( 10000000, 99999999 );
        } while ( DBS::table( 'pallet_deatils' )->where( 'pallet_id', $pallet_id )->exists() );
        
        return $pallet_id;
    }
}

if(!function_exists('generateSlug')){
    function generateSlug($title){
        // Normalize the title
        $slug = Str::slug($title);

        $allSlugs = User::select('slug')->where('slug', 'like', $slug.'%')->get();

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }
    }   
}

/** 
* Return Reason Value
* For missguided
*/
function reason_of_return($key = null){
    $arr =  [
        '1' => 'Changed my mind',
        '2' => 'Doesn’t suit me',
        '3' => 'Incorrect item received',
        '4' => 'Not like picture',
        '5' => 'Fit – Too big/ Too long',
        '6' => 'Fit - Too small/ Too short',
        '7' => 'Faulty',
        '8' => 'Poor value/ Poor quality',
        '50' => 'Bought more than one for style/colour/ size',
        '51' => 'Fabric – I don’t like it'
    ];

    if (!empty($key)) {
        # code...
        return $arr[$key] ?? '';
    }

    return $arr;
}

/** 
* Return Reason Value
* For Olive
*/
function olive_reason_of_return($key = null){
    $arr =  [
        'Does Not Suit' => 'Does Not Suit',
        'Too Small' => 'Too Small',
        'Too Large' => 'Too Large',
        'Dissatisfied With Quality' => 'Dissatisfied With Quality',
        'Faulty' => 'Faulty',
        'Received Incorrect Item' => 'Received Incorrect Item',
        // 'Looks Different Than Expected' => 'Looks Different Than Expected'
    ];

    if (!empty($key)) {
        # code...
        return $arr[$key] ?? '';
    }

    return $arr;
}

function displayOliveReason($key = null){
    $wv = olive_reason_of_return();

    if (!empty($key)) {
        # code...
        return $wv[$key] ?? '';
    }

    return 'N/A';
}

function displayMissguidedReason($key = null){
    $wv = reason_of_return();

    if (!empty($key)) {
        # code...
        if (strpos($key, ',') !== false) {
            # code...
            $rtn = explode(',', $key);
            $rtn_str = [];
            foreach ($rtn as $k => $v) {
                # code...
                $str = $wv[$v] ?? '';
                array_push($rtn_str, $str);
            }

            return implode(',', $rtn_str);
        }

        return $wv[$key] ?? '';
    }

    return 'N/A';
}

function getSource(){
    return [
        ReverseLogisticWaybill::CLIENT_ADMIN => 'CSR',
        ReverseLogisticWaybill::CUSTOMER => 'Customer',
    ];
}

function getSourceName($client_id = null){
    $client_user = User::select('name')->where(['user_type_id' => 4, 'created_by' => $client_id])->get()->toArray();
    // $user = [
    //     ReverseLogisticWaybill::SOURCE_NAME => 'Client Admin'
    // ];

    $user = [];

    if (!empty($client_user) && is_array($client_user)) {
        # code...
        foreach ($client_user as $key => $value) {
            $user[$value['name']]= $value['name'];            
        }
    }

    return $user;
}

function getWaiver(){
    return [
        'Return_Policy_Timeline' => 'Return Policy Timeline',
        'Waiver_of_Shipping_Cost' => 'Waiver of shipping cost',
        'Both' => 'Waiver of both Timeline and shipping cost',
    ];
}

function displayWaiver($key = null){
    $wv = getWaiver();

    if (!empty($key)) {
        # code...
        return $wv[$key] ?? 'N/A';
    }

    return 'N/A';
}

function getWareHouse($client_id = null){
    $data = [];
    if (!empty($client_id)) {
    	$wh = Warehouse::where(['user_id' => $client_id])->get()->toArray();
    } else{
    	$wh = Warehouse::get()->toArray();
    }
    
    if (!empty($wh) && is_array($wh)) {
        # code...
        foreach ($wh as $key => $value) {
            $data[$value['name']]= $value['name'];            
        }
    }

    return $data;
}

function getTrackingDetail($track_id){    
    try {
        $html = '';
        $client    = new Client();
        $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.secureKey').'&carrierWaybill='.$track_id;
        // $url = \Config::get('constants.trackingUrl'). '?secureKey='.\Config::get('constants.testSecureKey').'&carrierWaybill='.$id;

        $response = $client->get($url);
        $results = json_decode($response->getBody()->getContents());
        
        if ($results->messageType != 'Success') {
            # code...
            return 'No tracking details available';
        }

        $new_array = $deliverd = $transit = [];
        $docket = (isset($results->docketJson)) ? json_decode($results->docketJson) : '';
        //dd($docket);
        
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

                    if($value->waybillStatus == 'Delivered'){
                        array_push($deliverd, $value);
                    }

                    if ($value->waybillStatus == 'In Transit') {
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
                    $mark = 'Received Package at our Hub';
                    $label = ' | '.$value->carrierLabel;
                } elseif ($value->waybillStatus == 'Delivered') {
                    # code...
                    $mark = 'Received Package at our Hub';
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
        } else {
            $html = 'No tracking details available';
        }

        return $html;   
    } catch (\Exception $e) {
        // return $e->getMessage();
        return 'No tracking details available';
    }
}

function displayUnitType($key = null){
    $val = 'N/A';
    if(!empty($key)){
        if ($key == 'POUND') {
            $val = 'LBS';
        } elseif ($key == 'KILOGRAM') {
            $val = 'KGS';
        } else {
            $val = $key;
        }
    }

    return $val;
}


function getMetaValue($id, $key){
    $meta = DBS::table( 'meta' )->where(['key' => $key, 'owner_id' => $id])->first();
    if ($meta) {
        # code...
        return $meta->value;
    }

    return null;
}

function getAllMetaValue($order){
    $meta = $order->getMetas();
    return $meta;
}

function getCountryOfOrigin($order_id, $sku){
    $orderItem = OrderItem::select('country_of_origin')->where(['order_id' => $order_id, 'sku' => $sku])->first();
    if($orderItem){
        return $orderItem->country_of_origin;
    }

    return null;
}


/**
*
* display pdf in the iframe
**/
function showPdf($waybill){
    $order = ReverseLogisticWaybill::where(['id' => $waybill])->first();
    $final_url = '';
    if($order){
        if($order->hasMeta('_attachment_pdf')){
            $url = $order->getMeta('_attachment_pdf');
            $final_url = 'public/'.$url;
        } else{
            $gn_status = $order->getMeta('_generate_waywill_status');
            if($gn_status){
                $json = json_decode($gn_status);
                $label = reset($json->labelDetailList);

                # save pdf and send to mail...
                $upload_path = \Config::get('constants.upload_path');                
                $imagePath = public_path($upload_path);
                if(!File::exists($imagePath)) {
                    File::makeDirectory($imagePath, 0777, true, true );
                }

                $pdf_url  = $label->artifactUrl;
                $filename  = basename($pdf_url);
                $fileName  = $filename;
                $path_upload = $upload_path.$fileName;
                $ch = curl_init($pdf_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $pdf_data = curl_exec($ch);
                curl_close($ch);
                $result = file_put_contents($path_upload, $pdf_data);
                # if success upload..
                if ($result) {
                    $pdf_arr['attachment_pdf'] = $path_upload;
                    setCustomMeta($order, $pdf_arr);
                    $final_url = 'public/'.$path_upload;
                }
            }
        }
    }

    return $final_url;
}


function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {
    // Build URL
    $url = "https://" . $shop . "" . $api_endpoint;
    if (!is_null($query) && in_array($method, array('GET',  'DELETE'))) $url = $url . "?" . http_build_query($query);

    // Configure cURL
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
    // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
    curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    // Setup headers
    $request_headers[] = "";
    if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

    if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
        if (is_array($query)) $query = http_build_query($query);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
    }
    
    // Send request to Shopify and capture any errors
    $response = curl_exec($curl);
    $error_number = curl_errno($curl);
    $error_message = curl_error($curl);

    // Close cURL to be nice
    curl_close($curl);

    // Return an error is cURL has a problem
    if ($error_number) {
        return $error_message;
    } else {
        // No error, return Shopify's response by parsing out the body and the headers
        $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

        // Convert headers into an array
        $headers = array();
        $header_data = explode("\n",$response[0]);
        $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
        array_shift($header_data); // Remove status, we've already set it above
        foreach($header_data as $part) {
            $h = explode(":", $part);
            $headers[trim($h[0])] = trim($h[1]);
        }

        // Return headers and Shopify's response
        return array('headers' => $headers, 'response' => $response[1]);
    }
}


/** 
* Reason of Return
* For The Curated
*/
function curated_reason_of_return(){
    $arr =  [
        '1' => 'Too small',
        '2' => 'Too big',
        '3' => 'Doesn’t suit me',
        '4' => 'Received wrong item',
        '5' => 'Faulty/Damaged ',
        '6' => 'Other '
    ];

    return $arr;
}

function autoUpdateCuratedWaywill($request){
    return GetFunction::autoUpdateCuratedWaywill($request);   
}

function getClientName($client_id = null){
    $client_user = User::select('name')->where(['id' => $client_id])->first();
    if (!empty($client_user)) {
        return $client_user->name;
    }

    return null;
}


function getOrderType($order){
    $meta = getMetaKeyValye($order);
    $type = '';
    if($order->status == 'Pending'){
        $type = 'Label Failed';
    } else{
        if(isset($meta['_order_waywill_status']) && $order->status == 'Success' && $order->process_status == 'unprocessed'){
            $type = 'InScan';
        } else {
            if($order->cancel_return_status != null){
                $type = 'Cancelled';
            } else{
                if($order->status == 'Success' && $order->process_status == 'unprocessed'){
                    $type = 'Processed';
                }

                if($order->status == 'Success' && $order->process_status == 'processed'){
                    $type = 'Received at Hub';
                }
            }
        }
    }

    return $type;
}


function getMetaKeyValye($order){
    $mt = getAllMetaValue($order);
    $ar = [];
    foreach($mt as $k => $v){
        $ar[$k] = $v;
    }

    return $ar;
}

function getTriggerValue($id, $key){
    $meta = DBS::table( 'meta' )->where(['key' => $key, 'owner_id' => $id])->first();
    if ($meta) {
        # code...
        return $meta->created_at;
    }

    return null;
}

function getWareHouseName($id = null){
    if (empty($id)) {
        return 'N/A';
    } else{
        $wh = Warehouse::select('name')->where(['id' => $id])->first();
    }
    
    if (!empty($wh)) {
        return $wh->name;
    }

    return 'N/A';
}


function getOrderItems($bar_code , $waybill){
    return OrderItem::where('sku', $bar_code)->where('order_id', $waybill)->first();
}

function dt($time){
    return Carbon::parse($time)->timezone('America/Los_Angeles')->format('d/m/Y h:i:s a');
    // return Carbon::parse($time)->timezone('Asia/Kolkata')->format('d/m/Y h:i:s a');
}

// all meta key and value
function getMetaKeyValue($id, $key = null){
    $meta = DBS::table('meta')->whereIn('key', ['_customer_country', '_actual_weight', '_currency', '_customer_name', '_customer_state', '_customer_city', '_customer_pincode', '_customer_country', '_consignee_name', '_consignee_address', '_consignee_city', '_consignee_pincode', '_consignee_country', '_customer_address'])->where(['owner_id' => $id])->get()->toArray();

    $meta_data = array();
    array_map(function($item) use (&$meta_data) {
        $meta_data[$item->key] = $item->value;
    }, $meta);

    return $meta_data;
}

# 03-mar-2022
function updatePackageData(){
    $query = (new PackageDetail)->newQuery();
    $query->select("package_details.*", 'reverse_logistic_waybills.way_bill_number');
    $query->join('reverse_logistic_waybills', 'reverse_logistic_waybills.id', '=', 'package_details.reverse_logistic_waybill_id');
    $query->where('reverse_logistic_waybills.client_id','446');
    $pakage = $query->orderBy('package_details.id','desc')->get();

    // dd($pakage);
    foreach($pakage as $dt){
        $oi = getOrderItems($dt->bar_code, $dt->way_bill_number);
        $cn = $oi->country_of_origin ?? 'N/A';
        $hs = $oi->hs_code ?? 'N/A';
        $pkg = PackageDetail::where('id', $dt->id)->update(['country_of_origin' => $cn, 'hs_code' => $hs]);
    }
}

/**
* status list here
* 19-oct-22
* code by sanjay
*/
function getStatusList(){
    return [
        'ARR' => 'Arrived at',
        'BFF' => 'Booked for flight',
        'FAB' => 'Flown as booked',
        'DEL' => 'Delivered',
        'SIT' => 'Shipment in transit',
        'DLD' => 'Shipment delayed',
        'CCL' => 'Customs Clearance',
        'PAH' => 'Processed at hub',
        'ARO' => 'Arrived in origin warehouse',
        'RAH' => 'Received at hub',
        'BFE' => 'Booked for export',
        'COL' => 'Collection arranged',
        'CON' => 'Arranged delivery with consignee',
        'DEF' => 'Delivery failed',
        'ACC' => 'Awaiting customs clearance',
        'LIT' => 'Lost in transit',
        'DIT' => 'Damaged in transit',
        'DES' => 'Destroyed'
    ];
}


function getStatusValue($k = null){
    $list = getStatusList();
    if (!empty($k)) {
        return $list[$k];
    }

    return null;
}

function get_default_languages_data()
{

    return GetFunction::default_languages_data();

}


function get_recaptcha_data()
{

    return GetFunction::recaptcha_data();

}


function get_user_details($user_id)
{
    return GetFunction::get_user_all_details($user_id);
}

function get_budge_value($key){
    $arr = [
        'P_Returned' => 'dark',
        'Returned' => 'info',
        'Completed' => 'success',
        'Approved' => 'success',
        'Dispatched' => 'success',
        'Pending' => 'warning',
        'Pending Pick' => 'warning',
        'Single Scan Out' => 'warning',
        'Pending Dispatch' => 'warning',
        'Checked In' => 'pink',
        'Combined' => 'purple',
        'Combined Dispatched' => 'purple',
        'Combined Scan Out' => 'purple',
        'To wipe out' => 'info',
        'Disposal' => 'purple',
        'Cancelled' => 'danger',
        'Rejected' => 'danger',
        'Discrepancy' => 'warning',
        'Grade 1 N' => 'success',
        'Grade 2 U' => 'yellow',
        'Grade 3 D' => 'danger',
        'Not received' => 'danger',
        'Grade Discrepancies' => 'warning',
        'Grade Charity' => 'dark',
        'Disposal' => 'purple',
    ];

    return (isset($arr[$key]) && !empty($key)) ? $arr[$key] : 'default';
}

function set_post_key_value($post_id, $key, $value){
    $pextra = PostExtra::where(['post_id' => $post_id, 'key_name' => $key])->first();
    if($pextra){
      $old = array(
        'key_value' => $value
      );
      PostExtra::where(['post_id' => $post_id, 'key_name' => $key])->update($old);
    }else{
      PostExtra::insert(array(
        'post_id'    => $post_id,
        'key_name'   => $key,
        'key_value'  => $value,
        'created_at' => date("y-m-d H:i:s", strtotime('now')),
        'updated_at' => date("y-m-d H:i:s", strtotime('now')),
      ));
    }
  }


/**
 * Get post extra
 *
 * @param post_id, key_name
 * @return string
 */
function get_post_extra($post_id, $key_name)
{
    return GetFunction::post_extra($post_id, $key_name);
}


if(!function_exists('generateUniquePalletName')){
    function generateUniquePalletName(){
        $pallet_id = '';

        do {
           $pallet_id = 'SC-Chic'.'_'.date('mdY').'_'.mt_rand( 10000000, 99999999 );
        } while ( DBS::table( 'pallet_deatils' )->where( 'pallet_id', $pallet_id )->exists() );
        
        return $pallet_id;
    }
}


if(!function_exists('generateUniquePalletNames')){
    function generateUniquePalletNames(){
        $pallet_id = '';

        do {
           $pallet_id = 'SC-ORD-P-'.date('mdY').'-'.mt_rand( 10000000, 99999999 );
           // $pallet_id = 'SC-ORD-P-'.date('mdY').'-'.randomPassword();
        } while ( DBS::table( 'posts' )->where( 'pallet_id', $pallet_id )->exists() );
        
        return $pallet_id;
    }
}


function getwharehouse($code){
    // $countryEU = array("DE","FR","IT","ES","NL","DK","AT","BE","LU","BG","HU","CY","AU");
    // $countryUK = array("GB");
    // $countryUS = array("US");
    // $countryCA = array("CA");
    // $wh = '' ;
    // if(in_array($code , $countryEU)){
    //     $country= Country::where('sortname', 'NL')->first();
    //     $wh = Warehouse::where('country_id',$country->id)->first();
    // }

    // if(in_array($code , $countryUK)){
    //     $country= Country::where('sortname', 'GB')->first();
    //     $wh = Warehouse::where('country_id',$country->id)->first();
    // }

    // if(in_array($code , $countryUS)){
    //     $country= Country::where('sortname', 'US')->first();
    //     $wh = Warehouse::where('country_id',$country->id)->first();
    // }

    // if(in_array($code , $countryCA)){
    //     $country= Country::where('sortname', 'CA')->first();
    //     $wh = Warehouse::where('country_id',$country->id)->first();
    // }
    // return $wh;
    $wh = '' ;
    $warehouse = Warehouse::where('assigned_country', "Like", "%" . $code . "%")->first();
    return $warehouse ;
}


/**
 * Client user order type
 **/
function getClientOrderType($key = null){
    $tp = \Config::get('constants.order_type');

    if (!empty($key)) {
        # code...
        return $tp[$key] ?? '';
    }

    return '';
}

/**
 * get user client type based on user type
 **/
function getUserType($key = null){
    $tp = \Config::get('constants.client_id');

    if (!empty($key)) {
        # code...
        return $tp[$key] ?? '';
    }

    return '';
}

/**
 * Create a slug from title
 * @param  string $title
 * @return string $slug
 */
function createSlug(string $title): string{
    $slugsFound = getSlugs($title);
    $counter = 0;
    $counter += $slugsFound;

    $slug = Str::slug($title, $separator = "-", app()->getLocale());

    if ($counter) {
        $slug = $slug . '-' . $counter;
    }

    return $slug;
}

/**
 * Find same listing with same title
 * @param  string $title
 * @return int $total
 */
function getSlugs($title): int
{
    // return Category::select()->where('name', 'like', $title)->count();
    return 0;
}

function firstLevel(){
    return [
        'defects_product' => 'Are there any defects in the Product?',
        'rs_quantity' => 'Return Quantity and Shipping Quantity is same?',
        'match_product' => 'Does the product match the ordered Product?',
        'product_dirty' => 'Is the Product is dirty?',
        'price_tag' => 'Is a price tag available?',
    ];
}

function newFirstLevel(){
    return [
        'match_quantity' => 'Does the quantity of items returned match the expected quantity?',
        'ebay_listing_item' => 'Are all the items returned as described in the eBay listing?',
        'visible_damage' => 'Are there any items with visible damage?',
        'return_shipment' => 'Are there any empty boxes in the returned shipment?',
        'inspection' => 'Are there any discrepancies noted during the inspection that do not fit the other categories?',
        'cable_access' => 'Does item includes the cable & accessory?',
    ];
}

function firstLevelExtra($key){
    $arr = [
        'ebay_listing_item' => [
            'INCORRECT_SKU' => 'Incorrect SKU',
            'INCORRECT_COLOR' => 'Incorrect Color',
            'INCORRECT_SIZE' => 'Incorrect Size',
            'INCORRECT_SERIAL_NUMBER' => 'Incorrect Serial Number',
            'DIFFERENT_ITEM' => 'Different Item',
        ],
        'visible_damage' => [
            'SURFACE_DAMAGE' => 'Surface Damage',
            'COMPONENT_DESTROYE' => 'Component Destroye',
            'DESTROYED' => 'Destroyed'
        ],
        'match_quantity' => [
            'expected_quantity' => 'Expected Quantity',
            'actual_quantity' => 'Actual Quantity',
            'pin_number' => 'Pin Number',
        ],
        'inspection' => [
            'inspection_comment' => 'Comment'
        ]
    ];

    return $arr[$key] ?? '';
}

function firstLevelFieldType($key){
    $arr = [
        'ebay_listing_item' => 'select',
        'visible_damage' => 'select',
        'match_quantity' => 'text',
        'inspection' => 'text'
    ];

    return $arr[$key] ?? '';
}

function secondLevel(){
    return [
        'defects_item' => 'Are there any defects in the items?',
        'or_quantity' => 'Ordered Quantity and received Quantity are the same?',
        'image_product' => 'Does the product match the ordered Product image?',
        'item_dirty' => 'Is the item dirty?',
        'price_item' => 'Is a price tag available on the item?',
    ];
}

function inception_status($key = null){
    $arr =  [
        'IS-07' => 'Not received',
        'IS-01' => 'Checked In',
        'IS-02' => 'Approved',
        'IS-03' => 'Rejected',
        'IS-04' => 'Discrepancy',
        'IS-05' => 'Disposal',
        'IS-06' => 'To wipe out',
    ];

    if (!empty($key)) {
        return $arr[$key] ?? $key;
    }

    return $arr;
}

function inception_status_value($key = null){
    $arr =  [
        'IS-07' => 'Not received',
        'IS-01' => 'Checked In',
        'IS-02' => 'Approved',
        'IS-03' => 'Rejected',
        'IS-04' => 'Discrepancy',
        'IS-05' => 'Disposal',
        'IS-06' => 'To wipe out',
    ];

    return $arr[$key] ?? $key;
}

function order_status($key = null){
    $arr =  [
        'IS-01' => 'In Stock',
        'IS-02' => 'Pending Pick',
        'IS-03' => 'Pending Dispatch',
        'IS-04' => 'Dispatched',
        'IS-05' => 'Combined Dispatched',
        'IS-06' => 'Cancelled',
        'IS-07' => 'Combined Pending Pick',
    ];

    if (!empty($key)) {
        return $arr[$key] ?? $key;
    }

    return $arr;
}

function discrepancy_status($key = null){
    $arr =  [
        'DS-01' => 'CS to review',
        'DS-07' => 'Price adjustment',
        'DS-02' => 'Ecom to advise',
        'DS-03' => 'Ecom to process',
        'DS-04' => 'Completed',
        'DS-06' => 'Wipe out done',
    ];

    if (!empty($key)) {
        return $arr[$key] ?? $key;
    }

    return $arr;
}

function package_dis_status($key = null){
    $arr =  [
        'PDS-01' => 'CS to advise',
        'PDS-02' => 'Ecom to advise',
        'PDS-03' => 'Ecom to process',
        'PDS-04' => 'Completed'
    ];

    if (!empty($key)) {
        return $arr[$key] ?? $key;
    }

    return $arr;
}

function getCategoryName($title, $type = 'child'){
    $cat =  Category::where('code', $title)->first();
    if ($type == 'main') {
        $cat =  Category::where('id', $title)->first();
    }

    return $cat->name ?? $title;
}

function conditionCode(){
    return [
        'Grade 1 N',
        'Grade 2 U',
        'Grade 3 D',
        'Grade Discrepancies',
        'Grade Charity',
        'Disposal',
    ];
}

function getItemDate($value='') {
    $arr = [
        'Manufacturing Date',
        'Expiry Date',
        'Best Before Date',
        'Use By Date',
        'Sell By Date'
    ];

    return $arr;
}

function getResellingGrade($value='') {
    $arr = [
        'Grade 1N',
        'Grade 2U',
        'Grade 3D',
        'Grade 1N/2U',
        'Grade 1N/3D',
        'Grade 2U/3D',
        'Grade 1N/2U/3D'
    ];

    return $arr;
}


function returnColumn(){
    $arr =  ['order_status','reference_number','evtn_number','customer_name','tracking_number','customer_address_line_1','customer_address_line_2','customer_city','customer_state','customer_pincode','currency','value'];

    return $arr;
}

function eBayCondition($key = null){
    $code = [
        'New' => 'New',
        'New with box' => 'New',
        'New with Box' => 'New',
        'New with tags' => 'New',
        'New with Tags' => 'New',
        'New – Open box' => 'New',
        'Brand New' => 'New',
        'Brand new' => 'New',
        'Open box' => 'New other',
        'New without tags' => 'New other',
        'New without box' => 'New other',
        'New other (see details)' => 'New other',
        'New other' => 'New other',
        'New with defects' => 'New with defects',
        'Certified refurbished' => 'Certified refurbished',
        'Seller refurbished' => 'Seller refurbished',
        'Remanufactured' => 'Seller refurbished',
        'Retread' => 'Seller refurbished',
        'Certified pre-owned' => 'Seller refurbished',
        'Like New' => 'Like New',
        'Used' => 'Used EXCELLENT',
        'Pre-owned' => 'Used EXCELLENT',
        'Very Good' => 'Used Very Good',
        'Good' => 'Used Good',
        'For parts or not working' => 'For parts or not working',
        'Damaged' => 'Damaged',
        'Acceptable' => 'Used Acceptable',
        'Excellent - Refurbished' => 'Excellent Refurbished',
        'Good - Refurbished' => 'Good Refurbished',
    ];

    if (!empty($key)) {
        return $code[$key] ?? $key;
    }

    return $key;
}

function conditions(){
    $code = [
        'New' => 'New',
        'New with box' => 'New',
        'New with Box' => 'New',
        'New with tags' => 'New',
        'New with Tags' => 'New',
        'New – Open box' => 'New',
        'Brand New' => 'New',
        'Brand new' => 'New',
        'Open box' => 'New other',
        'New without tags' => 'New other',
        'New without box' => 'New other',
        'New other (see details)' => 'New other',
        'New other' => 'New other',
        'New with defects' => 'New with defects',
        'Certified refurbished' => 'Certified refurbished',
        'Seller refurbished' => 'Seller refurbished',
        'Remanufactured' => 'Seller refurbished',
        'Retread' => 'Seller refurbished',
        'Certified pre-owned' => 'Seller refurbished',
        'Like New' => 'Like New',
        'Used' => 'Used EXCELLENT',
        'Pre-owned' => 'Used EXCELLENT',
        'Very Good' => 'Used Very Good',
        'Good' => 'Used Good',
        'For parts or not working' => 'For parts or not working',
        'Damaged' => 'Damaged',
        'Acceptable' => 'Used Acceptable',
        'Excellent - Refurbished' => 'Excellent Refurbished',
        'Good - Refurbished' => 'Good Refurbished',
    ];

    return $code;
}


/**
 * Get customer order billing and shipping information
 *
 * @param order_id
 * @return array
 */
function get_customer_order_billing_shipping_info($order_id) {
    return GetFunction::customer_order_billing_shipping_info($order_id);
}

function updateOrCreatePostMeta($postId, $metaKey, $metaValue) {
    PostExtra::updateOrCreate(
        [
            'post_id' => $postId,
            'key_name' => $metaKey,
        ],
        [
            'key_value' => $metaValue,
        ]
    );
}

function cancel_reason($key = null){
    $arr =  [
        'CR-01' => 'ITEM NOT FOUND IN LOCATION',
        'CR-02' => 'ITEM DAMAGED',
        'CR-03' => 'PARTS MISSING',
        'CR-04' => 'DOES NOT MATCH DESCRIPTION',
        'CR-05' => 'AUTOMATIC ORDER CANCELLED',
        'CR-06' => 'Out of Stock or Damaged',
        'CR-07' => 'Buyer Asked to Cancel',
        'CR-08' => 'Issue with the Buyers shipping address',
    ];

    /*$arr =  [
        'CR-01' => 'Customer Cancelled',
        'CR-02' => 'eBay Cancelled',
        'CR-03' => 'Item not found',
        'CR-04' => 'Item Damaged',
        'CR-05' => 'Incorrect Item',
        'CR-06' => 'Dangerous Item cannot be shipped',
    ];*/

    if (!empty($key)) {
        return $arr[$key] ?? $key;
    }

    return $arr;
}


if (!function_exists('getLocationTitle')) {
    function getLocationTitle($location_id = '') {
        $get_order = (new Post)->newQuery();
        $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','location_id'],['p4.key_value', '=' , $location_id]]);
        $rack = $get_order->select('posts.post_title')->first();
        return $rack->post_title ?? '';
    }
}