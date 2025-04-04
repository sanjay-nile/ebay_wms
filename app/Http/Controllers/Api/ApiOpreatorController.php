<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;

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

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use GuzzleHttp\Client;

class ApiOpreatorController extends Controller
{
    public $perPage = 25;
    protected $OrderController;

    public function __construct(OrderController $OrderController) {
        $this->OrderController = $OrderController;
        $this->upload_path = \Config::get('constants.upload_path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777, true, true );
    }

    /**
     *  Dashboard
     */
    public function dashboard(){
        return response()->json([
            'status'              => true,
            'message'                 => 'Success',
        ], 200);
    }

    /**
     * Client Profile
     */
    public function clientProfile(){
        $user = Auth::user();
        // Associated data
        $warehouses      = \App\Models\Warehouse::where(['user_id' => $user->id])->orderBy('id', 'DESC')->get()->toArray();
        $warehouses      = array_map(function ($f) {
            $f['country_name'] = get_country_name_by_id($f['country_id']);
            $f['id']           = (string) $f['id'];
            $f['country_id']   = (string) $f['country_id'];
            $f['user_id']      = (string) $f['user_id'];
            return $f;
        }, $warehouses);

        $country_list = \App\Models\Country::where(['status' => '1'])->get();

        $user->warehouses     = $warehouses;
        $user->carriers     = Carrier::all()->toArray();

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $user], 200);
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
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
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
            DB::raw("(select po.post_title from posts as po JOIN post_extras as pos where po.id = pos.post_id and pos.key_name = 'location_id' and pos.key_value = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            DB::raw("(select status_date from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_date"),
            DB::raw("(select status_time from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_time"),
            DB::raw("(select user from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as rack_user"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);
        
        // Format response
        $response = [
            "current_page" => $data->currentPage(),
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
    }

    /**
     * scan out list search here
     */
    public function scanOutList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-02']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
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

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status' ORDER BY id DESC LIMIT 1) as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select po.post_title from posts as po JOIN post_extras as pos where po.id = pos.post_id and pos.key_name = 'location_id' and pos.key_value = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            DB::raw("(select DATE_FORMAT(key_value, '%m/%d/%Y') from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.ebay_date', $sort)->paginate($this->perPage);
        
        // Format response
        $response = [
            "current_page" => $data->currentPage(),
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
    }

    /**
     * combined scan out list search here
     */
    public function combinedScanOutList(Request $request){
        $get_order = (new Post)->newQuery();

        $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-07']);

        if($request->filled('from_date')){
            $end = Carbon::parse($request->from_date);
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
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

        if(Auth::user()->user_type_id==7){
            $get_order->where('posts.post_author_id', Auth::id());
        }

        $get_order->where(['posts.post_type' => 'scan']);
        $get_order->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_status' ORDER BY id DESC LIMIT 1) as order_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'buyer_username' ORDER BY id DESC LIMIT 1) as buyer_username"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select po.post_title from posts as po JOIN post_extras as pos where po.id = pos.post_id and pos.key_name = 'location_id' and pos.key_value = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            DB::raw("(select DATE_FORMAT(key_value, '%m/%d/%Y') from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
           DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.ebay_date', $sort)->paginate($this->perPage);
        
        // Format response
        $response = [
            "current_page" => $data->currentPage(),
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
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
            $get_order->whereDate('posts.created_at', '>=', $end->format('Y-m-d'));
        }

        if($request->filled('to_date')){
            $end = Carbon::parse($request->to_date);
            $get_order->whereDate('posts.created_at', '<=', $end->format('Y-m-d'));
        }

        if($request->has('scan_i_package_id') && $request->filled('scan_i_package_id')){
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
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
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_system_time') as create_system_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select DATE_FORMAT(key_value, '%m/%d/%Y') from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'label_url') as label_url"),
            DB::raw("(select DATE_FORMAT(key_value, '%m/%d/%Y') from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date') as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time') as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_user') as scan_dispatch_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number') as tracking_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'label_url') as label_url"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_type' ORDER BY id DESC LIMIT 1) as order_type"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'dispatch_images') as dispatch_images"),
            DB::raw("(select po.post_title from posts as po JOIN post_extras as pos where po.id = pos.post_id and pos.key_name = 'location_id' and pos.key_value = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);

        # pending dispatch orders...
        $pending = (new Post)->newQuery();
        $pending->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03']);
        $pending->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id")
        );
        $p_orders = $pending->where(['posts.post_type' => 'scan'])->orderBy('posts.id', $sort)->get();

        // Format response
        $response = [
            "status" => $status,
            "pending_orders" => $p_orders,
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "current_page" => $data->currentPage(),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
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

        /*if($request->has('location_name') && $request->filled('location_name')){
            $get_order->join('posts AS pl', 'pl.location_id', '=', 'posts.location_id')->where([['pl.post_type','rack'],['pl.post_title', 'like' , '%'.$request->location_name.'%']]);
        }*/

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
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'authorized_by') as authorized_by"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_location_id') as scan_i_location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'webcam_image') as webcam_image"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'sale_date' ORDER BY id DESC LIMIT 1) as sale_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date') as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time') as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number') as tracking_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'weight') as weight"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'length') as length"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'height') as height"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'width') as width"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'dispatch_images') as dispatch_images"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancelRequests') as cancelRequests"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'cancel_reason') as cancel_reason"),
            DB::raw("(select po.post_title from posts as po where po.post_type = 'rack' and po.location_id = posts.location_id ORDER BY id DESC LIMIT 1) as location_name"),
            'posts.id as p_id',
            'posts.created_at',
            'posts.post_author_id',
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        if ($request->query()) {
            $data = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);

            // Format response
            $response = [
                "current_page" => $data->currentPage(),
                "data" => $data->items(),
                "base_path" => asset('public/uploads', $secure = null),
                "first_page_url" => $data->url(1),
                "from" => $data->firstItem(),
                "last_page" => $data->lastPage(),
                "last_page_url" => $data->url($data->lastPage()),
                "links" => $this->generateLinks($data),
                "next_page_url" => $data->nextPageUrl(),
                "path" => $data->path(),
                "per_page" => $data->perPage(),
                "prev_page_url" => $data->previousPageUrl(),
                "to" => $data->lastItem(),
                "total" => $data->total(),
            ];
        } else {
            $response = [];
        }

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
    }

    private function generateLinks($paginator) {
       $links = [];
       foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url) {
           $links[] = [
               'url' => $url,
               'label' => (string)$page,
               'active' => $page === $paginator->currentPage(),
           ];
       }
       return [
           ['url' => $paginator->previousPageUrl(), 'label' => '&laquo; Previous', 'active' => false],
           ...$links,
           ['url' => $paginator->nextPageUrl(), 'label' => 'Next &raquo;', 'active' => false],
       ];
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
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ], [
                // 'images.required' => 'You must upload at least one image.',
                'scan_i_package_id.required' => 'Package Id is required.',
                'scan_i_location_id.required' => 'Location Id is required.',
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 2MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 400], 400);
            }

            if (strpos($request->scan_i_package_id, 'SC-ORD-') === false) {
                return response()->json(['message' => 'The scanned Package ID appears to be invalid. Please scan a valid Package ID.', 'status' => 400], 400);
            }

           // code...
           $get_order = (new Post)->newQuery();
           $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
           $posts = $get_order->where(['posts.post_type' => 'rack'])->first();
           
           if(empty($posts)){
               return response()->json(['message' => 'Location ID not found. Please contact the warehouse supervisor to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again', 'status' => 400], 400);
           }

            # check the dispatch validation here...
            $chk_P = (new Post)->newQuery();
            $chk_P->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_P->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03', 'IS-04', 'IS-05']);
            $CheckP = $chk_P->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$CheckP->isEmpty()) {
               return response()->json(['message' => "This item is already included in the Dispatch list.", 'status' => 400], 400);
            }

            $chk_D = (new Post)->newQuery();
            $chk_D->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_D->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk_D->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03', 'IS-04', 'IS-04']);
            $CheckD = $chk_D->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$CheckD->isEmpty()) {
               return response()->json(['message' => "This item is already included in the Dispatch list.", 'status' => 400], 400);
            }

            # check the other valition here...
            $chk = (new Post)->newQuery();
            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-01', 'IS-02', 'IS-07', 'IS-06']);
            $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$Check->isEmpty()) {
               return response()->json(['message' => "This package has already been scanned at this location: ".$request->scan_i_location_id.". To move it to a different location, please use the Move to Location section.", 'status' => 400], 400);
            }

            if($request->file('images')){
               $data['scan_in_images'] = [];
               foreach ($request->file('images') as $image) {
                   // $image = $request->file('image');
                   $currentDate = Carbon::now()->toDateString();
                   $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

                   if(!Storage::disk('public_uploads')->exists('order')){
                       Storage::disk('public_uploads')->makeDirectory('order');
                   }
                   
                   $propertyimage = Image::make($image)->stream();
                   Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
                   $data['scan_in_images'][] = 'order/'.$imagename;
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
               $post->post_slug      = Str::slug('Scan order', '-');;
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
                $order->created_at = Carbon::now(); // Set to 5 days ago
                $order->location_id      = $data['scan_i_location_id'];
                $order->package_id      = $data['scan_i_package_id'];
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
           $his->type = 'mob-scan_in';
           $his->status_date = date('Y-m-d');
           $his->status_time = date('H:i:s');
           $his->local_time = $data['create_system_time']; // Fetch local time;
           $his->user = Auth::user()->name;
           $his->save();

           return response()->json(['message' => 'Package successfully scanned into the specified Location', 'status' => 200], 200);
       } catch (\Exception $e) {
           return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
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
            ], [
                'scan_i_package_id.required' => 'Package Id is required.',
                'scan_i_location_id.required' => 'Location Id is required.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 400], 400);
            }

            # check the validation here...
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

            if (empty($post)) {
                return response()->json(['message' => 'The Location Scanned is incorrect, this item is not from this Location ID', 'status' => 400], 400);
            }

            # chek alreday scan out or not...
            $chk = (new Post)->newQuery();
            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' ,$request->scan_i_location_id]]);
            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereNotIn('pes.key_value', ['IS-07', 'IS-02', 'IS-01']);
            $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$Check->isEmpty()) {
                return response()->json(['message' => 'This item is already included in the Dispatch list.', 'status' => 400], 400);
            }

            # chek alreday ebay id...
            $chk_id = (new Post)->newQuery();
            $chk_id->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_id->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number")
            );
            $Check_or = $chk_id->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
            if (($Check_or->order_number == '' || $Check_or->order_number == null)){
                return response()->json(['message' => 'The package cannot be scanned out as it is not linked to any eBay Order ID.', 'status' => 400], 400);
            }

            /*# chek alreday scan out or not...
            $chk = (new Post)->newQuery();
            $chk->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereNotIn('pes.key_value', ['IS-07', 'IS-02', 'IS-01']);
            $Check = $chk->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get();
            if (!$Check->isEmpty()) {
                return response()->json(['message' => 'This item is not available to scan out.', 'status' => 400], 400);
            }

            # chek alreday ebay id...
            $chk_id = (new Post)->newQuery();
            $chk_id->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $chk_id->select(
                DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number') as order_number")
            );
            $Check_or = $chk_id->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
            if (($Check_or->order_number == '' || $Check_or->order_number == null)) {
                return response()->json(['message' => 'This item is already included in the Dispatch list.', 'status' => 400], 400);
            }*/
            
            set_post_key_value($post->id, 'scan_out_date', date('Y-m-d'));
            set_post_key_value($post->id, 'scan_out_time', date('H:i:s'));
            set_post_key_value($post->id, 'scan_out_user', Auth::user()->name);
            set_post_key_value($post->id, 'order_status', 'IS-03');

            $post->location_id      = $data['scan_i_location_id'];
            $post->package_id      = $data['scan_i_package_id'];
            $post->save();

            # store the user log...
            $his = new StatusHistory;
            $his->post_id = $post->id;
            $his->addition_info = 'Scan out to dispatch detail.';
            $his->type = 'mob-scan-out';
            $his->status_date = date('Y-m-d');
            $his->status_time = date('H:i:s');
            $his->user = Auth::user()->name;
            $his->local_time = $data['create_system_time']; // Fetch local time;
            $his->save();

            if ($request->filled('form_type') && $request->form_type == 'combined') {
                $flag = false;
                $itm = [];

                $chk_order = (new Post)->newQuery();
                $chk_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
                $chk_order->select(
                    DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number")
                );
                $ord = $chk_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();
                // dd($ord);

                # get the item based on the ebay order id...
                $order = (new Post)->newQuery();
                $order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','order_number'],['p1.key_value', '=' , $ord->order_number ?? '']]);
                $order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-07']);
                $order->select(
                    DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_i_package_id') as scan_i_package_id")
                );
                $ebay_order = $order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->get()->toArray();
                // dd($ebay_order);
                if (count($ebay_order) > 0) {
                    foreach ($ebay_order as $eb) {
                        if ($eb['scan_i_package_id'] != $request->scan_i_package_id) {
                            $flag = true;
                            array_push($itm, $eb['scan_i_package_id']);
                        }
                    }
                }

                if ($flag) {
                    $msg = "This package is part of eBay Order ID: ".$ord->order_number." and includes combine packages with the same name and address.:- <b>".implode(', ', $itm)."</b>";
                    return response()->json(['message' => $msg, 'status' => 200], 200);
                }
            }

            return response()->json(['message' => 'Package successfully scanned out of the specified location', 'status' => 200], 200);
         } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
         }
    }

    /**
     * store dispatch order
     */
    public function dispatchPackageStore(Request $request){
        try {
            $data = $request->only(['authorized_by', 'create_system_time', 'scan_i_package_id', 'scan_i_location_id', 'tracking_number']);
            $validator = Validator::make($request->all(), [
                'scan_i_package_id' => 'required',
                'scan_i_location_id' => 'required',
                'tracking_number' => 'required',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ], [
                'images.required' => 'You must upload at least one image.',
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 2MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 400], 400);
            }

            // code...
            $location = (new Post)->newQuery();
            $location->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
            $loc = $location->where(['posts.post_type' => 'rack'])->first();
            
            if(empty($loc)){
                return response()->json(['message' => 'Location ID not found. Please contact the warehouse supervisor to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again', 'status' => 400], 400);
            }

            if($request->file('images')){
                $data['dispatch_images'] = [];
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

            $count = 0;
            if (count($data['scan_i_package_id']) > 0) {
                $flag = false;
                $itm = [];

                #..same order id have multiple item validation here...
                foreach ($data['scan_i_package_id'] as $key => $package_id) {
                    $get_order = (new Post)->newQuery();
                    $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $package_id]]);
                    $get_order->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number")
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
                    return response()->json(['message' => $msg, 'status' => 400], 400);
                }

                #...insert data here...
                foreach ($data['scan_i_package_id'] as $key => $package_id) {
                    $get_order = (new Post)->newQuery();
                    $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $package_id]]);
                    $get_order->leftJoin('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status']])->whereIn('pes.key_value', ['IS-03']);
                    $get_order->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_number' ORDER BY id DESC LIMIT 1) as item_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_quantity' ORDER BY id DESC LIMIT 1) as item_quantity"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'shipping_carrier_code' ORDER BY id DESC LIMIT 1) as carrier_code"),
                        'posts.id'
                    );
                    $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first()->toArray();
                    if(empty($post)){
                        continue;
                    }

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

                    Post::whereId($post['id'])->update(['location_id' => $data['scan_i_location_id']]);

                    # push the shippment detail on ebay...
                    $cr_code = (!empty($request->carrier_code)) ? $request->carrier_code : $post['carrier_code'];
                    $this->pushTrackingNumber($post, $cr_code, $data['tracking_number']);

                    # store the user log...
                    $his = new StatusHistory;
                    $his->post_id = $post['id'];
                    $his->addition_info = 'Ready for dispatch into dispatch detail.';
                    $his->type = 'mob-dispatch';
                    $his->status_date = date('Y-m-d');
                    $his->status_time = date('H:i:s');
                    $his->user = Auth::user()->name;
                    $his->local_time = $data['create_system_time']; // Fetch local time;
                    $his->save();

                    $count += $count + 1;
                }
            }

            return response()->json(['message' => 'Package successfully moved to dispatch screen', 'total' => $count, 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
        }
    }


    /**
     * combined dispatch and cancelled package
     */
    public function cancelPackage(Request $request){
        try {
            $data = $request->all();
            // dd($data);
            if (count($data['order_ids']) > 0) {
                foreach ($data['order_ids'] as $key => $value) {
                    set_post_key_value($value, 'scan_cancel_date', date('Y-m-d'));
                    set_post_key_value($value, 'scan_cancel_time', date('H:i:s'));
                    set_post_key_value($value, 'scan_cancel_user', Auth::user()->name);
                    set_post_key_value($value, 'order_status', 'IS-06');
                    set_post_key_value($value, 'reason', $request->reason);

                    # store the user log...
                    $his = new StatusHistory;
                    $his->post_id = $value;
                    $his->addition_info = 'Scan out into cancelled dispatch.';
                    $his->type = 'mob-cancelled';
                    $his->status_date = date('Y-m-d');
                    $his->status_time = date('H:i:s');
                    $his->user = Auth::user()->name;
                    $his->save();
                }
            }

            return response()->json(['message' => 'Package successfully cancelled - Please check the Cancelled screen for details', 'status' => 201], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), 'status' => 200], 200);
        }
    }

    /**
     * Location List
     */
    public function locationList(){
        $location = (new Post)->newQuery();
        $location->select(
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'measurement') as measurement"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'location_id') as location_id"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'length') as length"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'width') as width"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'height') as height"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'weight') as weight"),
            'posts.*'
        );
        $loc = $location->where(['post_type' => 'rack'])->get()->toArray();

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $loc], 200);
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
                    'images' => 'required|array',
                    'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                ],[
                    'scan_i_location_id.required' => 'Location Id is required.',
                    'images.required' => 'You must upload at least one image.',
                    'images.*.image' => 'Each file must be a valid image.',
                    'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                    'images.*.max' => 'Each image must not exceed 2MB in size.',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first(), 'status' => 400], 400);
                }

                // code...
                $get_order = (new Post)->newQuery();
                $get_order->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['scan_i_location_id']]]);
                $posts = $get_order->where(['posts.post_type' => 'rack'])->first();
                
                if(empty($posts)){
                    return response()->json(['message' => 'Location ID not found. Please contact the warehouse supervisor to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again', 'status' => 400], 400);
                }

                # upload images here....
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

                if (count($data['order_ids']) > 0) {
                    $flag = false;
                    $itm = [];

                    #..validation here...
                    foreach ($data['order_ids'] as $key => $package_id) {
                        $get_order = (new Post)->newQuery();
                        $get_order->where(['post_type' => 'scan'])->where('id', $package_id);
                        $get_order->select(
                            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number")
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
                        return response()->json(['message' => $msg, 'status' => 400], 400);
                    }

                    # get the package with the id...
                    $get_package = (new Post)->newQuery();
                    $get_package->where(['post_type' => 'scan'])->whereIN('id', $data['order_ids']);
                    $get_package->select(
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_name' ORDER BY id DESC LIMIT 1) as ship_to_name"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_email' ORDER BY id DESC LIMIT 1) as ship_to_email"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_phone' ORDER BY id DESC LIMIT 1) as ship_to_phone"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_address_1' ORDER BY id DESC LIMIT 1) as ship_to_address_1"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_city' ORDER BY id DESC LIMIT 1) as ship_to_city"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_state' ORDER BY id DESC LIMIT 1) as ship_to_state"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_zip' ORDER BY id DESC LIMIT 1) as ship_to_zip"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ship_to_country' ORDER BY id DESC LIMIT 1) as ship_to_country"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'ebay_order_currency' ORDER BY id DESC LIMIT 1) as ebay_order_currency"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'order_number' ORDER BY id DESC LIMIT 1) as order_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_number' ORDER BY id DESC LIMIT 1) as item_number"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'item_quantity' ORDER BY id DESC LIMIT 1) as item_quantity"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'create_waybill_status') as create_waybill_status"),
                        DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'shipping_carrier_code' ORDER BY id DESC LIMIT 1) as carrier_code"),
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
                            return response()->json(['message' => 'These orders do not use the same shipping address. Please try again with matching shipping addresses.', 'status' => 400], 400);
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
                        return response()->json(['message' => 'No Warehouse found.', 'status' => 400], 400);
                    }

                    if($request->weight <= 1){
                        $request->request->add(['carrier' => 'USPS']);
                        $data['length'] = '10';
                        $data['width'] = '8';
                        $data['height'] = '1';
                    }

                    if($request->weight > 1){
                        $request->request->add(['carrier' => 'UPS']);
                    }
                    
                    # check the carrier...
                    $carrieravailable =  Carrier::where('countrycode', "Like", "%" .$one['ship_to_country']. "%")->where('name', "Like", "%" . $one['carrier_code'] . "%")->first();
                    if ($request->has('carrier') && $request->filled('carrier')) {
                        $carrieravailable = Carrier::where('countrycode', "Like", "%" . $one['ship_to_country'] . "%")->where('name', $request->carrier)->first();
                    }

                    if(!$carrieravailable) {
                        return response()->json(['message' => 'No Carrier found.', 'status' => 400], 400);
                    }

                    $request->request->add(['servicecode' => $carrieravailable->code]);
                    $request->request->add(['carrier_name' => $carrieravailable->name]);
                    $request->request->add(['unit_type' => $carrieravailable->unit_type]);

                    $no_of_pakg = 0 ;
                    $randomnumber =  rand ( 100 , 9999 );
                    $sq_rg_no = 'WMS-'.date('mdY').'-'.$randomnumber;
                    $waybill_array = $this->createSopifyOrderWaywillRequest($sq_rg_no, $one, $request, $carrieravailable);
                    // dd($waybill_array);

                    $js_data = json_encode($waybill_array);
                    foreach ($final as $key => $value) {
                        set_post_key_value($value['id'], 'create_waywill_request', $js_data);
                    }

                    $rtn_msg = "We're experiencing a temporary delay with the carrier API or a slow connection. Please try reprocessing this package in 1-5 minutes. If the issue continues, kindly reach out to the Supervisor for further assistance";
                    # create waybill api here...
                    if(!isset($one['create_waybill_status'])){
                        $create_response = $this->createShopifyOrderWaywillResponse($js_data);
                        $create_data = json_decode($create_response);
                        
                        if (empty($create_data)) {
                            return response()->json(['message' => $rtn_msg, 'status' => 400], 400);
                        } elseif (isset($create_data->messageType) && $create_data->messageType == 'Error') {            
                            foreach ($final as $key => $value) {
                                set_post_key_value($value['id'], 'create_waybill_response', $create_response);
                            }
                            return response()->json(['message' => $rtn_msg, 'status' => 400], 400);
                        } else{
                            foreach ($final as $key => $value) {
                                set_post_key_value($value['id'], 'create_waybill_status', $create_response);
                                set_post_key_value($value['id'], 'waybillNumber', $create_data->waybillNumber);
                                set_post_key_value($value['id'], 'ebay_sequence_order_number', $sq_rg_no);
                                set_post_key_value($value['id'], 'label_url', $create_data->labelURL);
                            }
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

                    foreach ($final as $key => $value) {
                        set_post_key_value($one['id'], 'generate_waybill_request', json_encode($g_arr));
                    }
                    $gr_response = $this->generateShopifyOrderWaywillResponse($g_arr);            
                    $gr_json = json_decode($gr_response);
                    
                    if (empty($gr_json)) {                
                        return response()->json(['message' => $rtn_msg, 'status' => 400], 400);
                    } elseif (isset($gr_json->messageType) && $gr_json->messageType == 'Error') {
                        set_post_key_value($one['id'], 'generate_waybill_response', $gr_response);
                        return response()->json(['message' => $rtn_msg, 'status' => 400], 400);
                    }

                    $label = reset($gr_json->labelDetailList);

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

                    return response()->json(['pdf_url' => $label->artifactUrl, 'message' => 'Label generated successfully.', 'track_id' => $gr_json->carrierWaybill, 'status' => 200], 200);
                }
            } else {
                if (count($data['order_ids']) > 0) {
                    foreach ($data['order_ids'] as $key => $value) {
                        set_post_key_value($value, 'scan_cancel_date', date('Y-m-d'));
                        set_post_key_value($value, 'scan_cancel_time', date('H:i:s'));
                        set_post_key_value($value, 'scan_cancel_user', Auth::user()->name);
                        set_post_key_value($value, 'order_status', 'IS-06');
                        set_post_key_value($value, 'reason', $request->reason);

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
                }
            }

            return response()->json(['message' => 'Package successfully cancelled - Please check the Cancelled screen for details', 'status' => 200], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
        }
    }

    /**
     * Generate the label here ebay order
     */
    public function generateLabel(Request $request){
        try {
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
            
            $carrieravailable =  Carrier::where('countrycode', "Like", "%" . $order_postmeta['ship_to_country'] . "%")->first();
            if ($request->has('carrier') && $request->filled('carrier')) {
                $carrieravailable = Carrier::where('countrycode', "Like", "%" . $order_postmeta['ship_to_country'] . "%")->where('code', $request->carrier)->first();
            }

            if(!$carrieravailable) {
                return response()->json(['message' => 'No Carrier found.', 'status' => 200], 200);
            }

            $request->request->add(['servicecode' => $carrieravailable->code]);
            $request->request->add(['carrier_name' => $carrieravailable->name]);
            $request->request->add(['unit_type' => $carrieravailable->unit_type]);

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
                $rtn_msg = 'Generate waywill Api:- This return method is currently unavailable. Please try again later.';
                // $rtn_msg = 'Generate waywill Api:- '.$gr_json->message;
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

        $order_postmeta['ship_to_phone'] = (!empty($order_postmeta['ship_to_phone']) && $order_postmeta['ship_to_phone'] != 'None') ? $order_postmeta['ship_to_phone'] : 9999999999;
        $phone = str_replace( array( '-', '(', ')'), '', $order_postmeta['ship_to_phone']);
        if(empty(strpbrk($phone, '+'))){
            $phone = '+1'.$phone;
        }

        $order_postmeta['ship_to_email'] = (!empty($order_postmeta['ship_to_email']) && $order_postmeta['ship_to_email'] != 'None') ? $order_postmeta['ship_to_email'] : 'customer@example.com';
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
                "ConsigneeAddress" => $order_postmeta['ship_to_address_1'],
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
        $order_data_by_id['carrier'] = Carrier::get()->toArray();
        $order_data_by_id['warehouse'] = Warehouse::get()->toArray();

        return response()->json(['status' => true, 'msg' => 'Success', 'data' => $order_data_by_id], 200);
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
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
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
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_date') as scan_dispatch_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_time') as scan_dispatch_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_dispatch_user') as scan_dispatch_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'tracking_number') as tracking_number"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_in_images') as scan_in_images"),
            DB::raw("(select status_date from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_date"),
            DB::raw("(select status_time from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_time"),
            DB::raw("(select user from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as rack_user"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);

        // Format response
        $response = [
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "current_page" => $data->currentPage(),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
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
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ], [
                'images.*.image' => 'Each file must be a valid image.',
                'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
                'images.*.max' => 'Each image must not exceed 10MB in size.',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'status' => 400], 400);
            }

            // check the locaiton id...
            $location = (new Post)->newQuery();
            $location->join('post_extras AS pe', 'posts.id', '=', 'pe.post_id')->where([['pe.key_name','location_id'],['pe.key_value', '=' , $data['new_location_id']]]);
            $location_data = $location->where(['posts.post_type' => 'rack'])->first();
            
            if(empty($location_data)){
                return response()->json(['message' => 'New Location ID not found. Please contact the warehouse supervisor to check ShipCycle whether this item is assigned to a Pallet and to a location and then try again', 'status' => 400], 400);
            }

            # check the package id or locaiton id
            $get_order = (new Post)->newQuery();
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
            $post = $get_order->where('posts.post_type', 'scan')->orderBy('posts.id', 'DESC')->first();

            if (empty($post)) {
                return response()->json(['message' => 'Package Id does not exists in this Location', 'status' => 400], 400);
            }

            if ($post) {
                if($request->file('images')){
                    $data['dispatch_images'] = [];
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

            return response()->json(['message' => 'Action successfully.', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
        }
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
            $get_order->join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where([['p1.key_name','scan_i_package_id'],['p1.key_value', '=' , $request->scan_i_package_id]]);
        }

        if($request->has('scan_i_location_id') && $request->filled('scan_i_location_id')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','scan_i_location_id'],['p2.key_value', '=' , $request->scan_i_location_id]]);
        }

        if($request->filled('order_id')){
            $get_order->where('posts.id', $request->order_id);
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
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_date') as scan_out_date"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_time') as scan_out_time"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_out_user') as scan_out_user"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'scan_status') as scan_status"),
            DB::raw("(select key_value from post_extras where posts.id = post_extras.post_id and post_extras.key_name = 'reason') as reason"),
            DB::raw("(select status_date from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_date"),
            DB::raw("(select status_time from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as status_time"),
            DB::raw("(select user from status_histories where posts.id = status_histories.post_id and status_histories.type = 'rack' ORDER BY id DESC
            LIMIT 1) as rack_user"),
            'posts.*'
        );

        $sort = ($request->filled('sort')) ? $request->sort : 'ASC';
        $data = $get_order->orderBy('posts.created_at', $sort)->paginate($this->perPage);

        // Format response
        $response = [
            "data" => $data->items(),
            "base_path" => asset('public/uploads', $secure = null),
            "current_page" => $data->currentPage(),
            "first_page_url" => $data->url(1),
            "from" => $data->firstItem(),
            "last_page" => $data->lastPage(),
            "last_page_url" => $data->url($data->lastPage()),
            "links" => $this->generateLinks($data),
            "next_page_url" => $data->nextPageUrl(),
            "path" => $data->path(),
            "per_page" => $data->perPage(),
            "prev_page_url" => $data->previousPageUrl(),
            "to" => $data->lastItem(),
            "total" => $data->total(),
        ];

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $response], 200);
    }
}
