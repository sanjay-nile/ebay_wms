<?php
namespace App\Library;

use App\Models\Country;
use App\User;
use App\Models\State;
use App\Models\ShippingPolicy;
use App\Models\ReverseLogisticWaybill;
use App\Models\PackageDetail;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use DB;
use GuzzleHttp\Client;
use App\Http\Controllers\SendPushNotificationController as PushNotification;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

use App\Models\Post;
use App\Models\PostExtra;

/**
 * About the class
 */
class GetFunction
{
    protected $notification;
    public static $upload_path;

    public function __construct()
    {
        # code...
        $this->notification = new PushNotification;

        self::$upload_path = \Config::get('constants.upload_path');
        $imagePath = public_path(self::$upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777, true, true );
    }

    public static function getCountryNameById($id)
    {
        $name    = '';
        $cnt_obj = Country::where('id', $id)->orWhere('sortname', $id)->first();

        if (!empty($cnt_obj)) {
            $name = $cnt_obj->name;
        }

        return $name;
    }

    public static function getPhoneCodeByCid($id)
    {
        $code    = '';
        $cnt_obj = Country::where('id', $id)->orWhere('sortname', $id)->first();

        if (!empty($cnt_obj)) {
            $code = '+' . $cnt_obj->phonecode;
        }

        return $code;
    }

    public static function getStateCodeByName($state_code)
    {

        $name    = 'N/A';
        $cnt_obj = State::where('name', $state_code)->orWhere('shortname', $state_code)->first();

        if (!empty($cnt_obj)) {
            $name = $cnt_obj->name;
        }

        return $name;
    }

    /**
     * Reason List
     **/
    public static function getOrderRefendList($key)
    {
        $arr = [
            'DRESS_LARGE'        => "Dress too large",
            'DRESS_SMALL'        => "Dress too small",
            'MORE_THAN_ONE_SIZE' => "I ordered more than one size",
            'DIFF_PRODUCT'       => "I received a different product",
            'DIFF_COLOR'         => "I received a different color",
            'DIFF_SIZE'          => "I received a different size",
            'DAMAGED'            => "The box was damaged",
            'LATE_ARRIVED'       => "The purchase arrived too late",
            'IN_BOUTIQUE'        => "In boutique",
            'ONLINE'             => "Online",
            'FAULTY'             => "The product was faulty",
            'DONT_LIKE_MATERIAL' => "I don't like the material",
            'DONT_LIKE_SHAPE'    => "I don't like the shape",
            'DONT_LIKE_COLOR'    => "I don't like the color",
            'DONT_LIKE_PRODUCT'  => "I don't like the product",
            'DONT_LIKE_IT'       => "Don't Like It",
            'WP'                 => "Wrong Product",
            'WC'                 => "Wrong Color",
            'WS'                 => "Wrong Size",
            'DP'                 => "Damage Product",
        ];

        if ($key) {
            return $arr[$key] ?? 'N/A';
        }

        if (empty($key)) {
            # code...
            return 'N/A';
        }

        return $arr;
    }

    /**
    * Lists
    *
    **/
    public static function getCountryList()
    {
        # code...
        $country_list = \App\Models\Country::where(['status' => '1'])->get();
        if ($country_list) {
            return $country_list;
        }

        return [];
    }

    /**
    * Lists
    *
    **/
    public static function getCarrierList()
    {
        # code...
        $carrier_list = \App\Models\Carrier::where(['status' => '1'])->get();
        if ($carrier_list) {
            return $carrier_list;
        }

        return [];
    }

    /**
    * Lists
    *
    **/
    public static function getShipmentList()
    {
        # code...
        $shipping_list = \App\Models\ShippingType::where(['status' => '1'])->get();
        if ($shipping_list) {
            return $shipping_list;
        }

        return [];
    }

    /**
    * Lists
    *
    **/
    public static function getchargesList()
    {
        # code...
        $charges_list = \App\Models\OtherCharge::where(['status' => '1'])->get();
        if ($charges_list) {
            return $charges_list;
        }

        return [];
    }

    public static function get_country_name_by_code( $code )
    {
      $country_code = strtoupper($code);
      $countryList = get_country_list();
      
      if(isset($countryList[$country_code]) && !$countryList[$country_code] ) 
      {
        return $country_code;
      }
      else 
      {
        return $countryList[$country_code];
      }
    }


    public static function get_all_countries()
    {
      return array(
        'AF' => 'Afghanistan',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'PW' => 'Belau',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BQ' => 'Bonaire, Saint Eustatius and Saba',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo (Brazzaville)',
        'CD' => 'Congo (Kinshasa)',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CW' => 'Cura&Ccedil;ao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Republic of Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'CI' => 'Ivory Coast',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Laos',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao S.A.R., China',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'KP' => 'North Korea',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barth&eacute;lemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin (French part)',
        'SX' => 'Saint Martin (Dutch part)',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'SM' => 'San Marino',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia/Sandwich Islands',
        'KR' => 'South Korea',
        'SS' => 'South Sudan',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syria',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom (UK)',
        'UK' => 'United Kingdom (UK)',
        'US' => 'United States (US)',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VA' => 'Vatican',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'WS' => 'Western Samoa',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
      );
    }
    
