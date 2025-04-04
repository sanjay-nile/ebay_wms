<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCarrierData extends Model
{
    protected $fillable = ['order_ref', 'parcel_code'];
}
