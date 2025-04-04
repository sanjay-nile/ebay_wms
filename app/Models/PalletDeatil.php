<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zoha\Metable;

class PalletDeatil extends Model
{
    use Metable;
    
    public function client()
    {
        return $this->belongsTo('App\User');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function shipmentType()
    {
        return $this->hasOne('App\Models\ShippingType','id','shipping_type_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\ReverseLogisticWaybill','pallet_id','pallet_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\PackageDetail','pallet_id','pallet_id');
    }

    public function children(){
        return $this->hasMany( 'App\Models\PalletDeatil', 'parent', 'id' );
    }

    public function parent(){
        return $this->hasOne( 'App\Models\PalletDeatil', 'id', 'parent' );
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post','pallet_id','pallet_id');
    }
}
