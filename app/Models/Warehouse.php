<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    
    public function getCountry(){
    	return $this->belongsTo('\App\Models\Country','country_id');
    }

    public function getstates()
    {
        return $this->belongsTo('App\Models\State' ,'state');
    }
}
