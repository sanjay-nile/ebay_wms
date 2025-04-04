<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FCMToken extends Model
{
    protected $table = 'user_fcm_token';

    protected $fillable = ['user_id','token','apns_id'];
}
