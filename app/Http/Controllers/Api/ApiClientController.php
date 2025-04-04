<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReverseLogisticWaybill;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiClientController extends Controller
{

    // Client Profile
    public function clientProfile()
    {
        $user = Auth::user();
        // Associated data
        $address_details = $user->getMeta('address_details');
        $warehouses      = \App\Models\Warehouse::where(['user_id' => $user->id])->orderBy('id', 'DESC')->get()->toArray();
        $warehouses      = array_map(function ($f) {
            $f['country_name'] = get_country_name_by_id($f['country_id']);
            $f['id']           = (string) $f['id'];
            $f['country_id']   = (string) $f['country_id'];
            $f['user_id']      = (string) $f['user_id'];
            return $f;
        }, $warehouses);

        $ship_obj = new \App\Models\ShippingPolicy;

        $shipments    = $ship_obj->getShipmentCarrierListBYClientId($user->id, 'shipment');
        $charges      = $ship_obj->getShipmentCarrierListBYClientId($user->id, 'charges');
        $country_list = \App\Models\Country::where(['status' => '1'])->get();

        unset($user->metarelation);
        $user->address        = $address_details;
        $user->warehouses     = $warehouses;
        $user->shipment_types = $shipments;
        $user->other_charges  = $charges;

        return response()->json(['status' => true, 'msg' => 'Success', 'data' => $user, 'country_list' => $country_list], 200);
    }

    // Client Dashboard
    public function clientDashboard()
    {
        $total_reverse_order = ReverseLogisticWaybill::where(['client_id' => Auth::id()])->count();
        $total_order         = ReverseLogisticWaybill::where(['client_id' => Auth::id()])->whereYear('created_at', '=', date('Y'))->get();
        $total_client_user   = \App\Models\UserOwnerMapping::where(['owner_id' => Auth::id()])->count();
        $data['month']       = \Config::get('constants.dateArr');
        $val                 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $mn_val              = [];
        foreach ($total_order as $order) {
            $mn = date('F', strtotime($order->created_at));
            if (isset($mn_val[$mn])) {
                $mn_val[$mn] += 1;
            } else {
                $mn_val[$mn] = 1;
            }
        }

        foreach ($data['month'] as $k => $v) {
            if (isset($mn_val[$v])) {
                $val[$k] = $mn_val[$v];
            }
        }

        $data['month_order'] = $val;
        return response()->json([
            'status'              => true,
            'msg'                 => 'Success',
            'data'                => $data,
            'total_reverse_order' => $total_reverse_order,
            'total_client_user'   => $total_client_user,
        ], 200);
    }

    // Client User List
    public function clientUserList()
    {
        $ob    = new \App\User;
        $users = $ob->getUserWithOwnerByTypeId(null, Auth::id());
        return response()->json(['status' => true, 'msg' => 'Success', 'data' => $users], 200);
    }

    // Client Reverse logistic list
    public function clientReverseLogisticList(Request $request)
    {
        $request->request->add(['api_type'=> true]);
        $obj   = new \App\Models\ReverseLogisticWaybill;
        $lists = $obj->getReverLogisticList($request);
        if ($lists) {
            foreach ($lists as $row) {
                $row->_client_code               = $row->meta->_client_code;
                $row->_customer_code             = $row->meta->_customer_code;
                $row->_customer_name             = $row->meta->_customer_name;
                $row->_customer_email            = $row->meta->_customer_email;
                $row->_customer_address          = $row->meta->_customer_address;
                $row->_customer_country          = $row->meta->_customer_country;
                $row->_customer_state            = $row->meta->_customer_state;
                $row->_customer_city             = $row->meta->_customer_city;
                $row->_customer_pincode          = $row->meta->_customer_pincode;
                $row->_customer_phone            = $row->meta->_customer_phone;
                $row->_service_code              = $row->meta->_service_code;
                $row->_cash_on_pickup            = $row->meta->_cash_on_pickup;
                $row->_number_of_packages        = $row->meta->_number_of_packages;
                $row->_weight_unit_type          = $row->meta->_weight_unit_type;
                $row->_actual_weight             = $row->meta->_actual_weight;
                $row->_charged_weight            = $row->meta->_charged_weight;
                $row->_description               = $row->meta->_description;
                $row->_rate                      = $row->meta->_rate;
                $row->_consignee_code            = $row->meta->_consignee_code;
                $row->_consignee_name            = $row->meta->_consignee_name;
                $row->_consignee_phone           = $row->meta->_consignee_phone;
                $row->_consignee_address         = $row->meta->_consignee_address;
                $row->_consignee_country         = $row->meta->_consignee_country;
                $row->_consignee_state           = $row->meta->_consignee_state;
                $row->_consignee_city            = $row->meta->_consignee_city;
                $row->_consignee_pincode         = $row->meta->_consignee_pincode;
                $row->_carrier_name              = $row->meta->_carrier_name;
                $row->_shipment_name             = $row->meta->_shipment_name;
                $row->_label_message             = $row->meta->_label_message;
                $row->_label_message_type        = $row->meta->_label_message_type;
                $row->_label_message_status      = $row->meta->_label_message_status;
                $row->_label_package_sticker_url = $row->meta->_label_package_sticker_url;
                $row->_label_url                 = $row->meta->_label_url;
                $row->_order_tracking_id         = $row->meta->_order_tracking_id;
                unset($row->metarelation);
                $row->packages;
            }
        }

        return response()->json(['status' => true, 'msg' => 'Success', 'data' => $lists], 200);

    }

    // Update client profile
    public function updateClientProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'contact_person' => 'required|max:50|min:2',
            'phone'          => 'required|max:15|min:8',
            'department'     => 'required|max:191',
            'role'           => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => $validator->errors()->first(), 'status' => false, 'data' => []], 422);
        }
        DB::beginTransaction();
        try {
            $user = \App\User::find($id);
            if (!$user) {
                return response()->json(['msg' => "Client not found", 'status' => false, 'data' => []], 404);
            }
            $user->contact_person_name = $request->contact_person;
            $user->phone               = $request->phone;
            $user->department          = $request->department;
            $user->role                = $request->role;
            $user->save();

        } catch (Exceptin $ex) {
            DB::rollback();
            return response()->json(['msg' => $ex->getMessage(), 'status' => false, 'data' => []], 400);
        }
        DB::commit();
        return response()->json(['msg' => 'Client has been updated successfully', 'status' => true, 'data' => $user], 200);
    }

    // Update client address
    public function updateClientAddress(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'billing_first_name'   => 'required|max:50|min:2',
            'billing_last_name'    => 'required|max:50|min:2',
            'billing_email'        => 'required|max:50|min:2|email',
            'billing_company_name' => 'required|max:50|min:2',
            'billing_address_1'    => 'required|max:50|min:2',
            'billing_country'      => 'required',
            'billing_state'        => 'required',
            'billing_postal_code'  => 'required',
            'billing_phone'        => 'required|max:15|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => $validator->errors()->first(), 'status' => false, 'data' => []], 422);
        }
        DB::beginTransaction();
        try {
            $user = \App\User::find($id);
            if (!$user) {
                return response()->json(['msg' => "Client not found", 'status' => false, 'data' => []], 404);
            }
            $arr = [
                'billing_first_name'   => $request->billing_first_name,
                'billing_last_name'    => $request->billing_last_name,
                'billing_email'        => $request->billing_email,
                'billing_company_name' => $request->billing_company_name,
                'billing_address_1'    => $request->billing_address_1,
                'billing_address_2'    => $request->billing_address_2,
                'billing_country'      => $request->billing_country,
                'billing_state'        => $request->billing_state,
                'billing_city'         => $request->billing_city,
                'billing_postal_code'  => $request->billing_postal_code,
                'billing_phone'        => $request->billing_phone,
            ];
            $user->setMeta('address_details', $arr);

            $address_details = $user->getMeta('address_details');
            $address         = $address_details;

        } catch (Exceptin $ex) {
            DB::rollback();
            return response()->json(['msg' => $ex->getMessage(), 'status' => false, 'data' => []], 400);
        }
        DB::commit();
        return response()->json(['msg' => 'Client address has been updated successfully', 'status' => true, 'data' => $address], 200);
    }

    public function clientRefWaybillStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id'             => 'required',
            'shipment_policy_id'    => 'required',
            'customer_name'         => 'required|max:50',
            'customer_email'        => 'required|max:50',
            'customer_address'      => 'required|max:191',
            'customer_country_code' => 'required|max:50',
            'customer_state_code'   => 'required|max:50',
            'customer_city'         => 'required|max:50',
            'customer_pincode'      => 'max:15',
            'customer_phone'        => 'required|min:8|max:15',
            'way_bill_number'       => 'max:191',
            'delivery_date'         => 'max:25',
            'payment_mode'          => 'required|max:50',
            'service_code'          => 'required|max:50',
            'cash_on_pickup'        => 'max:50',
            'amount'                => 'max:50',
            'number_of_packages'    => 'required|digits_between:0,10|numeric',
            'weight_unit_type'      => 'required|max:50',
            'actual_weight'         => 'required|digits_between:0,10|numeric',
            'charged_weight'        => 'digits_between:0,10|numeric',
            'description'           => 'max:5000',
            'warehouse_id'          => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Config('constants.reverseGearSecureKey') != $request->secureKey) {
            return response()->json(['status' => false, 'data' => [], 'message' => "Secure Key is not valid"], 400);
        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'title')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all title'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'packageCount')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package count'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'length')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package length'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'width')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package width'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'height')) {
            return (new \Illuminate\Http\Response)->setStatusCode(422, 'Please fill all package height');

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'weight')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package weight'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'chargedWeight')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package charged weight'], 422);

        } else if ($this->check_validation($request->packageDetails['packageJsonString'], 'selectedPackageTypeCode')) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Please fill all package selectedPackageTypeCode'], 422);

        }

        $war_obj = \App\Models\Warehouse::where(['id' => $request->warehouse_id, 'user_id' => $request->client_id])->first();
        if (!$war_obj) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Warehouse not found'], 400);
        }

        $country_data = $war_obj->getCountry;

        $request->request->add(['consignee_code' => '00000']);
        $request->request->add(['consignee_name' => $war_obj->name]);
        $request->request->add(['consignee_phone' => $war_obj->phone]);
        $request->request->add(['consignee_address' => $war_obj->address]);
        $request->request->add(['consignee_country' => $country_data->sortname]);
        $request->request->add(['consignee_country_name' => $country_data->name]);
        $request->request->add(['consignee_state' => $war_obj->state_code]);
        $request->request->add(['consignee_city' => $war_obj->city]);
        $request->request->add(['consignee_pincode' => $war_obj->zip_code]);

        $request->request->add(['login_id' => $request->client_id]);
        $request->request->add(['customer_code' => '00000']);
        $request->request->add(['type' => 'I']);
        $request->request->add(['status' => 'Success']);
        $request->request->add(['created_from' => 'CLIENT-REF']);
        $request->request->add(['client_code' => 'REVERSEGEAR']);

        $shipping_obj = new \App\Models\ShippingPolicy;
        $shipment     = $shipping_obj->getShipmentCarrierDetailById($request->shipment_policy_id);
        if (!$shipment) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Shipment not found'], 400);

        }
        $request->request->add(['carrier_name' => $shipment->carrier_name]);
        $request->request->add(['shipment_name' => $shipment->shipment_name]);
        $request->request->add(['rate' => $shipment->rate]);
        $request->request->add(['currency' => $shipment->currency]);

        return createClientRefWayBillNumber($request->all());
    }

    public function check_validation($request, $field)
    {

        if (isset($request) && array_filter($request, function ($e) use ($field) {
            if (empty($e[$field])) {
                return true;
            }
        }) == false) {
            return false;
        } else {
            return true;
        }

    }

}
