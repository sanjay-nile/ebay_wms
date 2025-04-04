<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCarrierStatus extends Model
{
    protected $fillable = ['parcel_code', 'parcel_status_id'];
}
