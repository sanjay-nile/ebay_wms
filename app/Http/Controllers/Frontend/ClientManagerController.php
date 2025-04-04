<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PackageDetail;
use App\Models\UserOwnerMapping;
use App\Models\PalletDeatil;
use App\Models\UserType;
use App\User;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Request as RequestsUrl;

use App\Mail\MainTemplate;
use Illuminate\Support\Facades\Mail;

use Config;
use GuzzleHttp\Client;

use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Country;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Arr;

use Validator;
use PHPExcel_Shared_Date;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use Session;

use Excel;
use App\Exports\ExportAmsClientPackages;

class ClientManagerController extends Controller {

	public $upload_path;
	public $perPage = 50;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware(['client', 'auth']);

		$this->upload_path = Config::get('constants.path');
        $imagePath = public_path($this->upload_path);
        if(!File::exists($imagePath)) File::makeDirectory($imagePath, 0777,true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		// code...
		$newOrder = Post::join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where(function($query){
		                        $query->where([['p1.key_name','_order_status'],['p1.key_value', '=' , 'Pending']]);
		                    })->where(['posts.post_type' => 'order', 'posts.parent_id' => 0])->get();

		$viewOrder = Post::join('post_extras AS p1', 'posts.id', '=', 'p1.post_id')->where(function($query){
		                        $query->where([['p1.key_name','_order_status'],['p1.key_value', '=' , 'Completed']]);
		                    })->where(['posts.post_type' => 'order', 'posts.parent_id' => 0])->get();            

		# code by sanjay...
		if ($newOrder && $newOrder->count() > 0) {
		    $dashboard['today_new_orders'] = $newOrder->count();
		    $todaysNewTotal           = 0;            
		    $dashboard['today_new_sales'] = $todaysNewTotal;
		} else {
		    $dashboard['today_new_sales'] = 0;
		    $dashboard['today_new_orders'] = 0;
		}

		if ($viewOrder && $viewOrder->count() > 0) {
		    $dashboard['today_view_orders'] = $viewOrder->count();
		    $todaysViewTotal           = 0;            
		    $dashboard['today_view_sales'] = $todaysViewTotal;
		} else {
		    $dashboard['today_view_sales'] = 0;
		    $dashboard['today_view_orders'] = 0;
		}

		# manifest files...
		$dashboard['clients'] = [];

		if (Auth::user()->user_type_id == 3) {
			$dashboard['packages'] = Post::where('post_type', 'order')->where(['client_id' => Auth::user()->user_code])->get()->count();

			$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getOwnerClients(Auth::id());
			$typ_ids = Arr::pluck($client_list, 'id');
			$dashboard['clients'] = User::whereIn('id',  $typ_ids)->where('user_type_id', '6')->get();
		} else if (Auth::user()->user_type_id == 6) {
			$dashboard['packages'] = Post::where('post_type', 'order')->where(['sub_client_id' => Auth::user()->user_code])->get()->count();
		} else {
			$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$dashboard['packages'] = Post::where('post_type', 'order')->whereIn('client_id', $ids)->get()->count();

			$typ_ids = Arr::pluck($client_list, 'id');
			if (Auth::user()->user_type_id == 4) {
				$owner_id = reset($typ_ids);
				$sub_client_list = $obj->getOwnerClients($owner_id);
				$sub_typ_ids = Arr::pluck($sub_client_list, 'id');
				$dashboard['clients'] = User::whereIn('id',  $sub_typ_ids)->where('user_type_id', '6')->get();
			}			
		}

		// $dashboard['process_pallet'] = PalletDeatil::where('pallet_type', 'InProcess')->get()->count();
		// $dashboard['close_pallet'] = PalletDeatil::where('pallet_type', 'Closed')->get()->count();
		// $dashboard['shipped_pallet'] = PalletDeatil::where('pallet_type', 'Shipped')->get()->count();
		$typ_ids = $typ_code = [];
		if (Auth::user()->user_type_id == 3) {
			$dashboard['process_pallet'] = PalletDeatil::where('pallet_type', 'InProcess')->where('client_id', Auth::id())->get()->count();
			$dashboard['close_pallet'] = PalletDeatil::where('pallet_type', 'Closed')->where('client_id', Auth::id())->get()->count();
			$dashboard['shipped_pallet'] = PalletDeatil::where('pallet_type', 'Shipped')->where('client_id', Auth::id())->get()->count();
		} else if (Auth::user()->user_type_id == 6) {
			$dashboard['process_pallet'] = PalletDeatil::where('pallet_type', 'InProcess')->where('sub_client_id', Auth::id())->get()->count();
			$dashboard['close_pallet'] = PalletDeatil::where('pallet_type', 'Closed')->where('sub_client_id', Auth::id())->get()->count();
			$dashboard['shipped_pallet'] = PalletDeatil::where('pallet_type', 'Shipped')->where('sub_client_id', Auth::id())->get()->count();
		} else {
			$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$typ_ids = Arr::pluck($client_list, 'id');
			$typ_code = Arr::pluck($client_list, 'user_code');

			$dashboard['process_pallet'] = PalletDeatil::where('pallet_type', 'InProcess')->whereIn('client_id', $typ_ids)->get()->count();
			$dashboard['close_pallet'] = PalletDeatil::where('pallet_type', 'Closed')->whereIn('client_id', $typ_ids)->get()->count();
			$dashboard['shipped_pallet'] = PalletDeatil::where('pallet_type', 'Shipped')->whereIn('client_id', $typ_ids)->get()->count();	    	
		}
		
		return view('pages.frontend.client.index', [
			'template' => $template,
			'segments' => $segments,
			'dashboard' => $dashboard,
			'typ_ids' => $typ_ids,
			'typ_code' => $typ_code,
		]);
	}

