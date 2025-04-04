<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{    
    /**
     * Get the user that owns the phone.
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }
}
