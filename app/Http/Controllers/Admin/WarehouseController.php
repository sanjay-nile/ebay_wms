<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
use App\Exports\ScanExport;
use Zip;
use Milon\Barcode\DNS1D;
use GuzzleHttp\Client;

class WarehouseController extends Controller
{
    public $perPage = 50;
    protected $guard = 'admin';
    protected $OrderController;

    public function __construct(OrderController $OrderController) {
        $this->OrderController = $OrderController;
        $this->middleware('auth:admin')->except(['redirectLocationInvoice','bulkLocationInvoice', 'cronSyncLocationData', 'cronSyncUpdateLocation']);
    }

    /**
     * Add the rack here
     */
    public function addRack($params = null){
        $data = array();
        $data['list']         = Warehouse::all();
        $data['clients']         = User::where('user_type_id', 3)->get();

        $data['location'] = '';
        if (!empty($params)) {
            $get_post = Post::where(['id' => $params])->first();
            $get_postmeta = PostExtra::where(['post_id' => $params])->get();
            if ($get_post->count() > 0 && $get_postmeta->count() > 0) {
                $order_date_format = new Carbon($get_post->created_at);
                $order_data_by_id['post_id']   = $get_post->id;
                $order_data_by_id['client_id']   = $get_post->client_id;
                $order_data_by_id['warehouse_id']   = $get_post->warehouse_id;
                $order_data_by_id['post_content']   = $get_post->post_content;
                $order_data_by_id['post_title']   = $get_post->post_title;
                $order_data_by_id['post_date'] = $order_date_format->toDayDateTimeString();

                foreach ($get_postmeta as $postmeta_row_data) {
                    $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
                }
            }

            $data['location'] = $order_data_by_id;
        }

        return view('pages.admin.warehouse.add-rack', $data);
    }