	public function profile(Request $request) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		# profile content
		$user = User::find(Auth::id());		

		return view('pages.frontend.client.index', compact('template', 'segments', 'user'));
	}

	public function updateProfile(Request $request) {
		DB::beginTransaction();
		try {
			// dd($request->all());
			# code for add client...
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required'
			]);

			if ($validator->fails()) {
				return response()->json(['message' => $validator->errors()->first(), 'status' => 200], 200);
			}

			// $full_name = explode(" ", $request->company_name);
			// $first_name = $full_name[0];
			// array_shift($full_name);

			$user = User::findOrFail($request->client_id);
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->phone = $request->phone;
			// $user->status = $request->status;
			// $user->contact_person_name = $request->contact_person_name;
			// $user->department = $request->department;
			// $user->role = $request->role;
			$user->save();
			
			DB::commit();
			return response()->json(['message' => "Record has been updated successfully.", 'status' => 200], 200);
		} catch (Exception $e) {
			DB::rollback();
			return (new \Illuminate\Http\Response)->setStatusCode(400, $e->getMessage());
		}
	}

	public function changePassword(Request $request) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		return view('pages.frontend.client.index', compact('template', 'segments'));
	}

	public function changePasswordUpdate(Request $request, User $user) {
		$validator = Validator::make($request->all(), [
			'password' => 'required|max:15|min:2',
			'confirm_password' => 'required|max:15|min:2|same:password',
		]);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}
		try {
			$user->password = bcrypt($request->password);
			$user->save();
		} catch (Exception $e) {
			return back()->withError($e->getMessage())->withInput();
		}

		return redirect()->back()->with('success', 'Password has been changed successfully.');
	}

	public function createClientUserForm(Request $request) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		$uertype = UserType::where('id', '!=', '1')->get();

		return view('pages.frontend.client.index', compact('template', 'segments', 'uertype'));
	}

	public function clientUserList(Request $request) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		$ob = new User;
		$users = $ob->getUserWithOwnerByTypeId(4, Auth::id(), $request);

		return view('pages.frontend.client.index', compact('template', 'segments', 'users'));
	}

	public function getSubClient(Request $request){
        $template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		$ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else {
			$users = $ob->getUserWithOwnerByTypeId(5, Auth::id(), $request);
		}

        return view('pages.frontend.client.index', compact('template', 'segments', 'users'));
    }

	public function storeClientUser(Request $request) {
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|max:20|min:2',
			'last_name' => 'required|max:20|min:2',
			'email' => 'required|max:50|min:2|email|unique:users',
			// 'phone' => 'required|max:15|min:8',
			// 'address' => 'required|max:191',
			'status' => 'required',
			// 'client_type' => 'required',
			'user_type_id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}
		try {

			// $pass_word = randomPassword();
			$full_name = $request->first_name. ' '. $request->last_name;
			$pass_word = 'password';
			$is_assigned = (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 6) ? "Y" : "N";
			$user = new User;
			$user->first_name = ucfirst($request->first_name);
			$user->last_name = ucfirst($request->last_name);
			$user->name = ucwords($full_name);
			$user->slug = generateSlug($full_name);
			$user->email = strtolower($request->email);
			$user->phone = $request->phone;
			$user->address = $request->address;
			$user->status = $request->status;
			$user->password = bcrypt($pass_word);
			$user->is_assigned = 'Y';
			$user->client_type = $request->client_type ?? '';
			$user->user_type_id = $request->user_type_id;
			$user->created_by = Auth::id();			
			$user->save();
			$id = $user->id;

			$user->user_code = Config('constants.rgUniqueId'). str_pad('', Config('constants.rgUniqueIdMaxDigit') - strlen((string) $id), '0', STR_PAD_LEFT) . $id;
			$user->save();

			// if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 6) {
				$u_o_m = new \App\Models\UserOwnerMapping;
				$u_o_m->user_id = $user->id;
				$u_o_m->owner_id = Auth::id();
				$u_o_m->save();
			// }

			# send mail...
			$get_view_data['subject']    =   'Create Account!';
			$get_view_data['view']       =   'mails.client-user';
			$get_view_data['user']       =   $user;
			$get_view_data['password']   =   $pass_word;

			# token create for reset link...			
			$token = Password::broker()->createToken($user);
			$get_view_data['token'] = $token;


			try{
			    $mail = Mail::to($user->email)->send(new MainTemplate( $get_view_data ));

				return redirect()->back()->with('success', 'Client has been created successfully');
			}catch(\Swift_TransportException $transportExp){
				return redirect()->back()->with('success', 'Client has been created successfully');
			}
		} catch (Exception $e) {
			return back()->withError($e->getMessage())->withInput();
		}
	}

	public function editClientUser(User $user) {
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

		$uertype = UserType::where('id', '!=', '1')->get();

		return view('pages.frontend.client.index', compact('template', 'segments', 'user', 'uertype'));
	}

	public function updateClientUser(User $user) {
		$this->validate(request(), [
			'first_name' => 'required|max:20|min:2',
			'last_name' => 'required|max:20|min:2',
			'phone' => 'max:15',
			'address' => 'max:191',
			'status' => 'required',
			// 'client_type' => 'required',
		]);

		try {
			$user->first_name = ucfirst(request('first_name'));
			$user->last_name = ucfirst(request('last_name'));
			$user->name = ucwords(request('first_name') . ' ' . request('last_name'));
			$user->phone = request('phone');
			$user->address = request('address');
			$user->status = request('status');
			// $user->client_type = request('client_type');			
			$user->user_type_id = request('user_type_id');			
			$user->save();
		} catch (Exception $e) {
			return back()->withError($e->getMessage())->withInput();
		}
		return back()->with('success', 'Client has been updated successfully');
	}

	public function clientUserDestory($id) {
		DB::beginTransaction();
		try {
			$client = User::where(['id' => $id]);
			$user_row = $client->first();
			if ($user_row) {
				\App\Models\UserOwnerMapping::where(['user_id' => $user_row->id])->delete();
				if ($client->delete()) {
					DB::commit();
					return back()->with('success', 'Client User has been deleted successfully');
				}
			} else {
				return back()->with('error', 'Record not found');
			}

		} catch (Exception $e) {
			DB::rollback();
			return back()->with('error', $e->getMessage());
		}
	}


	public function getOrderLists(Request $request){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $get_order = (new Post)->newQuery();
	    if($request->has('order_status') && $request->filled('order_status')){
	        if(in_array($request->order_status, ['Pending'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif(in_array($request->order_status, ['Completed'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
                $get_order->where('posts.process_status', 'unprocessed');
            } elseif (in_array($request->order_status, ['Cancelled'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif (in_array($request->order_status, ['at_hub'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
                $get_order->where('posts.process_status', 'processed');
            } elseif (in_array($request->order_status, ['First Scan', 'In Transit', 'Delivered'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
                $get_order->where('posts.inscan_status', $request->order_status);
            } else {
                $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','order_status'],['p7.key_value', '=' , $request->order_status]]);
            }
	    } else {
	        $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
	    }

	    if($request->has('eq_id') && $request->filled('eq_id')){
	        $get_order->where('posts.id', $request->eq_id);
	    }

	    if($request->has('client_id') && $request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        if($request->has('sub_client_id') && $request->filled('sub_client_id')){
            $get_order->where('posts.sub_client_id', $request->sub_client_id);
        }

        if($request->has('by_country') && $request->filled('by_country')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','customer_country'],['p4.key_value', '=' , $request->by_country]]);
        }

        if($request->has('by_warehouse') && $request->filled('by_warehouse')){
            $get_order->whereMeta('_warehouse_id', $request->by_warehouse);
        }

	    if($request->has('tracking_number') && $request->filled('tracking_number')){
	        $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','client_ref'],['p2.key_value', '=' , $request->tracking_number]]);
	    }

	    if($request->has('customer_name') && $request->filled('customer_name')){
	        $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','customer_name'],['p6.key_value', 'like' , '%' .$request->customer_name. '%']]);
	    }

	    if($request->filled('from_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
	    }

	    if($request->filled('to_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
	    }

	    if (Auth::user()->user_type_id == 3) {
	    	$get_order->where(['posts.client_id' => Auth::user()->user_code]);
	    } else if (Auth::user()->user_type_id == 6) {
	    	$get_order->where(['posts.sub_client_id' => Auth::user()->user_code]);
	    } elseif (Auth::user()->user_type_id == 4) {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.client_id', $ids);
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.sub_client_id', $ids);
	    }
	            
	    // $posts = $get_order->where(['posts.post_type' => 'order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
	    $posts = $get_order->where(['posts.post_type' => 'order'])->orderBy('posts.id', 'DESC')->get()->toArray();

	    if (count($posts) > 0) {
	        $order_data = $this->manageAllVendorOrders($posts);

	        if ($request->has('export_to') && $request->filled('export_to')) {
	            return $this->generateExcel($order_data, $request->order_status);
	        }
	        
	        $currentPage              = LengthAwarePaginator::resolveCurrentPage();
	        $col                      = new Collection($order_data);
	        $perPage                  = $this->perPage;
	        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
	        $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

	        $order_object->setPath(route('client.order.list'));
	        $orders = $order_object;
	    }
	    else{
	        $orders = [];
	    }

	    $sub_users = User::where('user_type_id', 6)->get();
	    $ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else if (Auth::user()->user_type_id == 4){
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::user()->created_by);
		}

		$country = Country::where('status', 1)->orderBy('name', 'ASC')->get();

	    return view('pages.frontend.client.index', compact('template', 'segments', 'orders', 'sub_users', 'country'));
	}

	/**
	* manage order meta key and value based
	*/
	public function manageAllVendorOrders($get_order){
	    $order_data = array();

	    if (count($get_order) > 0) {
	        foreach ($get_order as $order) {
	            $order_postmeta           = array();
	            if(isset($order['post_extras'])){
                    $get_postmeta_by_order_id = $order['post_extras'];
                } else {
                    $get_postmeta_by_order_id = PostExtra::where('post_id', $order['id'])->get();
                }

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
	                $order_postmeta['inscan_status']      = $order['inscan_status'] ?? '' ;

	                foreach ($get_postmeta_by_order_id as $postmeta_row) {
	                    $order_postmeta[$postmeta_row['key_name']] = $postmeta_row['key_value'];
	                }
	            }

	            array_push($order_data, $order_postmeta);
	        }
	    }

	    return $order_data;
	}

	public function importOrders(Request $request){
	    try {
	        $file      = $request->file('postdata_file')->getClientOriginalName();
	        $baseFilename = pathinfo($file, PATHINFO_FILENAME);
	        $extension = pathinfo($file, PATHINFO_EXTENSION);
	        if ($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv') {
	            $inputFileName = $request->file('postdata_file');
	            
	            /*check point*/
	            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	            $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
	            $objReader->setReadDataOnly(true);
	            $objPHPExcel = $objReader->load($inputFileName);
	            $objPHPExcel->setActiveSheetIndex(0);
	            $objWorksheet          = $objPHPExcel->getActiveSheet();
	            $CurrentWorkSheetIndex = 0;
	            /* row and column*/
	            // $sheet = $objPHPExcel->getSheet(0);
	            $highestRow    = $objWorksheet->getHighestRow();
	            $highestColumn = $objWorksheet->getHighestColumn();

	            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
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
	                if(!isset($value['client_ref'])){
	                    continue;
	                }

	                $fl = false;
	                $user = User::where('user_code', $value['client_id'])->first();
	                if(empty($user)){
	                    $fl = true;
	                }

	                $sub_user = User::where('user_code', $value['subclient_id'])->first();
	                if(empty($sub_user)){
	                    $fl = true;
	                }

	                $post = new  Post();
	                $post->post_author_id = Auth::user()->id;
	                $post->post_content   = 'Client Shop Order';
	                $post->post_title     = 'Client Shipment Orders';
	                $post->post_slug     = 'order';
	                $post->parent_id     = '0';
	                $post->client_id     = $value['client_id'] ?? '';
	                $post->sub_client_id     = $value['subclient_id'] ?? '';
	                $post->post_status     = '1';
	                $post->post_type     = 'order';

	                if ($post->save()) {
	                    $value['order_status'] = $value['order_status'] ?? 'Pending';
	                    $value['payment_mode'] = 'None';

	                    foreach($value as $k => $v){
	                        $postextra = new PostExtra();
	                        $postextra->post_id = $post->id;
	                        $postextra->key_name = $k;
	                        $postextra->key_value = $v;
	                        $postextra->save();
	                    }
	                }
	            }

	            return redirect()->back()->with('success', 'Action succcessfully');
	        } else {
	            return redirect()->back()->with('error', 'wrong extension');
	        }
	    }catch (Exception $e) {
	        return redirect()->back()->with('error', $e->getMessage());
	    }
	}

	public function getOrderDetails($params){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $data     = array();
	    $order_id = 0;
	    $get_post = Post::where(['id' => $params])->first();

	    if (!empty($get_post) && $get_post->parent_id > 0) {
	        $order_id = $get_post->parent_id;
	    } else {
	        $order_id = $params;
	    }

	    // $data                     = $this->classCommonFunction->commonDataForAllPages();
	    $get_post_by_order_id     = Post::where(['id' => $params])->first();
	    $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();

	    if ($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id->count() > 0) {
	        $order_date_format = new Carbon($get_post_by_order_id->created_at);
	        $order_data_by_id['_order_id']   = $get_post_by_order_id->id;
	        $order_data_by_id['_order_date'] = $order_date_format->toDayDateTimeString();

	        foreach ($get_postmeta_by_order_id as $postmeta_row_data) {
	            $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
	        }
	    }

	    $data['order_data_by_id'] = $order_data_by_id;

	    // dd($data);
	    return view('pages.frontend.client.index', compact('order_data_by_id', 'template', 'segments'));
	}

	public function palletList(Request $request){
		$template = last(RequestsUrl::segments());
    	$segments = RequestsUrl::segments();
        $client_list = '';
        
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

	    // $lists = $list->withMeta()->where(['type' => 'S', 'pallet_type' => 'InProcess'])->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));
	    if (Auth::user()->user_type_id == 3) {
	    	$lists = $list->where('type', 'S')->where('client_id', Auth::id())->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));
	    } else if (Auth::user()->user_type_id == 6) {
	    	$lists = $list->where('type', 'S')->where('sub_client_id', Auth::id())->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
	    	$client_list = $obj->getClients(Auth::id());
	    	$typ_ids = Arr::pluck($client_list, 'id');
	    	$lists = $list->where('type', 'S')->whereIn('client_id', $typ_ids)->orderBy('id','desc')->paginate(\Config('constants.adminDefaultPerPage'));	    	
	    }

	    // dd($lists);
	    return view('pages.frontend.client.index', compact('template', 'segments', 'lists', 'client_list'));
	}

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
	    // dd($lists);
	    return view('pages.frontend.client.index', compact('lists', 'client_list'));
	}

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
	    // dd($lists);
	    return view('pages.frontend.client.index', compact('lists', 'client_list'));
	}

	/**
    * Display pallet list data
    * Code by: sanjay
    **/
    public function palletShow(PalletDeatil $pallet){
    	$template = last(RequestsUrl::segments());
    	$segments = RequestsUrl::segments();

    	$query = (new Post)->newQuery();
    	$query->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')
    	            ->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]])
    	            ->where('posts.pallet_id' ,$pallet->pallet_id);

    	        
    	$posts = $query->where(['posts.post_type' => 'order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();
    	if (count($posts) > 0) {
    	    $order_data = $this->manageAllVendorOrders($posts);
    	    $currentPage              = LengthAwarePaginator::resolveCurrentPage();
    	    $col                      = new Collection($order_data);
    	    $perPage                  = $this->perPage;
    	    $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
    	    $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

    	    $order_object->setPath(route('client.pallet.show', $pallet->id));
    	    $orders = $order_object;
    	}
    	else{
    	    $orders = [];
    	}

        return view('pages.frontend.client.index', compact('template', 'segments', 'pallet', 'orders'));
    }

    public function orderImage(Request $request) {
    	$validator = Validator::make($request->all(), [
    		'order_image'     => 'required|image|mimes:jpeg,jpg,png',
    	]);
    	if ($validator->fails()) {
    		return redirect()->back()->withErrors($validator)->withInput();
    	}

    	$imagename = '';
    	if($request->file('order_image')){
    	    $image = $request->file('order_image');
    	    $currentDate = Carbon::now()->toDateString();
    	    $imagename = $currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

    	    if(!Storage::disk('public_uploads')->exists('order')){
    	        Storage::disk('public_uploads')->makeDirectory('order');
    	    }
    	    
    	    $propertyimage = Image::make($image)->stream();
    	    Storage::disk('public_uploads')->put('order/'.$imagename, $propertyimage);
    	}

    	set_post_key_value($request->post_id, 'order_image', 'order/'.$imagename);

    	return redirect()->back()->with('success', 'Action Successfully');
    }

	public function getOutboundOrderLists(Request $request){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $get_order = (new Post)->newQuery();
	    if($request->has('order_status') && $request->filled('order_status')){
	        if(in_array($request->order_status, ['Pending'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif(in_array($request->order_status, ['Completed'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
                $get_order->where('posts.process_status', 'unprocessed');
            } elseif (in_array($request->order_status, ['Cancelled'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif (in_array($request->order_status, ['at_hub'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
                $get_order->where('posts.process_status', 'processed');
            } elseif (in_array($request->order_status, ['First Scan', 'In Transit', 'Delivered'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
                $get_order->where('posts.inscan_status', $request->order_status);
            } else {
                $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','order_status'],['p7.key_value', '=' , $request->order_status]]);
            }
	    } else {
	        $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Pending']]);
	    }

	    if($request->has('eq_id') && $request->filled('eq_id')){
	        $get_order->where('posts.id', $request->eq_id);
	    }

	    if($request->has('client_id') && $request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        if($request->has('sub_client_id') && $request->filled('sub_client_id')){
            $get_order->where('posts.sub_client_id', $request->sub_client_id);
        }

        if($request->has('by_country') && $request->filled('by_country')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','customer_country'],['p4.key_value', '=' , $request->by_country]]);
        }

        if($request->has('by_warehouse') && $request->filled('by_warehouse')){
            $get_order->whereMeta('_warehouse_id', $request->by_warehouse);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','client_ref'],['p2.key_value', '=' , $request->order_number]]);
        }

	    if($request->has('tracking_number') && $request->filled('tracking_number')){
	        $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','client_ref'],['p3.key_value', '=' , $request->tracking_number]]);
	    }

	    if($request->has('customer_name') && $request->filled('customer_name')){
	        $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','customer_name'],['p6.key_value', 'like' , '%' .$request->customer_name. '%']]);
	    }

	    if($request->filled('from_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
	    }

	    if($request->filled('to_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
	    }

	    if (Auth::user()->user_type_id == 3) {
	    	$get_order->where(['posts.client_id' => Auth::user()->user_code]);
	    } else if (Auth::user()->user_type_id == 6) {
	    	$get_order->where(['posts.sub_client_id' => Auth::user()->user_code]);
	    } elseif (Auth::user()->user_type_id == 4) {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.client_id', $ids);
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			// dd($client_list);
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.sub_client_id', $ids);
	    }
	            
	    $posts = $get_order->where(['posts.post_type' => 'outbound_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

	    if (count($posts) > 0) {
	        $order_data = $this->manageAllVendorOrders($posts);

	        if ($request->has('export_to') && $request->filled('export_to')) {
	            return $this->generateExcel($order_data, $request->order_status, 'outbound');
	        }

	        $currentPage              = LengthAwarePaginator::resolveCurrentPage();
	        $col                      = new Collection($order_data);
	        $perPage                  = $this->perPage;
	        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
	        $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

	        $order_object->setPath(route('client.outbound.order.list'));
	        $orders = $order_object;
	    }
	    else{
	        $orders = [];
	    }

	    $sub_users = User::where('user_type_id', 6)->get();
	    $ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else if (Auth::user()->user_type_id == 4){
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::user()->created_by);
		}

		$country = Country::where('status', 1)->orderBy('name', 'ASC')->get();

	    return view('pages.frontend.client.index', compact('template', 'segments', 'orders', 'sub_users', 'country'));
	}

	public function getProcessOutboundOrderLists(Request $request){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $get_order = (new Post)->newQuery();
	    if($request->has('order_status') && $request->filled('order_status')){
	        if(in_array($request->order_status, ['Pending'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif(in_array($request->order_status, ['Completed'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
                $get_order->where('posts.process_status', 'unprocessed');
            } elseif (in_array($request->order_status, ['Cancelled'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif (in_array($request->order_status, ['at_hub'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
                $get_order->where('posts.process_status', 'processed');
            } elseif (in_array($request->order_status, ['First Scan', 'In Transit', 'Delivered'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
                $get_order->where('posts.inscan_status', $request->order_status);
            } else {
                $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','order_status'],['p7.key_value', '=' , $request->order_status]]);
            }
	    } else {
	        $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
	    }

	    if($request->has('eq_id') && $request->filled('eq_id')){
	        $get_order->where('posts.id', $request->eq_id);
	    }

	    if($request->has('client_id') && $request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        if($request->has('sub_client_id') && $request->filled('sub_client_id')){
            $get_order->where('posts.sub_client_id', $request->sub_client_id);
        }

        if($request->has('by_country') && $request->filled('by_country')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','customer_country'],['p4.key_value', '=' , $request->by_country]]);
        }

        if($request->has('by_warehouse') && $request->filled('by_warehouse')){
            $get_order->whereMeta('_warehouse_id', $request->by_warehouse);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','client_ref'],['p2.key_value', '=' , $request->order_number]]);
        }

	    if($request->has('tracking_number') && $request->filled('tracking_number')){
	        $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','client_ref'],['p3.key_value', '=' , $request->tracking_number]]);
	    }

	    if($request->has('customer_name') && $request->filled('customer_name')){
	        $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','customer_name'],['p6.key_value', 'like' , '%' .$request->customer_name. '%']]);
	    }

	    if($request->filled('from_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
	    }

	    if($request->filled('to_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
	    }

	    if (Auth::user()->user_type_id == 3) {
	    	$get_order->where(['posts.client_id' => Auth::user()->user_code]);
	    } else if (Auth::user()->user_type_id == 6) {
	    	$get_order->where(['posts.sub_client_id' => Auth::user()->user_code]);
	    } elseif (Auth::user()->user_type_id == 4) {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.client_id', $ids);
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.sub_client_id', $ids);
	    }
	            
	    $posts = $get_order->where(['posts.post_type' => 'outbound_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

	    if (count($posts) > 0) {
	        $order_data = $this->manageAllVendorOrders($posts);

	        if ($request->has('export_to') && $request->filled('export_to')) {
	            return $this->generateExcel($order_data, $request->order_status, 'outbound');
	        }

	        $currentPage              = LengthAwarePaginator::resolveCurrentPage();
	        $col                      = new Collection($order_data);
	        $perPage                  = $this->perPage;
	        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
	        $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

	        $order_object->setPath(route('client.p.outbound.order.list'));
	        $orders = $order_object;
	    }
	    else{
	        $orders = [];
	    }

	    $sub_users = User::where('user_type_id', 6)->get();
	    $ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else if (Auth::user()->user_type_id == 4){
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::user()->created_by);
		}

		$country = Country::where('status', 1)->orderBy('name', 'ASC')->get();

	    return view('pages.frontend.client.index', compact('template', 'segments', 'orders', 'sub_users', 'country'));
	}

	public function getShippedOutboundOrderLists(Request $request){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $get_order = (new Post)->newQuery();
	    if($request->has('order_status') && $request->filled('order_status')){
	        if(in_array($request->order_status, ['Pending'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif(in_array($request->order_status, ['Completed'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
                $get_order->where('posts.process_status', 'unprocessed');
            } elseif (in_array($request->order_status, ['Cancelled'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif (in_array($request->order_status, ['at_hub'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
                $get_order->where('posts.process_status', 'processed');
            } elseif (in_array($request->order_status, ['First Scan', 'In Transit', 'Delivered'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
                $get_order->where('posts.inscan_status', $request->order_status);
            } else {
                $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','order_status'],['p7.key_value', '=' , $request->order_status]]);
            }
	    } else {
	        $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Shipped']]);
	    }

	    if($request->has('eq_id') && $request->filled('eq_id')){
	        $get_order->where('posts.id', $request->eq_id);
	    }

	    if($request->has('client_id') && $request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        if($request->has('sub_client_id') && $request->filled('sub_client_id')){
            $get_order->where('posts.sub_client_id', $request->sub_client_id);
        }

        if($request->has('by_country') && $request->filled('by_country')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','customer_country'],['p4.key_value', '=' , $request->by_country]]);
        }

        if($request->has('by_warehouse') && $request->filled('by_warehouse')){
            $get_order->whereMeta('_warehouse_id', $request->by_warehouse);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','client_ref'],['p2.key_value', '=' , $request->order_number]]);
        }

	    if($request->has('tracking_number') && $request->filled('tracking_number')){
	        $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','client_ref'],['p3.key_value', '=' , $request->tracking_number]]);
	    }

	    if($request->has('customer_name') && $request->filled('customer_name')){
	        $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','customer_name'],['p6.key_value', 'like' , '%' .$request->customer_name. '%']]);
	    }

	    if($request->filled('from_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
	    }

	    if($request->filled('to_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
	    }

	    if (Auth::user()->user_type_id == 3) {
	    	$get_order->where(['posts.client_id' => Auth::user()->user_code]);
	    } else if (Auth::user()->user_type_id == 6) {
	    	$get_order->where(['posts.sub_client_id' => Auth::user()->user_code]);
	    } elseif (Auth::user()->user_type_id == 4) {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.client_id', $ids);
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.sub_client_id', $ids);
	    }
	            
	    $posts = $get_order->where(['posts.post_type' => 'outbound_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

	    if (count($posts) > 0) {
	        $order_data = $this->manageAllVendorOrders($posts);
	        if ($request->has('export_to') && $request->filled('export_to')) {
	            return $this->generateExcel($order_data, $request->order_status, 'outbound');
	        }

	        $currentPage              = LengthAwarePaginator::resolveCurrentPage();
	        $col                      = new Collection($order_data);
	        $perPage                  = $this->perPage;
	        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
	        $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

	        $order_object->setPath(route('client.s.outbound.order.list'));
	        $orders = $order_object;
	    }
	    else{
	        $orders = [];
	    }

	    $sub_users = User::where('user_type_id', 6)->get();
	    $ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else if (Auth::user()->user_type_id == 4){
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::user()->created_by);
		}

		$country = Country::where('status', 1)->orderBy('name', 'ASC')->get();

	    return view('pages.frontend.client.index', compact('template', 'segments', 'orders', 'sub_users', 'country'));
	}

	public function getCancelledOutboundOrderLists(Request $request){
		$template = last(RequestsUrl::segments());
		$segments = RequestsUrl::segments();

	    $get_order = (new Post)->newQuery();
	    if($request->has('order_status') && $request->filled('order_status')){
	        if(in_array($request->order_status, ['Pending'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif(in_array($request->order_status, ['Completed'])){
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
                $get_order->where('posts.process_status', 'unprocessed');
            } elseif (in_array($request->order_status, ['Cancelled'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , $request->order_status]]);
            } elseif (in_array($request->order_status, ['at_hub'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Completed']]);
                $get_order->where('posts.process_status', 'processed');
            } elseif (in_array($request->order_status, ['First Scan', 'In Transit', 'Delivered'])) {
                $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '!=' , null]]);
                $get_order->where('posts.inscan_status', $request->order_status);
            } else {
                $get_order->join('post_extras AS p7', 'posts.id', '=', 'p7.post_id')->where([['p7.key_name','order_status'],['p7.key_value', '=' , $request->order_status]]);
            }
	    } else {
	        $get_order->join('post_extras AS pes', 'posts.id', '=', 'pes.post_id')->where([['pes.key_name','order_status'],['pes.key_value', '=' , 'Cancelled']]);
	    }

	    if($request->has('eq_id') && $request->filled('eq_id')){
	        $get_order->where('posts.id', $request->eq_id);
	    }

	    if($request->has('client_id') && $request->filled('client_id')){
            $get_order->where('posts.client_id', $request->client_id);
        }

        if($request->has('sub_client_id') && $request->filled('sub_client_id')){
            $get_order->where('posts.sub_client_id', $request->sub_client_id);
        }

        if($request->has('by_country') && $request->filled('by_country')){
            $get_order->join('post_extras AS p4', 'posts.id', '=', 'p4.post_id')->where([['p4.key_name','customer_country'],['p4.key_value', '=' , $request->by_country]]);
        }

        if($request->has('by_warehouse') && $request->filled('by_warehouse')){
            $get_order->whereMeta('_warehouse_id', $request->by_warehouse);
        }

        if($request->has('order_number') && $request->filled('order_number')){
            $get_order->join('post_extras AS p2', 'posts.id', '=', 'p2.post_id')->where([['p2.key_name','client_ref'],['p2.key_value', '=' , $request->order_number]]);
        }

	    if($request->has('tracking_number') && $request->filled('tracking_number')){
	        $get_order->join('post_extras AS p3', 'posts.id', '=', 'p3.post_id')->where([['p3.key_name','client_ref'],['p3.key_value', '=' , $request->tracking_number]]);
	    }

	    if($request->has('customer_name') && $request->filled('customer_name')){
	        $get_order->join('post_extras AS p6', 'posts.id', '=', 'p6.post_id')->where([['p6.key_name','customer_name'],['p6.key_value', 'like' , '%' .$request->customer_name. '%']]);
	    }

	    if($request->filled('from_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),">=",$request->from_date);
	    }

	    if($request->filled('to_date')){
	        $get_order->where(DB::raw("(DATE_FORMAT(posts.created_at,'%Y/%m/%d'))"),"<=",$request->to_date);
	    }

	    if (Auth::user()->user_type_id == 3) {
	    	$get_order->where(['posts.client_id' => Auth::user()->user_code]);
	    } else if (Auth::user()->user_type_id == 6) {
	    	$get_order->where(['posts.sub_client_id' => Auth::user()->user_code]);
	    } elseif (Auth::user()->user_type_id == 4) {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.client_id', $ids);
	    } else {
	    	$obj = new \App\Models\UserOwnerMapping;
			$client_list = $obj->getClients(Auth::id());
			$ids = Arr::pluck($client_list, 'user_code');
			$get_order->whereIn('posts.sub_client_id', $ids);
	    }
	            
	    $posts = $get_order->where(['posts.post_type' => 'outbound_order'])->with('post_extras')->orderBy('posts.id', 'DESC')->get()->toArray();

	    if (count($posts) > 0) {
	        $order_data = $this->manageAllVendorOrders($posts);

	        if ($request->has('export_to') && $request->filled('export_to')) {
	            return $this->generateExcel($order_data, $request->order_status, 'outbound');
	        }
	        
	        $currentPage              = LengthAwarePaginator::resolveCurrentPage();
	        $col                      = new Collection($order_data);
	        $perPage                  = $this->perPage;
	        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
	        $order_object             = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

	        $order_object->setPath(route('client.c.outbound.order.list'));
	        $orders = $order_object;
	    }
	    else{
	        $orders = [];
	    }

	    $sub_users = User::where('user_type_id', 6)->get();
	    $ob = new User;
		if (Auth::user()->user_type_id == 3) {
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::id(), $request);
		} else if (Auth::user()->user_type_id == 4){
			$sub_users = $ob->getUserWithOwnerByTypeId(6, Auth::user()->created_by);
		}

		$country = Country::where('status', 1)->orderBy('name', 'ASC')->get();

	    return view('pages.frontend.client.index', compact('template', 'segments', 'orders', 'sub_users', 'country'));
	}

	public function generateExcel($orders, $status = '', $type = 'inbound'){
	    $data_ar = [];
	    if (count($orders) > 0) {
	        foreach ($orders as $row) {
	            // dd($row);
	            $tt = $status;
	            $ot = getOrderType($row, $tt);
	            if(empty($tt)){
	                $ot = getNewOrderType($row, $tt);
	            }

	            $address = $row['customer_address_line_1'].' '. $row['customer_address_line_2'] ?? '';
	            $address .= ' ,'.$row['customer_city']. ' '.$row['customer_country'];
	            $amt = 0;
	            if (isset($row['value'])) {
	                $amt = $row['currency'] . ' '. $row['value'];
	            }
	            if (isset($row['total_price'])) {
	                $amt = $row['currency'] . ' '. $row['total_price'];
	            }
	            
	            if ($type == 'inbound') {
                    $data_ar[] = [
                        date('d-m-Y', strtotime($row['_order_date'])),
                        $row['_post_id'],
                        $row['client_ref'],
                        $row['customer_name'],
                        $row['customer_email_id'] ?? '',
                        $address,
                        $amt,
                        $row['total_weight(kg)'] ?? '',
                        $row['client'] ?? '',
                        $row['subclient'] ?? '',
                        $row['tracking_id'] ?? '',
                        $row['serial_number'] ?? '',
                        $row['hs_code'] ?? '',
                        $row['country_of_origin'] ?? '',
                        $ot,
                        $row['order_status']
                    ];
                } else {
                    $item = json_decode($row['items']);
                    $first = reset($item);
                    $data_ar[] = [
                        date('d-m-Y', strtotime($row['_order_date'])),
                        $row['_post_id'],
                        $row['order_number'],
                        $row['customer_name'],
                        $row['customer_email_id'] ?? '',
                        $address,
                        $amt,
                        $row['total_weight(kg)'] ?? '',
                        $row['client'] ?? '',
                        $row['subclient'] ?? '',
                        $row['tracking_id'] ?? '',
                        $row['hs_code'] ?? '',
                        $row['country_of_origin'] ?? '',
                        $ot,
                        $row['order_status'],
                        $first->item_number ?? '',
                        $first->sku ?? '',
                        $first->hs_code ?? '',
                        $first->country_of_origin ?? '',
                    ];
                }
	        }
	    }

	    return Excel::download(new ExportAmsClientPackages($data_ar, $type), "LinkShipCycle-" . time() . '.xls');
	}
}
