<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\defaultMail;
use Illuminate\Http\Request;
use App\User;
use App\Models\UserOwnerMapping;
use App\Models\Warehouse;
use Config;
use Validator;
use Mail;
use Auth;
use DB;
use App\Models\ReverseLogisticWaybill;
use App\Models\PalletDeatil;
use App\Models\PackageDetail;

use Request as RequestsUrl;
use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Input;
use Excel;
use App\Exports\PalletExport;

use App\Models\Post;
use App\Models\PostExtra;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PalletController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $guard = 'admin';

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index(){
        if(Auth::user()->user_type_id!=1){ return redirect(getDashboardUrl()['dashboard']); }
        $users = User::where('user_type_id',2)->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        return view('pages.admin.sub-admin.list',compact('users'));
    }


    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function addPallet(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $lists = [];
        if($request->all()){
            $obj = new ReverseLogisticWaybill;
            $lists = $obj->getProcessedList($request);
        }

        return view('pages.admin.pallet.add', compact('client_list', 'lists'));
    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function palletStore(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required',
                // 'tracking_id' => 'required',
                // 'fright_charges' => 'required',
                // 'custom_duty' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $pallet = new PalletDeatil();
            $pallet->type = 'S';
            $pallet->pallet_id = $request->pallet_id;
            $pallet->client_id = $request->client_id;
            $pallet->warehouse_id = $request->warehouse_id;
            $pallet->shipping_type_id = $request->shipping_type_id;
            $pallet->rate = $request->rate;
            $pallet->carrier = $request->carrier;
            $pallet->tracking_id = $request->tracking_id;
            $pallet->fright_charges = $request->fright_charges;
            $pallet->custom_duty = $request->custom_duty;
            $pallet->save();

            return redirect(route('admin.pallet.list'))->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        
    }

    public function palletEdit(PalletDeatil $pallet){
        $obj = new \App\Models\ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = \App\User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $items = $pallet->items;
        $pallet->withMeta();

        $warehouse = Warehouse::get();

        $query = (new PackageDetail)->newQuery();
        $query->where('pallet_id',$pallet->pallet_id);
        $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);

        if ($pallet->pallet_type == 'Closed') {
            return view('pages.admin.pallet.close-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'items', 'pallet', 'warehouse', 'orders'));
        } elseif ($pallet->pallet_type == 'Shipped') {
            return view('pages.admin.pallet.shipped-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'items', 'pallet', 'warehouse', 'orders'));
        } else {
            return view('pages.admin.pallet.edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'items', 'pallet', 'warehouse', 'orders'));
        }
    }

    /**
     * Inprocess and closed Pallet data update
     * 
     **/
    public function palletUpdate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $pallet = PalletDeatil::find($request->p_id);
            $pallet->pallet_id = $request->pallet_id;
            $pallet->client_id = $request->client_id;
            $pallet->warehouse_id = $request->warehouse_id;
            $pallet->return_type = $request->return_type;
            $pallet->shipment_status = 'Shipment Completed';

            if ($request->has('pallet_type') && $request->filled('pallet_type')) {
                // code...
                $pallet->pallet_type = $request->pallet_type;
            }

            $pallet->save();

            $pkg = PackageDetail::where('pallet_id', $pallet->pallet_id)->update(['shipment_status' => 'Shipment Completed']);

            if ($request->has('fr_warehouse_id') && $request->filled('fr_warehouse_id')) {
                $pallet->setMeta('fr_warehouse_id', $request->fr_warehouse_id);
            }

            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
             return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Shipped Pallet data update
     * 
     **/
    public function shippedPalletUpdate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $pallet = PalletDeatil::find($request->p_id);
            $pallet->pallet_id = $request->pallet_id;
            $pallet->client_id = $request->client_id;
            $pallet->warehouse_id = $request->warehouse_id;
            $pallet->shipping_type_id = $request->shipping_type_id;
            $pallet->rate = $request->rate;
            $pallet->carrier = $request->carrier;
            $pallet->tracking_id = $request->tracking_id;
            $pallet->fright_charges = $request->fright_charges;
            $pallet->custom_duty = $request->custom_duty;
            $pallet->custom_vat = $request->custom_vat;
            $pallet->export_vat_number = $request->export_vat_number;
            $pallet->import_duty_paid = $request->import_duty_paid;
            $pallet->import_vat_paid = $request->import_vat_paid;
            $pallet->import_vat_number = $request->import_vat_number;
            $pallet->weight_unit_type = $request->weight_unit_type;
            $pallet->weight_of_shipment = $request->weight_of_shipment;
            $pallet->hawb_number = $request->hawb_number;
            $pallet->mawb_number = $request->mawb_number;
            $pallet->manifest_number = $request->manifest_number;
            $pallet->return_type = $request->return_type;
            $pallet->rtn_import_entry_number = $request->rtn_import_entry_number;
            $pallet->rtn_import_entry_date = $request->rtn_import_entry_date;
            $pallet->export_declaration_number = $request->export_declaration_number;
            $pallet->export_declaration_date = $request->export_declaration_date;
            $pallet->exchange_rate = $request->exchange_rate;
            $pallet->flight_number = $request->flight_number;
            $pallet->flight_date = $request->flight_date;
            // $pallet->pallet_type = $request->pallet_type;
            $pallet->shipment_status = 'Delivered';
            $pallet->save();

            $pkg = PackageDetail::where('pallet_id', $pallet->pallet_id)->update(['shipment_status' => 'Delivered']);

            if ($request->has('fr_warehouse_id') && $request->filled('fr_warehouse_id')) {
                $pallet->setMeta('fr_warehouse_id', $request->fr_warehouse_id);
            }

            if ($request->has('shipment_date') && $request->filled('shipment_date')) {
                $pallet->setMeta('shipment_date', $request->shipment_date);
            }

            if ($request->has('no_of_box') && $request->filled('no_of_box')) {
                $pallet->setMeta('no_of_box', $request->no_of_box);
            }

            if ($request->has('no_of_packages') && $request->filled('no_of_packages')) {
                $pallet->setMeta('no_of_packages', $request->no_of_packages);
            }

            if ($request->has('shipper_name') && $request->filled('shipper_name')) {
                $pallet->setMeta('shipper_name', $request->shipper_name);
            }

            if ($request->has('delivery_date') && $request->filled('delivery_date')) {
                $pallet->setMeta('delivery_date', $request->delivery_date);
            }

            if ($request->has('delivery_time') && $request->filled('delivery_time')) {
                $pallet->setMeta('delivery_time', $request->delivery_time);
            }

            if ($request->has('signed_by') && $request->filled('signed_by')) {
                $pallet->setMeta('signed_by', $request->signed_by);
            }

            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
             return redirect()->back()->with('error', $e->getMessage());
        }   
    }

    /**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function palletShow(PalletDeatil $pallet){
        $query = (new PackageDetail)->newQuery();
        $query->where('pallet_id',$pallet->pallet_id);
        $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);

        $pallet->withMeta();

        if ($pallet->pallet_type == 'Shipped') {
            return view('pages.admin.pallet.shipped-pallet-show', compact('pallet', 'orders'));
        } else {
            return view('pages.admin.pallet.show', compact('pallet', 'orders'));
        } 
    }

    /*
    * Get order list by barcode or tracking id...
    *
    **/
    public function getOrderList(Request $request){
        $lists = '';
        if($request->all()){
            $obj = new ReverseLogisticWaybill;
            $lists = $obj->getProcessedList($request);
        }
        
        $html = '';
        if(!empty($lists)){
            $html = view('pages.admin.pallet.add-pallet',compact('lists'))->render();
        }
        echo json_encode(array('htm'=>$html));
    }

    /*
    * create pallet and update on orders...
    *
    **/
    public function palletByOrders(Request $request){
        try {            
            if ($request->has('pallet_name')) {
                # code...
                $ex_pall = PalletDeatil::where('pallet_id', $request->pallet_name)->first();
                if ($ex_pall) {
                    # code...
                    $pallet = $ex_pall;
                } else {
                    $pallet = new PalletDeatil();
                    $pallet->pallet_id = $request->pallet_name;
                    $pallet->type = 'S';
                    $pallet->return_type = $request->return_type;
                    $pallet->save();
                }

                if ($pallet->id && count($request->pallet_orders) > 0) {
                    # code...
                    foreach ($request->pallet_orders as $key => $value) {
                        # code...
                        // $waywill = ReverseLogisticWaybill::find($value);
                        // $waywill->where('id', $value)->update(array('pallet_id' => $request->pallet_name));

                        $pkg = PackageDetail::find($value);
                        $pkg->where('id', $value)->update(array('pallet_id' => $request->pallet_name));
                    }

                    return redirect(route('admin.pallet.list'))->with('succes', 'Create Pallet Name succcessfully');
                }
            }

            return redirect()->back()->with('error', 'Something wrong.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /*
    * create pallet and update on orders...
    **/
    public function addPalletToOrders(Request $request){
        DB::beginTransaction();
        try {
            if ($request->has('pallet_name') && !empty($request->pallet_name)) {
                $ex_pall = PalletDeatil::where('pallet_id', $request->pallet_name)->first();
                if ($ex_pall) {
                    $pallet = $ex_pall;
                } else {
                    $pallet = new PalletDeatil();
                    $pallet->pallet_id = $request->pallet_name;
                    $pallet->type = 'S';
                    $pallet->return_type = $request->return_type;
                    $pallet->client_id = $request->client_id;
                    $pallet->warehouse_id = $request->warehouse_id;
                    $pallet->pallet_type = $request->pallet_type;
                    $pallet->shipment_status = 'Processed for return';
                    $pallet->save();

                    if ($request->has('fr_warehouse_id') && $request->filled('fr_warehouse_id')) {
                        $pallet->setMeta('fr_warehouse_id', $request->fr_warehouse_id);
                    }
                }

                if ($pallet->id && count($request->pallet_orders) > 0) {
                    foreach ($request->pallet_orders as $key => $value) {
                        # code...
                        // $waywill = ReverseLogisticWaybill::find($value);
                        // $waywill->where('id', $value)->update(array('pallet_id' => $request->pallet_name));

                        $pkg = PackageDetail::find($value);
                        $pkg->where('id', $value)->update(array('pallet_id' => $request->pallet_name, 'shipment_status' => 'Processed for return'));
                    }

                    DB::commit();
                    // return redirect(route('admin.pallet.list'))->with('succes', 'Action succcessfully');
                    return redirect()->back()->with('succes', 'Action succcessfully');
                }
            } else {
                DB::rollback();
                return redirect()->back()->with('error', 'Please select one of the Pallet action.');
            }            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
    * Display Inprocess pallet list data
    * Code by: sanjay
    **/
    public function palletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $list->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        $lists = $list->withMeta()->where(['type' => 'S', 'pallet_type' => 'InProcess'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.list', compact('lists', 'client_list'));
    }

    /**
    * Display Closed pallet list data
    * Code by: sanjay
    **/
    public function closedPalletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $list->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        $lists = $list->withMeta()->where(['type' => 'S', 'pallet_type' => 'Closed'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.list', compact('lists', 'client_list'));
    }

    /**
    * Display Closed pallet list data
    * Code by: sanjay
    **/
    public function shippedPalletList(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = \App\User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $list = (new PalletDeatil)->newQuery();
        if($request->has('client') && $request->filled('client')){
            $list->where('client_id', $request->client);
        }
        if($request->has('pallet_id') && $request->filled('pallet_id')){
            $list->where('pallet_id', $request->pallet_id);
        }
        if($request->has('return_type') && $request->filled('return_type')){
            $list->where('return_type', $request->return_type);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $list->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $list->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        $lists = $list->withMeta()->where(['type' => 'S', 'pallet_type' => 'Shipped'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.pallet.shipped-pallet-list', compact('lists', 'client_list'));
    }


    /*
    * remove pallet and update on orders...
    **/
    public function removeOrdersFromPallet(Request $request){
        DB::beginTransaction();
        try {
            // dd($request->pkg_orders);
            if ($request->has('pkg_orders') && count($request->pkg_orders) > 0) {                
                foreach ($request->pkg_orders as $key => $value) {
                    # code...
                    // $waywill = ReverseLogisticWaybill::find($value);
                    // $waywill->where('id', $value)->update(array('pallet_id' => $request->pallet_name));

                    $pkg = PackageDetail::find($value);
                    $pkg->where('id', $value)->update(array('pallet_id' => null));
                }

                DB::commit();
                return redirect()->back()->with('succes', 'Action succcessfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('error', 'Please select one of the Pallet action.');
            }            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * 
     * 
     **/
    public function downloadPayroll(Request $request){
        try {
            $pallet = PalletDeatil::withMeta()->where('pallet_id', $request->pallet_id)->first();
            if(!$pallet){
                return response()->json(['flag' => true, 'msg' => 'Pallet not found.']);
            }

            // $query = (new PackageDetail)->newQuery();
            // $query->select("package_details.*", 'reverse_logistic_waybills.id as order_id', 'reverse_logistic_waybills.way_bill_number', 'reverse_logistic_waybills.tracking_id', 'reverse_logistic_waybills.rcvd_at_hub_date');
            // $query->join('reverse_logistic_waybills', 'reverse_logistic_waybills.id', '=', 'package_details.reverse_logistic_waybill_id');
            // $query->where('package_details.pallet_id',$request->pallet_id);
            // $pakage = $query->orderBy('package_details.id','desc')->get();

            $pakage = [];
            $query = (new Post)->newQuery();
            $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id', $request->pallet_id);
            $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

            if (count($posts) > 0) {
                $order_data = $this->manageAllVendorOrders($posts);
                $pakage = new Collection($order_data);               
            }

            $data_ar = [];
            if (count($pakage) > 0) {
                $fr = $pallet->meta->fr_warehouse_id ?? '';
                foreach ($pakage as $key => $pkg) {                    
                    $cn = $pkg['coo'] ?? '';

                    if ($pallet->pallet_type == 'Shipped') {
                        $arr = array(
                            $key +1,
                            $pkg['evtn_number'] ?? '',
                            $pkg['_post_id'] ?? '',
                            $pkg['order_number'] ?? '',
                            $pkg['sku'] ?? '',
                            $pkg['tracking_number'] ?? '',
                            $pallet->pallet_id,
                            getWareHouseName($fr),
                            $pallet->warehouse->name ?? 'N/A',
                            $pallet->meta->shipper_name ?? '',
                            $pallet->mawb_number ?? '',
                            $pallet->hawb_number ?? '',
                            $pallet->tracking_id ?? '',
                            $pallet->custom_duty ?? '',
                            $pallet->custom_vat ?? '',
                            $pallet->return_type ?? '',
                            $pkg['tracking_number'] ?? '',
                            "N/A",
                            $pkg['value'] ?? 0,
                            $cn,
                            $pkg['hs_code'] ?? '',
                            '0',
                            '10',
                            '8',
                            '3',
                            '',
                            '',
                            '',
                            $pallet->meta->delivery_date ?? '',
                            $pallet->meta->delivery_time ?? '',
                            $pallet->meta->signed_by ?? '',
                            $pkg['category_name'] ?? '',
                            $pkg['sub_category_name'] ?? ''
                        );
                    } else {
                        $arr = array(
                            $key +1,
                            $pallet->pallet_id,
                            getWareHouseName($fr),
                            $pallet->warehouse->name ?? 'N/A',
                            $pallet->return_type,
                            $pkg['evtn_number'] ?? '',
                            $pkg['_post_id'] ?? '',
                            $pkg['order_number'] ?? '',
                            $pkg['sku'] ?? '',
                            $pkg['tracking_number'] ?? '',
                            $pkg['tracking_number'] ?? '',
                            "N/A",
                            $pkg['value'] ?? 0,
                            $cn,
                            $pkg['hs_code'] ?? '',
                            '0',
                            '10',
                            '8',
                            '3',
                            '' ,
                            '',
                            '',
                            $pkg['category_name'] ?? '',
                            $pkg['sub_category_name'] ?? ''
                        );
                    }                    

                    array_push($data_ar, $arr);
                }
            }
            // dd($data_ar);
            return Excel::download(new PalletExport($data_ar, $pallet->pallet_type), "pallet_order_" . time() . '.xlsx');
        } catch (\Exception $e) {
            return response()->json(['flag' => true, 'msg' => $e->getMessage()]);
        }        
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
                    $order_postmeta['_post_id']    = $order['id'];
                    $order_postmeta['_pallet_id']    = $order['pallet_id'] ?? '';
                    $order_postmeta['_order_date'] = $date_format->toDayDateTimeString();
                    $order_postmeta['process_status'] = $order['process_status'] ?? '' ;
                    $order_postmeta['pallet_id']      = $order['pallet_id'] ?? '' ;

                    foreach ($get_postmeta_by_order_id as $postmeta_row) {
                        $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
                    }
                }

                array_push($order_data, $order_postmeta);
            }
        }

        return $order_data;
    }
}
