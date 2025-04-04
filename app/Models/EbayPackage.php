<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zoha\Metable;

class EbayPackage extends Model
{
    use Metable;

    public function history(){
        return $this->hasMany('App\Models\StatusHistory', 'post_id', 'id')->where('type', 'ins_level');
    }

    public function pallet()
    {
        return $this->hasOne('App\Models\PalletDeatil','pallet_id','pallet_id');
    }
}