    /**
     * store rack data
     */
    public function rackStore(Request $request){
        try {
            $data = $request->only(['length', 'width', 'height', 'weight', 'level', 'shelves', 'authorized_by', 'create_system_time', 'location_id', 'measurement']);
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'warehouse_id' => 'required',
                'measurement' => 'required',
                'short_title' => 'required',
                'title' => 'required',
                'length' => 'required',
                'width' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'location_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            if($request->has('post_id') && $request->filled('post_id')){
                $post = Post::where(['id' => $request->post_id])->first();
                $msg = 'Rack has been updated successfully.';
            } else {
                $post = new Post;
                $msg = 'Rack has been created successfully.';
            }

            $user_id  = Auth::user()->id;
            $post->post_author_id = $user_id ?? 1;
            $post->client_id = $request->client_id ?? 1;
            $post->warehouse_id = $request->warehouse_id ?? 1;
            $post->post_content   = $request->short_title;
            $post->post_title     = $request->title;
            $post->post_slug      = Str::slug($request->title, '-');
            $post->parent_id      = 0;
            $post->post_status    = $request->status;
            $post->location_id    = $request->location_id;
            $post->post_type      = 'rack';

            $data['sync_status'] = 'Pending';
            if ($post->save()) {
                foreach ($data as $key => $value) {
                    $ar_val = $value;
                    if (is_array($value)) {
                        $ar_val = json_encode($value);
                    }

                    set_post_key_value($post->id, $key, $ar_val);
                }

                # store the user log...
                $his = new StatusHistory;
                $his->post_id = $post->id;
                $his->addition_info = 'Added Rack detail.';
                $his->type = 'rack';
                $his->status_date = date('Y-m-d');
                $his->status_time = date('H:i:s');
                $his->local_time = $data['create_system_time']; // Fetch local time;
                $his->user = Auth::user()->name;
                $his->save();
            }

            return response()->json(['message' => $msg, 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * edit the rack here
     */
    public function getRackLists(Request $request){
        $get_order = (new Post)->newQuery();

        if(empty($request->all())){
            $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','length'],['pes.key_value', '!=' , null]]);
        }

        if($request->has('location_id') && $request->filled('location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','location_id'],['p2.key_value', '=' , $request->location_id]]);
        }

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->filled('warehouse_name')){
            $get_order->where('posts.warehouse_id', $request->warehouse_name);
        }

        if($request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        $get_order->where(['posts.post_type' => 'rack']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'length') as length"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'width') as width"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'height') as height"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'weight') as weight"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'level') as level"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'shelves') as shelves"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'measurement') as measurement"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'location_id') as org_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sync_status') as sync_status"),
            DB::raw("(select status_date from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_date"),
            DB::raw("(select status_time from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_time"),
            DB::raw("(select user from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as rack_user"),
            'posts.*'
        );

        $orders = $get_order->orderBy('posts.id', 'DESC')->paginate($this->perPage);
        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();

        return view('pages.admin.warehouse.rack-list', compact('orders', 'Warehouse', 'clients'));
    }

    /**
     * All scan out list search here
     */
    public function getAllScanDataList(Request $request){
        $get_order = (new Post)->newQuery();

        if($request->filled('from_date')){
            $end_f = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end_f->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end_t = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end_t->format('Y-m-d'));
        }

        if($request->filled('eb_from_date')){
            $end_ff = Carbon::parse($request->eb_from_date);
            $get_order->whereDate('posts.ebay_date', '>=', $end_ff->format('Y-m-d'));
        }

        if($request->filled('eb_to_date')){
            $end_tt = Carbon::parse($request->eb_to_date);
            $get_order->whereDate('posts.ebay_date', '<=', $end_tt->format('Y-m-d'));
        }

        if($request->has('order_status') && $request->filled('order_status')){
            $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', [$request->order_status]);
        } else {
            $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03', 'IS-04', 'IS-06', 'IS-07', 'IS-05', 'IS-02', 'IS-01']);
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', 'like' , '%'.$request->scan_i_location_id.'%']]);
        }

        if($request->has('scan_out_user') && $request->filled('scan_out_user')){
            $get_order->join('post_extras AS p21', 'posts.id', '=', 'p21.post_id')->where([['p21.key_name','scan_out_user'],['p21.key_value', 'like' , '%'.$request->scan_out_user.'%']]);
        }

        if($request->has('location_name') && $request->filled('location_name')){
            $get_order->join('posts AS pl', 'posts.location_id', '=', 'pl.location_id')->where([['pl.post_type','rack'],['pl.post_title', 'like' , '%'.$request->location_name.'%']]);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $request->order_number]]);
        }

        if($request->has('tracking_number') && $request->filled('tracking_number')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','tracking_number'],['p4.key_value', '=' , $request->tracking_number]]);
        }

        if($request->has('customer_address') && $request->filled('customer_address')){
            $get_order->join('post_extras AS p5', 'posts.id', '=', 'p5.post_id')->where([['p5.key_name','ship_to_address_1'],['p5.key_value', 'like' , '%'.$request->customer_address.'%']]);
        }

        if($request->has('zip_code') && $request->filled('zip_code')){
            $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','ship_to_zip'],['p6.key_value', '=' , $request->zip_code]]);
        }

        if($request->has('so_from_date') && $request->filled('so_from_date')){
            $get_order->join('post_extras AS sof', 'posts.id', '=', 'sof.post_id');
            $get_order->where([['sof.key_name','scan_out_date'],['sof.key_value', '>=' , $request->so_from_date]]);
        }

        if($request->has('so_to_date') && $request->filled('so_to_date')){
            $get_order->join('post_extras AS sot', 'posts.id', '=', 'sot.post_id');
            $get_order->where([['sot.key_name','scan_out_date'],['sot.key_value', '<=' , $request->so_to_date]]);
        }

        if($request->has('dis_from_date') && $request->filled('dis_from_date')){
            $get_order->join('post_extras AS disf', 'posts.id', '=', 'disf.post_id');
            $get_order->where([['disf.key_name','scan_dispatch_date'],['disf.key_value', '>=' , $request->dis_from_date]]);
        }

        if($request->has('dis_to_date') && $request->filled('dis_to_date')){
            $get_order->join('post_extras AS dist', 'posts.id', '=', 'dist.post_id');
            $get_order->where([['dist.key_name','scan_dispatch_date'],['dist.key_value', '<=' , $request->dis_to_date]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status' ORDER BY id DESC LIMIT 1) as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1' ORDER BY id DESC LIMIT 1) as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city' ORDER BY id DESC LIMIT 1) as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state' ORDER BY id DESC LIMIT 1) as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip' ORDER BY id DESC LIMIT 1) as ship_to_zip"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by' ORDER BY id DESC LIMIT 1) as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id' ORDER BY id DESC LIMIT 1) as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id' ORDER BY id DESC LIMIT 1) as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date' ORDER BY id DESC LIMIT 1) as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user' ORDER BY id DESC LIMIT 1) as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time' ORDER BY id DESC LIMIT 1) as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images' ORDER BY id DESC LIMIT 1) as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'webcam_image' ORDER BY id DESC LIMIT 1) as webcam_image"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date' ORDER BY id DESC LIMIT 1) as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time' ORDER BY id DESC LIMIT 1) as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number' ORDER BY id DESC LIMIT 1) as tracking_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'weight' ORDER BY id DESC LIMIT 1) as weight"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'length' ORDER BY id DESC LIMIT 1) as length"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'height' ORDER BY id DESC LIMIT 1) as height"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'width' ORDER BY id DESC LIMIT 1) as width"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancelRequests' ORDER BY id DESC LIMIT 1) as cancelRequests"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancel_reason' ORDER BY id DESC LIMIT 1) as cancel_reason"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.id',
            'posts.created_at',
            'posts.post_author_id',
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        if ($request->query()) {
            if ($request->has('export_to') && $request->filled('export_to')) {
                $all_orders = $get_order->orderBy('posts.created_at', $sort)->get();
                return $this->downloadExcelSheet($all_orders, $request);
            } else {
                $orders = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);
            }
        } else {
            $orders = [];
        }

        return view('pages.admin.warehouse.all-scan-out-list', compact('orders'));
    }

    /**
     * scan in list search here
     */
    public function scanInList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username') as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name') as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email') as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country') as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'webcam_image') as webcam_image"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $orders = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);
        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();
        $operators = User::where('user_type_id', 7)->get();

        return view('pages.admin.warehouse.scan-in-list', compact('orders', 'operators'));
    }


    /**
     * single scan out list search here
     */
    public function scanOutList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.ebay_date', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.ebay_date', '<=', $end->format('Y-m-d'));
        }

        /*if($request->has('from_date') && $request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->leftJoin('post_extras AS sd', 'posts.id', '=', 'sd.post_id');
            $get_order->where([['sd.key_name','sale_date'],['sd.key_value', '>=' , $end->format('Y-m-d')]]);
        }

        if($request->has('to_date') && $request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->leftJoin('post_extras AS st', 'posts.id', '=', 'st.post_id');
            $get_order->where([['st.key_name','sale_date'],['st.key_value', '<=' , $end->format('Y-m-d')]]);
        }*/

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', 'like' , '%'.$request->scan_i_location_id.'%']]);
        }

        if($request->has('location_name') && $request->filled('location_name')){
            $get_order->join('posts AS pl', 'posts.location_id', '=', 'pl.location_id')->where([['pl.post_type','rack'],['pl.post_title', 'like' , '%'.$request->location_name.'%']]);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $request->order_number]]);
        }

        if($request->has('tracking_number') && $request->filled('tracking_number')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','tracking_number'],['p4.key_value', '=' , $request->tracking_number]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->filled('user_id')){
            $get_order->where('posts.post_author_id', $request->user_id);
        }

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1' ORDER BY id DESC LIMIT 1) as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city' ORDER BY id DESC LIMIT 1) as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state' ORDER BY id DESC LIMIT 1) as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip' ORDER BY id DESC LIMIT 1) as ship_to_zip"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'webcam_image') as webcam_image"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.*'
        );

        if($request->filled('per_page')){
            $this->perPage = $request->per_page;
        }

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $orders = $get_order->orderBy('posts.ebay_date', $sort)->paginate($this->perPage);
        // $orders = $get_order->orderByRaw('sale_date '.$sort)->paginate($this->perPage);

        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();
        $operators = User::where('user_type_id', 7)->get();

        return view('pages.admin.warehouse.scan-out-list', compact('orders', 'operators'));
    }

    /**
     * combined scan out list search here
     */
    public function combinedScanOutList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-07']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.ebay_date', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.ebay_date', '<=', $end->format('Y-m-d'));
        }

        /*if($request->has('from_date') && $request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->leftJoin('post_extras AS sd', 'posts.id', '=', 'sd.post_id');
            $get_order->where([['sd.key_name','sale_date'],['sd.key_value', '>=' , $end->format('Y-m-d')]]);
        }

        if($request->has('to_date') && $request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->leftJoin('post_extras AS st', 'posts.id', '=', 'st.post_id');
            $get_order->where([['st.key_name','sale_date'],['st.key_value', '<=' , $end->format('Y-m-d')]]);
        }*/

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', 'like' , '%'.$request->scan_i_location_id.'%']]);
        }

        if($request->has('location_name') && $request->filled('location_name')){
            $get_order->join('posts AS pl', 'posts.location_id', '=', 'pl.location_id')->where([['pl.post_type','rack'],['pl.post_title', 'like' , '%'.$request->location_name.'%']]);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $request->order_number]]);
        }

        if($request->has('tracking_number') && $request->filled('tracking_number')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','tracking_number'],['p4.key_value', '=' , $request->tracking_number]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->filled('user_id')){
            $get_order->where('posts.post_author_id', $request->user_id);
        }

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1' ORDER BY id DESC LIMIT 1) as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city' ORDER BY id DESC LIMIT 1) as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state' ORDER BY id DESC LIMIT 1) as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip' ORDER BY id DESC LIMIT 1) as ship_to_zip"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'webcam_image') as webcam_image"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $orders = $get_order->orderBy('posts.ebay_date', $sort)->paginate($this->perPage);
        // $orders = $get_order->orderByRaw('sale_date '.$sort)->paginate($this->perPage);

        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();
        $operators = User::where('user_type_id', 7)->get();

        return view('pages.admin.warehouse.combined-scan-out', compact('orders', 'operators'));
    }

    /**
     * Dispatch list search here
     */
    public function dispatchList(Request $request, $status){
        $get_order = (new Post)->newQuery();

        if($status == 'new'){
            $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03']);
        } else{
            if($request->has('order_status') && $request->filled('order_status')){
                $term = $request->order_status;
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $term]]);
            }else{
                $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-05','IS-04']);
            }            
        }

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.ebay_date', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.ebay_date', '<=', $end->format('Y-m-d'));
        }

        /*if($request->has('from_date') && $request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->leftJoin('post_extras AS sd', 'posts.id', '=', 'sd.post_id');
            $get_order->where([['sd.key_name','sale_date'],['sd.key_value', '>=' , $end->format('Y-m-d')]]);
        }

        if($request->has('to_date') && $request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->leftJoin('post_extras AS st', 'posts.id', '=', 'st.post_id');
            $get_order->where([['st.key_name','sale_date'],['st.key_value', '<=' , $end->format('Y-m-d')]]);
        }*/

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $request->order_number]]);
        }

        if($request->has('tracking_number') && $request->filled('tracking_number')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','tracking_number'],['p4.key_value', '=' , $request->tracking_number]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if($request->has('so_from_date') && $request->filled('so_from_date')){
            $get_order->leftJoin('post_extras AS sof', 'posts.id', '=', 'sof.post_id');
            $get_order->where([['sof.key_name','scan_out_date'],['sof.key_value', '>=' , $request->so_from_date]]);
        }

        if($request->has('so_to_date') && $request->filled('so_to_date')){
            $get_order->leftJoin('post_extras AS sot', 'posts.id', '=', 'sot.post_id');
            $get_order->where([['sot.key_name','scan_out_date'],['sot.key_value', '<=' , $request->so_to_date]]);
        }

        if($request->has('dis_from_date') && $request->filled('dis_from_date')){
            $get_order->leftJoin('post_extras AS disf', 'posts.id', '=', 'disf.post_id');
            $get_order->where([['disf.key_name','scan_dispatch_date'],['disf.key_value', '>=' , $request->dis_from_date]]);
        }

        if($request->has('dis_to_date') && $request->filled('dis_to_date')){
            $get_order->leftJoin('post_extras AS dist', 'posts.id', '=', 'dist.post_id');
            $get_order->where([['dist.key_name','scan_dispatch_date'],['dist.key_value', '<=' , $request->dis_to_date]]);
        }

        /*if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }*/

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status' ORDER BY id DESC LIMIT 1) as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1' ORDER BY id DESC LIMIT 1) as ship_to_address_1"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city' ORDER BY id DESC LIMIT 1) as ship_to_city"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state' ORDER BY id DESC LIMIT 1) as ship_to_state"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip' ORDER BY id DESC LIMIT 1) as ship_to_zip"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by' ORDER BY id DESC LIMIT 1) as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time' ORDER BY id DESC LIMIT 1) as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id' ORDER BY id DESC LIMIT 1) as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id' ORDER BY id DESC LIMIT 1) as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date' ORDER BY id DESC LIMIT 1) as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time' ORDER BY id DESC LIMIT 1) as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user' ORDER BY id DESC LIMIT 1) as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images' ORDER BY id DESC LIMIT 1) as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'dispatch_images' ORDER BY id DESC LIMIT 1) as dispatch_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date' ORDER BY id DESC LIMIT 1) as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time' ORDER BY id DESC LIMIT 1) as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_user' ORDER BY id DESC LIMIT 1) as scan_dispatch_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number' ORDER BY id DESC LIMIT 1) as tracking_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'label_url' ORDER BY id DESC LIMIT 1) as label_url"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_type' ORDER BY id DESC LIMIT 1) as order_type"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'weight' ORDER BY id DESC LIMIT 1) as weight"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'length' ORDER BY id DESC LIMIT 1) as length"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'height' ORDER BY id DESC LIMIT 1) as height"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'width' ORDER BY id DESC LIMIT 1) as width"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.*'
        );

        if($request->filled('per_page')){
            $this->perPage = $request->per_page;
        }

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $orders = $get_order->orderBy('posts.ebay_date', $sort)->paginate($this->perPage);
        // $orders = $get_order->orderByRaw('sale_date '.$sort)->paginate($this->perPage);

        $Warehouse = Warehouse::all();
        $Carrier = Carrier::all();
        $clients = User::where('user_type_id', 3)->get();
        $operators = User::where('user_type_id', 7)->get();

        # pending dispatch orders...
        $pending = (new Post)->newQuery();
        $pending->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03']);
        $pending->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id")
        );
        $p_orders = $pending->where(['posts.post_type' => 'scan'])->orderBy('posts.id', $sort)->get();

        return view('pages.admin.warehouse.dispatch-list', compact('orders', 'p_orders', 'status', 'Carrier', 'operators'));
    }

    /**
     * Cancelled package list search here
     */
    public function cancelledList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-06']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','order_number'],['p3.key_value', '=' , $request->order_number]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancelRequests') as cancelRequests"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancel_reason') as cancel_reason"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $orders = $get_order->orderBy('posts.id', $sort)->paginate($this->perPage);
        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();

        return view('pages.admin.warehouse.cancelled-list', compact('orders'));
    }


    /**
     * 
     * Download excel the file 
     */
    public function downloadExcelSheet($order, $request) {
        $data_ar = [];
        if (count($order) > 0) {
            foreach ($order as $key => $row) {
                $sl_d = $daysDifference = $difference = '';
                if(!empty($row->sale_date)) {
                    $sl_d = Carbon::parse($row->sale_date)->format('Y-m-d H:i:s');

                    $currentDate = Carbon::now();
                    $givenDate = Carbon::parse(date('Y-m-d', strtotime($row->sale_date))); // Replace with your date
                    $daysDifferences = $currentDate->diffInDays($givenDate);
                    $differences = $daysDifferences - 3;

                    $daysDifference = '+ '.$daysDifferences.' Days';
                    $difference = sprintf('%+d', $differences) .'Days';
                }

                $so_dt = $dis_dt = '';
                if(!empty($row->scan_out_date)) {
                    $so_dt = Carbon::parse($row->scan_out_date.' '.$row->scan_out_time)->format('Y-m-d H:i:s');
                }

                if(!empty($row->scan_dispatch_date)) {
                    $dis_dt = Carbon::parse($row->scan_dispatch_date.' '.$row->scan_dispatch_time)->format('Y-m-d H:i:s');
                }

                $dim = '';
                if (!empty($row->length)) {
                    $dim = $row->length.' X '.$row->width.' X '.$row->height;
                }

                $reason = 'N/A';
                if(!empty($row->cancelRequests)){
                    $res = json_decode($row->cancelRequests);
                    $reason = $res[0]->cancelReason ?? 'N/A';
                } elseif (!empty($row->cancel_reason)) {
                    $reason = cancel_reason($row->cancel_reason);
                }

                /*$address = $row->ship_to_address_1 ?? '';
                $address .= $row->ship_to_city ?? '';
                $address .= $row->ship_to_state ?? '';
                $address .= $row->ship_to_zip ?? '';
                $address .= $row->ship_to_country ?? '';*/

                $data_ar[] = [
                    $row->id,
                    Carbon::parse($row->created_at)->format('Y-m-d H:i:s'),
                    $sl_d,
                    $row->authorized_by ?? '',
                    $row->user->name ?? '',
                    $row->scan_i_package_id ?? '',
                    $row->order_number ?? '',
                    $row->scan_i_location_id ?? '',
                    $row->location_name ?? '',
                    $row->tracking_number ?? '',
                    $so_dt,
                    $dis_dt,
                    $row->weight ?? '',
                    $dim,
                    $reason,
                    order_status($row->order_status),
                ];
            }
        }

        return Excel::download(new ScanExport($data_ar), "WMS-Report-" . time() . '.xlsx');
    }

    /**
     * store scan in
     */
    public function scanInStore(Request $request){
        try {
            $data = $request->only(['authorized_by', 'create_system_time', 'scan_i_package_id', 'scan_i_location_id']);
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'scan_i_location_id' => 'required',
                // 'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                // 'images.required' => 'You must upload at least one image.',
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 2MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            if (strpos($request->scan_i_package_id, 'SC-ORD-') === false) {
                return response()->json(['message' => 'The scanned Package ID appears to be invalid. Please scan a valid Package ID.', 'status' => 200], 200);
            }

            // code...
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
            $posts = $get_order->where(['posts.post_type' => 'rack'])->first();
            
            if(empty($posts)){
                return response()->json(['message' => 'Location ID not found. Please contact your administrator to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again.', 'status' => 200], 200);
            }

            # check the dispatch validation here...
            $chk_P = (new Post)->newQuery();
            $chk_P->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_P->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03', 'IS-04', 'IS-05']);
            $CheckP = $chk_P->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$CheckP->isEmpty()) {
                return response()->json(['message' => "This item is already included in the Dispatch list.", 'status' => 200], 200);
            }

            $chk_D = (new Post)->newQuery();
            $chk_D->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_D->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk_D->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03', 'IS-04', 'IS-05']);
            $CheckD = $chk_D->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$CheckD->isEmpty()) {
                return response()->json(['message' => "This item is already included in the Dispatch list.", 'status' => 200], 200);
            }

            # check the other valition here...
            $chk = (new Post)->newQuery();
            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01', 'IS-02', 'IS-07', 'IS-06']);
            $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$Check->isEmpty()) {
                return response()->json(['message' => "This package has already been scanned at this location: ".$request->scan_i_location_id.". To move it to a different location, please use the Move to Location section.", 'status' => 200], 200);
            }

            $currentDate = Carbon::now()->toDateString();
            $data['scan_in_images'] = [];
            if($request->file('images')){
                foreach ($request->file('images') as $image) {
                    // $image = $request->file('image');
                    $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                    if(!Storage::disk('public_uploads')->exists('order')){
                        Storage::disk('public_uploads')->makeDirectory('order');
                    }
                    
                    $propertyimage = Image::make($image)->stream();
                    Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
                    $data['scan_in_images'][] = 'order/'.$imagename;
                }
            }

            # web cam image save here...
            if ($request->has('webcam_image') && $request->filled('webcam_image')) {
                # web cam image save here...
                $images = $request->input('webcam_image'); // Get the array of images
                if (count($images) > 4) {
                    return response()->json(['message' => 'You can only upload up to 4 webcam images.', 'status' => 200], 200);
                }

                foreach ($images as $imageData) {
                    $imageData = str_replace('data:image/png;base64,', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $imageName = $currentDate.'-'.uniqid() . '.png';

                    // Store the image in the 'public/images' directory
                    $filePath = 'order/' . $imageName;
                    array_push($data['scan_in_images'], $filePath);
                    Storage::disk('public_uploads')->put($filePath, base64_decode($imageData));
                }
            }

            // code...
            $package = (new Post)->newQuery();
            $package->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','scan_i_package_id'],['pe.key_value', '=' , $data['scan_i_package_id']]]);
            $package->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status') as order_status"),
                'posts.*'
            );
            $order = $package->where(['posts.post_type' => 'scan'])->first();
            
            $post_id = '';
            if(empty($order)){
                $post     = new Post;
                $user_id  = Auth::user()->id;
                $post->post_author_id = $user_id ?? 1;
                $post->post_content   = 'Scan order';
                $post->post_title     = 'Scan order';
                $post->post_slug      = Str::slug('Scan order', '-');
                $post->parent_id      = 0;
                $post->post_status    = 1;
                $post->post_type      = 'scan';
                $post->location_id      = $data['scan_i_location_id'];
                $post->package_id      = $data['scan_i_package_id'];
                $post->save();
                $post_id = $post->id;
                $data['order_status'] = 'IS-01';
            } else {
                if ($order->order_status == 'IS-06') {
                    $data['order_status'] = 'IS-01';
                }
                $post_id = $order->id;
                $order->location_id      = $data['scan_i_location_id'];
                $order->package_id      = $data['scan_i_package_id'];
                $order->created_at = Carbon::now(); // Set to 5 days ago
                $order->save();
            }

            foreach ($data as $key => $value) {
                $ar_val = $value;
                if (is_array($value)) {
                    $ar_val = json_encode($value);
                }
                updateOrCreatePostMeta($post_id, $key, $ar_val);
            }

            # store the user log...
            $his = new StatusHistory;
            $his->post_id = $post_id;
            $his->addition_info = 'Add Scan In detail.';
            $his->type = 'scan_in';
            $his->status_date = date('Y-m-d');
            $his->status_time = date('H:i:s');
            $his->local_time = $data['create_system_time']; // Fetch local time;
            $his->user = Auth::user()->name;
            $his->save();

            return response()->json(['message' => 'Package successfully scanned into the specified Location', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * store scan out and move in to dispatch
     */
    public function scanOutStore(Request $request){
        try {
            $data = $request->only(['authorized_by', 'create_system_time', 'scan_i_package_id', 'scan_i_location_id']);
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'scan_i_location_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            # check the validation here...
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

            if (empty($post)) {
                // return response()->json(['message' => 'If your package id is '.$request->scan_i_package_id.'. Please recheck once by searching only last xxxxx-xxxx', 'status' => 200], 200);
                return response()->json(['message' => 'Please Search for the package ID using the Date and Item serial number only by removing the prefix "SC-ORD-" Example: 07022025-54321', 'status' => 200], 200);
            }

            # chek alreday scan out or not...
            $chk = (new Post)->newQuery();
            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereNotIn('pes.key_value', ['IS-07', 'IS-02', 'IS-01']);
            $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$Check->isEmpty()) {
                return response()->json(['message' => 'This item is already included in the Dispatch list.', 'status' => 200], 200);
            }

            # chek alreday ebay id...
            $chk_id = (new Post)->newQuery();
            $chk_id->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_id->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number")
            );
            $Check_or = $chk_id->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
            if (($Check_or->order_number == '' || $Check_or->order_number == null)){
                return response()->json(['message' => 'The package cannot be scanned out as it is not linked to any eBay Order ID.', 'status' => 200], 200);
            }

            $post->location_id = $data['scan_i_location_id'];
            $post->package_id  = $data['scan_i_package_id'];
            $post->save();
            
            set_post_key_value($post->id, 'scan_out_date', date('Y-m-d'));
            set_post_key_value($post->id, 'scan_out_time', date('H:i:s'));
            set_post_key_value($post->id, 'scan_out_user', Auth::user()->name);
            set_post_key_value($post->id, 'order_status', 'IS-03');

            # store the user log...
            $his = new StatusHistory;
            $his->post_id = $post->id;
            $his->addition_info = 'Scan out to dispatch detail.';
            $his->type = 'dispatch';
            $his->status_date = date('Y-m-d');
            $his->status_time = date('H:i:s');
            $his->user = Auth::user()->name;
            $his->local_time = $data['create_system_time']; // Fetch local time;
            $his->save();

            if ($request->filled('form_type') && $request->form_type == 'combined') {
                $flag = false;
                $itm = [];
                $msg = '';

                $chk_order = (new Post)->newQuery();
                $chk_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
                $chk_order->select(
                    DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number")
                );
                $ord = $chk_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
                // dd($ord);

                # get the item based on the ebay order id...
                $tt_order = (new Post)->newQuery();
                $tt_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number ?? '']]);
                $total = $tt_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->count();

                $min_order = (new Post)->newQuery();
                $min_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number ?? '']]);
                $min_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-07']);
                $min_total = $min_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->count();

                $order = (new Post)->newQuery();
                $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number ?? '']]);
                // $order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-07']);
                $order->select(
                    DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
                    DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id")
                );
                $ebay_order = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->toArray();
                // dd($ebay_order);
                if (count($ebay_order) > 0) {
                    foreach ($ebay_order as $k => $eb) {
                        $i = $k+1;
                        // if ($eb['scan_i_package_id'] != $request->scan_i_package_id) {
                            $flag = true;
                            array_push($itm, $eb['scan_i_package_id']);
                            if (empty($msg)) {
                                $msg = $i. "/".$total." Pkg Id:- ".$eb['scan_i_package_id'].", Location ID:- ".$eb['scan_i_location_id'];
                            } else {
                                $msg .= "<br>".$i."/".$total." Pkg Id:- ".$eb['scan_i_package_id'].", Location ID:- ".$eb['scan_i_location_id'];   
                            }
                        // }
                    }
                }

                if ($flag) {
                    $msg = "This package is part of eBay Order ID: <b>".$ord->order_number."</b> and includes combine packages with the same name and address.:- <br><b>".$msg."</b>";
                    return response()->json(['message' => $msg, 'status' => 201], 201);
                }
            }

            return response()->json(['message' => 'Package successfully scanned out of the specified location', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * store dispatch order
     */
    public function dispatchPackageStore(Request $request){
        try {
            // dd($request->all());
            $data = $request->only(['authorized_by', 'create_system_time', 'scan_i_package_id', 'scan_i_location_id', 'tracking_number']);
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'scan_i_location_id' => 'required',
                'tracking_number' => 'required',
                'images' => 'nullable|array|max:4|required_without:webcam_image',
                'webcam_image' => 'nullable|array|max:4|required_without:images',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                // 'images.required' => 'You must upload at least one image.',
                'images.required_without' => 'The images field is required when web cam photo is not provided.',
                'webcam_image.required_without' => 'The web cam  field is required when images is not provided.',
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 2MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            // code...
            $location = (new Post)->newQuery();
            $location->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
            $loc = $location->where(['posts.post_type' => 'rack'])->first();
            
            if(empty($loc)){
                return response()->json(['message' => 'Location ID not found. Please contact your administrator to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again.', 'status' => 200], 200);
            }
            
            $currentDate = Carbon::now()->toDateString();
            $data['dispatch_images'] = [];
            if($request->file('images')){
                foreach ($request->file('images') as $image) {
                    // $image = $request->file('image');
                    $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                    if(!Storage::disk('public_uploads')->exists('order')){
                        Storage::disk('public_uploads')->makeDirectory('order');
                    }
                    
                    $propertyimage = Image::make($image)->stream();
                    Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
                    $data['dispatch_images'][] = 'order/'.$imagename;
                }
            }

            # web cam image save here...
            if ($request->has('webcam_image') && $request->filled('webcam_image')) {
                $images = $request->input('webcam_image'); // Get the array of images
                if (count($images) > 4) {
                    return response()->json(['message' => 'You can only upload up to 4 webcam images.', 'status' => 200], 200);
                }

                foreach ($images as $imageData) {
                    $imageData = str_replace('data:image/png;base64,', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $imageName = $currentDate.'-'.uniqid() . '.png';

                    // Store the image in the 'public/images' directory
                    $filePath = 'order/' . $imageName;
                    array_push($data['dispatch_images'], $filePath);
                    Storage::disk('public_uploads')->put($filePath, base64_decode($imageData));
                }
            }

            if (count($data['scan_i_package_id']) > 0) {
                $flag = false;
                $itm = [];

                #..same order id have multiple item validation here...
                foreach ($data['scan_i_package_id'] as $key => $package_id) {
                    $get_order = (new Post)->newQuery();
                    $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $package_id]]);
                    $get_order->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number")
                    );
                    $ord = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

                    # get the item based on the ebay order id...
                    $order = (new Post)->newQuery();
                    $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number]]);
                    $order->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id")
                    );
                    $ebay_order = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->toArray();
                    if (count($ebay_order) > 0) {
                        foreach ($ebay_order as $eb) {
                            if (!in_array($eb['scan_i_package_id'], $data['scan_i_package_id'])) {
                                $flag = true;
                                array_push($itm, $eb['scan_i_package_id']);
                            }
                        }
                    }
                }

                if ($flag) {
                    $msg = "The following packages are associated with the same name and address in eBay’s order. Please combine these packages into a single shipment and proceed with the dispatch process. Package Details:- ".implode(', ', $itm);
                    return response()->json(['message' => $msg, 'status' => 200], 200);
                }

                #...insert data here...
                foreach ($data['scan_i_package_id'] as $key => $package_id) {
                    $get_order = (new Post)->newQuery();
                    $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $package_id]]);
                    $get_order->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_number') as item_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_quantity') as item_quantity"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'shipping_carrier_code') as carrier_code"),
                        'posts.id'
                    );
                    $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first()->toArray();
                    $ord = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

                    set_post_key_value($post['id'], 'scan_dispatch_date', date('Y-m-d'));
                    set_post_key_value($post['id'], 'scan_dispatch_time', date('H:i:s'));
                    set_post_key_value($post['id'], 'scan_dispatch_user', Auth::user()->name);
                    if (count($data['scan_i_package_id']) > 1) {
                        set_post_key_value($post['id'], 'order_status', 'IS-05');
                    } else {
                        set_post_key_value($post['id'], 'order_status', 'IS-04');
                    }
                    set_post_key_value($post['id'], 'tracking_number', $data['tracking_number']);
                    set_post_key_value($post['id'], 'scan_i_location_id', $data['scan_i_location_id']);
                    set_post_key_value($post['id'], 'dispatch_images', json_encode($data['dispatch_images']));

                    set_post_key_value($post['id'], 'length', $request->length ?? '10');
                    set_post_key_value($post['id'], 'width', $request->width ?? '8');
                    set_post_key_value($post['id'], 'height', $request->height ?? '3');
                    set_post_key_value($post['id'], 'weight', $request->weight ?? '0.5');

                    $ord->location_id      = $data['scan_i_location_id'];
                    $ord->package_id      = $package_id;
                    $ord->save();

                    # push the shippment detail on ebay...
                    $cr_code = (!empty($request->carrier_code)) ? $request->carrier_code : $post['carrier_code'];
                    $this->pushTrackingNumber($post, $cr_code, $data['tracking_number']);

                    # store the user log...
                    $his = new StatusHistory;
                    $his->post_id = $post['id'];
                    $his->addition_info = 'Scan out into dispatch detail.';
                    $his->type = 'dispatch';
                    $his->status_date = date('Y-m-d');
                    $his->status_time = date('H:i:s');
                    $his->user = Auth::user()->name;
                    $his->local_time = $data['create_system_time']; // Fetch local time;
                    $his->save();
                }
            }

            return response()->json(['message' => 'Package successfully moved to dispatch screen', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * redirct to the package images
     */
    public function redirectPackageImage($post_id = ''){
        $package = Post::where(['id' => $post_id])->first();

        return view('pages.admin.warehouse.image', compact('package'));
    }

    /**
     * Remove orders
     */
    public function removePackage($id){
        $dlt_1 = PostExtra::where('post_id', $id)->delete();
        $dlt_2 = Post::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Action completed.');
    }

    /**
     * Import Rack data
     */
    public function importRack(Request $request){
        try {
            $file      = $request->file('postdata_file')->getClientOriginalName();
            $baseFilename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv') {
                $inputFileName = $request->file('postdata_file');

                /*check point*/
                $inputFileType = IOFactory::identify($inputFileName);
                $objReader     = IOFactory::createReader($inputFileType);
                $objReader->setDelimiter(',');
                $objReader->setEnclosure('"');
                $objReader->setSheetIndex(0);
                $objReader->setReadDataOnly(true);

                $objPHPExcel = $objReader->load($inputFileName);
                $objPHPExcel->setActiveSheetIndex(0);
                $objWorksheet          = $objPHPExcel->getActiveSheet();
                $CurrentWorkSheetIndex = 0;
                /* row and column*/
                // $sheet = $objPHPExcel->getSheet(0);
                $highestRow    = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();

                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5
                $headingsArray      = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray      = $headingsArray[1];

                $r              = -1;
                $namedDataArray = $keys = array();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '') || empty($dataRow[$row]['A'])) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {                            
                            $key                      = strtolower(str_replace(' ', '_', $columnHeading));
                            $namedDataArray[$r][$key] = $dataRow[$row][$columnKey];
                            array_push($keys,$key);
                        }
                    }
                }

                // dd($namedDataArray);
                foreach($namedDataArray as $key => $value){
                    if(!isset($value['client_id'])){
                        continue;
                    }

                    $post     = new Post;
                    $user_id  = Auth::user()->id;
                    $post->post_author_id = $user_id ?? 1;
                    $post->client_id = $value['client_id'] ?? null;
                    $post->warehouse_id = $value['warehouse_id'] ?? null;
                    $post->post_content   = $value['title'] ?? null;
                    $post->post_title     = $value['title'] ?? null;
                    $post->post_slug      = Str::slug($value['title'], '-');;
                    $post->parent_id      = 0;
                    $post->post_status    = 1;
                    $post->location_id    = $value['location_id'];
                    $post->post_type      = 'rack';
                    if ($post->save()) {
                        $data = [
                            'length' => $value['length'],
                            'width' => $value['width'],
                            'height' => $value['height'],
                            'weight' => $value['weight'],
                            'level' => $value['label'],
                            'shelves' => $value['shelves'],
                            'authorized_by' => Auth::user()->name,
                            'location_id' => $value['location_id'],
                            'measurement' => $value['measurement_type'],
                            'sync_status' => 'Pending',
                        ];

                        foreach($data as $k => $v){
                            $postextra = new PostExtra();
                            $postextra->post_id = $post->id;
                            $postextra->key_name = $k;
                            $postextra->key_value = $v;
                            $postextra->save();
                        }
                    }
                }

                return response()->json(['message' => 'Action successfully.', 'status' => 201], 201);
            } else {
                return response()->json(['message' => 'wrong extension', 'status' => 200], 200);
            }
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * location id print invoice
     */
    public function redirectLocationInvoice($post_id = ''){
        $order_id = $post_id;
        $get_post_by_order_id     = Post::where(['id' => $post_id])->first();
        $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();

        $order_data_by_id = [];

        if ($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id->count() > 0) {
            $order_date_format = new Carbon($get_post_by_order_id->created_at);
            $order_data_by_id['_order_id']   = $get_post_by_order_id->id;
            $order_data_by_id['_order_date'] = $order_date_format->toDayDateTimeString();

            foreach ($get_postmeta_by_order_id as $postmeta_row_data) {
                $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
            }
        }

        // dd($order_data_by_id);
        return view('pages.admin.invoice.location-invoice', compact('order_data_by_id'));
    }

    /**
     * bulk location id print invoice
     */
    public function bulkLocationInvoice(Request $request){
        try {
            $get_order = (new Post)->newQuery();
            $get_order->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'location_id') as location_id"),
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sync_status') as sync_status"),
                'posts.id'
            );

            if ($request->has('form_type') && $request->form_type == 'syncdata') {
                $post['order_id'] = $get_order->whereIn('id', $request->order_ids)->orderBy('id', 'DESC')->pluck('location_id')->toArray();
                // dd(json_encode($post));

                # get the data with api from the ebay shipcycle...
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    // CURLOPT_URL => 'https://ebay.ecomglobalsystems.com/api/pallet/data/sync',
                    CURLOPT_URL => 'https://ebay.ecomglobalsystems.com/api/pallet/multi-data/sync',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($post),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $item = json_decode($response);
                // dd($item);

                # store the data and update...
                if (isset($item->data) && count($item->data) > 1) {
                    foreach ($item->data as $k => $pallet) {
                        // check pakage exits or not...
                        $chk = (new Post)->newQuery();
                        $chk->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','scan_i_package_id'],['pe.key_value','=',$pallet->package_id]]);
                        $chk->join('post_extras AS pes','posts.id','=','pes.post_id')->where([['pes.key_name','location_id'],['pes.key_value','=',$pallet->pallet_id]]);
                        $Check = $chk->where(['posts.post_type' => 'scan'])->get();
                        if (!$Check->isEmpty()) {
                            continue;
                        }

                        // check pakage exits or not...
                        $package = (new Post)->newQuery();
                        $package->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','scan_i_package_id'],['pe.key_value','=',$pallet->package_id]]);
                        $order = $package->where(['posts.post_type' => 'scan'])->first();
                        
                        $post_id = '';
                        if(empty($order)){
                            $post     = new Post;
                            $user_id  = Auth::user()->id ?? 1;
                            $post->post_author_id = $user_id;
                            $post->post_content   = 'Scan order';
                            $post->post_title     = 'Scan order';
                            $post->post_slug      = Str::slug('Scan order', '-');;
                            $post->parent_id      = 0;
                            $post->post_status    = 1;
                            $post->post_type      = 'scan';
                            $post->package_id      = $pallet->package_id;
                            $post->location_id      = $pallet->pallet_id;
                            $post->save();
                            $post_id = $post->id;
                            $data['order_status'] = 'IS-01';
                            $data['authorized_by'] = $request->authorized_by;
                            $data['create_system_time'] = $request->create_system_time;
                        } else {
                            $post_id = $order->id;
                            $order->package_id      = $pallet->package_id;
                            $order->location_id      = $pallet->pallet_id;
                            $order->save();
                        }

                        $data['scan_i_package_id'] = $pallet->package_id;
                        $data['scan_i_location_id'] = $pallet->pallet_id;
                        foreach ($data as $key => $value) {
                            $ar_val = $value;
                            if (is_array($value)) {
                                $ar_val = json_encode($value);
                            }
                            updateOrCreatePostMeta($post_id, $key, $ar_val);
                        }

                        # update the sync status..
                        $rack = (new Post)->newQuery();
                        $rack->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value','=',$pallet->pallet_id]]);
                        $rc_order = $rack->where(['posts.post_type' => 'rack'])->first();
                        if (!empty($rc_order)) {
                            updateOrCreatePostMeta($rc_order->id, 'sync_status', 'Completed');
                        }
                    }
                }

                return redirect()->back()->with('success', 'Action succcessfully.');
            } else {
                $post = $get_order->whereIn('id', $request->order_ids)->orderBy('id', 'DESC')->get()->toArray();
                // dd($post);
                return view('pages.admin.invoice.bulk-rack-invoice', compact('post'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * move package one location to another location
     */
    public function moveLocationList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','location_move'],['pes.key_value', '=' , 'move']]);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', 'like' , '%'.$request->scan_i_package_id.'%']]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date') as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time') as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_user') as scan_dispatch_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number') as tracking_number"),
            DB::raw("(select status_date from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_date"),
            DB::raw("(select status_time from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_time"),
            DB::raw("(select user from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as rack_user"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'DESC';
        $orders = $get_order->orderBy('posts.id', $sort)->paginate($this->perPage);
        $Warehouse = Warehouse::all();
        $clients = User::where('user_type_id', 3)->get();

        return view('pages.admin.warehouse.location-move', compact('orders'));
    }


    /**
     * change one location to another location
     */
    public function moveLocationStore(Request $request){
        try {
            $data = $request->only(['authorized_by', 'create_system_time', 'scan_i_package_id', 'scan_i_location_id', 'new_location_id']);
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'scan_i_location_id' => 'required',
                'new_location_id' => 'required',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 2MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            // check the locaiton id...
            $location = (new Post)->newQuery();
            $location->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['new_location_id']]]);
            $location_data = $location->where(['posts.post_type' => 'rack'])->first();
            
            if(empty($location_data)){
                return response()->json(['message' => 'New Location ID not found. Please contact your administrator to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again.', 'status' => 200], 200);
            }

            # check the package id or locaiton id
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
            $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

            if (empty($post)) {
                return response()->json(['message' => 'If your package id is '.$request->scan_i_package_id.'. Please recheck once by searching only last xxxxx-xxxx', 'status' => 200], 200);
            }

            if ($post) {
                $data['dispatch_images'] = [];
                if($request->file('images')){
                    foreach ($request->file('images') as $image) {
                        // $image = $request->file('image');
                        $currentDate = Carbon::now()->toDateString();
                        $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                        if(!Storage::disk('public_uploads')->exists('order')){
                            Storage::disk('public_uploads')->makeDirectory('order');
                        }
                        
                        $propertyimage = Image::make($image)->stream();
                        Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
                        $data['dispatch_images'][] = 'order/'.$imagename;
                    }
                }

                set_post_key_value($post->id, 'moveing_date', date('Y-m-d'));
                set_post_key_value($post->id, 'moveing_time', date('H:i:s'));
                set_post_key_value($post->id, 'moveing_user', Auth::user()->name);
                set_post_key_value($post->id, 'scan_i_location_id', $data['new_location_id']);
                set_post_key_value($post->id, 'old_location_id', $data['scan_i_location_id']);
                set_post_key_value($post->id, 'location_move', 'move');

                if (count($data['dispatch_images'])) {
                    set_post_key_value($post->id, 'dispatch_images', json_encode($data['dispatch_images']));
                }

                # store the user log...
                $his = new StatusHistory;
                $his->post_id = $post->id;
                $his->addition_info = 'Move one location to another location';
                $his->type = 'move_location';
                $his->status_date = date('Y-m-d');
                $his->status_time = date('H:i:s');
                $his->user = Auth::user()->name;
                $his->local_time = $data['create_system_time']; // Fetch local time;
                $his->save();
            }

            return response()->json(['message' => 'Package successfully moved to the specified location', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
    * Cancel ebay order
    * @param id
    **/
    public function cancelledOrder($id){
        $data = ['key_value' => 'IS-06'];
        PostExtra::where(['post_id' => $id, 'key_name' => 'order_status'])->update($data);
        return redirect()->back()->with('success', 'Action Completed');
    }

    /**
     * combined dispatch and cancelled package
     */
    public function combinedCancelPackage(Request $request){
        try {
            $data = $request->all();
            // dd($data);
            if ($request->has('form_type') && $request->form_type == 'com_dis') {
                $validator = Validator::make($request->all(), [
                    'scan_i_location_id' => 'required',
                    'images' => 'nullable|array|max:4|required_without:webcam_image',
                    'webcam_image' => 'nullable|array|max:4|required_without:images',
                    'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ],[
                    'scan_i_location_id.required' => 'Location Id is required.',
                    'images.required_without' => 'The images field is required when web cam photo is not provided.',
                    'webcam_image.required_without' => 'The web cam  field is required when images is not provided.',
                    'images.*.image' => 'Each file must be a valid image.',
                    'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                    'images.*.max' => 'Each image must not exceed 2MB in size.',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
                }

                // code...
                $get_order = (new Post)->newQuery();
                $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
                $posts = $get_order->where(['posts.post_type' => 'rack'])->first();
                
                if(empty($posts)){
                    return response()->json(['message' => 'Location ID not found. Please contact your administrator to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again.', 'status' => 200], 200);
                }

                # image upload and web cam images...
                $currentDate = Carbon::now()->toDateString();
                $data['dispatch_images'] = [];
                if($request->file('images')){
                    foreach ($request->file('images') as $image) {
                        // $image = $request->file('image');
                        $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                        if(!Storage::disk('public_uploads')->exists('order')){
                            Storage::disk('public_uploads')->makeDirectory('order');
                        }
                        
                        $propertyimage = Image::make($image)->stream();
                        Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
                        $data['dispatch_images'][] = 'order/'.$imagename;
                    }
                }

                # web cam image save here...
                if ($request->has('webcam_image') && $request->filled('webcam_image')) {
                    $images = $request->input('webcam_image'); // Get the array of images
                    if (count($images) > 4) {
                        return response()->json(['message' => 'You can only upload up to 4 webcam images.', 'status' => 200], 200);
                    }

                    foreach ($images as $imageData) {
                        $imageData = str_replace('data:image/png;base64,', '', $imageData);
                        $imageData = str_replace(' ', '+', $imageData);
                        $imageName = $currentDate.'-'.uniqid() . '.png';

                        // Store the image in the 'public/images' directory
                        $filePath = 'order/' . $imageName;
                        array_push($data['dispatch_images'], $filePath);
                        Storage::disk('public_uploads')->put($filePath, base64_decode($imageData));
                    }
                }

                if (count($data['order_ids']) > 0) {
                    $flag = false;
                    $itm = [];

                    #..validation here...
                    foreach ($data['order_ids'] as $key => $package_id) {
                        $get_order = (new Post)->newQuery();
                        $get_order->where(['post_type' => 'scan'])->where('id', $package_id);
                        $get_order->select(
                            DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number")
                        );
                        $ord = $get_order->orderBy('id', 'DESC')->first();

                        # get the item based on the ebay order id...
                        $order = (new Post)->newQuery();
                        $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number]]);
                        $order->select(
                            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
                            'posts.id'
                        );
                        $ebay_order = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->toArray();
                        if (count($ebay_order) > 0) {
                            foreach ($ebay_order as $eb) {
                                if (!in_array($eb['id'], $data['order_ids'])) {
                                    $flag = true;
                                    array_push($itm, $eb['scan_i_package_id']);
                                }
                            }
                        }
                    }

                    # validation true then return here...
                    if ($flag) {
                        $msg = "The following packages are associated with the same name and address in eBay’s order. Please combine these packages into a single shipment and proceed with the dispatch process. Package Details:- ".implode(', ', $itm);
                        return response()->json(['message' => $msg, 'status' => 200], 200);
                    }

                    # get the package with the id...
                    $get_package = (new Post)->newQuery();
                    $get_package->where(['post_type' => 'scan'])->whereIN('id', $data['order_ids']);
                    $get_package->select(
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name') as ship_to_name"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email') as ship_to_email"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_phone') as ship_to_phone"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1') as ship_to_address_1"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1') as ship_to_address_2"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city') as ship_to_city"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state') as ship_to_state"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip') as ship_to_zip"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country') as ship_to_country"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ebay_order_currency') as ebay_order_currency"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_number') as item_number"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_quantity') as item_quantity"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_waybill_status') as create_waybill_status"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'shipping_carrier_code') as carrier_code"),
                        DB::raw("(select DISTINCT key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_waywill_request_time') as create_waywill_request_time"),
                        'posts.id'
                    );
                    $orders = $get_package->get()->toArray();
                    $one = reset($orders);
                    $final = [];

                    # check if ids greater then one...
                    if (count($orders) > 1) {
                        foreach ($orders as $k => $kv) {
                            if (($one['ship_to_address_1'] == $kv['ship_to_address_1']) && ($one['ship_to_city'] == $kv['ship_to_city']) && ($one['ship_to_state'] == $kv['ship_to_state']) && ($one['ship_to_zip'] == $kv['ship_to_zip']) && ($one['ship_to_country'] == $kv['ship_to_country'])) {
                                array_push($final, $kv);
                            }
                        }

                        if (count($final) <= 1) {
                            return response()->json(['message' => 'These orders do not use the same shipping address. Please try again with matching shipping addresses.', 'status' => 200], 200);
                        }
                    } else {
                        foreach ($orders as $k => $kv) {
                            array_push($final, $kv);
                        }
                    }

                    # check the warehouse...
                    $war_obj = getwharehouse($one['ship_to_country']);
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
                    
                    # check carrier label...
                    $carrieravailable = Carrier::where('countrycode', "Like", "%" .$one['ship_to_country']. "%")->where('name', "Like", "%" . $one['carrier_code'] . "%")->first();
                    if ($request->has('carrier') && $request->filled('carrier')) {
                        $carrieravailable = Carrier::where('countrycode', "Like", "%" . $one['ship_to_country'] . "%")->where('name', $data['carrier'])->first();
                    }

                    if(!$carrieravailable) {
                        return response()->json(['message' => 'No Carrier found.', 'status' => 200], 200);
                    }

                    $request->request->add(['servicecode' => $carrieravailable->code]);
                    $request->request->add(['carrier_name' => $carrieravailable->name]);
                    $request->request->add(['unit_type' => $carrieravailable->unit_type]);

                    # checking the time between create time...
                    if (!empty($one['create_waywill_request_time'])) {
                        $cd = date('Y-m-d H:i:s');
                        $from_time = strtotime($one['create_waywill_request_time']); 
                        $to_time = strtotime($cd);
                        $diff_minutes = round(abs($from_time - $to_time) / 60,2);
                        // dd([$cd, $minutes]);
                        if (intval($diff_minutes) <= 10) {
                            $msg = "We're experiencing a temporary delay with the carrier API or a slow connection. Please try reprocessing this package in 1-5 minutes. If the issue continues, kindly reach out to the Supervisor for further assistance";
                            return response()->json(['message' => $msg, 'status' => 200], 200);
                        }
                    }

                    $no_of_pakg = 0 ;
                    $randomnumber =  rand ( 100 , 9999 );
                    $cn = count($final);
                    $sq_rg_no = 'WMS-'.date('mdY').'-'.$one['id'].'-'.$cn;
                    $waybill_array = $this->createSopifyOrderWaywillRequest($sq_rg_no, $one, $request, $carrieravailable, $data);

                    $js_data = json_encode($waybill_array);
                    foreach ($final as $key => $value) {
                        set_post_key_value($value['id'], 'create_waywill_request', $js_data);
                        set_post_key_value($value['id'], 'create_waywill_request_time', date('Y-m-d H:i:s'));
                    }


                    $rtn_msg = "We're experiencing a temporary delay with the carrier API or a slow connection. Please try reprocessing this package in 1-5 minutes. If the issue continues, kindly reach out to the Supervisor for further assistance";
                    # create waybill api here...
                    if(empty($one['create_waybill_status'])){
                        $create_response = $this->createShopifyOrderWaywillResponse($js_data);
                        $create_data = json_decode($create_response);
                        if (empty($create_data)) {
                            return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                        } elseif (isset($create_data->messageType) && $create_data->messageType == 'Error') {
                            set_post_key_value($one['id'], 'create_waybill_response', $create_response);
                            return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                        } else{
                            set_post_key_value($one['id'], 'create_waybill_status', $create_response);
                            set_post_key_value($one['id'], 'waybillNumber', $create_data->waybillNumber);
                            set_post_key_value($one['id'], 'ebay_sequence_order_number', $sq_rg_no);
                            set_post_key_value($one['id'], 'label_url', $create_data->labelURL);
                        }
                    } else {
                        $create_data = json_decode($one['create_waybill_status']);
                    }

                    # generate the label here...
                    $g_arr = [
                        'waybillNumber' => $create_data->waybillNumber,
                        'carrierCode'    => $carrieravailable->code,
                        'aggregator'     => '',
                        'labelFormat'     => 'PNG',
                        'carrierProduct' => ($carrieravailable->product[0]->code) ? ($carrieravailable->product[0]->code): "",
                    ];

                    set_post_key_value($one['id'], 'generate_waybill_request', json_encode($g_arr));
                    $gr_response = $this->generateShopifyOrderWaywillResponse($g_arr);
                    $gr_json = json_decode($gr_response);
                    
                    if (empty($gr_json)) {
                        return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                    } elseif (isset($gr_json->messageType) && $gr_json->messageType == 'Error') {
                        set_post_key_value($one['id'], 'generate_waybill_response', $gr_response);
                        // $rtn_msg = 'Generate waywill Api:- '.$gr_json->message;
                        return response()->json(['message' => $rtn_msg, 'status' => 200], 200);
                    }

                    $label = reset($gr_json->labelDetailList);
                    
                    # save the waywill data here...
                    foreach ($final as $key => $value) {
                        set_post_key_value($value['id'], 'scan_dispatch_date', date('Y-m-d'));
                        set_post_key_value($value['id'], 'scan_dispatch_time', date('H:i:s'));
                        set_post_key_value($value['id'], 'scan_dispatch_user', Auth::user()->name);
                        if (count($final) > 1) {
                            set_post_key_value($value['id'], 'order_status', 'IS-05');
                        } else {
                            set_post_key_value($value['id'], 'order_status', 'IS-04');
                        }
                        set_post_key_value($value['id'], 'scan_i_location_id', $data['scan_i_location_id']);
                        set_post_key_value($value['id'], 'dispatch_images', json_encode($data['dispatch_images']));

                        set_post_key_value($value['id'], 'generate_waybill_status', $gr_response);
                        set_post_key_value($value['id'], 'order_reference_number', $waybill_array['waybillRequestData']['WaybillNumber']);
                        set_post_key_value($value['id'], 'tracking_number', $gr_json->carrierWaybill);
                        set_post_key_value($value['id'], 'label_url', $label->artifactUrl);

                        set_post_key_value($value['id'], 'length', $data['length'] ?? '10');
                        set_post_key_value($value['id'], 'width', $data['width'] ?? '8');
                        set_post_key_value($value['id'], 'height', $data['height'] ?? '1');
                        set_post_key_value($value['id'], 'weight', $request->weight ?? '0.5');

                        Post::whereId($value['id'])->update(['location_id' => $data['scan_i_location_id']]);

                        # push the shippment detail on ebay...
                        $this->pushTrackingNumber($value, $value['carrier_code'], $gr_json->carrierWaybill);

                        # store the user log...
                        $his = new StatusHistory;
                        $his->post_id = $value['id'];
                        $his->addition_info = 'Scan out into dispatch detail.';
                        $his->type = 'dispatch';
                        $his->status_date = date('Y-m-d');
                        $his->status_time = date('H:i:s');
                        $his->user = Auth::user()->name;
                        $his->save();
                    }

                    return response()->json(['message' => 'Label generated successfully.', 'label_url' => $label->artifactUrl, 'status' => 201], 201);
                }
            } else {
                if (count($data['order_ids']) > 0) {
                    foreach ($data['order_ids'] as $key => $value) {
                        set_post_key_value($value, 'scan_cancel_date', date('Y-m-d'));
                        set_post_key_value($value, 'scan_cancel_time', date('H:i:s'));
                        set_post_key_value($value, 'scan_cancel_user', Auth::user()->name);
                        set_post_key_value($value, 'order_status', 'IS-06');
                        set_post_key_value($value, 'cancel_reason', $data['cancel_reason']);

                        # store the user log...
                        $his = new StatusHistory;
                        $his->post_id = $value;
                        $his->addition_info = 'Scan out into cancelled dispatch.';
                        $his->type = 'cancelled';
                        $his->status_date = date('Y-m-d');
                        $his->status_time = date('H:i:s');
                        $his->user = Auth::user()->name;
                        $his->save();
                    }

                    return response()->json(['message' => 'Package successfully cancelled - Please check the Cancelled screen for details', 'status' => 201], 201);
                }
            }

            return response()->json(['message' => 'Action successfully.', 'status' => 201], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    public function createShopifyOrderWaywillResponse($js_data){
        try {
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
            $url = Config('constants.cuactiveUrl').'GenerateCarrierWaybill?secureKey='.Config('constants.secureKey');
            $g_client = new Client(['headers'=>['AccessKey'=> Config('constants.AccessKey'), 'Content-Type' => 'application/json']]);
            $rg = $g_client->post($url,['form_params' => $g_arr]);
            $g_response = $rg->getBody()->getContents();
            return $g_response;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function createSopifyOrderWaywillRequest($sq_rg_no, $order_postmeta, $request, $carrieravailable, $data_arr){
        # create package array...
        $package_array = array();
        $no_of_pakg = 0;
        $package = array(
            'barCode' => '',
            'packageCount' => 1,
            'length' => $data_arr['length'] ?? '10',
            'width' => $data_arr['width'] ?? '8',
            'height' => $data_arr['height'] ?? '1',
            'weight' => $data_arr['weight'] ?? '0.5',
            'chargedWeight' => $data_arr['weight'] ?? '0.5',
            'selectedPackageTypeCode'=>'BOX',
            'itemCount' => 1
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
                "ReferenceNumber" => $order_postmeta['order_number'] ?? $sq_rg_no,
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

    /**
     * sync location data here..
     */
    public function syncLocationData(Request $request){
        try {
            $get_order = (new Post)->newQuery();
            $get_order->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'location_id') as location_id"),
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sync_status') as sync_status"),
                'posts.id'
            );
            $post = $get_order->whereIn('id', $request->order_ids)->orderBy('id', 'ASC')->get()->toArray();
            dd($post);

            if ($request->has('form_type') && $request->form_type == 'syncdata') {
                
            } else {
                return view('pages.admin.invoice.bulk-rack-invoice', compact('post'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * update the cancel status on ebay
     */
    public function pushCancelStatus($data, $legacyOrderId){
        // dd([$data, $carrier, $tracking]);

        # curl response...
        $response = $this->OrderController->checkEbayConfigration();        
        if(isset($response['type'])){
            return response()->json(['message' => $response['msg'], 'status' => 200], 200);
        }

        $result = json_decode($response['response']);
        $url = $response['api_url']."/post-order/v2/cancellation";
        $json = [
            "cancelReason" =>  "BUYER_CANCEL",
            "legacyOrderId" => $legacyOrderId
        ];

        // dd(json_encode($json));
        set_post_key_value($data['id'], 'cancel_request', json_encode($json));
        if (isset($result->access_token)) {
            $res = $this->OrderController->postCurlResponse($url, $result->access_token, json_encode($json));
            set_post_key_value($data['id'], 'cancel_reponse', $res);
        } else {
            set_post_key_value($data['id'], 'cancel_error', 'Token is missing');   
        }
    }


    /**
     * sync location data form the eBay Sc with cron
     */
    public function cronSyncLocationData(Request $request){
        try {
            $get_order = (new Post)->newQuery();
            $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','location_id'],['pes.key_value', 'like' , '%SC-ORD-P%']]);
            $get_order->leftJoin('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','sync_status'],['pe.key_value', '!=' , 'Completed']]);
            $get_order->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'location_id') as location_id"),
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sync_status') as sync_status"),
                'posts.id'
            );

            $post['order_id'] = $get_order->orderBy('posts.id', 'ASC')->take(50)->pluck('location_id')->toArray();
            // dd($post);
            // dd(json_encode($post));

            # get the data with api from the ebay shipcycle...
            $curl = curl_init();
            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://ebay.ecomglobalsystems.com/api/pallet/data/sync',
                CURLOPT_URL => 'https://ebay.ecomglobalsystems.com/api/pallet/multi-data/sync',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($post),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $item = json_decode($response);
            // dd($item);

            # store the data and update...
            if (isset($item->data) && count($item->data) > 1) {
                foreach ($item->data as $k => $pallet) {
                    // check pakage exits or not...
                    $chk = (new Post)->newQuery();
                    $chk->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','scan_i_package_id'],['pe.key_value','=',$pallet->package_id]]);
                    $chk->join('post_extras AS pes','posts.id','=','pes.post_id')->where([['pes.key_name','location_id'],['pes.key_value','=',$pallet->pallet_id]]);
                    $Check = $chk->where(['posts.post_type' => 'scan'])->get();
                    if (!$Check->isEmpty()) {
                        continue;
                    }


                    // check pakage exits or not...
                    $package = (new Post)->newQuery();
                    $package->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','scan_i_package_id'],['pe.key_value','=',$pallet->package_id]]);
                    $order = $package->where(['posts.post_type' => 'scan'])->first();
                    
                    $post_id = '';
                    if(empty($order)){
                        $post     = new Post;
                        $user_id  = 1;
                        $post->post_author_id = $user_id;
                        $post->post_content   = 'Scan order';
                        $post->post_title     = 'Scan order';
                        $post->post_slug      = Str::slug('Scan order', '-');;
                        $post->parent_id      = 0;
                        $post->post_status    = 1;
                        $post->post_type      = 'scan';
                        $post->package_id      = $pallet->package_id;
                        $post->location_id      = $pallet->pallet_id;
                        $post->save();
                        $post_id = $post->id;
                        $data['order_status'] = 'IS-01';
                        $data['authorized_by'] = 'Ecom';
                        $data['create_system_time'] = $request->create_system_time;
                    } else {
                        $post_id = $order->id;
                        $order->package_id      = $pallet->package_id;
                        $order->location_id      = $pallet->pallet_id;
                        $order->save();
                    }

                    $data['scan_i_package_id'] = $pallet->package_id;
                    $data['scan_i_location_id'] = $pallet->pallet_id;
                    foreach ($data as $key => $value) {
                        $ar_val = $value;
                        if (is_array($value)) {
                            $ar_val = json_encode($value);
                        }
                        updateOrCreatePostMeta($post_id, $key, $ar_val);
                    }

                    # update the sync status..
                    $rack = (new Post)->newQuery();
                    $rack->join('post_extras AS pe','posts.id','=','pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value','=',$pallet->pallet_id]]);
                    $rc_order = $rack->where(['posts.post_type' => 'rack'])->first();
                    if (!empty($rc_order)) {
                        updateOrCreatePostMeta($rc_order->id, 'sync_status', 'Completed');
                    }

                    sleep(1);
                }
            }

            return response()->json(['message' => 'Action successfully.', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
        }
    }

    /**
     * change package data.
     */
    public function changePackageData(){
        return view('pages.admin.warehouse.change-package-data');
    }

    public function changePackageStore(Request $request){
        try {
            $file      = $request->file('cat_file')->getClientOriginalName();
            $baseFilename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv') {
                $inputFileName = $request->file('cat_file');
                
                /*check point*/
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $objReader     = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objReader->setDelimiter(',');
                $objReader->setEnclosure('"');
                $objReader->setSheetIndex(0);
                $objReader->setReadDataOnly(true);

                $objPHPExcel = $objReader->load($inputFileName);
                $objPHPExcel->setActiveSheetIndex(0);
                $objWorksheet          = $objPHPExcel->getActiveSheet();
                $CurrentWorkSheetIndex = 0;
                /* row and column*/
                // $sheet = $objPHPExcel->getSheet(0);
                $highestRow    = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();

                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($highestColumn); // e.g. 5
                $headingsArray      = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray      = $headingsArray[1];

                $r              = -1;
                $namedDataArray = $keys = array();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '') || empty($dataRow[$row]['A'])) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {                            
                            $key                      = strtolower(str_replace(' ', '_', $columnHeading));
                            $namedDataArray[$r][$key] = $dataRow[$row][$columnKey];
                            array_push($keys,$key);
                        }
                    }
                }

                // dd($namedDataArray);
                $count = 1;
                foreach ($namedDataArray as $key => $td) {
                    $get_order = (new Post)->newQuery();
                    $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $td['ebay_oder_id']]]);
                    $order = $get_order->orderBy('posts.id', 'ASC')->get();

                    if (!$order->isEmpty()) {
                        foreach ($order as $key => $value) {
                            set_post_key_value($value->id, 'scan_dispatch_date', date('Y-m-d'));
                            set_post_key_value($value->id, 'scan_dispatch_time', date('H:i:s'));
                            set_post_key_value($value->id, 'scan_dispatch_user', Auth::user()->name);
                            set_post_key_value($value->id, 'order_status', 'IS-04');
                            // set_post_key_value($value->id, 'tracking_number', $td['tracking_id']);
                        }
                    }

                    $count++;
                }

                return redirect()->back()->with('success', $count.' item update succcessfully');
            } else {
                return redirect()->back()->with('error', 'wrong extension');
            }
        }catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * store assign operator
     */
    public function assignOperatorToItem(Request $request){
        try {
            $data = $request->only(['user_id', 'order_ids', 'cancel_reason']);
            if ($request->has('form_type') && $request->form_type == 'cancel') {
                $validator = Validator::make($request->all(), [
                    'cancel_reason' => 'required'
                ], [
                    'cancel_reason.required' => 'Reason is required.'
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
                }

                if (count($data['order_ids']) > 0) {
                    foreach ($data['order_ids'] as $key => $value) {
                        set_post_key_value($value, 'scan_cancel_date', date('Y-m-d'));
                        set_post_key_value($value, 'scan_cancel_time', date('H:i:s'));
                        set_post_key_value($value, 'scan_cancel_user', Auth::user()->name);
                        set_post_key_value($value, 'order_status', 'IS-06');
                        set_post_key_value($value, 'cancel_reason', $data['cancel_reason']);
                    }
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
                ], [
                    'user_id.required' => 'Operator is required.'
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
                }

                if (count($data['order_ids']) > 0) {
                    // dd($data['user_id']);
                    Post::whereIn('id', $data['order_ids'])->update(['post_author_id' => $data['user_id']]);
                }
            }

            return response()->json(['message' => 'Action successfully.', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }


    /**
     * move scan out package to dispatch 
     */
    public function moveScanOutToDispatch(Request $request){
        return view('pages.admin.warehouse.move-to-dispatch');
    }

    /**
     * store move scan out package to dispatch 
     */
    public function moveDispatchedStore(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'tracking_number' => 'required'
            ], [
                'scan_i_package_id.required' => 'Package ID Or eBay ID is required.'
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
            }

            // check the locaiton id...
            if ($request->filled('scan_i_location_id')) {
                $location = (new Post)->newQuery();
                $location->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $request->scan_i_location_id]]);
                $location_data = $location->where(['posts.post_type' => 'rack'])->first();
                
                if(empty($location_data)){
                    return response()->json(['message' => 'Location ID not found. Please contact your administrator to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again.', 'status' => 200], 200);
                }
            }

            # check the package id or locaiton id
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
            if (empty($post)) {
                $order = (new Post)->newQuery();
                $order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','order_number'],['p2.key_value', '=' , $request->scan_i_package_id]]);
                $post = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
            }

            if (empty($post)) {
                return response()->json(['message' => 'Data does not exists.', 'status' => 200], 200);
            }

            if ($post) {
                if ($request->filled('dis_date')) {
                    set_post_key_value($post->id, 'scan_dispatch_date', $request->dis_date);
                } else {
                    set_post_key_value($post->id, 'scan_dispatch_date', date('Y-m-d'));
                }

                set_post_key_value($post->id, 'scan_dispatch_time', date('H:i:s'));
                set_post_key_value($post->id, 'scan_dispatch_user', Auth::user()->name);
                set_post_key_value($post->id, 'order_status', 'IS-04');

                if ($request->filled('tracking_number')) {
                    set_post_key_value($post->id, 'tracking_number', $request->tracking_number);
                }

                if ($request->filled('scan_i_location_id')) {
                    set_post_key_value($post->id, 'scan_i_location_id', $request->scan_i_location_id);
                }
            }

            return response()->json(['message' => 'The order has been successfully moved to Dispatch.', 'status' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }


    /**
     * sync update location find the crosponds pakackes
     */
    public function cronSyncUpdateLocation(Request $request) {
        try {
            $get_order = (new Post)->newQuery();
            $get_order->leftJoin('post_extras AS ps', 'posts.id', '=', 'ps.post_id')->where([['ps.key_name','scan_i_package_id'],['ps.key_value', 'like' , '%SC-ORD-O%']]);
            $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','scan_i_location_id'],['pes.key_value', '=' , '']]);
            $get_order->leftJoin('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','order_status']])->whereIn('pe.key_value', ['IS-07', 'IS-02']);
            $get_order->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
                'posts.id'
            );
            $orders = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'ASC')->take(250)->get();
            // dd($orders);

            # store the data and update...
            if (count($orders) > 0) {
                foreach ($orders as $k => $post) {
                    $str = str_replace('SC-ORD-O-', '', $post->scan_i_package_id);
                    // check pakage exits or not...
                    $chk = (new Post)->newQuery();
                    $chk->leftJoin('post_extras AS ps', 'posts.id', '=', 'ps.post_id')->where([['ps.key_name','scan_i_package_id'],['ps.key_value', 'like' , '%'.$str.'%']]);
                    $chk->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','scan_i_location_id'],['pes.key_value', '!=' , '']]);
                    $chk->leftJoin('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','order_status']])->whereIn('pe.key_value', ['IS-01','IS-07', 'IS-02']);
                    $chk->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
                        'posts.id'
                    );
                    $Check = $chk->where(['posts.post_type' => 'scan'])->first();
                    // dd($Check);
                    if (!empty($Check)) {
                        $post->package_id      = $post->scan_i_package_id;
                        $post->location_id      = $Check->scan_i_location_id;
                        $post->save();
                        updateOrCreatePostMeta($post->id, 'scan_i_location_id', $Check->scan_i_location_id);
                    }
                }
            }

            return response()->json(['message' => 'Action successfully.', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
        }
    }
}
