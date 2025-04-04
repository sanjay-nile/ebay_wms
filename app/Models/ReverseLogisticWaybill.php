<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Zoha\Metable;
use Auth;
use DB;
use App\Models\UserOwnerMapping;
use Carbon\Carbon;

class ReverseLogisticWaybill extends Model
{
    use Metable;
    use SoftDeletes;

    const EQTOR_ADMIN = 'EQTOR_ADMIN';
    const EQTOR_CUSTOMER = 'EQTOR_CUSTOMER';
    const RG_ADMIN = 'RG_ADMIN';
    const RG_CUSTOMER = 'RG_CUSTOMER';

    const CUSTOMER = 'CUSTOMER';
    const CLIENT_ADMIN = 'CSR';
    const CLIENT_USER = 'CSR';

    const MISS_TYPE = 'Missguided';
    const JADED_TYPE = 'Jaded';
    const OLIVE_TYPE = 'Olive';
    const SHOPIFY_TYPE = 'Shopify';

    const SOURCE_NAME = 'Client Admin';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];

    /**
    * Client relation
    */
    public function client()
    {
        return $this->belongsTo('App\User');
    }

    /**
    * Shipping Policy
    */
    public function shippingPolicy()
    {
        return $this->hasOne('App\Models\ShippingPolicy','id','shipping_policy_id');
    }
    
    /*
    * Get Reverse Logistic package details
    */
    public function packages(){
        return $this->hasMany(PackageDetail::class);
    }

    /*
    * Unprocessed order
    */
    public function unprocessed_item(){
        return $this->packages()->where('process_status','=', 'UnProcessed');
    }

    /*
    * Processed order
    */
    public function processed_item(){
        return $this->packages()->where('process_status','=', 'Processed')->where('pallet_id', '=', null);
    }


    /*
    * Get Reverse Logistic response
    */
    public function wayBillResponse(){
        return $this->hasMany(ReverseLogisticResponse::class);
    }

    /*
    * Order Data value
    */
    public function OrderData(){
        return $this->hasOne('App\Models\OrderData','order_id','way_bill_number');
    }

    /*
    * Order Item value
    */
    public function OrderItem(){
        return $this->hasOne('App\Models\OrderItem','order_id','way_bill_number');
    }

    /*
    * warehouse value
    */
    public function warehouse(){
        return $this->hasOne('App\Models\Warehouse','id','warehouse_id');
    }

    /*
    * Order carrier data
    */
    public function carrierData(){
        return $this->hasOne('App\Models\OrderCarrierData','order_ref','way_bill_number');
    }

    /*
    * Get Reverse Logistic tracking details
    */
    public function trakingDetails(){
        return $this->hasMany(ReverseLogisticTracking::class);
    }

    /**
    * Store Reverse Logistic
    */
    public function store($arr){
        $data = array(
            'created_by'=> $arr['login_id'],
            'client_id'=>$arr['client_id'],
            'warehouse_id'=>$arr['warehouse_id']??NULL,
            'shipping_policy_id'=>$arr['shipment_id']??NULL,
            'way_bill_number'=>$arr['way_bill_number'],
            'delivery_date'=>$arr['delivery_date']??NULL,
            'payment_mode'=>$arr['payment_mode']??NULL,
            'created_from'=>$arr['created_from']??NULL,
            'status'=>$arr['status']??"Pending",
            'amount'=>$arr['amount']??NULL,
            'type'=>$arr['type']??NULL,
            'return_by'=>$arr['return_by']??NULL,
            'cod_payment_mode'=>$arr['cash_on_pickup']??NULL,
            'rtn_total' => $arr['rtn_total'] ?? NULL,
        );
        return $this->insertGetId($data);
    }

    /*
    * Get Reverse Logistic for generate tracking id
    */
    public function getTrackingList($request){
        $query = $this->whereNull('tracking_id');
        
        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        }
        if(Auth::user()->user_type_id==3){
            $query->where('client_id',Auth::id());
        }
        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where('created_by',Auth::id());
        }
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }
        if($request->has('client_code') && !empty($request->client_code)){
            $query->whereMeta('_client_code','like','%' . $request->client_code . '%');
        }
        if($request->has('name') && !empty($request->name)){
            $query->whereMeta('_consignee_name','like','%' . $request->name . '%');
        }
        $query->withMeta();
        return $query->orderBy('id','desc')->paginate(10);
    }

    /**
    * code by: sanjay
    * does not have custom duty
    **/
    public function getCustomDuty($request){
        $query = $this->where('status', 'Success')->whereMetaDoesntHave('_custom_duties');
        
        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $query->where('client_id',Auth::id());
        }
        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where('created_by',Auth::id());
        }
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }
        
        $query->withMeta();

        return $query->orderBy('id','desc')->paginate(30);
    }

    /**
    * admin dashboard return orders
    */
    public function getAdminDashboardOrders($request){
        $query = $this->newQuery();
        $query->withMeta();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        # cancle order..        
        $query->where('reverse_logistic_waybills.cancel_return_status', null);
        
        if($request->has('status')){
            $query->where('reverse_logistic_waybills.status',$request->status)->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        if ($request->has('s') && $request->filled('s')) {
            # code...
            $query->where(function($q) use ($request){
                                $q->orWhere('reverse_logistic_waybills.id' , $request->s);
                                $q->orWhere('reverse_logistic_waybills.way_bill_number' , $request->s);
                            })->orWhere(function($q) use ($request){
                                $q->orWhereMeta('_customer_name' , 'like' , '%'.$request->s.'%');
                            });
        }
        
        return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate($request->per_page);
    }

    /**
    * Get return order list based on condition
    */
    public function getReverLogisticList($request){ 
        //\DB::enableQueryLog();
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        $query->whereIn('reverse_logistic_waybills.type',['I','A']);

        if($request->has('package_status')){
            $query->join('package_details as pd','reverse_logistic_waybills.id','=','pd.reverse_logistic_waybill_id');
            $query->where('pd.status',$request->package_status);
        }

        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }

        if($request->has('type')){
            $query->where('reverse_logistic_waybills.type',$request->type);
        }

        if($request->has('status')){
            $query->where('reverse_logistic_waybills.status',$request->status)->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        if($request->has('process_status')){
            $query->where('reverse_logistic_waybills.process_status',$request->process_status);
        }

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        if($request->has('start') && $request->has('end')){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        }

        if($request->has('start')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        }

        if($request->has('end')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        # cancle order..        
        $query->where('reverse_logistic_waybills.cancel_return_status', null);
        
        $query->withMeta();

        if($request->has('api_type')){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->get();
        }

        if($request->has('refund_status') && !empty($request->refund_status)){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(1000);
        }

        if($request->has('per_page')){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate($request->per_page);
        } else{
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        }
        //$query->get();
        //dd($query->get());die();
        //$q = \DB::getQueryLog();
        //print_r(end($q));die;
    }

    /**
    * Get inscan and cancel order list
    */
    public function getCancelReturnOrder($request){ 
        //\DB::enableQueryLog();
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        // $query->whereIn('reverse_logistic_waybills.type',['I','A']);

        if($request->has('package_status')){
            $query->join('package_details as pd','reverse_logistic_waybills.id','=','pd.reverse_logistic_waybill_id');
            $query->where('pd.status',$request->package_status);
        }

        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }

        if($request->has('type')){
            $query->where('reverse_logistic_waybills.type',$request->type);
        }

        if($request->has('status')){
            $query->where('reverse_logistic_waybills.status',$request->status)->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        if($request->has('start') && $request->has('end')){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        }

        if($request->has('start')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        }

        if($request->has('end')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        # cancle order..
        if($request->has('cancel') && !empty($request->cancel)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '_order_waywill_status');                
                });
            });
        } else {
            /*$query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '_order_waywill_status');                
                });
                $q->orWhere('reverse_logistic_waybills.cancel_return_status', '!=', '');
            });*/

            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        }        
        
        $query->withMeta();

        if($request->has('api_type')){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->get();
        }

        if($request->has('refund_status') && !empty($request->refund_status)){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(1000);
        }

        if($request->has('per_page')){
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate($request->per_page);
        } else{
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        }
    }


    /**
    * Get unprocessed order list
    */
    public function getUnprocessedList($request){ 
        $query = $this->newQuery();
        // $query->join('package_details as pd','reverse_logistic_waybills.id','=','pd.reverse_logistic_waybill_id');

        // if($request->has('process_status')){
        //     $query->where('pd.process_status',$request->process_status);
        // }

        if($request->has('barcode') && !empty($request->barcode)){
            $code = $request->barcode;
            $query->where(function ($q) use ($code) {
                $q->where('way_bill_number', $code)->orWhere('tracking_id',$code);
            });
        }

        if($request->has('qr_code') && !empty($request->qr_code)){
            $qr_code = $request->qr_code;
            $query->where(function ($q) use ($qr_code) {
                $q->where('qr_code', $qr_code);
            });
        }

        if($request->has('client') && !empty($request->client)){
            $query->where('client_id',$request->client);
        }

        if($request->has('date') && !empty($request->date)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->date);
        }

        if($request->has('status')){
            $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
        }

        $query->withMeta();
        
        return $query->with('unprocessed_item')->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        // return $query->->where('process_status','unprocessed')->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }

    /**
    * Get unprocessed order list
    */
    public function getProcessedList($request){ 
        $query = $this->newQuery();
        if($request->has('barcode') && !empty($request->barcode)){
            $code = $request->barcode;
            $query->where(function ($q) use ($code) {
                $q->where('way_bill_number', $code)->orWhere('tracking_id',$code);
            });
        } elseif ($request->has('client') && !empty($request->client)){
            $query->where('client_id',$request->client);
        } elseif ($request->has('start') && $request->has('end')){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start')){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end')){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        $query->where(function($q) use ($request){
            $q->whereIn('id', function($query) use ($request){
                $query->select(DB::raw('reverse_logistic_waybill_id'))
                    ->from('package_details')
                    ->where('process_status', 'Processed')->where('pallet_id', '=', null);
            });
        });
        
        // return $query->where('process_status','processed')->where('pallet_id', '=', null)->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        return $query->withMeta()->orderBy('id','desc')->paginate(200);
    }

    /**
    * Get new or old orders list
    */
    public function getNewOrOldOrders($request){
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        
        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where('reverse_logistic_waybills.created_by',Auth::id());
        }
        
        $dt = date('Y/m/d');
        if($request->has('type') && $request->type == 'new'){
            // $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$dt);
            // $query->whereMetaNull('_label_url');
            $query->where(function($query){                
                $query->whereMeta('_label_url' , '=' , '')->orWhereMeta('_label_url' , '!=' , '');
            });
        } else{
            // $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<",$dt);
            $query->where(function($query){                
                $query->whereMeta('_label_url' , '!=' , '');
            });
        }

        $query->withMeta();

        return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }


    /**
    * Get orders for the api
    */
    public function getOrdersForApi($request){
        //\DB::enableQueryLog();
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        $query->whereIn('reverse_logistic_waybills.type',['I','A']);

        if($request->has('package_status')){
            $query->join('package_details as pd','reverse_logistic_waybills.id','=','pd.reverse_logistic_waybill_id');
            $query->where('pd.status',$request->package_status);
        }

        if($request->has('status')){
            $query->where('reverse_logistic_waybills.status',$request->status)->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        if($request->has('process_status')){
            $query->where('reverse_logistic_waybills.process_status',$request->process_status);
        }
        
        if($request->has('client_id') && !empty($request->client_id)){
            $query->where('reverse_logistic_waybills.client_id',$request->client_id);
        }

        if($request->has('start') && $request->has('end')){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        }

        if($request->has('start')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        }

        if($request->has('end')){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }
        
        // $query->withMeta();

        return $query->orderBy('reverse_logistic_waybills.id','desc')->with('packages')->get();
        // return $query->orderBy('reverse_logistic_waybills.id','desc')->with('packages')->get()->toArray();
        
        //$query->get();
        //dd($query->get());die();
        //$q = \DB::getQueryLog();
        //print_r(end($q));die;
    }

    /**
    * get the all returns order here and also based on fillter
    * from admin side and client side
    */
    public function getAllReturnOrders($request){
        // \DB::enableQueryLog();
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } else {
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id', $request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        // $query->whereBetween('created_at',[$dateS,$dateE]);
        
        return $query->withMeta()->withTrashed()->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }

    /**
    * get the all returns order here and also based on fillter
    * from admin side and client side
    */
    public function getAllAdminReturnOrders($request){
        // \DB::enableQueryLog();
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else {
            $query->whereNotIn('status',['Deleted']);   
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id', $request->client);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        } else {
            $query->whereBetween('created_at',[$dateS,$dateE]);
        }
        
        return $query->withMeta()->withTrashed()->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }

    /**
    * get the all returns order here and also based on fillter
    * from admin side
    */
    public function getAllActualStaticsReturnOrders($request){
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();        
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number');

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed')->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);                    
                });
            });
        } else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }        

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }
        
        $query->withMeta();

        // dd(str_replace_array('?', $query->getBindings(), $query->toSql()));
        $query->whereBetween('reverse_logistic_waybills.created_at',[$dateS,$dateE]);

        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            return $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', DB::raw('count(*) as waywill_total'))
                ->orderBy('reverse_logistic_waybills.id','desc')
                ->groupBy('reverse_logistic_waybills.way_bill_number')
                ->distinct()
                ->paginate(Config('constants.adminDefaultPerPage'));
        } else {
            $query->withTrashed();
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        }
    }

    /**
    * inscan orders
    */
    public function inscanOrders($request){
        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }       

        $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
        $query->where(function($q) use ($request){
            $q->whereIn('id', function($query) use ($request){
                $query->select(DB::raw('owner_id'))
                    ->from('meta')
                    ->where('key', '=', '_order_waywill_status');
            });
        });

        return $query->orderBy('id','desc')->get();
    }

    /**
    * cancel orders
    */
    public function cancelOrders($request){
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');

        $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }       

        # cancle order..        
        $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        return $query->withMeta()->orderBy('reverse_logistic_waybills.id','desc')->get();
    }

    /**
    * Super Admin all type Return Orders
    */
    public function processReturnOrders($client_type){
        $query = $this->newQuery();
        $query->where('status', 'Success');
        $query->where('process_status', 'unprocessed');
        $query->where('cancel_return_status', '=', null);
        $query->whereMetaDoesntHave('_order_waywill_status');        
        $query->where(function($q) use ($client_type){
            $q->whereIn('client_id', function($query) use ($client_type){
                $query->select(DB::raw('id'))
                    ->from('users')
                    ->where('user_type_id', 3)->where('client_type', $client_type);
            });
        });

        return $query->withTrashed()->get()->count();
    }

    public function failReturnOrders($client_type){
        $query = $this->newQuery();
        $query->where('status', 'Pending');
        $query->where(function($q) use ($client_type){
            $q->whereIn('client_id', function($query) use ($client_type){
                $query->select(DB::raw('id'))
                    ->from('users')
                    ->where('user_type_id', 3)->where('client_type', $client_type);
            });
        });

        return $query->withTrashed()->get()->count();
    }
    
    public function receivedReturnOrders($client_type){
        $query = $this->newQuery();
        $query->where('status', 'Success')->where('process_status', 'processed');        
        $query->where(function($q) use ($client_type){
            $q->whereIn('client_id', function($query) use ($client_type){
                $query->select(DB::raw('id'))
                    ->from('users')
                    ->where('user_type_id', 3)->where('client_type', $client_type);
            });
        });

        return $query->withTrashed()->get()->count();
    }

    public function inscanReturnOrders($client_type){
        $query = $this->newQuery();
        $query->where(function($q) use ($client_type){
            $q->whereIn('client_id', function($query) use ($client_type){
                $query->select(DB::raw('id'))
                    ->from('users')
                    ->where('user_type_id', 3)->where('client_type', $client_type);
            });
        });
        $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
        $query->whereMetaHas('_order_waywill_status');

        return $query->withTrashed()->get()->count();
    }

    public function cancelReturnOrders($client_type){
        $query = $this->newQuery();
        $query->where('status', 'Success')->where('cancel_return_status', '!=', '');
        // $query->whereMetaDoesntHave('_order_waywill_status');
        $query->where(function($q) use ($client_type){
            $q->whereIn('client_id', function($query) use ($client_type){
                $query->select(DB::raw('id'))
                    ->from('users')
                    ->where('user_type_id', 3)->where('client_type', $client_type);
            });
        });        

        return $query->withTrashed()->get()->count();
    }  

    /**
    * Supar Admin Actula Failed Return Orders
    */
    public function actualFailedReturnOrders($request){
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','reverse_logistic_waybills.way_bill_number');
        $query->where('reverse_logistic_waybills.status', 'Pending');        
        $query->where(function($q) use ($request){
            $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                $query->select(DB::raw('id'))
                    ->from('reverse_logistic_waybills')
                    ->where('status', 'Success');
            });
        });

        return $query->groupBy('reverse_logistic_waybills.way_bill_number')->distinct()->get()->count();
    }

    /**
    * Supar Admin Repeated Attempt Orders
    */
    public function repeatedReturnOrders($request){
        $query = $this->newQuery();
        $query->where('status', 'Pending');        
        $query->where(function($q) use ($request){
            $q->whereIn('id', function($query) use ($request){
                $query->select(DB::raw('id'))
                    ->from('reverse_logistic_waybills')
                    ->where('status', 'Success');                
            });
        });

        return $query->get()->count();
    }

    /**
    * get the all returns order here and also based on fillter
    */
    public function getAdminAllReturnOrders($request, $start, $limit){
        $waywill = $this->newQuery();

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $waywill->where('client_id',$request->client);
        }

        # return type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $waywill->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $waywill->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $waywill->where('process_status', 'unprocessed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $waywill->where('status', 'Success')->where('process_status', 'unprocessed');
            $waywill->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $waywill->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $waywill->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $waywill->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $waywill->where('process_status', 'processed');
        } else{
            $waywill->whereNotIn('status',['Deleted']);
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $waywill->where(function($query) use ($request){
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $waywill->where(function($query) use ($request){
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $waywill->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $waywill->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $waywill->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $waywill->where('way_bill_number',$request->way_bill_number);
        }

        if($request->has('sku') && !empty($request->sku)){
            $waywill->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $waywill->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $waywill->where('tracking_id',$request->tracking_id);
        }

        # date fillter...
        if($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)){
            $waywill->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->from,$request->to));
        }

        if($request->has('from') && !empty($request->from)){
            $waywill->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",trim($request->from));
        }

        if($request->has('to') && !empty($request->to)){
            $waywill->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->to);
        }

        return $waywill->withTrashed()->offset($start)->limit($limit)->orderBy('id','desc')->get();
    }

    /**
    * get the all returns order here and also based on fillter
    */
    public function getAdminActualStaticsReturnOrders($request, $start, $limit){
        $query = $this->newQuery();
        $query->distinct();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name');
        
        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {            
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'unprocessed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } elseif (!$request->has('order_type')){
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->from,$request->to));
        }

        if($request->has('from') && !empty($request->from)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->from);
        }

        if($request->has('to') && !empty($request->to)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->to);
        }
        
        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }
        
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            return $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name', DB::raw('count(*) as waywill_total'))
                ->orderBy('reverse_logistic_waybills.id','desc')
                ->groupBy('reverse_logistic_waybills.way_bill_number')
                ->withMeta()->distinct()->offset($start)->limit($limit)->get();
        } else {
            return $query->withMeta()->orderBy('reverse_logistic_waybills.id','desc')->offset($start)->limit($limit)->get();
        }
    }

    /**
    * all order to S3 buket
    *
    **/
    public function getApiAllReturnOrders($request){
        $query = $this->newQuery();
        
        # date fillter...
        if($request->has('from') && !empty($request->from) && $request->has('to') && !empty($request->to)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->from,$request->to));
        } elseif ($request->has('from') && !empty($request->from)) {
            # code...
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->from);
        } elseif ($request->has('to') && !empty($request->to)) {
            # code...
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->to);
        }

        $query->withMeta();

        return $query->where('status', 'Success')->orderBy('id','desc')->get();
    }

    /**
    * get the all returns order here and also based on fillter
    */
    public function getAllClientReturnOrders($request,$start, $limit){
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'unprocessed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->from,$request->to));
        }

        if($request->has('from') && !empty($request->from)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->from);
        }

        if($request->has('to') && !empty($request->to)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->to);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        return $query->withMeta()->withTrashed()->orderBy('reverse_logistic_waybills.id','desc')->offset($start)->limit($limit)->get();
    }

    public function getClientTotalReturnOrders(){
        $query = $this->newQuery();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*','c.name as client_name');
        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        return $query->withMeta()->withTrashed()->orderBy('reverse_logistic_waybills.id','desc')->get()->count();
    }

    /**
    * get the all returns order here and also based on fillter
    * from admin side
    */
    public function getAllReturnOrdersToExcel($request){
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else{
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id',$request->client);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        } else{
            $query->whereBetween('created_at',[$dateS,$dateE]);
        }

        return $query->withMeta()->withTrashed()->orderBy('id','desc')->get();
    }

    /**
    * get the all actual returns order here and also based on fillter
    * from admin side
    */
    public function getAllActualOrdersToExcel($request){
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();
        $query->distinct();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name');

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed')->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        } else{
            $query->whereBetween('reverse_logistic_waybills.created_at',[$dateS,$dateE]);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }
        
        $query->withMeta();

        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            return $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name', DB::raw('count(*) as waywill_total'))->orderBy('reverse_logistic_waybills.id','desc')->groupBy('reverse_logistic_waybills.way_bill_number')->distinct()->get();
        } else {
            return $query->orderBy('reverse_logistic_waybills.id','desc')->get();
        }
    }


    /**
    * get the all previous returns order here and also based on fillter
    * from admin side and client admin side
    */
    public function getAllPreviousReturnOrders($request){
        // \DB::enableQueryLog();
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else{
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id', $request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('shippingboxbarcode') && !empty($request->shippingboxbarcode)){
            $query->where('shippingBoxBarcode',$request->shippingboxbarcode);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        

        // $query->whereBetween('created_at',[$dateS,$dateE]);
        
        return $query->withMeta()->withTrashed()->orderBy('id','desc')->paginate(50);
    }

    /**
    * get the all previous returns order here and also based on fillter
    * from admin side
    */
    public function getAllPreviousActualReturnOrders($request){
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();        
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number');

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed')->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }        

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }
        
        $query->withMeta();

        // dd(str_replace_array('?', $query->getBindings(), $query->toSql()));
        // $query->whereBetween('created_at',[$dateS,$dateE]);

        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            return $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', DB::raw('count(*) as waywill_total'))
                ->orderBy('reverse_logistic_waybills.id','desc')
                ->groupBy('reverse_logistic_waybills.way_bill_number')
                ->distinct()
                ->paginate(Config('constants.adminDefaultPerPage'));
        } else {
            $query->withTrashed();
            return $query->orderBy('reverse_logistic_waybills.id','desc')->paginate(Config('constants.adminDefaultPerPage'));
        }
    }

    /**
    * get the all returns order here and also based on fillter
    * from admin side
    */
    public function getAllPreviousReturnOrdersToExcel($request){
        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else{
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id',$request->client);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        return $query->withMeta()->withTrashed()->orderBy('id','desc')->get();
    }

    /**
    * get the all actual returns order here and also based on fillter
    * from admin side
    */
    public function getAllPreviousActualOrdersToExcel($request){
        $query = $this->newQuery();
        $query->distinct();
        $query->join('users as c','reverse_logistic_waybills.client_id','=','c.id');
        $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name');

        if(Auth::user()->user_type_id==2){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==3){
            $obj = new \App\Models\UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('reverse_logistic_waybills.client_id',$ids);
        }

        if(Auth::user()->user_type_id==6){
            $query->where('reverse_logistic_waybills.client_id',Auth::id());
        }

        if(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('reverse_logistic_waybills.created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('reverse_logistic_waybills.status', 'Pending');
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed');
            $query->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('reverse_logistic_waybills.status', 'Success')->where('reverse_logistic_waybills.process_status', 'unprocessed')->where('reverse_logistic_waybills.cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('reverse_logistic_waybills.status', 'Success')->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
            $query->where('reverse_logistic_waybills.process_status', 'processed');
        } elseif ($request->has('order_type') && !empty($request->order_type) && in_array($request->order_type, ['Delivered', 'Shipment completed', 'Shipment Completed', 'Processed for return'])) {
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('shipment_status', $request->order_type);
                });
            });
        } else{
            $query->whereNotIn('reverse_logistic_waybills.status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('reverse_logistic_waybills.way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('reverse_logistic_waybills.client_id',$request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(reverse_logistic_waybills.created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('reverse_logistic_waybills.tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('reverse_logistic_waybills.id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }
        
        $query->withMeta();

        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            return $query->select('reverse_logistic_waybills.*', 'reverse_logistic_waybills.way_bill_number', 'c.name as client_name', DB::raw('count(*) as waywill_total'))->orderBy('reverse_logistic_waybills.id','desc')->groupBy('reverse_logistic_waybills.way_bill_number')->distinct()->get();
        } else {
            return $query->orderBy('reverse_logistic_waybills.id','desc')->get();
        }
    }

    /**
     * 05-october-2021
    * get the all monthly returns order here and also based on fillter
    * from client side
    */
    public function getAllMonthlyReturnOrders($request){
        // \DB::enableQueryLog();
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } else {
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id', $request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        $query->whereBetween('created_at',[$dateS,$dateE]);
        
        return $query->withMeta()->withTrashed()->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }

    /**
     * 05-october-2021
    * get the all monthly returns order here and also based on fillter
    * from client side
    */
    public function getAllClientPreviousReturnOrders($request){
        // \DB::enableQueryLog();
        $dateS = Carbon::now()->subMonth(1);
        $dateE = Carbon::now();

        $query = $this->newQuery();

        if(Auth::user()->user_type_id==2){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==3){
            $obj = new UserOwnerMapping;
            $client_list = $obj->getOwnerClients(Auth::id());
            $ids = Arr::pluck($client_list, 'user_id');
            array_push($ids, Auth::id());
            $query->whereIn('client_id',$ids);
        } elseif(Auth::user()->user_type_id==4 || Auth::user()->user_type_id==5){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('client_id', Auth::user()->created_by);
                    if($request->has('status')){
                        $query->where('status',$request->status)->whereNotIn('status',['Deleted']);
                    } else{
                        $query->whereNotIn('status',['Deleted']);   
                    }
                });
                $q->orWhere('created_by',Auth::id());
            });
        }

        # fillters calling here...
        if($request->has('refund_status') && !empty($request->refund_status)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('refund_status', $request->refund_status);                    
                });
            });
        }        

        # order type fillters...
        if($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'new'){
            $query->where(function($q) use ($request){
                $q->where('status', 'Pending');
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('id'))
                        ->from('reverse_logistic_waybills')
                        ->where('status', 'Success');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'intransit') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed');
            $query->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereNotIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'inscan') {
            $query->where('status', 'Success')->where('process_status', 'unprocessed')->where('cancel_return_status', '=', null);
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('owner_id'))
                        ->from('meta')
                        ->where('key', '=', '_order_waywill_status');
                });
            });
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'cancel') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('cancel_return_status', '!=', '');
        } elseif ($request->has('order_type') && !empty($request->order_type) && $request->order_type == 'at_hub') {
            $query->where('status', 'Success')->whereNotIn('status',['Deleted']);
            $query->where('process_status', 'processed');
        } else {
            $query->whereNotIn('status',['Deleted']);
        }

        # waywill fillter...
        if($request->has('way_bill_number') && !empty($request->way_bill_number)){
            $query->where('way_bill_number',$request->way_bill_number);
        }

        # client fillter...
        if($request->has('client') && !empty($request->client)){
            $query->where('client_id', $request->client);
        }

        # date fillter...
        if($request->has('start') && $request->has('end') && !empty($request->start) && !empty($request->end)){
            $query->whereBetween(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),array($request->start,$request->end));
        } elseif ($request->has('start') && !empty($request->start)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),">=",$request->start);
        } elseif ($request->has('end') && !empty($request->end)){
            $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y/%m/%d'))"),"<=",$request->end);
        }

        # fillter here...
        if($request->has('by_source') && !empty($request->by_source)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source' , '=' , $request->by_source);
            });
        }

        if($request->has('by_csr') && !empty($request->by_csr)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_source_name' , 'like' , '%'.$request->by_csr.'%');
            });
        }

        if($request->has('by_exception') && !empty($request->by_exception)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_waiver' , '=' , $request->by_exception);
            });
        }

        if($request->has('by_warehouse') && !empty($request->by_warehouse)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_consignee_name' , 'like' , '%'.$request->by_warehouse.'%');
            });
        }

        if($request->has('customer_name') && !empty($request->customer_name)){
            $query->where(function($query) use ($request){
                $query->whereMeta('_customer_name' , 'like' , '%'.$request->customer_name.'%');
            });
        }

        if($request->has('customer_email') && !empty($request->customer_email)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_email' , 'like' , '%'.$request->customer_email.'%');
            });
        }

        if($request->has('by_country') && !empty($request->by_country)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('_customer_country' , '=' , $request->by_country);
            });
        }

        # filtter ..
        if($request->has('tracking_id') && !empty($request->tracking_id)){
            $query->where('tracking_id',$request->tracking_id);
        }

        if($request->has('hs_code') && !empty($request->hs_code)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('hs_code', $request->hs_code);
                });
            });
        }

        if($request->has('sku') && !empty($request->sku)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('bar_code', $request->sku);
                });
            });
        }

        if($request->has('return_reson') && !empty($request->return_reson)){
            $query->where(function($q) use ($request){
                $q->whereIn('id', function($query) use ($request){
                    $query->select(DB::raw('reverse_logistic_waybill_id'))
                        ->from('package_details')
                        ->where('return_reason', $request->return_reson);                    
                });
            });
        }

        if($request->has('shipment_status') && !empty($request->shipment_status)){
            $query->where(function($query) use ($request){                
                $query->whereMeta('shipment_status' , '=' , $request->shipment_status);
            });
        }

        // $query->whereBetween('created_at',[$dateS,$dateE]);
        
        return $query->withMeta()->withTrashed()->orderBy('id','desc')->paginate(Config('constants.adminDefaultPerPage'));
    }
}
