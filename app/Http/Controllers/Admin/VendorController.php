<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

use App\User;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Warehouse;
use App\Models\Carrier;
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
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;

use Validator;
use PHPExcel_Shared_Date;
use Session;

use Excel;
use App\Exports\ParcelOrdersExport;
use App\Exports\ItemOrdersExport;
use Zip;
use Milon\Barcode\DNS1D;

use GuzzleHttp\Client;

class VendorController extends Controller
{
    public $perPage = 250;
    protected $guard = 'admin';
    protected $OrderController;

    public function __construct(OrderController $OrderController) {
        $this->middleware('auth:admin')->except(['redirectOrderInvoice']);
        $this->OrderController = $OrderController;
        $this->upload_path = \Config::get('constants.upload_path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777, true, true );
    }


    /**
     * code by sanjay
     * get orders from the ebay account
     */
    public function getEbayOrderList(Request $request, $status){
        $get_order = (new Post)->newQuery();
        if($status == 'new'){
            $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','_ebay_order_status'],['pes.key_value', '=' , 'Pending']]);
        } else{
            if($request->has('order_status') && $request->filled('order_status')){
                $term = $request->order_status;
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','_ebay_order_status'],['pes.key_value', '=' , $term]]);
            }else{
                $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pe.key_name','_sales_order_status'],['pe.key_value', '!=' , null]])->where([['pes.key_name','_ebay_order_status'],['pes.key_value', '!=' , 'Pending']]);
            }            
        }        

        if($request->has('order_id') && $request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->has('ebay_id') && $request->filled('ebay_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','order_number'],['p2.key_value', '=' , $request->ebay_id]]);
        }

        if($request->has('user_name') && $request->filled('user_name')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','ship_to_name'],['p3.key_value', 'like' , '%' .$request->user_name. '%']]);
        }

        if($request->has('user_id') && $request->filled('user_id')){
            $get_order->join('post_extras AS p5', 'posts.id', '=', 'p5.post_id')->where([['p5.key_name','buyer_username'],['p5.key_value', 'like' , '%' .$request->user_id. '%']]);
        }

        if($request->has('user_mail') && $request->filled('user_mail')){
            $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','ship_to_email'],['p6.key_value', 'like' , '%' .$request->user_mail. '%']]);
        }

        if($request->has('return_id') && $request->filled('return_id')){
            $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','_ebay_return_order_id'],['p7.key_value', 'like' , '%' .$request->return_id. '%']]);
        }

        if($request->filled('from_date')){
            $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
        }

