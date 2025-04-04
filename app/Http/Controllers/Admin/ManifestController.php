<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Mail\defaultMail;
use App\Mail\MainTemplate;

use App\User;
use App\Models\UserOwnerMapping;
use App\Models\ReverseLogisticWaybill;
use App\Models\PalletDeatil;
use App\Models\PackageDetail;
use App\Models\ShippingPolicy;
use App\Models\Post;
use App\Models\PostExtra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Excel;
use App\Exports\ManifestExport;
use App\Exports\EuropeBladeExport;

use PHPExcel_Cell;
use PHPExcel_IOFactory;

use Request as RequestsUrl;
use Config;
use Validator;
use Mail;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as DBA;

class ManifestController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $guard = 'admin';
    public $perPage = 500;

    public function __construct() {
        $this->middleware('auth:admin');
    }
    
    /**
    * Code by: sanjay
    **/
    public function exportUkManifest(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
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

        $lists = $list->withMeta()->where('type', 'S')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.manifest.export-uk-manifest', compact('lists', 'client_list'));
    }

    public function customerBrokerManifest(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
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

        $lists = $list->withMeta()->where('type', 'S')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.manifest.customer-broker-manifest', compact('lists', 'client_list'));
    }

    public function importUkManifest(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
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

        $lists = $list->withMeta()->where('type', 'S')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.manifest.import-uk-manifest', compact('lists', 'client_list'));
    }

    public function vatReturnManifest(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
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

        $lists = $list->withMeta()->where('type', 'S')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.manifest.vat-return-manifest', compact('lists', 'client_list'));
    }

    public function exportEuropeManifest(Request $request){
        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
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

        $lists = $list->withMeta()->where('type', 'S')->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));

        return view('pages.admin.manifest.export-europe-manifest', compact('lists', 'client_list'));
    }


    ############### manifest show function ##################
    ################################################################
    /**
    * Display Manifest Detail data
    * Code by: sanjay
    **/
    public function exportUkManifestShow(PalletDeatil $pallet){
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.export.uk.show', $pallet));
            $orders = $order_object;
        }

        $pallet->withMeta();

        return view('pages.admin.manifest.export-uk-detail', compact('pallet', 'orders'));
    }

    public function customsManifestShow(PalletDeatil $pallet){
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.cust.broker.show', $pallet));
            $orders = $order_object;
        }

        $pallet->withMeta();
        return view('pages.admin.manifest.custom-broker-detail', compact('pallet', 'orders'));
    }

    public function importUkManifestShow(PalletDeatil $pallet){
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.import.uk.show', $pallet));
            $orders = $order_object;
        }

        $pallet->withMeta();
        return view('pages.admin.manifest.import-uk-detail', compact('pallet', 'orders'));
    }

    public function vatReturnManifestShow(PalletDeatil $pallet){
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.vat.return.show', $pallet));
            $orders = $order_object;
        }

        $pallet->withMeta();
        return view('pages.admin.manifest.vat-return-detail', compact('pallet', 'orders'));
    }

    public function exportEuropeManifestShow(PalletDeatil $pallet){
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        // dd($orders);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.export.europe.show', $pallet));
            $orders = $order_object;
        }

        $pallet->withMeta();
        return view('pages.admin.manifest.export-europe-detail', compact('pallet', 'orders'));
    }

    ############### manifest edit form function ##################
    ################################################################
    public function importUkManifestEdit(PalletDeatil $pallet){
        $obj = new ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $pallet->withMeta();

        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);

        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.import.uk.edit', $pallet));
            $orders = $order_object;
        }

        return view('pages.admin.manifest.import-uk-manifest-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'orders'));
    }

    public function exportUkManifestEdit(PalletDeatil $pallet){
        $obj = new ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $pallet->withMeta();
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.export.uk.edit', $pallet));
            $orders = $order_object;
        }

        return view('pages.admin.manifest.export-uk-manifest-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'orders'));
    }

    public function exportEuropeManifestEdit(PalletDeatil $pallet){
        $obj = new ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $pallet->withMeta();
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.export-europe.edit', $pallet));
            $orders = $order_object;
        }

        return view('pages.admin.manifest.export-europe-manifest-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'orders'));
    }

    public function customerBrokerManifestEdit(PalletDeatil $pallet){
        $obj = new ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $pallet->withMeta();
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);
        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.cust.broker.edit', $pallet));
            $orders = $order_object;
        }

        return view('pages.admin.manifest.customer-broker-manifest-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'orders'));
    }

    public function vatReturnManifestEdit(PalletDeatil $pallet){
        $obj = new ShippingPolicy;

        $shipment_list = $warehouse_list = [];
        if(!empty($pallet->client_id)){
            $shipment_list = $obj->getShipmentCarrierListBYClientId($pallet->client_id,'shipment');
            $warehouse_list = User::find($pallet->client_id)->getWarehouse;
        }

        if(Auth::user()->user_type_id==1){
            $client_list = User::where(['user_type_id'=>3])->get();
        }else if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
        }

        $pallet->withMeta();
        // $query = (new PackageDetail)->newQuery();
        // $query->where('pallet_id',$pallet->pallet_id);
        // $orders = $query->orderBy('id','desc')->paginate(500);

        $orders = [];
        $query = (new Post)->newQuery();
        $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id' ,$pallet->pallet_id);
        $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
        if (count($posts) > 0) {
            $order_data = $this->manageAllVendorOrders($posts);
            $currentPage              = LengthAwarePaginator::resolveCurrentPage();
            $col                      = new Collection($order_data);
            $perPage                  = $this->perPage;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

            $order_object->setPath(route('admin.vat.return.edit', $pallet));
            $orders = $order_object;
        }

        return view('pages.admin.manifest.vat-return-manifest-edit',compact('pallet', 'client_list', 'shipment_list','warehouse_list', 'orders'));
    }


    ############### manifest update form function ##################
    ################################################################
    /** 
    * import uk update detail
    */
    public function importUkManifestUpdate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pallet_id' => 'required',
                'client_id' => 'required',
                'warehouse_id' => 'required'
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
            $pallet->save();

            // return redirect(route('admin.import.uk'))->with('success', 'Action Completed');
            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** 
    * Export uk update detail
    */
    public function exportUkManifestUpdate(Request $request){
        try {            
            $pallet = PalletDeatil::find($request->p_id);
            
            $pallet->export_declaration_number = $request->export_declaration_number;
            $pallet->export_declaration_date = $request->export_declaration_date;
            $pallet->save();

            $pallet->setMeta([
                'parcel_id' => $request->parcel_id,
                'tracking_ref' => $request->tracking_ref,
                'shipper_name' => $request->shipper_name,
                'shipper_vat' => $request->shipper_vat,
                'cpc_bonded' => $request->cpc_bonded,
                'cpc_non_bounded' => $request->cpc_non_bounded,
                'cpc' => $request->cpc,
            ]);

            // return redirect(route('admin.export.uk'))->with('success', 'Action Completed');
            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** 
    * Export uk update detail
    */
    public function customerBrokerManifestUpdate(Request $request){
        try {            
            $pallet = PalletDeatil::find($request->p_id);
            
            /*$pallet->export_declaration_number = $request->export_declaration_number;
            $pallet->export_declaration_date = $request->export_declaration_date;
            $pallet->save();*/

            $pallet->setMeta([
                'declaration_date' => $request->declaration_date,
                'declaration_type' => $request->declaration_type,
                'addtion_declaration_type' => $request->addtion_declaration_type,
                'customs_procedure' => $request->customs_procedure,
                'additional_procedure' => $request->additional_procedure,
                'inco_term' => $request->inco_term,
                'invoice_currency' => $request->invoice_currency,
                'unlo_code' => $request->unlo_code,
                'net_mass' => $request->net_mass,
                'gross_mass' => $request->gross_mass,
                'number_packages' => $request->number_packages,
                'lrn' => $request->lrn,
                'avg_custom_value' => $request->avg_custom_value,
                'unique_id_number' => $request->unique_id_number,
                'container_id_number' => $request->container_id_number,
                'seller_item_ref' => $request->seller_item_ref,
                'internet_hypertext' => $request->internet_hypertext,
                'email_consignee' => $request->email_consignee,
                'id_mother_package' => $request->id_mother_package,
                'consignee_status' => $request->consignee_status,
                'payment_method' => $request->payment_method,
                'postal_marketing' => $request->postal_marketing,
            ]);

            // return redirect(route('admin.cust.broker'))->with('success', 'Action Completed');
            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** 
    * Vat Return update detail
    */
    public function vatReturnManifestUpdate(Request $request){
        try {            
            $pallet = PalletDeatil::find($request->p_id);            
            $pallet->pallet_id = $request->pallet_id;
            $pallet->save();

            $pallet->setMeta([
                'pakage_number' => $request->pakage_number,
                'order_number' => $request->order_number,
                'customer_name' => $request->customer_name,
                'consignee_address' => $request->consignee_address,
                'sku' => $request->sku,
                'item_description' => $request->item_description,
                'country_of_origin' => $request->country_of_origin,
                'awb_number' => $request->awb_number,
                'rg_entry_number' => $request->rg_entry_number,
                'entry_date' => $request->entry_date,
                'rate_of_exchange' => $request->rate_of_exchange,                
            ]);

            // return redirect(route('admin.vat.return'))->with('success', 'Action Completed');
            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** 
    * Export uk update detail
    */
    public function exportEuropeManifestUpdate(Request $request){
        try {            
            $pallet = PalletDeatil::find($request->p_id);
            
            // $pallet->export_declaration_number = $request->export_declaration_number;
            // $pallet->export_declaration_date = $request->export_declaration_date;
            // $pallet->save();

            $pallet->setMeta([
                'parcel_id' => $request->parcel_id,
                'tracking_ref' => $request->tracking_ref,
                'shipper_name' => $request->shipper_name,
                'shipper_vat' => $request->shipper_vat,
                'cpc_bonded' => $request->cpc_bonded,
                'cpc_non_bounded' => $request->cpc_non_bounded,
                'cpc' => $request->cpc,
                'billing_currency' => $request->billing_currency,
                'import_entry_number' => $request->import_entry_number,
                'import_entry_date' => $request->import_entry_date,
                'vat_paid' => $request->vat_paid,
                'duty_paid' => $request->duty_paid,
            ]);

            // return redirect(route('admin.export.europe'))->with('success', 'Action Completed');
            return redirect()->back()->with('success', 'Action Completed');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
    * generate csv
    * Code by: sanjay
    **/
    public function generateManifest(){
        try {
            $data = Input::all();        
            if (isset($data['pallet_id']) && !empty($data['pallet_id'])) {
                $pallet = PalletDeatil::withMeta()->find($data['pallet_id']);
                if(!$pallet){
                    // return redirect()->back();
                    return response()->json(['flag' => true, 'msg' => 'Pallet not found.']);
                }

                // $pakage = PackageDetail::select("package_details.*", 'reverse_logistic_waybills.id as order_id', 'reverse_logistic_waybills.way_bill_number', 'reverse_logistic_waybills.tracking_id', 'reverse_logistic_waybills.rcvd_at_hub_date')
                //     ->join('reverse_logistic_waybills', 'reverse_logistic_waybills.id', '=', 'package_details.reverse_logistic_waybill_id')
                //     ->where('package_details.pallet_id', $pallet->pallet_id)->get();

                $pakage = [];
                $query = (new Post)->newQuery();
                $query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])->where('posts.pallet_id', $pallet->pallet_id);
                $posts = $query->where(['posts.post_type' => 'ebay_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

                if (count($posts) > 0) {
                    $order_data = $this->manageAllVendorOrders($posts);
                    $pakage = new Collection($order_data);               
                }

                $rows = $columnNames = [];
                $data_ar = [];
                $imp_entry_date = ($pallet->getMeta('import_entry_date')) ? date('Y-m-d', strtotime($pallet->getMeta('import_entry_date'))) : '';
                $declaration_date = ($pallet->getMeta('declaration_date')) ? date('Y-m-d', strtotime($pallet->getMeta('declaration_date'))) : '';
                $shipper_name = $pallet->getMeta('shipper_name');
                $shipper_vat = $pallet->getMeta('shipper_vat');
                $cpc_bonded = $pallet->getMeta('cpc_bonded') ?? 'N';
                $cpc_non_bounded = $pallet->getMeta('cpc_non_bounded') ?? 'Y';
                $cpc = $pallet->getMeta('cpc') ?? 'Y';
                $import_entry_number = $pallet->getMeta('import_entry_number');
                $vat_paid = $pallet->getMeta('vat_paid');
                $duty_paid = $pallet->getMeta('duty_paid');
                $export_declaration_date = ($pallet->export_declaration_date) ? date('Y-m-d', strtotime($pallet->export_declaration_date)) : '';
                $export_declaration_number = $pallet->export_declaration_number;
                $fr = $pallet->meta->fr_warehouse_id ?? '';
                
                # for Expoert UK manifest data...
                if (isset($data['manifest_type']) && $data['manifest_type'] == 'export_uk') {
                    if (count($pakage) > 0) {
                        foreach ($pakage as $key => $pkg) {
                            $pw = $itmw = '';
                            $cnt_of_rgn = $pkg['coo'] ?? '';
                            $arr = array(
                                $pkg['_post_id'] ?? '',
                                $pkg['tracking_number'] ?? '',
                                $shipper_name,
                                $shipper_vat,
                                $cpc_bonded,
                                $cpc_non_bounded,
                                $pkg['customer_id'] ?? '',
                                $pkg['customer_name'] ?? '',
                                $pkg['delivery_address1'] ?? '',
                                $pkg['customer_city'] ?? '',
                                $pkg['customer_pincode'] ?? '',
                                $pkg['customer_state'] ?? '',
                                '',
                                $pkg['sku'] ?? '',
                                '',
                                $cnt_of_rgn,
                                $pkg['hs_code'] ?? '',
                                $itmw,
                                $pw,
                                '',
                                $pkg['billing_currency'] ?? '',
                                $pkg['value'] ?? '',
                                $cpc,
                                $export_declaration_number,
                                $export_declaration_date
                            );

                            array_push($data_ar, $arr);
                        }
                    }
                }

                # for UK manifest data...
                if (isset($data['manifest_type']) && $data['manifest_type'] == 'import_uk') {
                    if (count($pakage) > 0) {
                        foreach ($pakage as $pkg) {
                            $fl_date = ($pallet->flight_date) ? date('Y-m-d', strtotime($pallet->flight_date)) : '';
                            $rt_imp_date = ($pallet->rtn_import_entry_date) ? date('Y-m-d', strtotime($pallet->rtn_import_entry_date)) : '';
                            $ex_date = ($pallet->export_declaration_date) ? date('Y-m-d', strtotime($pallet->export_declaration_date)) : '';

                            // $cnt_of_rgn = getCountryOfOrigin($pkg->way_bill_number, $pkg->bar_code);
                            $cnt_of_rgn = $pkg['coo'] ?? '';
                            
                            $arr = array(
                                date('Y-m-d', strtotime($pallet->created_at)),
                                $pallet->pallet_id,
                                $pallet->mawb_number,
                                $pallet->hawb_number,
                                $pallet->manifest_number,
                                $fl_date,
                                $pallet->flight_number,
                                $pallet->rtn_import_entry_number,
                                $rt_imp_date,
                                $pallet->export_declaration_number,
                                $ex_date,
                                $pallet->exchange_rate,
                                '',
                                $pkg['order_number'] ?? '',
                                '',
                                $pkg['sku'] ?? '',
                                $pkg['hs_code'] ?? '',
                                $cnt_of_rgn,
                                '',
                                $cnt_of_rgn,
                                '',
                                $pkg['hs_code'] ?? '',
                                '',
                                '',
                                $pkg['weight'] ?? '',
                                $pkg['package_count'] ?? '',
                                '',
                                '',
                                $pkg['value'] ?? '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '0',                                
                                $pkg['customer_name'] ?? '',
                                $pkg['customer_state'] ?? '',
                                $pkg['customer_city'] ?? '',
                                $pkg['customer_pincode'] ?? '',
                                $pkg['customer_pincode'] ?? '',
                                '0',
                                $pallet->warehouse->name ?? 'N/A',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                ''
                            );

                            array_push($data_ar, $arr);
                        }
                    }
                }

                # for Expoert UK manifest data...
                if (isset($data['manifest_type']) && $data['manifest_type'] == 'export_europe') {
                    if (count($pakage) > 0) {
                        foreach ($pakage as $key => $pkg) {                            
                            $pw = $itmw = '';
                            $cnt_of_rgn = $pkg['coo'] ?? '';

                            $arr = array(
                                $pkg['_post_id'] ?? '',
                                $pkg['tracking_number'] ?? '',
                                $pkg['tracking_number'] ?? '',
                                $pkg['order_number'] ?? '',
                                $shipper_name,
                                $shipper_vat,
                                $cpc_bonded,
                                $cpc_non_bounded,
                                $pkg['customer_id'] ?? '',
                                $pkg['customer_name'] ?? '',
                                $pkg['delivery_address1'] ?? '',
                                $pkg['customer_city'] ?? '',
                                $pkg['customer_pincode'] ?? '',
                                $pkg['customer_state'] ?? '',
                                '',
                                $pkg['sku'] ?? '',
                                '',
                                $cnt_of_rgn,
                                $pkg['hs_code'] ?? '',
                                $itmw,
                                $pw,
                                '',
                                '',
                                $pkg['value'] ?? '',
                                $cpc,
                                $import_entry_number,
                                $imp_entry_date,
                                $vat_paid,
                                $duty_paid
                            );

                            array_push($data_ar, $arr);
                        }
                    }
                }

                # for Expoert UK manifest data...
                if (isset($data['manifest_type']) && $data['manifest_type'] == 'custom_broker') {
                    if (count($pakage) > 0) {
                        foreach ($pakage as $key => $pkg) {
                            $arr = array(
                                $declaration_date,
                                $pallet->meta->declaration_type ?? 'IM',
                                $pallet->meta->addtion_declaration_type ?? 'C',
                                $pallet->meta->customs_procedure ?? '400',
                                $pallet->meta->additional_procedure ?? '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $pkg['customer_name'] ?? '',
                                $pkg['customer_address'] ?? '',
                                $pkg['customer_pincode'] ?? '',
                                $pkg['customer_city'] ?? '',
                                $pkg['customer_state'] ?? '',
                                '',
                                $pallet->meta->inco_term ?? 'DDP',
                                '',
                                $pkg['value'] ?? '',
                                $pallet->meta->unlo_code ?? 'GB',
                                '',
                                '',
                                $pkg['hs_code'] ?? '',
                                '',
                                '',
                                '',
                                $pallet->meta->lrn ?? '',
                                $pkg['tracking_number'] ?? '',
                                $pallet->meta->avg_custom_value ?? '',
                                $pallet->meta->unique_id_number ?? '',
                                $pallet->meta->container_id_number ?? '',
                                $pallet->meta->seller_item_ref ?? '',
                                $pallet->meta->internet_hypertext ?? '',
                                $pallet->meta->email_consignee ?? '',
                                $pallet->pallet_id,
                                $pallet->meta->consignee_status ?? '',
                                '',
                                $pallet->meta->postal_marketing ?? ''
                            );

                            array_push($data_ar, $arr);
                        }
                    }
                }

                # for Expoert UK manifest data...
                if (isset($data['manifest_type']) && $data['manifest_type'] == 'vat_return') {
                    if (count($pakage) > 0) {
                        foreach ($pakage as $key => $pkg) {
                            $rcv_date = '';  
                            $cnt_of_rgn = $pkg['coo'] ?? '';

                            $arr = array(
                                $rcv_date,
                                $pkg['tracking_number'] ?? '',
                                $pkg['order_number'] ?? '',
                                $pkg['customer_name'] ?? '',
                                $pkg['customer_state'] ?? '',
                                'MG',
                                $pkg['sku'] ?? '',
                                '',
                                $cnt_of_rgn,
                                '',
                                $pkg['_order_date'] ?? '',
                                '',
                                '',
                                $pallet->pallet_id,
                                $pkg['order_number'] ?? '',
                                '',
                                '',
                                $pkg['hs_code'] ?? '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                ''
                            );

                            array_push($data_ar, $arr);
                        }
                    }
                }

                return Excel::download(new ManifestExport($data_ar, $data['manifest_type']), $data['manifest_type']."_manifest_" . time() . '.xlsx');
            } else {
                # code...
                // return redirect()->back();
                return response()->json(['flag' => true, 'msg' => 'Pallet Id not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['flag' => true, 'msg' => $e->getMessage()]);
        }
    }

    /**
    *
    * Import Uk columns
    */
    public function getImportUkColoumnName(){
        $clmn_arr = [
            'Manifest Date',
            'Pallet ID',
            'MAWB Number',
            'HAWB#',
            'Manifest #',
            'Flight Date',
            'Flight Number',
            'Return Import Entry Number',
            'Return Import Entry Date',
            'Export Declaration Number',
            'Export Declaration Date',
            'Exchange Rate',
            'Line Number',
            'Order Number',
            'Product Code',
            'SKU',
            'HS Code',
            'Dest Cntry',
            'Disp Cntry',
            'Orig Cntry',
            'Goods Description',
            'Commodity Code',
            'Item Gross Mass',
            'CPC',
            'Item Net Mass',
            'Quantity',
            'Value',
            'Currency',
            'Selling Price',
            'Item Third Quantity',
            'Item Stat Val.',
            'UN Dangerous Goods Code',
            'Packages Number',
            'Packages Kind',
            'Packages Marks and Numbers',
            'Document Code',
            'Document Reference',
            'Document Status',
            'Consignor ID',
            'Consignor Name',
            'Consignor Street',
            'Consignor City',
            'Consignor Postcode',
            'Consignor Country',
            'Consignee ID',
            'Consignee Name',
            'Consignee Street',
            'Consignee City',
            'Consignee Postcode',
            'Consignee Country',
            'AI Statement Code',
            'AI Statement Text',
            'AI Statement Code 2',
            'AI Statement Text 2',
            'AI Statement Code 3',
            'AI Statement Text 3',
            'AI Statement Code 4',
            'AI Statement Text 4',
            'AI Statement Code 5',
            'AI Statement Text 5',
            'Prev Doc Class 1',
            'Prev Doc Type 1',
            'Prev Doc Reference 1',
            'Prev Doc Class 2',
            'Prev Doc Type 2',
            'Prev Doc Reference 2',
            'Prev Doc Class 3',
            'Prev Doc Type 3',
            'Prev Doc Reference 3',
            'Commodity Add Code',
            'Serial no.',
            'Purchase Order',
            'Invoice Number',
            'Customer Defined 1',
            'Customer Defined 2',
            'Document Code 2',
            'Document Reference 2',
            'Document Status 2',
            'Document Code 3',
            'Document Reference 3',
            'Document Status 3',
            'Document Code 4',
            'Document Reference 4',
            'Document Status 4',
            'Document Code 5',
            'Document Reference 5',
            'Document Status 5'
        ];

        return $clmn_arr;
    }

    /**
    *
    * Export Uk columns
    */
    public function getExportUkColumnName(){
        $clmn_arr = [
            'ParcelID',
            'TrackingRef',
            'ShipperName',
            'ShippersVAT',
            'CPC:CustomsBonded',
            'CPC:NonCustomsBonded',
            'CustomerId',
            'CustomerName',
            'Delivery.AddressLine1',
            'Delivery.AddressLine2',
            'Delivery.Postcode',
            'Delivery.CountryCode',
            'Name',
            'Item ref',
            'TotalQty',
            'Country of Origin',
            'Customs Code',
            'ItemWeightKg',
            'ParcelWeightKg',
            'Dimensions',
            'Billing.Currency',
            'Price',
            'CPC',
            'Export Declaration Number',
            'Export Declaration Date'
        ];

        return $clmn_arr;
    }

    /**
    *
    * Customs Uk columns
    */
    public function getCustomsColumnName(){
        $clmn_arr = [
            'DeclarationDate',
            'DeclarationType',
            'AdditionalDeclarationType',
            'CustomsProcedure',
            'AdditionalProcedure',
            'GoodsItemNumber',
            'CountryOriginCode',
            'ConsignorName',
            'ConsignorStreetAndNr',
            'ConsignorCity',
            'ConsignorPostcode',
            'ConsignorCountry',
            'ConsigneeName',
            'ConsigneeStreetAndNr',
            'ConsigneePostcode',
            'ConsigneeCity',
            'ConsigneeCountry',
            'ConsigneeID',
            'INCOTerm',
            'InvoiceCurrency',
            'ItemPrice_Amount',
            'UNLOcode',
            'NetMassKg',
            'GrossMassKg',
            'CommodityCodeCombinedNomenclatureCode',
            'DescriptionGoods',
            'TypePackage',
            'NumberPackages',
            'LRN',
            'TrackingNumber',
            'UseAverageCustomsValue',
            'UniqueIDNumber',
            'ContainerIDNumber',
            'SellerItemReference',
            'InternetHyperTextLinkItem',
            'EmailConsignee',
            'IDMotherPackage',
            'ConsigneeStatus',
            'MethodPayment',
            'PostalMarking'
        ];

        return $clmn_arr;
    }

    /**
    *
    * Vat Return Uk columns
    */
    public function getVatColumnName(){
        $clmn_arr = [
            'DATE OF  RETURN',
            'PKG NO',
            'Order Number',
            'CUSTOMER NAME',
            'CONSIGNEE ADDRESS',
            'SUPPLIER NAMES',
            'SKU#',
            'ITEM DESCRIPTION',
            'COUNTRY OF ORIGIN',
            'COMMENTS',
            'DATE  RETURNED',
            'CONF ORDER',
            'AWB NUMBER',
            'Pallet NUMBER',
            'RG Reference Number',
            'RG TO COMPLETE ENTRY NO',
            'ENTRY DATE',
            'HSCODE',
            'VALUE',
            'RATE OF EXCHANGE',
            'VALUE  EUR',
            'DUTY RATE',
            'DUTY',
            'VALUE + DUTY',
            'VAT',
            'TOTAL  RECLAIM',
            'TYPE OF VALUE',
            'IMPORT  TARIFF CPC'
        ];

        return $clmn_arr;
    }

    /**
    *
    * Export Europe Uk columns
    */
    public function getExportEuropeColumnName(){
        $clmn_arr = [
            'ParcelID',
            'TrackingRef',
            'Supplier Code',
            'Order No',
            'ShipperName',
            'ShippersVAT',
            'CPC:CustomsBonded',
            'CPC:NonCustomsBonded',
            'CustomerId',
            'CustomerName',
            'Delivery.AddressLine1',
            'Delivery.AddressLine2',
            'Delivery.Postcode',
            'Delivery.CountryCode',
            'Name',
            'Item ref',
            'TotalQty',
            'Country of Origin',
            'Customs Code',
            'ItemWeightKg',
            'ParcelWeightKg',
            'Dimensions',
            'Billing.Currency',
            'Price',
            'CPC',
            'Import Entry Number',
            'Import Entry Date',
            'VAT Paid',
            'Duty Paid'
        ];

        return $clmn_arr;
    }

    public function b2cLayoutUpload(){        
        return view('pages.admin.manifest.b2c-layout');
    }

    public function b2cLayoutStore(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'layout_file' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $file      = Input::file('layout_file')->getClientOriginalName();
            $baseFilename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'xlsx' || $extension == 'xls') {
                $inputFileName = Input::file('layout_file');
                
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

                // create a uqique variation for the similar product
                if (!is_array($namedDataArray) || empty($namedDataArray)) {
                    return redirect()->back()->with('error', 'No data found.');
                }

                # loop here...
                foreach ($namedDataArray as $key => $layouts) {
                    # code...
                    foreach ($layouts as $k => $data) {
                        # code...
                        // echo $item_key = preg_replace("/[^a-zA-Z]/", "", $k);
                        // echo ",";
                    }
                }
                dd($namedDataArray);
                
            } else {
                return redirect()->back()->with('error', 'Wrong Extension');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
