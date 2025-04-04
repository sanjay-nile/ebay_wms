<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zoha\Metable;
use DB as DBS;
class PackageDetail extends Model
{
    use Metable;

    public function order(){
        return $this->hasOne('App\Models\ReverseLogisticWaybill','id','reverse_logistic_waybill_id');
    }

    /*
    * Order Item value
    */
    public function OrderItem(){
        return $this->hasOne('App\Models\OrderItem','sku','bar_code')->where('order_id',$this->order->way_bill_number);
    }

    /*
    * Order Item value
    */
    public function packageItem(){
        return $this->hasOne('App\Models\OrderItem','sku','bar_code');
    }

    public function getorderItem(){
        return \App\Models\OrderItem::where('sku', $this->bar_code)->where('order_id', $this->order->way_bill_number)->first();
    }

    public function getsupplierCode(){        
        return DBS::table( 'meta' )->where(['key' => '_order_suppliercode', 'owner_id' => $this->order->id])->first();
    }

    public function pallet()
    {
        return $this->hasOne('App\Models\PalletDeatil','pallet_id','pallet_id');
    }

    /**
     * code by sanjay
     * common create package
     **/
    public function createPackageByWayBillId($request, $way_bill_id)
    {
        if (is_array($request['package_count']) && count($request['package_count']) > 0) {
            foreach ($request['package_count'] as $key => $value) {
                if (isset($request['package_arr'][$key]) && !empty($request['package_arr'][$key])) {
                    $package_obj = $this->find($request['package_arr'][$key]);
                } else {
                    $package_obj            = new $this;
                    $package_obj->file_data = (isset($request['upload_images'][$key])) ? json_encode($request['upload_images'][$key]) : '';
                }

                $package_obj->bar_code                    = $request['bar_code'][$key] ?? null;
                $package_obj->title                       = $request['title'][$key] ?? null;
                $package_obj->package_count               = $value;
                $package_obj->reverse_logistic_waybill_id = $way_bill_id;
                $package_obj->length                      = $request['length'][$key] ?? '10';
                $package_obj->width                       = $request['width'][$key] ?? '8';
                $package_obj->height                      = $request['height'][$key] ?? '3';
                $package_obj->weight                      = $request['weight'][$key] ?? '1';
                $package_obj->charged_weight              = $request['charged__weight'][$key] ?? '1';
                $package_obj->custom_price                = $request['custom_price'][$key] ?? '1';
                $package_obj->color                       = $request['color'][$key] ?? '';
                $package_obj->size                        = $request['size'][$key] ?? '';
                $package_obj->dimension                   = $request['dimension'][$key] ?? '';
                $package_obj->weight_unit_type            = $request['weight_unit_type'][$key] ?? '';
                $package_obj->estimated_value             = $request['estimated_value'][$key] ?? '';
                $package_obj->hs_code                     = $request['hs_code'][$key] ?? '';
                $package_obj->status                      = $request['status'][$key] ?? '';
                $package_obj->selected_package_type_code  = 'DOCUMENT';
                $package_obj->return_reason               = $request['item_return_reason'][$value] ?? '';
                if (isset($request['country_of_origin'][$key])) {
                    # code...
                    $package_obj->country_of_origin          = $request['country_of_origin'][$key] ?? '';
                }
                
                $package_obj->save();
            }
            return true;
        } else {
            return true;
        }
    }

    /**
     * code by sanjay
     * olive create package
     **/
    public function createOlivePackageByWayBillId($request, $way_bill_id)
    {
        if (is_array($request['item-select']) && count($request['item-select']) > 0) {
            foreach ($request['item-select'] as $key => $value) {
                if (isset($request['package_arr'][$value]) && !empty($request['package_arr'][$value])) {
                    $package_obj = $this->find($request['package_arr'][$value]);
                } else {
                    $package_obj            = new $this;
                    $package_obj->file_data = (isset($request['upload_images'][$value])) ? json_encode($request['upload_images'][$value]) : '';
                }

                $package_obj->bar_code                    = $request['bar_code'][$value] ?? null;
                $package_obj->title                       = $request['title'][$value] ?? null;
                $package_obj->price                       = $request['price'][$value] ?? 0;
                $package_obj->package_count               = $request['package_count'][$value] ?? '0';
                $package_obj->reverse_logistic_waybill_id = $way_bill_id;
                $package_obj->length                      = $request['length'][$value] ?? '10';
                $package_obj->width                       = $request['width'][$value] ?? '8';
                $package_obj->height                      = $request['height'][$value] ?? '3';
                $package_obj->weight                      = $request['weight'][$value] ?? '1';
                $package_obj->charged_weight              = $request['charged__weight'][$value] ?? '1';
                $package_obj->custom_price                = $request['custom_price'][$value] ?? '1';
                $package_obj->color                       = $request['color'][$value] ?? '';
                $package_obj->size                        = $request['size'][$value] ?? '';
                $package_obj->dimension                   = $request['dimension'][$value] ?? '';
                $package_obj->weight_unit_type            = $request['weight_unit_type'][$value] ?? '';
                $package_obj->estimated_value             = $request['estimated_value'][$value] ?? '';
                $package_obj->hs_code                     = $request['hs_code'][$value] ?? '';
                $package_obj->status                      = $request['status'][$value] ?? '';
                $package_obj->selected_package_type_code  = 'DOCUMENT';
                $package_obj->return_reason               = $request['item_return_reason'][$value] ?? '';
                $package_obj->image_url                  = (isset($request['image_of_item'][$value])) ? $request['image_of_item'][$value] : '';
                if (isset($request['country_of_origin'][$value])) {
                    # code...
                    $package_obj->country_of_origin          = $request['country_of_origin'][$value] ?? '';
                }
                $package_obj->save();
            }
            return true;
        } else {
            return true;
        }
    }
}