    public static function available_currency_name()
    {
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
        'EGP' => 'Egyptian Pound (EGP)'
      );
    }

    /*
    * Olive Create Or Update Return order
    * $request is array
    **/
    public static function createOrUpdateReturnOrder($request){
        // DB::beginTransaction();
        try {
            if (isset($request['way_bill_id']) && !empty($request['way_bill_id'])) {
                $reverse_obj                        = ReverseLogisticWaybill::find($request['way_bill_id']);
                $reverse_obj->warehouse_id          = $request['warehouse_id'];
                $reverse_obj->shipping_policy_id    = $request['shipment_id'];
                $reverse_obj->payment_mode          = $request['payment_mode'];
                $reverse_obj->cod_payment_mode      = $request['cash_on_pickup'];
                $reverse_obj->amount                = $request['amount'];  
                $reverse_obj->save();

                $way_bill_id = $request['way_bill_id'];
                # Meta Arrray...
                $meta_array = Arr::only($request, ['customer_state', 'customer_pincode','service_code', 'cash_on_pickup', 'unit_type', 'actual_weight', 'charged_weight', 'remark', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);
            } else {
                $obj = new ReverseLogisticWaybill;
                if ($request['warehouse_id'] == 'discrepency') {
                	$request['warehouse_id'] = null;
                }

                $way_bill_id = $obj->store($request);
                # Meta Arrray...
                $meta_array  = Arr::only($request, ['customer_name', 'customer_email', 'customer_address', 'customer_country', 'customer_state', 'customer_city', 'customer_pincode', 'customer_phone', 'service_code', 'cash_on_pickup', 'number_of_packages', 'unit_type', 'actual_weight', 'charged_weight', 'remark', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);
                $reverse_obj = ReverseLogisticWaybill::find($way_bill_id);
            }

            # package count data...
            if (isset($request['package_count'])) {
            	$l_image = [];            	
            	if(isset($request['item_images'])){
                    $images  = $request['item_images'];
                    if (!empty($images)) {
                        # code...
                        foreach ($images as $k => $image) {
                            $p_image = [];
                            foreach ($image as $key => $value) {
                                $imageName = time() . '-' . rand(0,99999) . '.' . $value->getClientOriginalExtension();
                                $value->move($request['upload_path'], $imageName);
                                array_push($p_image, $request['upload_path'] . '' . $imageName);
                            }
                            $l_image[$k] = $p_image;
                        }
                    }            	    
            	}

            	$request['upload_images'] = $l_image;
                $packge = new PackageDetail;
                $packge->createPackageByWayBillId($request, $way_bill_id);
            }

            # set meta value..
            setCustomMeta($reverse_obj, $meta_array);

            if (!isset($request['warehouse_type'])) {
            	$way_bill_request = GetFunction::generateWayBillRequest($request);
            	// dd(json_encode($way_bill_request));
            	$meta_array['create_waywill_request'] = json_encode($way_bill_request);
            	setCustomMeta($reverse_obj, $meta_array);

                if (!$reverse_obj->hasMeta('_label_url') || ($reverse_obj->hasMeta('_label_url') && $reverse_obj->meta->_label_url == '')) {
                    # Call Logix Grid Api...                    
                    $create_res = GetFunction::createWaywillResponse($way_bill_request);
                    // dd($create_res);
                    if ($create_res->messageType == 'Success') {
                        $meta_array['label_message']             = $create_res->message;
                        $meta_array['label_message_type']        = $create_res->messageType;
                        $meta_array['label_message_status']      = $create_res->status;
                        $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
                        $meta_array['label_url']                 = $create_res->labelURL;
                        $meta_array['waybillNumber']             = $create_res->waybillNumber;
                        $meta_array['create_waywill_data']       = json_encode($create_res);
                        setCustomMeta($reverse_obj, $meta_array);                        
                    } else {                        
                        if(in_array($create_res->message, ['_waybill_is_already_saved', 'Waybill is already saved', 'Waybill already exist'])){
                            $waywill = 'OLIVE-'.$reverse_obj->id;
                            $meta_array['waybillNumber'] = 'OLIVE-'.$reverse_obj->id;
                            setCustomMeta($reverse_obj, $meta_array);
                            goto gnr;
                        }
                        
                        return response()->json(['status' => false, 'message' => $create_res->message, 'status' => 200], 200);
                    }

                    $waywill = $create_res->waybillNumber;
                } else {
                    $waywill = $reverse_obj->meta->_waybillNumber;
                }

                gnr:
                # generate waywill ...
                // dd($waywill);
                $g_arr    = [
                    'waybillNumber'  => $waywill,
                    'carrierCode'    => 'UPS',
                    'aggregator'     => '',
                    'carrierProduct' => '03',
                    // 'carrierProduct' => 'ECOMPACKAGE',
                ];

                $meta_array['generate_waywill_request'] = json_encode($g_arr);
                setCustomMeta($reverse_obj, $meta_array);

                $generate_res = GetFunction::generateWaywillResponse($g_arr);
                if ($generate_res->messageType == 'Error') {
                    $meta_array['generate_waywill_status'] = json_encode($generate_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    return response()->json(['status' => false, 'message' => $generate_res->message, 'status' => 200], 200);
                }

                $meta_array['generate_waywill_status'] = json_encode($generate_res);
                setCustomMeta($reverse_obj, $meta_array);

                $sq_rg_no = $way_bill_request['waybillRequestData']['WaybillNumber'];
                $reverse_obj->status                = 'Success';
                $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
                $reverse_obj->rg_reference_number   = $sq_rg_no;
                $reverse_obj->save();

                // ReverseLogisticWaybill::where(['id' => $reverse_obj->id])->update(['status' => 'Success', 'tracking_id' => $generate_res->carrierWaybill]);

                # send mail to customer...
                $labelDetailList = reset($generate_res->labelDetailList);
                $get_view_data['subject']    =   'Return Order number :-'.$request['way_bill_number'];
                $get_view_data['view']       =   'mails.waywill-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return label has generated. Please click the View URL button below to view your label.', 'url' => $labelDetailList->artifactUrl];
                $get_view_data['attach_pdf'] = '';
                
                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));
                                        
                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);   
                } catch (\Swift_TransportException $e) {
                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);      
                }
            } else {
                $meta_array['order_type'] = 'discrepency';
                setCustomMeta($reverse_obj, $meta_array);
                return response()->json(['status' => true, 'message' => 'Action Completed.', 'status' => 201], 201);
            }
        } catch (Exception $e) {
            return (new \Illuminate\Http\Response)->setStatusCode(200, $e->getMessage());
        }
    }

    /*
    * Olive Generate waybill request response
    * $request_arr is array
    **/
    public static function generateWayBillRequest($request_arr){
        $package_json_string_array = array();
        $i = 1;
        if (isset($request_arr['package_count']) && is_array($request_arr['package_count']) && count($request_arr['package_count']) > 0) {
            $tl_wt = 0;
            $qty = 0;
            foreach ($request_arr['package_count'] as $key => $value) {
                $qty += $value;
                $tl_wt += (double) $weight;
            }

            foreach ($request_arr['package_count'] as $key => $value) {
                if($i > 1){
                    continue;
                }
                $bar_code       = $request_arr['bar_code'][$key] ?? "";
                $length         = $request_arr['length'][$key] ?? '1';
                $width          = $request_arr['width'][$key] ?? '1';
                $height         = $request_arr['height'][$key] ?? '1';
                $weight         = $request_arr['weight'][$key] ?? '1';
                $charged_weight = $request_arr['charged__weight'][$key] ?? '1';

                $weight         = (double) $weight;
                $data           = array(
                    'barCode'                 => $bar_code,
                    'packageCount'            => 1,
                    'length'                  => '10',
                    'width'                   => '8',
                    'height'                  => '3',
                    'weight'                  => $tl_wt,
                    'chargedWeight'           => $tl_wt,
                    'itemCount'               => $qty,
                    'selectedPackageTypeCode' => 'DOCUMENT',
                );
                array_push($package_json_string_array, $data);
                $i++;
            }
        }

        $phone = $request_arr['customer_phone'] ?? $order->meta->_customer_phone;
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $remark = '';
        if (isset($request_arr['remark'])) {
            # code...
            $remark = (strlen($request_arr['remark']) > 45) ? substr($request_arr['remark'],0,45).'.' : $request_arr['remark'];
        }

        $sq_rg_no = 'OLIVE-'.$request_arr['way_bill_id'];
        $ref = 'OLIVE-'.$request_arr['way_bill_number'].'-'.$request_arr['way_bill_id'];

        $add = (strlen($request_arr['customer_address']) > 35) ? substr($request_arr['customer_address'],0,30).'.' : $request_arr['customer_address'];

        $array = array(
            "waybillRequestData" => array(
                "FromOU"                    => Config('constants.warehouse'),
                "bookingOu"                 => Config('constants.warehouse'),
                "WaybillNumber"             => $sq_rg_no,
                "DeliveryDate"              => $request_arr['delivery_date'] ?? "",
                "CustomerCode"              => $request_arr['customer_code'],
                "CustomerName"              => mb_strimwidth($request_arr['customer_name'], 0, 35),
                "CustomerAddress"           => $add,
                "CustomerCity"              => $request_arr['customer_city'],
                "CustomerCountry"           => $request_arr['customer_country'],
                "CustomerPhone"             => '1234567891',
                "CustomerState"             => str_replace(' ', '', $request_arr['customer_state']),
                "CustomerPincode"           => $request_arr['customer_pincode'],
                "ConsigneeCode"             => $request_arr['consignee_code'] ?? "",
                "ConsigneeAddress"          => $request_arr['consignee_address'],
                "ConsigneeCountry"          => $request_arr['consignee_country'],
                "ConsigneeState"            => $request_arr['consignee_state'],
                "ConsigneeCity"             => $request_arr['consignee_city'],
                "ConsigneePincode"          => $request_arr['consignee_pincode'],
                "ConsigneeName"             => $request_arr['consignee_name'],
                "ConsigneePhone"            => $request_arr['consignee_phone'],
                "ClientCode"                => $request_arr['client_code'],
                "NumberOfPackages"          => $request_arr['number_of_packages'],
                "ActualWeight"              => $request_arr['actual_weight'],
                "ChargedWeight"             => $request_arr['charged_weight'],
                "CargoValue"                => "1",
                "ReferenceNumber"           => $ref ?? "REVERSEGEAR",
                "InvoiceNumber"             => $ref ?? "",
                "ServiceCode"               => "03",
                "WeightUnitType"            => $request_arr['unit_type'],
                "Description"               => $remark ?? "ReverseGear Return order",
                "COD"                       => "",
                "PaymentMode"               => $request_arr['payment_mode'],
                "CODPaymentMode"            => "",
                "CreateWaybillWithoutStock" => "True",
                "stockIn" => true,
                "packageDetails"            => array(
                    'packageJsonString' => $package_json_string_array,
                ),
            ),
        );

        return $array;
    }

    /*
    * client side auto create Olive order waywill
    * code by sanjay
    **/
    public static function autoCreateWaywill($request){
        DB::beginTransaction();
        try {
            $reverse_obj = ReverseLogisticWaybill::find($request['way_bill_id']);
            $way_bill_id = $request['way_bill_id'];
            
            if (!isset($request['warehouse_type'])) {
                $return_request = GetFunction::autoGenerateRequest($reverse_obj, $request);
                $create_res = GetFunction::createWaywillResponse($return_request);
                if ($create_res->messageType != 'Success') {
                    $meta_array['create_waywill_data'] = json_encode($create_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    DB::commit();
                    return true;
                }

                $meta_array['label_message']             = $create_res->message;
                $meta_array['label_message_type']        = $create_res->messageType;
                $meta_array['label_message_status']      = $create_res->status;
                $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
                $meta_array['label_url']                 = $create_res->labelURL;
                $meta_array['waybillNumber']             = $create_res->waybillNumber;
                $meta_array['create_waywill_data']       = json_encode($create_res);
                setCustomMeta($reverse_obj, $meta_array);

                $waywill = $create_res->waybillNumber;

                # generate waywill request...
                $g_arr = [
                    'waybillNumber'  => $waywill,
                    'carrierCode'    => 'UPS',
                    'aggregator'     => '',
                    'carrierProduct' => '03',
                    // 'carrierProduct' => 'ECOMPACKAGE',
                ];

                $generate_res = GetFunction::generateWaywillResponse($g_arr);
                if ($generate_res->messageType != 'Success') {
                    $meta_array['generate_waywill_status'] = json_encode($generate_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    DB::commit();
                    return true;
                }

                $meta_array['generate_waywill_status'] = json_encode($generate_res);
                setCustomMeta($reverse_obj, $meta_array);
                
                $sq_rg_no = $return_request['waybillRequestData']['WaybillNumber'];
                $reverse_obj->warehouse_id          = $request['warehouse_id'] ?? '';
                $reverse_obj->shipping_policy_id    = $request['shipment_id'] ?? '';
                $reverse_obj->payment_mode          = $request['payment_mode'] ?? 'TBB';
                $reverse_obj->cod_payment_mode      = "Cash";
                $reverse_obj->amount                = "0";  
                $reverse_obj->status                = 'Success';
                $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
                $reverse_obj->rg_reference_number   = $sq_rg_no;
                $reverse_obj->save();

                DB::commit();

                # send mail to customer...
                $labelDetailList = reset($generate_res->labelDetailList);
                $get_view_data['subject']    =   'Return Order number :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.waywill-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return label has generated. Please click the View URL button below to view your label.', 'url' => $labelDetailList->artifactUrl];
                $get_view_data['attach_pdf'] = '';
                
                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));

                    return false;
                } catch (\Swift_TransportException $e) {
                    return false;   
                }                
            } else {
                $meta_array['order_type'] = 'discrepency';
                setCustomMeta($reverse_obj, $meta_array);
                DB::commit();
                return false;
            }
        } catch (Exception $e) {
            DB::rollback();
            return true;
        }
    }

    /*
    * customer side Olive create or generate waywill
    * $request_arr is array
    **/
    public static function autoGenerateWaywill($request){
    	DB::beginTransaction();
    	try {
    	    $reverse_obj = ReverseLogisticWaybill::find($request['way_bill_id']);
    	    $way_bill_id = $request['way_bill_id'];
    	    
    	    $return_request = GetFunction::autoGenerateRequest($reverse_obj, $request);
    	    $meta_array['create_waywill_request'] = json_encode($return_request);
    	    setCustomMeta($reverse_obj, $meta_array);
            // dd($return_request);

            $create_res = GetFunction::createWaywillResponse($return_request);
            if ($create_res->messageType != 'Success') {
            	$meta_array['create_waywill_data'] = json_encode($create_res);
            	setCustomMeta($reverse_obj, $meta_array);
            	DB::commit();
                return true;
            }

            # create waywill data...
            $meta_array['label_message']             = $create_res->message;
            $meta_array['label_message_type']        = $create_res->messageType;
            $meta_array['label_message_status']      = $create_res->status;
            $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
            $meta_array['label_url']                 = $create_res->labelURL;
            $meta_array['waybillNumber']             = $create_res->waybillNumber;
            $meta_array['create_waywill_data']       = json_encode($create_res);
            setCustomMeta($reverse_obj, $meta_array);

            $waywill = $create_res->waybillNumber;

            # generate waywill request...
            $g_arr = [
                'waybillNumber'  => $waywill,
                'carrierCode'    => 'UPS',
                'aggregator'     => '',
                'carrierProduct' => '03',
                // 'carrierProduct' => 'ECOMPACKAGE',
            ];

            $meta_array['generate_waywill_request'] = json_encode($g_arr);
            setCustomMeta($reverse_obj, $meta_array);

            $generate_res = GetFunction::generateWaywillResponse($g_arr);
            // dd($generate_res);
            if ($generate_res->messageType != 'Success') {
            	$meta_array['generate_waywill_status'] = json_encode($generate_res);
            	setCustomMeta($reverse_obj, $meta_array);
            	DB::commit();
                return true;
            }

            $meta_array['generate_waywill_status'] = json_encode($generate_res);
            setCustomMeta($reverse_obj, $meta_array);

            $sq_rg_no = $return_request['waybillRequestData']['WaybillNumber'];

            $reverse_obj->warehouse_id          = $request['warehouse_id'] ?? '';
            $reverse_obj->shipping_policy_id    = $request['shipment_id'] ?? '';
            $reverse_obj->payment_mode          = $request['payment_mode'] ?? 'TBB';           
            $reverse_obj->cod_payment_mode      = "Cash";
            $reverse_obj->amount                = "0";              
            $reverse_obj->status                = 'Success';
            $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
            $reverse_obj->rg_reference_number   = $sq_rg_no;
            $reverse_obj->save();
            DB::commit();

            # send mail to customer...
            $labelDetailList = reset($generate_res->labelDetailList);
            $get_view_data['subject']    =   'Return Order number :-'.$reverse_obj->way_bill_number;
            $get_view_data['view']       =   'mails.waywill-order';
            $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return label has generated. Please click the View URL button below to view your label.', 'url' => $labelDetailList->artifactUrl];
            $get_view_data['attach_pdf'] = '';            
            try {
                $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));

                return false;
            } catch (\Swift_TransportException $e) {
                return false;
            }            
    	} catch (Exception $e) {
    	    DB::rollback();
    	    return true;
    	}    
    }

    /*
    * customer side Olive create or generate request
    * $request_arr is array
    **/
    public static function autoGenerateRequest($order, $request_arr = null){
        $package_json_string_array = array();
        $no_of_pkg = $wt = 0;
        $i = 1;

        $tl_wt = 0;
        $qty = 0;
        foreach ($order->packages as $key => $value) {
            $qty += $value->package_count;
            $tl_wt += (double) $value->weight;
        }

        foreach ($order->packages as $key => $value) {
            if($i > 1){
                continue;
            }

            $weight = (double) $value->weight;
            $data = array(
                'barCode'                 => $value->bar_code,
                'packageCount'            => 1,
                'length'                  => '10',
                'width'                   => '8',
                'height'                  => '3',
                'weight'                  => $tl_wt,
                'chargedWeight'           => $tl_wt,
                'itemCount'               => $qty,
                'selectedPackageTypeCode' => 'DOCUMENT',
            );
            array_push($package_json_string_array, $data);
            $no_of_pkg += $value->package_count;
            $wt += $value->weight;
            $i++;
        }

        $phone = $request_arr['customer_phone'] ?? $order->meta->_customer_phone;
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }
        
        $remark = '';
        if (isset($request_arr['remark'])) {
            # code...
            $remark = (strlen($request_arr['remark']) > 45) ? substr($request_arr['remark'],0,45).'.' : $request_arr['remark'];
        }

        $sq_rg_no = 'OLIVE-'.$order->id;
        $ref = 'OLIVE-'.$order->way_bill_number.'-'.$order->id;

        $add = (strlen($request_arr['customer_address']) > 35) ? substr($request_arr['customer_address'],0,30).'.' : $request_arr['customer_address'];

        $array = array(
            "waybillRequestData" => array(
                "FromOU"                    => Config('constants.warehouse'),
                "bookingOu"                 => Config('constants.warehouse'),
                "WaybillNumber"             => $sq_rg_no,
                "DeliveryDate"              => "",
                "CustomerCode"              => "00000",
                "CustomerName"              => mb_strimwidth($order->meta->_customer_name, 0, 35),
                "CustomerAddress"           => $add,
                "CustomerCity"              => $order->meta->_customer_city,
                "CustomerCountry"           => $order->meta->_customer_country,
                "CustomerState"             => str_replace(' ', '', $order->meta->_customer_state),
                "CustomerPincode"           => $request_arr['customer_pincode'] ?? $order->meta->_customer_pincode,
                "CustomerPhone"             => '1234567891',
                // "CustomerAddress"           => '511 S. Carondelet St',
                // "CustomerCity"              => 'Los Angeles',
                // "CustomerCountry"           => 'US',
                // "CustomerState"             => 'CA',
                // "CustomerPincode"           => '90057',
                // "CustomerPhone"             => '+19650977447',
                "ConsigneeCode"             => "00000",
                "ConsigneeAddress"          => $request_arr['consignee_address'] ?? "1250 Greenbriar Dr Unit C",
                "ConsigneeCountry"          => $request_arr['customer_country'] ?? "US",
                "ConsigneeState"            => $request_arr['consignee_state'] ?? "IL",
                "ConsigneeCity"             => $request_arr['consignee_city'] ?? "Addison",
                "ConsigneePincode"          => $request_arr['consignee_pincode'] ?? "60101",
                "ConsigneeName"             => $request_arr['consignee_name'] ?? "Ecom Global TeamWW",
                "ConsigneePhone"            => $request_arr['consignee_phone'] ?? "8664426160",
                "ClientCode"                => "REVERSEGEAR",
                "NumberOfPackages"          => $no_of_pkg,
                "ActualWeight"              => $request_arr['actual_weight'] ?? $wt,
                "ChargedWeight"             => $request_arr['actual_weight'] ?? $no_of_pkg,
                "CargoValue"                => "1",
                "ReferenceNumber"           => $ref ?? "REVERSEGEAR",
                "InvoiceNumber"             => $ref ?? $order->id,
                "ServiceCode"               => "03",
                "WeightUnitType"            => $request_arr['unit_type'] ?? "POUND",
                "Description"               => $remark ?? "ReverseGear Return order",
                "COD"                       => "",
                "PaymentMode"               => $request_arr['payment_mode'] ?? "TBB",
                "CODPaymentMode"            => "",
                "CreateWaybillWithoutStock" => "True",
                "stockIn" => true,
                "packageDetails"            => array(
                    'packageJsonString' => $package_json_string_array,
                ),
            ),
        );

        return $array;
    }

    /*
    * client side auto create missguided waywill
    * code by sanjay
    **/
    public static function autoCreateMissguidedWaywill($request){
        DB::beginTransaction();
        try {
            $reverse_obj = ReverseLogisticWaybill::find($request['way_bill_id']);
            $way_bill_id = $request['way_bill_id'];            
            
            if (!isset($request['warehouse_type'])) {
                $return_request = GetFunction::autoGenerateMissguidedRequest($reverse_obj, $request);
                $js_data = stripslashes(json_encode($return_request));

                $meta_array['create_waywill_request'] = json_encode($return_request);
            	setCustomMeta($reverse_obj, $meta_array);

                // $url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.testSecureKey');
                // $url = Config('constants.b2cRetuenUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');
                $url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');

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

                $create_res = json_decode($create_response);
                if ($create_res->messageType != 'Success') {
                    $meta_array['create_waywill_data'] = json_encode($create_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    DB::commit();
                    return redirect()->back()->with('error', 'Create waywill Api:- '.$create_res->message);
                }

                if (empty($create_res)) {
                    # code...
                    DB::rollback();
                    return redirect()->back()->with('error', 'Create waywill Api:- no response');
                }

                $meta_array['label_message']             = $create_res->message;
                $meta_array['label_message_type']        = $create_res->messageType;
                $meta_array['label_message_status']      = $create_res->status;
                $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
                $meta_array['label_url']                 = $create_res->labelURL;
                $meta_array['waybillNumber']             = $create_res->waybillNumber;
                $meta_array['create_waywill_data']       = json_encode($create_res);
                setCustomMeta($reverse_obj, $meta_array);

                $waywill = $create_res->waybillNumber;

                # generate waywill request...               
                /*$g_arr = [
                    'waybillNumber' => $waywill,
                    'carrierCode' => 'HERMESGLOBAL',
                    'aggregator' => '',
                    'carrierProduct' => 'PickUpDropOff',
                ];*/

                $g_arr = [
                    'waybillNumber' => $waywill,
                    'carrierCode' => 'B2CRETURNYOURPARCEL',
                    'aggregator' => '',
                    'carrierProduct' => 'BOX',
                ];

                $meta_array['generate_waywill_request'] = json_encode($g_arr);
            	setCustomMeta($reverse_obj, $meta_array);

                // $gen_url = Config('constants.activeUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.testSecureKey');
                // $gen_url = Config('constants.b2cRetuenUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');
                $gen_url = Config('constants.activeUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');

                $g_client = new Client(['headers'=>['AccessKey'=> 'logixerp', 'Content-Type' => 'application/json']]);
                $rg = $g_client->post($gen_url,['form_params' => $g_arr]);
                $g_response = $rg->getBody()->getContents();
                $generate_res = json_decode($g_response);

                // dd($generate_res);
                if ($generate_res->messageType != 'Success') {
                    $meta_array['generate_waywill_status'] = json_encode($generate_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    DB::commit();
                    return redirect()->back()->with('error', 'Generate waywill Api:- '.$create_res->message);
                }

                if (empty($generate_res)) {
                    # code...
                    DB::rollback();
                    return redirect()->back()->with('error', 'Generate waywill Api:- no response');
                }

                $meta_array['generate_waywill_status'] = json_encode($generate_res);
                setCustomMeta($reverse_obj, $meta_array);

                # rg refrence number...
                $sq_rg_no = $return_request['waybillRequestData']['WaybillNumber'];
                
                $reverse_obj->warehouse_id          = $request['warehouse_id'] ?? '';
                $reverse_obj->shipping_policy_id    = $request['shipment_id'] ?? '';
                $reverse_obj->payment_mode          = $request['payment_mode'] ?? '';
                $reverse_obj->cod_payment_mode      = "Cash";
                $reverse_obj->amount                = "0";  
                $reverse_obj->status                = 'Success';
                $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
                $reverse_obj->rg_reference_number   = $sq_rg_no;
                $reverse_obj->save();

                DB::commit();

                $dlt = ReverseLogisticWaybill::where(['way_bill_number' => $reverse_obj->way_bill_number, 'status' => 'Pending'])->delete();

                # send mail to customer...
                $labelDetailList = reset($generate_res->labelDetailList);
                /*$get_view_data['subject']    =   'Return Order number :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.waywill-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return label has generated. Please click the View URL button below to view your label.', 'url' => $labelDetailList->artifactUrl];*/

                $get_view_data['subject']    =   'Missguided Return Confirmation :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.missguided-order';
                $get_view_data['user']       =   [
                    'name' =>  $request['customer_name'],
                    'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                    'url' => $labelDetailList->artifactUrl,
                    'order_no' => $reverse_obj->way_bill_number,
                    'track_id' => $generate_res->carrierWaybill,
                    'return_date' => date('d/m/Y', strtotime($reverse_obj->created_at)),
                    'return_service' => $request['carrier_name'],
                    'return_cost' => 0,
                ];
                $get_view_data['attach_pdf'] = '';

                # save pdf and send to mail...
                $pdf_url  = $labelDetailList->artifactUrl;
                $filename  = basename($pdf_url);
                $fileName  = $filename;
                $path_upload = self::$upload_path.$fileName;
                $ch = curl_init($pdf_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $pdf_data = curl_exec($ch);
                curl_close($ch);
                $result = file_put_contents($path_upload, $pdf_data);

                if ($result) {
                    $get_view_data['attach_pdf'] = public_path($path_upload);
                    $get_view_data['pdf_filename'] = $fileName;
                }

                # mail send here...
                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));

                    # unlink uploaded pdf file...
                    if (!empty($get_view_data['attach_pdf'])) {
                        # code...
                        // unlink(public_path($path_upload));
                    }

                    return redirect()->back()->with('success', 'Your Return request has been successfully processed.');
                } catch (\Swift_TransportException $e) {
                    return redirect()->back()->with('success', 'Your Return request has been successfully processed.');
                }                
            } else {
                $meta_array['order_type'] = 'discrepency';
                setCustomMeta($reverse_obj, $meta_array);
                DB::commit();
                return redirect()->back()->with('success', 'Your Return request has been successfully processed.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*
    * client side auto update missguided waywill
    * code by sanjay
    **/
    public static function autoUpdateMissguidedWaywill($request){
        DB::beginTransaction();
        try {
            $reverse_obj                        = ReverseLogisticWaybill::find($request['way_bill_id']);
            $reverse_obj->warehouse_id          = $request['warehouse_id'];
            $reverse_obj->shipping_policy_id    = $request['shipment_id'];
            $reverse_obj->payment_mode          = $request['payment_mode'];
            $reverse_obj->cod_payment_mode      = $request['cash_on_pickup'];
            $reverse_obj->amount                = $request['amount'];  
            $reverse_obj->save();

            $way_bill_id = $request['way_bill_id'];
            # Meta Arrray...
            $meta_array = Arr::only($request, ['service_code', 'cash_on_pickup', 'unit_type', 'actual_weight', 'charged_weight', 'remark', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);

            # set meta value..
            setCustomMeta($reverse_obj, $meta_array);

            if (!isset($request['warehouse_type'])) {
            	$return_request = GetFunction::autoGenerateMissguidedRequest($reverse_obj, $request);
            	$meta_array['create_waywill_request'] = json_encode($return_request);
            	setCustomMeta($reverse_obj, $meta_array);

                if (!$reverse_obj->hasMeta('_label_url') || ($reverse_obj->hasMeta('_label_url') && $reverse_obj->meta->_label_url == '')) {
                    # Call Logix Grid Api...
                    $js_data = stripslashes(json_encode($return_request));

                    // $url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.testSecureKey');
                    // $url = Config('constants.b2cRetuenUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');
                    $url = Config('constants.activeUrl').'CreateWaybill?secureKey='.Config('constants.secureKey');

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
                    $create_res = json_decode($create_response);

                    if ($create_res->messageType != 'Success') {
                        $meta_array['create_waywill_data'] = json_encode($create_res);
                        setCustomMeta($reverse_obj, $meta_array);
                        DB::commit();
                        return response()->json(['status' => false, 'message' => $create_res->message, 'status' => 200], 200);
                    }

                    if (empty($create_res)) {
                        # code...
                        DB::rollback();
                        return response()->json(['status' => false, 'message' => 'Create waywill return no response.', 'status' => 200], 200);
                    }

                    $meta_array['label_message']             = $create_res->message;
                    $meta_array['label_message_type']        = $create_res->messageType;
                    $meta_array['label_message_status']      = $create_res->status;
                    $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
                    $meta_array['label_url']                 = $create_res->labelURL;
                    $meta_array['waybillNumber']             = $create_res->waybillNumber;
                    $meta_array['create_waywill_data']       = json_encode($create_res);
                    setCustomMeta($reverse_obj, $meta_array);

                    $waywill = $create_res->waybillNumber;
                } else {
                    $waywill = $reverse_obj->meta->_waybillNumber;
                }

                # generate waywill request...
                $g_arr = [
                    'waybillNumber' => $waywill,
                    'carrierCode' => 'B2CRETURNYOURPARCEL',
                    'aggregator' => '',
                    'carrierProduct' => 'BOX',
                ];

                $meta_array['generate_waywill_request'] = json_encode($g_arr);
            	setCustomMeta($reverse_obj, $meta_array);

                // $gen_url = Config('constants.activeUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.testSecureKey');
                // $gen_url = Config('constants.b2cRetuenUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');
                $gen_url = Config('constants.activeUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');

                $g_client = new Client(['headers'=>['AccessKey'=> 'logixerp', 'Content-Type' => 'application/json']]);
                $rg = $g_client->post($gen_url,['form_params' => $g_arr]);
                $g_response = $rg->getBody()->getContents();
                $generate_res = json_decode($g_response);

                // dd($generate_res);
                if ($generate_res->messageType != 'Success') {
                    $meta_array['generate_waywill_status'] = json_encode($generate_res);
                    setCustomMeta($reverse_obj, $meta_array);
                    DB::commit();
                    return response()->json(['status' => false, 'message' => $generate_res->message, 'status' => 200], 200);
                }

                if (empty($generate_res)) {
                    # code...
                    DB::rollback();
                    return response()->json(['status' => false, 'message' => 'Generate waywill return no response.', 'status' => 200], 200);
                }

                $meta_array['generate_waywill_status'] = json_encode($generate_res);
                setCustomMeta($reverse_obj, $meta_array);

                # rg refrence number...
                $sq_rg_no = $return_request['waybillRequestData']['WaybillNumber'];
                
                $reverse_obj->warehouse_id          = $request['warehouse_id'] ?? '';
                $reverse_obj->shipping_policy_id    = $request['shipment_id'] ?? '';
                $reverse_obj->payment_mode          = $request['payment_mode'] ?? 'TBB';
                $reverse_obj->cod_payment_mode      = "Cash";
                $reverse_obj->amount                = "0";  
                $reverse_obj->status                = 'Success';
                $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
                $reverse_obj->rg_reference_number   = $sq_rg_no;
                $reverse_obj->save();

                DB::commit();

                $dlt = ReverseLogisticWaybill::where(['way_bill_number' => $reverse_obj->way_bill_number, 'status' => 'Pending'])->delete();
                
                # send mail to customer...
                $labelDetailList = reset($generate_res->labelDetailList);
                /*$get_view_data['subject']    =   'Return Order number :-'.$request['way_bill_number'];
                $get_view_data['view']       =   'mails.waywill-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return label has generated. Please click the View URL button below to view your label.', 'url' => $labelDetailList->artifactUrl];*/

                $get_view_data['subject']    =   'Missguided Return Confirmation :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.missguided-order';
                $get_view_data['user']       =   [
                    'name' =>  $request['customer_name'],
                    'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                    'url' => $labelDetailList->artifactUrl,
                    'order_no' => $reverse_obj->way_bill_number,
                    'track_id' => $generate_res->carrierWaybill,
                    'return_date' => date('d/m/Y', strtotime($reverse_obj->created_at)),
                    'return_service' => $request['carrier_name'],
                    'return_cost' => 0,
                ];
                $get_view_data['attach_pdf'] = '';

                # save pdf and send to mail...
                $pdf_url  = $labelDetailList->artifactUrl;
                $filename  = basename($pdf_url);
                $fileName  = $filename;
                $path_upload = self::$upload_path.$fileName;
                $ch = curl_init($pdf_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $pdf_data = curl_exec($ch);
                curl_close($ch);
                $result = file_put_contents($path_upload, $pdf_data);

                if ($result) {
                    $get_view_data['attach_pdf'] = public_path($path_upload);
                    $get_view_data['pdf_filename'] = $fileName;

                    $pdf_arr['attachment_pdf'] = $path_upload;
                    setCustomMeta($reverse_obj, $pdf_arr);
                }
                
                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));

                    # unlink uploaded pdf file...
                    if (!empty($get_view_data['attach_pdf'])) {
                        # code...
                        // unlink(public_path($path_upload));
                    }

                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);
                } catch (\Swift_TransportException $e) {
                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);   
                }                
            } else {
                $meta_array['order_type'] = 'discrepency';
                setCustomMeta($reverse_obj, $meta_array);
                return response()->json(['status' => true, 'message' => 'Action Completed.', 'status' => 201], 201);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return (new \Illuminate\Http\Response)->setStatusCode(400, $e->getMessage());
        }
        
    }

    /*
    * Missguided create or generate waywill request
    * $request_arr is array
    **/
    public static function autoGenerateMissguidedRequest($order, $request_arr = null){
        $package_json_string_array = array();
        $no_of_pkg = $wt = 0;
        foreach ($order->packages as $key => $value) {
            $data = array(
                'barCode'                 => $value->bar_code,
                'packageCount'            => $value->package_count,
                'length'                  => '10',
                'width'                   => '8',
                'height'                  => '3',
                'weight'                  => $value->weight,
                'chargedWeight'           => "1",
                'selectedPackageTypeCode'=>'BOX'
                // 'selectedPackageTypeCode' => 'PC',
            );
            array_push($package_json_string_array, $data);
            $no_of_pkg += $value->package_count;
            $wt += $value->weight;
        }

        $phone = $request_arr['customer_phone'] ?? $order->meta->_customer_phone;
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }
        
        $remark = '';
        if (isset($request_arr['remark'])) {
            # code...
            $remark = (strlen($request_arr['remark']) > 50) ? substr($request_arr['remark'],0,50).'...' : $request_arr['remark'];
        }

        $order_no = rand(0,999999);

        # rg refrence number...
        // $sq_rg_no = $order->way_bill_number.'-'.date('dmy').'-'.$request_arr['customer_country'].'-1251-'.$order->id;
        $sq_rg_no = 'MISS-'.$order->way_bill_number.'-'.$order->id.'-1251-'.$request_arr['customer_country'];
        

        $waybill_array = array(
            "waybillRequestData" => array(
                "consigneeGeoLocation" => "",
                "FromOU" => 'LONDON',
                // "FromOU" => 'HO DELHI',
                // "FromOU" => 'CHICAGO',
                "DeliveryDate" => "",
                // "WaybillNumber" => 'MG'.$order->id,
                "WaybillNumber" => $sq_rg_no,
                "CustomerCode" => "00000",
                "CustomerPhone" => $request_arr['customer_phone'],
                "CustomerAddress" => $request_arr['customer_address'],
                "CustomerCity" => $request_arr['customer_city'],
                "CustomerCountry" => $request_arr['customer_country'],
                "CustomerState" => $request_arr['customer_state'],
                "CustomerPincode" => $request_arr['customer_pincode'],
                "ConsigneeCode" => "00000",
                "ConsigneeName" => "Reverse Gear Repolog",
                "ConsigneePhone" => "061754009960",
                "ConsigneeAddress" => "Otto-Hahn-Strasse 30",
                "ConsigneeCountry" => 'DE',
                "ConsigneeState" => 'Friedrichsdorf',
                "ConsigneeCity" => 'Friedrichsdorf',
                "ConsigneePincode" => '61381',
                "ConsigneeWhat3Words" => "",
                "CreateWaybillWithoutStock" => "true",
                "stockIn" => true,
                "StartLocation" => "",
                "EndLocation" => "",
                // "ClientCode" => 'HERO',
                "ClientCode" => 'REVERSEGEAR',
                "NumberOfPackages" => $no_of_pkg,
                "ActualWeight" => 1,
                "ChargedWeight" => 1,
                "CargoValue" => "",
                // "ReferenceNumber" => $order->way_bill_number ?? "REVERSEGEAR",
                "ReferenceNumber" => $order->way_bill_number,
                "InvoiceNumber" => $order->way_bill_number,
                "PaymentMode" => "TBB",
                // "ServiceCode" => "COURIER",
                // "WeightUnitType" => "POUND",
                "ServiceCode" => "01",
                "WeightUnitType" => "KILOGRAM",
                "Description" => "ReverseGear Client return order",
                "COD" => "",
                "salesInvoiceNumber" => "",
                "CODPaymentMode" => "",
                "packageDetails" => array(
                    'packageJsonString' => $package_json_string_array
                )                        
            )
        );

        return $waybill_array;
    }

    /*
    * create or generate waywill
    * $request_arr is array
    **/
    public static function createWaywillResponse($request_arr){
        // $post_url = Config('constants.activeUrl') . 'CreateWaybill?secureKey=' . Config('constants.testSecureKey');
        $post_url   = Config('constants.activeUrl') . 'CreateWaybill?secureKey=' . Config('constants.secureKey');
        $client     = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json', 'AccessKey' => Config('constants.AccessKey')]]);
        $r  = $client->post($post_url, ['json' => $request_arr]);
        $response   = $r->getBody()->getContents();
        return json_decode($response);
    }

    /*
    * create or generate waywill
    * $request_arr is array
    **/
    public static function generateWaywillResponse($request_arr){
    	// $url = Config('constants.activeUrl') . 'GenerateCarrierWaybill?secureKey=' . Config('constants.testSecureKey');
    	$url = Config('constants.activeUrl') . 'GenerateCarrierWaybill?secureKey=' . Config('constants.secureKey');
    	$g_client = new \GuzzleHttp\Client(['headers' => ['AccessKey' => Config('constants.AccessKey')]]);    	
    	$rg         = $g_client->post($url, ['form_params' => $request_arr]);
    	$g_response = $rg->getBody()->getContents();
    	return json_decode($g_response);
    }

    /*
    * Happy Return order
    * $request is array
    **/
    public static function happyReturnsOrder($request){
        DB::beginTransaction();
        try {
            if (!isset($request['way_bill_id']) && empty($request['way_bill_id'])) {
                return response()->json(['status' => false, 'message' => 'Order Id Not found.', 'status' => 200], 200);
            }

            $reverse_obj = ReverseLogisticWaybill::find($request['way_bill_id']);
            if(!$reverse_obj){
                return response()->json(['status' => false, 'message' => 'Order Id Not found.', 'status' => 200], 200);
            }

            // $reverse_obj->status = 'Success';
            $reverse_obj->save();
            $way_bill_id = $request['way_bill_id'];

            $request['when'] = date('Y-m-d').'T20:19:24Z';
            $request['reason'] = get_order_refund_list($reverse_obj->meta->_reason_of_return);

            # Meta Arrray...
            $meta_array = Arr::only($request, ['service_code', 'cash_on_pickup', 'unit_type', 'actual_weight', 'charged_weight', 'description', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);

            # package count data...
            if (isset($request['package_count'])) {
                $l_image = [];              
                if(isset($request['item_images'])){
                    $images  = $request['item_images'];
                    if (!empty($images)) {
                        # code...
                        foreach ($images as $k => $image) {
                            $p_image = [];
                            foreach ($image as $key => $value) {
                                $imageName = time() . '-' . rand(0,99999) . '.' . $value->getClientOriginalExtension();
                                $value->move($request['upload_path'], $imageName);
                                array_push($p_image, $request['upload_path'] . '' . $imageName);
                            }
                            $l_image[$k] = $p_image;
                        }
                    }                   
                }

                $request['upload_images'] = $l_image;
                $packge = new \App\Models\PackageDetail;
                $packge->createPackageByWayBillId($request, $way_bill_id);
            }            

            $return_request = GetFunction::happyReturnRequest($request);
            // dd($return_request);
            $post_url = Config('constants.happyReturnUrl') . 'create_return';
            // $post_url = Config('constants.happyReturnStageUrl') . 'create_return';

            $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'text/plain', 'X-HR-APIKEY' => Config('constants.happyReturnKey')]]);
            $r = $client->post($post_url, ['json' => $return_request]);
            $response = $r->getBody()->getContents();
            $json_data = json_decode($response);

            $meta_array['happy_return_status'] = $response;
            setCustomMeta($reverse_obj, $meta_array);

            if (isset($json_data->rma_id) && !empty($json_data->rma_id)) {
                # code...
                setCustomMeta($reverse_obj, $meta_array);
                $qr = explode('=', $json_data->qr_code);
                ReverseLogisticWaybill::where(['id' => $reverse_obj->id])->update(['status' => 'Success', 'qr_code' => last($qr)]);
                DB::commit();                

                # send mail to customer...
                $get_view_data['subject']    =   'Return Order number :-'.$request['way_bill_number'];
                $get_view_data['view']       =   'mails.return-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return is authorized! Please show the QR Code on the Return Bar™ location and proceed with your return.', 'url' => $json_data->qr_code];
                if($reverse_obj->hasMeta('_bar_address')){
                    $get_view_data['user']['bar_address'] = json_decode($reverse_obj->meta->_bar_address);
                }

                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));

                    return response()->json(['status' => true, 'message' => 'Action Completed', 'status' => 201], 201);
                } catch (\Swift_TransportException $e) {
                    return response()->json(['status' => true, 'message' => 'Action Completed', 'status' => 201], 201);
                }                
            }

            return response()->json(['status' => false, 'message' => 'Action not completed', 'status' => 200], 200);

        } catch (Exception $e) {
            DB::rollback();
            return (new \Illuminate\Http\Response)->setStatusCode(400, $e->getMessage());
        }
    }

    /*
    * Happy Return order
    * $request_arr is array
    **/
    public static function happyReturnRequest($request_arr){
        $package_json_string_array = array();
        if (isset($request_arr['package_count']) && is_array($request_arr['package_count']) && count($request_arr['package_count']) > 0) {
            foreach ($request_arr['package_count'] as $key => $value) {
                $image = [];
                if (isset($request_arr['image_url'][$key]) && !empty($request_arr['image_url'][$key])) {
                    # code...
                    array_push($image, $request_arr['image_url'][$key]);
                }

                # code...
                if(!empty($request_arr['product_image'][$key])){
                    foreach (json_decode($request_arr['product_image'][$key]) as $k => $img) {
                        # code...
                        // array_push($image, url('/').'/'.$img);
                        array_push($image, asset('public/'.$img));
                    }
                }               

                for ($i=0; $i < $value ; $i++) {
                    $data = array(
                        'id' => $request_arr['package_arr'][$key],
                        'order_number' => $request_arr['way_bill_number'],
                        'name' => $request_arr['title'][$key],
                        'when' => $request_arr['when'],
                        'reason' => $request_arr['reason'],
                        'zip_code' => $request_arr['customer_pincode'],
                        'display' => [
                            [
                                'label' => 'Size', 'value' => $request_arr['size'][$key]
                            ],
                            [
                                'label' => 'Color', 'value' => $request_arr['color'][$key]
                            ]
                        ],
                        'images' => $image,
                    );
                    array_push($package_json_string_array, $data);
                }                
            }
        }

        $arr_request = [
            'retailer_id' => 'olive',
            'email' => $request_arr['customer_email'],
            'returning' => $package_json_string_array,            
        ];

        return $arr_request;
    }


    /*
    * Auto Happy Return order from frontend
    * $request is array
    **/
    public static function autoHappyReturnOrder($request){
        DB::beginTransaction();
        try {
            $reverse_obj = \App\Models\ReverseLogisticWaybill::find($request['way_bill_id']);
            $way_bill_id = $request['way_bill_id'];
            
            $return_request = GetFunction::autoHappyReturnRequest($reverse_obj);
            // dd(json_encode($return_request, JSON_UNESCAPED_SLASHES));

            // $post_url = Config('constants.happyReturnUrl') . 'create_return';
            $post_url = Config('constants.happyReturnStageUrl') . 'create_return';

            $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'text/plain', 'X-HR-APIKEY' => Config('constants.happyReturnKey')]]);
            // $r = $client->post($post_url, ['json' => $return_request]);
            $r = $client->post($post_url, ['body' => json_encode($return_request, JSON_UNESCAPED_SLASHES)]);
            $response = $r->getBody()->getContents();
            $json_data = json_decode($response);

            $meta_array['happy_return_status'] = $response;
            setCustomMeta($reverse_obj, $meta_array);

            if (isset($json_data->rma_id) && !empty($json_data->rma_id)) {
                # code...
                setCustomMeta($reverse_obj, $meta_array);
                $qr = explode('=', $json_data->qr_code);
                ReverseLogisticWaybill::where(['id' => $reverse_obj->id])->update(['status' => 'Success', 'qr_code' => last($qr)]);
                DB::commit();

                # send mail to customer...
                $get_view_data['subject']    =   'Return Order number :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.return-order';
                $get_view_data['user']       =   ['name' =>  $request['customer_name'], 'message' => 'Your return is authorized! Please show the QR Code on the Return Bar™ location and proceed with your return.', 'url' => $json_data->qr_code];
                if($reverse_obj->hasMeta('_bar_address')){
                    $get_view_data['user']['bar_address'] = json_decode($reverse_obj->meta->_bar_address);
                }

                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));
                    return false;
                } catch (\Swift_TransportException $e) {
                    return false;   
                }                
            }

            return true;
        } catch (Exception $e) {
            DB::rollback();
            return true;
        }
    }

    /*
    * Happy Return order
    * $request_arr is array
    **/
    public static function autoHappyReturnRequest($order){
        $package_json_string_array = array();
        foreach ($order->packages as $key => $value) {
            # code...
            $image = [];
            if (isset($value->image_url) && !empty($value->image_url)) {
                array_push($image, $value->image_url);
            }
            
            if(!empty($value->file_data)){
                foreach (json_decode($value->file_data) as $k => $img) {
                    array_push($image, asset('public/'.$img));
                }
            }

            for ($i=0; $i < $value->package_count ; $i++) {
                $data = array(
                    'id' => strval($value->id),
                    'order_number' => $order->way_bill_number,
                    'name' => $value->title,
                    'when' => date('Y-m-d').'T20:19:24Z',
                    'reason' => $value->return_reason ?? 'N/A',
                    'zip_code' => strval($order->meta->_customer_pincode),
                    'display' => [
                        [
                            'label' => 'Size', 'value' => $value->size
                        ],
                        [
                            'label' => 'Color', 'value' => $value->color
                        ]
                    ],
                    'images' => $image,
                );
                array_push($package_json_string_array, $data);
            }
        }

        $arr_request = [
            'retailer_id' => 'olive',
            'email' => $order->meta->_customer_email,
            'returning' => $package_json_string_array,            
        ];

        return $arr_request;
    }

    /*
    * test request waywill
    * code by sanjay
    **/
    public static function getEtailerList(){
        return User::where(['user_type_id' => 3])->get()->toArray();
    }

    /*
    * Request waywill
    * code by sanjay
    **/
    public static function createClientRefWayBillNumber($request){
        DB::beginTransaction();
        try {

            $waybill = \App\Models\ReverseLogisticWaybill::where(['way_bill_number' => $request['way_bill_number']])->first();
            if ($waybill) {
                return response()->json(['status' => false, 'message' => 'This waybill already exist', 'data' => []], 200);
            }

            $obj         = new \App\Models\ReverseLogisticWaybill;
            $way_bill_id = $obj->store($request);

            //Meta Arrray
            $meta_array = Arr::only($request, ['customer_name', 'customer_email', 'customer_address', 'customer_country', 'customer_state', 'customer_city', 'customer_pincode', 'customer_phone', 'service_code', 'cash_on_pickup', 'number_of_packages', 'weight_unit_type', 'actual_weight', 'charged_weight', 'description', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'return_type', 'product_images']);

            $reverse_obj = \App\Models\ReverseLogisticWaybill::find($way_bill_id);

            if (isset($request['packageDetails']['packageJsonString'])) {
                foreach ($request['packageDetails']['packageJsonString'] as $packge) {
                    $packge_obj                              = new \App\Models\PackageDetail;
                    $packge_obj->bar_code                    = $packge['barCode'];
                    $packge_obj->title                       = $packge['title'];
                    $packge_obj->package_count               = $packge['packageCount'];
                    $packge_obj->length                      = $packge['length'];
                    $packge_obj->width                       = $packge['width'];
                    $packge_obj->height                      = $packge['height'];
                    $packge_obj->weight                      = $packge['weight'];
                    $packge_obj->charged_weight              = $packge['chargedWeight'];
                    $packge_obj->selected_package_type_code  = $packge['selectedPackageTypeCode'];
                    $packge_obj->reverse_logistic_waybill_id = $way_bill_id;
                    $packge_obj->save();
                }

            }
            // Call Logix Grid Api
            $way_bill_request = GetFunction::generateClientRefWayBillRequest($request);
            $post_url         = Config('constants.activeUrl') . 'CreateWaybill?secureKey=' . Config('constants.secureKey');
            $client           = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json', 'AccessKey' => Config('constants.AccessKey')]]);
            $r                = $client->post($post_url, ['json' => $way_bill_request]);
            $response         = $r->getBody()->getContents();
            $json_data        = json_decode($response);
            if ($json_data->messageType == 'Success') {
                $label_arr = array(
                    "label_message"             => $json_data->message,
                    "label_message_type"        => $json_data->messageType,
                    "label_message_status"      => $json_data->status,
                    "label_package_sticker_url" => $json_data->packageStickerURL,
                    "label_url"                 => $json_data->labelURL,
                );
                $meta_array['label_message']             = $json_data->message;
                $meta_array['label_message_type']        = $json_data->messageType;
                $meta_array['label_message_status']      = $json_data->status;
                $meta_array['label_package_sticker_url'] = $json_data->packageStickerURL;
                $meta_array['label_url']                 = $json_data->labelURL;

                setCustomMeta($reverse_obj, $meta_array);
                DB::commit();
                sendCreatedWaybillMail(array('name' => $request['customer_name'], 'label' => $json_data->labelURL, 'email' => $request['customer_email']));

                # send notification...
                $message = 'ALERT: Dear ' . \Config::get('app.name') . ' Customer your return order initiated successfully against the waybill number:#' . $reverse_obj->way_bill_number;
                $user    = User::where('email', $request['customer_email'])->first();
                if ($user) {
                    # code...
                    $this->notification->sendNotification($user->id, $message);
                }

                return response()->json(['status' => true, 'message' => 'Return request saved successfully', 'waybillNumber' => $request['way_bill_number']], 201);
            }
            DB::rollback();
            return response()->json(['status' => true, 'message' => $json_data->message, 'status' => 200], 200);

        } catch (Exception $e) {

        }
    }

    public static function generateClientRefWayBillRequest($request_arr){

        $arr['packageJsonString'] = array_map(function ($e) {
            unset($e['title']);
            return $e;
        }, $request_arr['packageDetails']['packageJsonString']);
        $array = array(
            "waybillRequestData" => array(
                "FromOU"                    => "CHICAGO",
                "bookingOu"                 => "CHICAGO",
                "WaybillNumber"             => $request_arr['way_bill_number'] ?? "",
                "DeliveryDate"              => $request_arr['delivery_date'] ?? "",
                "CustomerCode"              => $request_arr['customer_code'],
                "CustomerName"              => $request_arr['customer_name'],
                "CustomerAddress"           => $request_arr['customer_address'],
                "CustomerCity"              => $request_arr['customer_city'],
                "CustomerCountry"           => $request_arr['customer_country_code'],
                "CustomerPhone"             => $request_arr['customer_phone'],
                "CustomerState"             => $request_arr['customer_state_code'],
                "CustomerPincode"           => $request_arr['customer_pincode'],
                "ConsigneeCode"             => $request_arr['consignee_code'] ?? "",
                "ConsigneeAddress"          => $request_arr['consignee_address'],
                "ConsigneeCountry"          => $request_arr['consignee_country'],
                "ConsigneeState"            => $request_arr['consignee_state'],
                "ConsigneeCity"             => $request_arr['consignee_city'],
                "ConsigneePincode"          => $request_arr['consignee_pincode'],
                "ConsigneeName"             => $request_arr['consignee_name'],
                "ConsigneePhone"            => $request_arr['consignee_phone'],
                "ClientCode"                => $request_arr['client_code'],
                "NumberOfPackages"          => $request_arr['number_of_packages'],
                "ActualWeight"              => $request_arr['actual_weight'],
                "ChargedWeight"             => $request_arr['charged_weight'],
                "ReferenceNumber"           => $request_arr['way_bill_number'] ?? "REVERSEGEAR",
                "InvoiceNumber"             => $request_arr['invoice_number'] ?? "",
                "ServiceCode"               => "03",
                "WeightUnitType"            => $request_arr['weight_unit_type'],
                "Description"               => $request_arr['description'] ?? "",
                "COD"                       => $request_arr['amount'] ?? "",
                "PaymentMode"               => $request_arr['payment_mode'],
                "CODPaymentMode"            => $request_arr['cash_on_pickup'] ?? "",
                "CreateWaybillWithoutStock" => "True",
                "packageDetails"            => $arr,
            ),
        );
        return $array;
    }

    /*
    * client side auto update curated waywill
    * code by sanjay
    **/
    public static function autoUpdateCuratedWaywill($request){
        try {
            if (isset($request['way_bill_id']) && !empty($request['way_bill_id'])) {
                $reverse_obj                        = ReverseLogisticWaybill::find($request['way_bill_id']);
                $reverse_obj->warehouse_id          = $request['warehouse_id'];
                $reverse_obj->shipping_policy_id    = $request['shipment_id'];
                $reverse_obj->payment_mode          = $request['payment_mode'];
                $reverse_obj->cod_payment_mode      = $request['cash_on_pickup'];
                $reverse_obj->amount                = $request['amount'];  
                $reverse_obj->save();

                $way_bill_id = $request['way_bill_id'];
                # Meta Arrray...
                $meta_array = Arr::only($request, ['service_code', 'cash_on_pickup', 'unit_type', 'actual_weight', 'charged_weight', 'remark', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);
            } else {
                $obj = new ReverseLogisticWaybill;
                if ($request['warehouse_id'] == 'discrepency') {
                    $request['warehouse_id'] = null;
                }

                $way_bill_id = $obj->store($request);
                # Meta Arrray...
                $meta_array  = Arr::only($request, ['customer_name', 'customer_email', 'customer_address', 'customer_country', 'customer_state', 'customer_city', 'customer_pincode', 'customer_phone', 'service_code', 'cash_on_pickup', 'number_of_packages', 'unit_type', 'actual_weight', 'charged_weight', 'remark', 'consignee_code', 'consignee_name', 'consignee_phone', 'consignee_address', 'consignee_country', 'consignee_country_name', 'consignee_state', 'consignee_city', 'consignee_pincode', 'carrier_name', 'shipment_name', 'rate', 'client_code', 'customer_code', 'currency', 'product_images']);
                $reverse_obj = ReverseLogisticWaybill::find($way_bill_id);
            }

            # package count data...
            if (isset($request['package_count'])) {
                $l_image = [];              
                if(isset($request['item_images'])){
                    $images  = $request['item_images'];
                    if (!empty($images)) {
                        # code...
                        foreach ($images as $k => $image) {
                            $p_image = [];
                            foreach ($image as $key => $value) {
                                $imageName = time() . '-' . rand(0,99999) . '.' . $value->getClientOriginalExtension();
                                $value->move($request['upload_path'], $imageName);
                                array_push($p_image, $request['upload_path'] . '' . $imageName);
                            }
                            $l_image[$k] = $p_image;
                        }
                    }                   
                }

                $request['upload_images'] = $l_image;
                $packge = new PackageDetail;
                $packge->createPackageByWayBillId($request, $way_bill_id);
            }

            # set meta value..
            setCustomMeta($reverse_obj, $meta_array);

            if (!isset($request['warehouse_type'])) {
                $way_bill_request = GetFunction::generateCuratedWayBillRequest($request);
                // dd(json_encode($way_bill_request));
                $meta_array['create_waywill_request'] = json_encode($way_bill_request);
                setCustomMeta($reverse_obj, $meta_array);

                if (!$reverse_obj->hasMeta('_label_url') || ($reverse_obj->hasMeta('_label_url') && $reverse_obj->meta->_label_url == '')) {
                    # Call Logix Grid Api...                    
                    $create_res = GetFunction::createWaywillResponse($way_bill_request);
                    if ($create_res->messageType == 'Success') {
                        $meta_array['label_message']             = $create_res->message;
                        $meta_array['label_message_type']        = $create_res->messageType;
                        $meta_array['label_message_status']      = $create_res->status;
                        $meta_array['label_package_sticker_url'] = $create_res->packageStickerURL;
                        $meta_array['label_url']                 = $create_res->labelURL;
                        $meta_array['waybillNumber']             = $create_res->waybillNumber;
                        $meta_array['create_waywill_data']       = json_encode($create_res);
                        setCustomMeta($reverse_obj, $meta_array);                        
                    } else {
                        if($create_res->message == '_waybill_is_already_saved'){
                            $waywill = $reverse_obj->id;
                            $meta_array['waybillNumber'] = $reverse_obj->id;
                            setCustomMeta($reverse_obj, $meta_array);
                            goto gnr;
                        }
                        
                        return response()->json(['status' => false, 'message' => $create_res->message, 'status' => 200], 200);
                    }

                    $waywill = $create_res->waybillNumber;
                } else {
                    $waywill = $reverse_obj->meta->_waybillNumber;
                }

                gnr:
                # generate waywill ...                
                $g_arr    = [
                    'waybillNumber'  => $waywill,
                    'carrierCode'    => 'UPS',
                    'aggregator'     => '',
                    'carrierProduct' => '03',
                    // 'carrierProduct' => 'ECOMPACKAGE',
                ];

                $meta_array['generate_waywill_request'] = json_encode($g_arr);
                setCustomMeta($reverse_obj, $meta_array);

                $generate_res = GetFunction::generateWaywillResponse($g_arr);
                if ($generate_res->messageType == 'Error') {
                    return response()->json(['status' => false, 'message' => $generate_res->message, 'status' => 400], 400);
                }

                $meta_array['generate_waywill_status'] = json_encode($generate_res);
                setCustomMeta($reverse_obj, $meta_array);

                # rg refrence number...
                $sq_rg_no = $way_bill_request['waybillRequestData']['WaybillNumber'];
                $reverse_obj->status                = 'Success';
                $reverse_obj->tracking_id           = $generate_res->carrierWaybill;
                $reverse_obj->rg_reference_number   = $sq_rg_no;
                $reverse_obj->save();
                // ReverseLogisticWaybill::where(['id' => $reverse_obj->id])->update(['status' => 'Success', 'tracking_id' => $generate_res->carrierWaybill]);

                # send mail to customer...
                $labelDetailList = reset($generate_res->labelDetailList);

                $get_view_data['subject']    =   'Curated Return Confirmation :-'.$reverse_obj->way_bill_number;
                $get_view_data['view']       =   'mails.curated-order';
                $get_view_data['user']       =   [
                    'name' =>  $request['customer_name'],
                    'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                    'url' => $labelDetailList->artifactUrl,
                    'order_no' => $reverse_obj->way_bill_number,
                    'track_id' => $generate_res->carrierWaybill,
                    'return_date' => date('d/m/Y', strtotime($reverse_obj->created_at)),
                    'return_service' => $request['carrier_name'],
                    'return_cost' => 0,
                ];
                
                try {
                    $mail = Mail::to($request['customer_email'])->send(new MainTemplate( $get_view_data ));
                                        
                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);   
                } catch (\Swift_TransportException $e) {
                    return response()->json(['status' => true, 'message' => 'Label generated successfully.', 'status' => 201], 201);      
                }
            } else {
                $meta_array['order_type'] = 'discrepency';
                setCustomMeta($reverse_obj, $meta_array);
                return response()->json(['status' => true, 'message' => 'Action Completed.', 'status' => 201], 201);
            }
        } catch (Exception $e) {
            return (new \Illuminate\Http\Response)->setStatusCode(400, $e->getMessage());
        }        
    }

    /*
    * Curated Generate waybill request response
    * $request_arr is array
    **/
    public static function generateCuratedWayBillRequest($request_arr){
        $package_json_string_array = array();
        if (isset($request_arr['package_count']) && is_array($request_arr['package_count']) && count($request_arr['package_count']) > 0) {
            foreach ($request_arr['package_count'] as $key => $value) {
                $bar_code       = $request_arr['bar_code'][$key] ?? "";
                $length         = $request_arr['length'][$key] ?? '1';
                $width          = $request_arr['width'][$key] ?? '1';
                $height         = $request_arr['height'][$key] ?? '1';
                $weight         = $request_arr['weight'][$key] ?? '1';
                $charged_weight = $request_arr['charged__weight'][$key] ?? '1';
                $data           = array(
                    'barCode'                 => $bar_code,
                    'packageCount'            => $value,
                    'length'                  => '10',
                    'width'                   => '8',
                    'height'                  => '3',
                    'weight'                  => $weight,
                    'chargedWeight'           => $weight,
                    'selectedPackageTypeCode' => 'DOCUMENT',
                );
                array_push($package_json_string_array, $data);
            }
        }

        $phone = $request_arr['customer_phone'] ?? $order->meta->_customer_phone;
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $remark = '';
        if (isset($request_arr['remark'])) {
            # code...
            $remark = (strlen($request_arr['remark']) > 45) ? substr($request_arr['remark'],0,45).'.' : $request_arr['remark'];
        }

        $sq_rg_no = 'CUR-'.$order->way_bill_number.'-'.$order->id.'-287-'.$request_arr['customer_country'];

        $array = array(
            "waybillRequestData" => array(
                "FromOU"                    => Config('constants.warehouse'),
                "bookingOu"                 => Config('constants.warehouse'),
                "WaybillNumber"             => $sq_rg_no,
                "DeliveryDate"              => $request_arr['delivery_date'] ?? "",
                "CustomerCode"              => $request_arr['customer_code'],
                "CustomerName"              => $request_arr['customer_name'],
                "CustomerAddress"           => $request_arr['customer_address'],
                "CustomerCity"              => $request_arr['customer_city'],
                "CustomerCountry"           => $request_arr['customer_country'],
                "CustomerPhone"             => $phone,
                "CustomerState"             => str_replace(' ', '', $request_arr['customer_state']),
                "CustomerPincode"           => $request_arr['customer_pincode'],
                "ConsigneeCode"             => $request_arr['consignee_code'] ?? "",
                "ConsigneeAddress"          => $request_arr['consignee_address'],
                "ConsigneeCountry"          => $request_arr['consignee_country'],
                "ConsigneeState"            => $request_arr['consignee_state'],
                "ConsigneeCity"             => $request_arr['consignee_city'],
                "ConsigneePincode"          => $request_arr['consignee_pincode'],
                "ConsigneeName"             => $request_arr['consignee_name'],
                "ConsigneePhone"            => $request_arr['consignee_phone'],
                "ClientCode"                => $request_arr['client_code'],
                "NumberOfPackages"          => $request_arr['number_of_packages'],
                "ActualWeight"              => $request_arr['actual_weight'],
                "ChargedWeight"             => $request_arr['charged_weight'],
                "ReferenceNumber"           => $request_arr['way_bill_number'] ?? "REVERSEGEAR",
                "InvoiceNumber"             => $request_arr['way_bill_number'] ?? "",
                "ServiceCode"               => "03",
                "WeightUnitType"            => $request_arr['unit_type'],
                "Description"               => $remark ?? "",
                "COD"                       => "",
                "PaymentMode"               => $request_arr['payment_mode'],
                "CODPaymentMode"            => "",
                "CreateWaybillWithoutStock" => "True",
                "stockIn" => true,
                "packageDetails"            => array(
                    'packageJsonString' => $package_json_string_array,
                ),
            ),
        );

        return $array;
    }

    public static function post_extra($post_id, $key_name){
      $data = '';
      $get_post_extra = PostExtra::where(['post_id' => $post_id, 'key_name' => $key_name])->first();
      if(!empty($get_post_extra)){
        $data = $get_post_extra->key_value;
      }
      
      return $data;
    }

    public static function customer_order_billing_shipping_info($order_id){
      $user_address = array();
      
      if(isset($order_id) &&  $order_id > 0){
        $get_order_user = PostExtra::where(['post_id' => $order_id, 'key_name' => '_customer_user'])->first();
        
        if(!empty($get_order_user)){
          $order_user = unserialize($get_order_user->key_value);
            $get_order_post_meta = PostExtra::where('post_id', $order_id)->get();
            
            if(!empty($get_order_post_meta) && $get_order_post_meta->count() > 0){
              foreach($get_order_post_meta as $rows){
                if($rows->key_name == '_billing_title'){
                  $user_address['_billing_title'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_first_name'){
                  $user_address['_billing_first_name'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_last_name'){
                  $user_address['_billing_last_name'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_company'){
                  $user_address['_billing_company'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_email'){
                  $user_address['_billing_email'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_phone'){
                  $user_address['_billing_phone'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_fax'){
                  $user_address['_billing_fax'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_country'){
                  $user_address['_billing_country'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_address_1'){
                  $user_address['_billing_address_1'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_address_2'){
                  $user_address['_billing_address_2'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_city'){
                  $user_address['_billing_city'] = $rows->key_value;
                }
                elseif($rows->key_name == '_billing_postcode'){
                  $user_address['_billing_postcode'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_title'){
                  $user_address['_shipping_title'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_title'){
                  $user_address['_shipping_title'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_first_name'){
                  $user_address['_shipping_first_name'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_last_name'){
                  $user_address['_shipping_last_name'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_company'){
                  $user_address['_shipping_company'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_email'){
                  $user_address['_shipping_email'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_phone'){
                  $user_address['_shipping_phone'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_fax'){
                  $user_address['_shipping_fax'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_country'){
                  $user_address['_shipping_country'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_address_1'){
                  $user_address['_shipping_address_1'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_address_2'){
                  $user_address['_shipping_address_2'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_city'){
                  $user_address['_shipping_city'] = $rows->key_value;
                }
                elseif($rows->key_name == '_shipping_postcode'){
                  $user_address['_shipping_postcode'] = $rows->key_value;
                }
              }
            }
        }
      }
      
      return $user_address;
    }
}