        if($request->filled('to_date')){
            $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
        }

        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_status') as _ebay_order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_sales_order_status') as _sales_order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date') as sale_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_return_order_id') as _ebay_return_order_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name') as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username') as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email') as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1') as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_2') as ship_to_address_2"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city') as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state') as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country') as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sold_for') as sold_for"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_currency') as _ebay_order_currency"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'payment_method') as payment_method"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_order_tracking_id') as _order_tracking_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_generate_waybill_status') as _generate_waybill_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_refund_date') as _ebay_order_refund_date"),
            'posts.*'
        );

        $orders = $get_order->where(['posts.parent_id' => 0, 'posts.post_type' => 'return_ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->paginate($this->perPage);

        // dd($get_order->toSql(), $get_order->getBindings());

        return view('pages.admin.ebay.ebay-orders-list', compact('orders', 'status'));
    }


    /**
     * code by sanjay
     * get cancle orders from the ebay account
     */
    public function getEbayCancleOrderList(Request $request, $status){
        $key = null;
        if($status != 'new'){
            $key = 'Cancelled';
        }

        $get_order = (new Post)->newQuery();
        $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pe.key_name','_ebay_cancel_order_status'],['pe.key_value', '=' , $key]])->where([['pes.key_name','_ebay_order_status'],['pes.key_value', '=' , $key]]);

        if($request->has('order_id') && $request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->has('ebay_id') && $request->filled('ebay_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','order_number'],['p2.key_value', '=' , $request->ebay_id]]);
        }

        if($request->has('user_name') && $request->filled('user_name')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','ship_to_name'],['p3.key_value', 'like' , '%' .$request->user_name. '%']]);
        }

        if($request->has('user_name') && $request->filled('user_name')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','ship_to_name'],['p4.key_value', 'like' , '%' .$request->user_name. '%']]);
        }

        if($request->has('user_id') && $request->filled('user_id')){
            $get_order->join('post_extras AS p5', 'posts.id', '=', 'p5.post_id')->where([['p5.key_name','buyer_username'],['p5.key_value', 'like' , '%' .$request->user_id. '%']]);
        }

        if($request->has('user_mail') && $request->filled('user_mail')){
            $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','ship_to_email'],['p6.key_value', 'like' , '%' .$request->user_mail. '%']]);
        }
        
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_status') as _ebay_order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_sales_order_status') as _sales_order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date') as sale_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_return_order_id') as _ebay_return_order_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name') as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username') as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email') as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1') as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_2') as ship_to_address_2"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city') as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state') as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country') as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sold_for') as sold_for"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_currency') as _ebay_order_currency"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'payment_method') as payment_method"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_order_tracking_id') as _order_tracking_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_generate_waybill_status') as _generate_waybill_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = '_ebay_order_refund_date') as _ebay_order_refund_date"),
            'posts.*'
        );

        $orders = $get_order->where(['posts.parent_id' => 0, 'posts.post_type' => 'return_ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->paginate($this->perPage);

        return view('pages.admin.ebay.ebay-cancle-orders-list', compact('orders', 'status'));
    }

    /**
     * Code By Sanjay
     * Get the orders from the ebay based on configration
     *
     */
    public function manageAllEbayOrders($get_order){
        $order_data = array();

        if (count($get_order) > 0) {
            foreach ($get_order as $order) {
                $order_postmeta           = array();
                // $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order['id']])->get();
                $get_postmeta_by_order_id = $order['post_extras'];

                if (count($get_postmeta_by_order_id) > 0) {
                    $date_format                   = new Carbon($order['created_at']);
                    $order_postmeta['_post_id']    = $order['id'];
                    $order_postmeta['_order_date'] = $date_format->toDayDateTimeString();

                    foreach ($get_postmeta_by_order_id as $postmeta_row) {
                        $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
                    }
                }

                array_push($order_data, $order_postmeta);
            }
        }

        return $order_data;
    }


    /**
     * Ebay Order details content
     *
     * @param order_id
     * @return response
     */
    public function ebayOrderDetailsPageContent($params) {
        $order_id = 0;
        $get_post = Post::where(['id' => $params, 'post_type' => 'return_ebay_order'])->first();

        if (!empty($get_post) && $get_post->parent_id > 0) {
            $order_id = $get_post->parent_id;
        } else {
            $order_id = $params;
        }

        $get_post_by_order_id     = Post::where(['id' => $params])->first();
        $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();

        if ($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id->count() > 0) {
            $order_date_format               = new Carbon($get_post_by_order_id->created_at);
            $order_data_by_id['_order_id']   = $get_post_by_order_id->id;
            $order_data_by_id['_order_date'] = $order_date_format->toDayDateTimeString();

            foreach ($get_postmeta_by_order_id as $postmeta_row_data) {
                $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
            }
        }

        $order_data_by_id = $order_data_by_id;
        $carrier = Carrier::get();
        $warehouse = Warehouse::get();

        return view('pages.admin.ebay.ebay-order-details', compact('order_data_by_id', 'carrier', 'warehouse'));
    }

    /**
     * New/View Ebay Order details content
     *
     * @param order_id
     * @return response
     */
    public function ebayNewOrderDetailContent($params) {
        $order_id = 0;
        $get_post = Post::where(['id' => $params, 'post_type' => 'return_ebay_order'])->first();

        if (!empty($get_post) && $get_post->parent_id > 0) {
            $order_id = $get_post->parent_id;
        } else {
            $order_id = $params;
        }

        $get_post_by_order_id     = Post::where(['id' => $params])->first();
        $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();

        if ($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id->count() > 0) {
            $order_date_format               = new Carbon($get_post_by_order_id->created_at);
            $order_data_by_id['_order_id']   = $get_post_by_order_id->id;
            $order_data_by_id['_order_date'] = $order_date_format->toDayDateTimeString();

            foreach ($get_postmeta_by_order_id as $postmeta_row_data) {
                $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
            }
        }

        $order_data_by_id = $order_data_by_id;
        $carrier = Carrier::get();
        $warehouse = Warehouse::get();

        return view('pages.admin.ebay.ebay-new-order-detail', compact('order_data_by_id', 'carrier', 'warehouse'));
    }


    /**
    * Cancel ebay order
    * @param id
    **/
    public function cancelledEbayOrder($id){
        $data = ['key_value' => 'Cancelled'];
        PostExtra::where(['post_id' => $id, 'key_name' => '_ebay_order_status'])->update($data);
        PostExtra::where(['post_id' => $id, 'key_name' => '_ebay_cancel_order_status'])->update($data);
        PostExtra::where(['post_id' => $id, 'key_name' => '_sales_order_status'])->update(['key_value' => '{"status":"Cancelled"}']);
        return redirect()->back()->with('success', 'Action Completed');
    }


    /**
     *
     * Redirect to order invoice
     *
     * @param null
     * @return void
     */
    public function redirectOrderInvoice($params){
        $order_id = 0;
        $get_post = Post::where(['id' => $params])->first();
        // $get_post = Post::where(['id' => $params, 'post_type' => 'shop_order'])->first();

        if (!empty($get_post) && $get_post->parent_id > 0 && $get_post->post_type == 'shop_order') {
            $order_id = $get_post->parent_id;
        } else {
            $order_id = $params;
        }

        $get_post_by_order_id     = Post::where(['id' => $params])->first();
        $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();

        if ($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id->count() > 0) {
            $order_date_format = new Carbon($get_post_by_order_id->created_at);

            $order_data_by_id                = get_customer_order_billing_shipping_info($order_id);
            $order_data_by_id['_order_id']   = $get_post_by_order_id->id;
            $order_data_by_id['_order_date'] = $order_date_format->toDayDateTimeString();

            foreach ($get_postmeta_by_order_id as $postmeta_row_data) {
                if ($postmeta_row_data->key_name === '_order_shipping_method') {
                    $order_data_by_id[$postmeta_row_data->key_name] = $this->classCommonFunction->get_shipping_label($postmeta_row_data->key_value);
                } elseif ($postmeta_row_data->key_name == '_customer_user') {
                    $user_data = unserialize($postmeta_row_data->key_value);
                    if ($user_data['user_mode'] == 'guest') {
                        $order_data_by_id['_member'] = array('name' => 'Guest', 'url' => '');
                    } elseif ($user_data['user_mode'] == 'login') {
                        $user_details_by_id          = get_user_details($user_data['user_id']);
                        $order_data_by_id['_member'] = array('name' => $user_details_by_id['user_display_name'] ?? '', 'url' => $user_details_by_id['user_photo_url'] ?? '');
                    }
                } else {
                    $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
                }
            }
        }

        return view('pages.admin.invoice.package-invoice', compact('order_data_by_id'));
    }


    /**
     * Generate the label here ebay order
     */
    public function generateLabel(Request $request){
        try {
            $data = $request->all();
            $postdata = Post::rightJoin('post_extras','post_extras.post_id','=','posts.id')
                        ->select('post_extras.*')
                        ->where('posts.id',$request->post_id)
                        ->get();
            $order_postmeta = array();
            $orderdata = array();
            foreach($postdata as $postmeta_row) {
                $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
            }

            $order_postmeta['post'] = Post::find($request->post_id);
            $order_postmeta['id'] = $request->post_id;
            $war_obj = getwharehouse($order_postmeta['ship_to_country']);

            if($war_obj){
                $country = Country::where('id',$war_obj->country_id)->first();
                $st = State::where('shortname',$war_obj->state)->first();
                $request->request->add(['warehouse_id' => $war_obj->id]);
                $request->request->add(['consignee_name' => $war_obj->name]);
                $request->request->add(['ConsigneePhone' => $war_obj->phone]);
                $request->request->add(['ConsigneeAddress' => $war_obj->address]);
                $request->request->add(['ConsigneeCountry' => $country->sortname]);
                $request->request->add(['ConsigneeState' => $st->shortname ?? $war_obj->state->shortname]);
                $request->request->add(['ConsigneeCity' => $war_obj->city]);
                $request->request->add(['ConsigneePincode' => $war_obj->zip_code]);
                $request->request->add(['ConsigneeEmail' => $war_obj->email]);
                $request->request->add(['FromOU' => $war_obj->FromOU]);
            } else{
                return response()->json(['message' => 'No Warehouse found.', 'status' => 200], 200);
            }

            if($request->weight <= 1){
                $data['carrier'] = 'USPS';
                $data['length'] = '10';
                $data['width'] = '8';
                $data['height'] = '1';
            }

            if($request->weight > 1){
                $data['carrier'] = 'UPS';
            }
            
            $carrieravailable =  Carrier::where('countrycode', "Like", "%" . $order_postmeta['ship_to_country'] . "%")->first();
            if ($request->has('carrier') && $request->filled('carrier')) {
                $carrieravailable = Carrier::where('countrycode', "Like", "%" . $order_postmeta['ship_to_country'] . "%")->where('name', $data['carrier'])->first();
            }

            if(!$carrieravailable) {
                return response()->json(['message' => 'No Carrier found.', 'status' => 200], 200);
            }

            $request->request->add(['servicecode' => $carrieravailable->code]);
            $request->request->add(['carrier_name' => $carrieravailable->name]);
            $request->request->add(['unit_type' => $carrieravailable->unit_type]);

            #.. validation here....
            $flag = false;
            $itm = [];

            # get the item based on the ebay order id...
            $order = (new Post)->newQuery();
            $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $order_postmeta['order_number']]]);
            $order->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id")
            );
            $ebay_order = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->toArray();
            
            foreach ($ebay_order as $eb) {
                if ($eb['scan_i_package_id'] != $order_postmeta['scan_i_package_id']) {
                    $flag = true;
                    array_push($itm, $eb['scan_i_package_id']);
                }
            }

            if ($flag) {
                return response()->json(['message' => "PLEASE GO TO DISPATCH SCREEN AND COMBINE THE FOLLOWING ORDERS: ".implode(',', $itm)." TO THIIS ORDER AND COMBINE THEM", 'status' => 200], 200);
            }

            $no_of_pakg = 0 ;
            $randomnumber =  rand ( 100 , 999 );
            $sq_rg_no = 'WMS-'.$request->post_id.'-'.date('mdY').'-'.$randomnumber;
            $waybill_array = $this->createSopifyOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable);
            // dd($waybill_array);
                    
            $js_data = json_encode($waybill_array);
            set_post_key_value($request->post_id, 'create_waywill_request', $js_data);

            # create waybill api here...
            if(!isset($order_postmeta['create_waybill_status'])){
                $create_response = $this->createShopifyOrderWaywillResponse($js_data);
                $create_data = json_decode($create_response);
                
                if (empty($create_data)) {
                    $rtn_msg = "Create waywill Api:- no response";
                    return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                } elseif (isset($create_data->messageType) && $create_data->messageType == 'Error') {            
                    $rtn_msg = 'Create waywill Api:- '.$create_data->message;
                    set_post_key_value($request->post_id, 'create_waybill_response', $create_response);
                    return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                } else{
                    set_post_key_value($request->post_id, 'create_waybill_status', $create_response);
                    set_post_key_value($request->post_id, 'waybillNumber', $create_data->waybillNumber);
                    set_post_key_value($request->post_id, 'ebay_sequence_order_number', $sq_rg_no);
                    set_post_key_value($request->post_id, 'label_url', $create_data->labelURL);
                }
            } else {
                $create_data = json_decode($order_postmeta['create_waybill_status']);
            }

            # generate the label here...
            $g_arr = [
                'waybillNumber' => $create_data->waybillNumber,
                'carrierCode'    => $carrieravailable->code,
                'aggregator'     => '',
                'labelFormat'     => 'PNG',
                'carrierProduct' => ($carrieravailable->product[0]->code) ? ($carrieravailable->product[0]->code): "",
            ];

            /*if (isset($order_postmeta['order_number'])) {
                $g_arr['labelType'] = '4x6';
            }*/

            set_post_key_value($request->post_id, 'generate_waybill_request', json_encode($g_arr));
            $gr_response = $this->generateShopifyOrderWaywillResponse($g_arr);            
            $gr_json = json_decode($gr_response);
            
            if (empty($gr_json)) {                
                $rtn_msg = "Generate waywill Api:- no response";
                return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
            } elseif (isset($gr_json->messageType) && $gr_json->messageType == 'Error') {
                set_post_key_value($request->post_id, 'generate_waybill_response', $gr_response);
                $rtn_msg = 'Generate waywill Api:- '.$gr_json->message;
                return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
            }

            $label = reset($gr_json->labelDetailList);
            set_post_key_value($request->post_id, 'generate_waybill_status', $gr_response);
            set_post_key_value($request->post_id, 'order_reference_number', $waybill_array['waybillRequestData']['WaybillNumber']);
            set_post_key_value($request->post_id, 'tracking_number', $gr_json->carrierWaybill);
            set_post_key_value($request->post_id, 'label_url', $label->artifactUrl);
            set_post_key_value($request->post_id, 'order_status', 'IS-04');
            set_post_key_value($request->post_id, 'scan_dispatch_date', date('Y-m-d'));
            set_post_key_value($request->post_id, 'scan_dispatch_time', date('H:i:s'));
            set_post_key_value($request->post_id, 'scan_dispatch_user', Auth::user()->name);

            set_post_key_value($request->post_id, 'length', $data['length'] ?? '10');
            set_post_key_value($request->post_id, 'width', $data['width'] ?? '8');
            set_post_key_value($request->post_id, 'height', $data['height'] ?? '1');
            set_post_key_value($request->post_id, 'weight', $request->weight ?? '0.5');

            Post::whereId($request->post_id)->update(['location_id' => $request->location_id]);

            # push the shippment detail on ebay...
            // $this->pushTrackingNumber($order_postmeta, $order_postmeta['shipping_carrier_code'], $gr_json->carrierWaybill);

            # send mail to customer...
            /*if (in_array($order_postmeta['client_id'], ['RG00000008']) && in_array($order_postmeta['subclient_id'], ['RG00000037', 'RG00000030'])) {
                $get_view_data['subject']    =   'Virginia Mileage Choice Program - OBD Return Label';
                $get_view_data['view'] = 'mails.ims-order';
            } else {
                if (isset($order_postmeta['order_number'])) {
                    $get_view_data['subject']    =   'LinkShipcycle :-'.$order_postmeta['order_number'] ?? $request->post_id;
                } else {
                    $get_view_data['subject']    =   'LinkShipcycle :-'.$order_postmeta['client_ref'] ?? $request->post_id;
                }
                $get_view_data['view'] = 'mails.jaded-order';
            }*/
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

           /* $get_view_data['user']       =   [
                'name' =>  $order_postmeta['customer_name'],
                'message' => 'Your return label has generated. Please click the View URL button below to view your label.',
                'url' => $pdf_url,
                'order_no' => $order_postmeta['client_ref'] ?? $request->post_id,
                'track_id' => $gr_json->carrierWaybill ?? '',
                'return_date' => date('d/m/Y'),
                'return_service' => 'Postal Service',
                'description' => $order_postmeta['description'] ?? '',
            ];*/

            try{
                /*$m = $order_postmeta['customer_email_id'] ?? 'vibhuti.mca@gmail.com';
                // $m = 'vibhuti.mca@gmail.com';
                if (isset($order_postmeta['client_ref'])) {
                    $mail = Mail::to($m)->send(new MainTemplate( $get_view_data ));
                }*/
                return response()->json(['pdf_url' => $label->artifactUrl, 'message' => 'Label generated successfully.', 'track_id' => $gr_json->carrierWaybill, 'status' => 201], 201);
            }catch(\Swift_TransportException $transportExp){                
                return response()->json(['pdf_url' => $label->artifactUrl, 'message' => 'Mail not send but Label generated successfully.', 'track_id' => $gr_json->carrierWaybill, 'status' => 201], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);   
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
            $sku_url = 'https://api.logixplatform.com/webservice/v2/GetStockProductWise?secureKey=DB7FACCA8A3640648D918B4A4818178A&warehouseCode=ECOMMOSORD&productCode='.str_replace(' ', '', $value->sku);
            $jsonData = json_decode(file_get_contents($sku_url), true);
            if(isset($jsonData['stock'][0]['partNumber'])){
                $products[$i]['product_sku']    = $jsonData['stock'][0]['partNumber'];
            } else {
                $products[$i]['product_sku']    = str_replace(' ', '', $value->sku);
            }
            $products[$i]['price']          = $value->price;
            $products[$i]['quantity']       = $value->quantity;
            $products[$i]['measurmentUnit'] = 'Pieces';
            $i++;
        }

        # shipping address...
        $shipperName     = $order['ship_to_name'];
        $shippingAddress = $order['ship_to_address_1'] . ' ' . $order['ship_to_address_2'];
        $shippingCity    = $order['ship_to_city'];
        $shippingState   = $order['ship_to_state'];
        $shippingCountry = $order['ship_to_country'];
        $shippingPincode = $order['ship_to_zip'];
        $shippingPhone = $order['ship_to_phone'];

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
            'salesOrderNumber'      => date('m').'-'.$order['id'],
            'warehouse'             => 'ECOMMOSORD',
            'SecureKey'             => \Config('constants.secureKey'),
            'products'              => json_encode($products),
            'customerCode'          => 'MOSCHINO123',
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
            'eCommerceSite'         => 'eBay Ecomglobal',
            'carrierCode'           => '',
            'carrierProductCode'    => '',
            'dutyPaidBy'            => 'SENDER',
        ];

        // dd($form_data);
        set_post_key_value($order['id'], 'sales_order_request', json_encode($form_data));
        $res = $client->post(\Config('constants.salesOrderUrl'), [
            'form_params' => $form_data,
        ]);

        return $res;
    }

    public function createOutboundOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable, $sno){
        # create package array...
        $items = json_decode($order_postmeta['items']);
        $package_array = array();
        $no_of_pakg = 0;
        foreach($items as $key => $value){
            $no_of_pakg += $value->quantity;
        }

        $package = array(
            'barCode' => '',
            'packageCount' => 1,
            'length' => $request->length ?? '10',
            'width' => $request->width ?? '8',
            'height' => $request->height ?? '3',
            'weight' => $request->weight ?? '0.5',
            'chargedWeight' => $request->weight ?? '0.5',
            'selectedPackageTypeCode'=>'BOX',
            'itemCount' => $no_of_pakg
        );
        array_push($package_array, $package);

        $phone = $order_postmeta['ship_to_phone'];
        $phone = str_replace( array( '-', '(', ')'), '', $phone);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $ref = 'EBAY-'.$order_postmeta['order_number'];

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
                "ConsigneeName" => $order_postmeta['ship_to_name'],
                "ConsigneePhone" => $phone,
                "ConsigneeAddress" => $order_postmeta['ship_to_address_1'],
                "ConsigneeCountry" => $order_postmeta['ship_to_country'],
                "ConsigneeState" => $order_postmeta['ship_to_state'] ?? $order_postmeta['ship_to_city'],
                "ConsigneeCity" => $order_postmeta['ship_to_city'],
                "ConsigneePincode" => $order_postmeta['ship_to_zip'],
                "ConsigneeEmail" => $order_postmeta['ship_to_email'] ?? 'customer@example.com',
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
                "ReferenceNumber" => $ref,
                "InvoiceNumber" => $sq_rg_no,
                "PaymentMode" => "TBB",
                "ServiceCode" => $carrieravailable->service[0]->code,                
                "WeightUnitType" => $carrieravailable->unit_type,
                "Description" => "Client eBay Ecomglobal Order",
                "COD" => "",
                "Currency" => $order_postmeta['_ebay_order_currency'],
                "salesInvoiceNumber" => $sno,
                "CODPaymentMode" => "",
                "skipCityStateValidation" => true,
                "packageDetails" => array(
                    'packageJsonString' => $package_array
                    )                        
                )
        );

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
            return null;
        }
    }

    public function createSopifyOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable){
        # create package array...
        $package_array = array();
        $no_of_pakg = 0;

        $package = array(
            'barCode' => $order_postmeta['scan_i_package_id'],
            'packageCount' => 1,
            'length' => $request->length ?? '10',
            'width' => $request->width ?? '8',
            'height' => $request->height ?? '3',
            'weight' => $request->weight ?? '0.5',
            'chargedWeight' => $request->weight ?? '0.5',
            'selectedPackageTypeCode'=>'BOX',
            'itemCount' => $order_postmeta['item_quantity']
        );
        array_push($package_array, $package);

        $order_postmeta['ship_to_phone'] = (!empty($order_postmeta['ship_to_phone']) && $order_postmeta['ship_to_phone'] != 'None') ? $order_postmeta['ship_to_phone'] : 9999999999;
        $phone = str_replace( array( '-', '(', ')'), '', $order_postmeta['ship_to_phone']);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $order_postmeta['ship_to_email'] = (!empty($order_postmeta['ship_to_email']) && $order_postmeta['ship_to_email'] != 'None') ? $order_postmeta['ship_to_email'] : 'customer@example.com';
        $address = $order_postmeta['ship_to_address_1'];
        if ($order_postmeta['ship_to_address_2'] != 'None') {
            $address = $address.' '.$order_postmeta['ship_to_address_2'];
        }

        $waybill_array = array(
            "waybillRequestData" => array(
                "consigneeGeoLocation" => "",
                "FromOU" => $request->FromOU,
                "DeliveryDate" => "",
                "WaybillNumber" => $sq_rg_no,
                "CustomerName" => $request->consignee_name,
                "CustomerPhone" => $request->ConsigneePhone,
                "CustomerEmail" => $request->ConsigneeEmail,
                "CustomerAddress" => $request->ConsigneeAddress,
                "CustomerCountry" => $request->ConsigneeCountry,
                "CustomerState" => $request->ConsigneeState,
                "CustomerCity" => $request->ConsigneeCity,
                "CustomerPincode" => $request->ConsigneePincode,
                "CustomerCode" => "00000",
                "ConsigneeCode" => "00000",
                "ConsigneeName" => $order_postmeta['ship_to_name'],
                "ConsigneePhone" => $order_postmeta['ship_to_phone'],
                "ConsigneeEmail" =>  $order_postmeta['ship_to_email'],
                "ConsigneeAddress" => $address,
                "ConsigneeCountry" => $order_postmeta['ship_to_country'],
                "ConsigneeState" => strtoupper($order_postmeta['ship_to_state'] ?? $order_postmeta['ship_to_city']),
                "ConsigneeCity" => $order_postmeta['ship_to_city'],
                "ConsigneePincode" => strtoupper($order_postmeta['ship_to_zip']),
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
                "Currency" => $order_postmeta['ebay_order_currency'] ?? 'USD',
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
     * update the tracking number and carrien in to ebay
     */
    public function pushTrackingNumber($data, $carrier, $tracking){
        // dd([$data, $carrier, $tracking]);

        # curl response...
        $response = $this->OrderController->checkEbayConfigration();
        if(isset($response['type'])){
            return response()->json(['message' => $response['msg'], 'status' => 200], 200);
        }

        $result = json_decode($response['response']);
        $url = $response['api_url']."/sell/fulfillment/v1/order/".$data['order_number']."/shipping_fulfillment";
        # code...
        $dt = date('Y-m-d').'T'.date('H:i:s').'.999Z';
        $json = [
            "lineItems" => [
                [
                    "lineItemId" =>  $data['item_number'],
                    "quantity" =>  $data['item_quantity']
                ]
            ],
            "shippedDate" => $dt,
            "shippingCarrierCode" =>  $carrier,
            "trackingNumber" => $tracking
        ];

        // dd(json_encode($json));
        set_post_key_value($data['id'], 'shipping_request', json_encode($json));
        if (isset($result->access_token)) {
            $res = $this->OrderController->postCurlResponse($url, $result->access_token, json_encode($json));
            set_post_key_value($data['id'], 'shipping_reponse', $res);
        } else {
            set_post_key_value($data['id'], 'shipping_error', 'Token is missing');   
        }
    }
}
