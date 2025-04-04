<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarrierProduct;
use App\Models\CarrierService;

class Carrier extends Model
{
    public function product(){
        return $this->hasMany(CarrierProduct::class);
    }

    public function service(){
        return $this->hasMany(CarrierService::class);
    }

    public function getProductListAttribute()
    {
        return $this->product->pluck('name')->implode(',');
    }

    public function getServiceListAttribute()
    {
        return $this->service->pluck('name')->implode(',');
    }
}
