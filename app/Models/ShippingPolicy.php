<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPolicy extends Model
{
    public function shippingType()
    {
        return $this->hasOne('App\Models\ShippingType','id','shipping_type_id');
    }

    public function carrier()
    {
        return $this->hasOne('App\Models\Carrier','id','carrier_id');
    }

    public function getShipmentCarrierListBYClientId($client_id,$type){
    	$shipment = $this->newQuery();
        $select = array('shipping_policies.id','shipping_policies.user_id','shipping_policies.rate','shipping_policies.currency','shipping_policies.is_default');

        if($type=='shipment'){
            $shipment->join('shipping_types as st','st.id','=','shipping_policies.shipping_type_id');
            $shipment->join('carriers as c','c.id','=','shipping_policies.carrier_id');
            $custom = ['c.name as carrier_name','c.id as carrier_id','shipping_policies.carrier_id','shipping_policies.shipping_type_id','st.name as shipment_name', 'c.code'];
            $select = array_merge($select,$custom);
        }
    	
        if($type=='charges'){
            $shipment->join('other_charges as oc','oc.id','=','shipping_policies.other_charge_id');
            $custom = ['oc.name as charge_name','shipping_policies.other_charge_id'];
            $select = array_merge($select,$custom);
        }

    	$shipment->select($select);
    	$shipment->where(['shipping_policies.user_id'=>$client_id,'shipping_policies.status'=>'1','shipping_policies.type'=>$type]);
    	return $shipment->get();
    }

    public function getShipmentCarrierDetailById($id){
    	$shipment = $this->newQuery();
    	$shipment->join('shipping_types as st','st.id','=','shipping_policies.shipping_type_id');
    	$shipment->join('carriers as c','c.id','=','shipping_policies.carrier_id');
    	$shipment->select('c.name as carrier_name','shipping_policies.carrier_id','shipping_policies.shipping_type_id','st.name as shipment_name','shipping_policies.user_id','shipping_policies.rate','shipping_policies.id','shipping_policies.currency');
    	$shipment->where(['shipping_policies.id'=>$id,'shipping_policies.status'=>'1']);
    	return $shipment->first();
    }
}
