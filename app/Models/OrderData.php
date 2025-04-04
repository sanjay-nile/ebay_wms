<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    protected $fillable = ['order_id'];

    /**
     * Get the comments for the blog post.
     */
    public function items()
    {
        return $this->hasMany('App\Models\OrderItem', 'order_data_id', 'id');
    }

    public function itemShipped(){
        return $this->items()->where('order_status','=', 'SHP');
    	
    }
}
