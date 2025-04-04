<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zoha\Metable;
use App\Models\PostExtra;

class Post extends Model
{
    use Metable;
    protected $table = 'posts';

    /**
     * Get the comments for the blog post.
    */
    public function post_extras()
    {
        return $this->hasMany('App\Models\PostExtra', 'post_id', 'id');
    }

    public function metas(){
        return $this->hasMany(PostExtra::class);
    }

    public function order_items(){
    	return $this->hasOne('App\Models\OrdersItem','order_id','id');
    }

    public function package()
    {
        return $this->hasMany('App\Models\EbayPackage', 'post_id', 'id');
    }


    public function us_package(){
        return $this->hasMany('App\Models\AmsPackage', 'post_id', 'id')->whereNotNull('pallet_id');
    }

    public function package_list(){
        return $this->hasMany('App\Models\AmsPackage', 'post_id', 'id')->whereNull('pallet_id')->whereNull('uk_pallet_id');
    }

    public function vendor_package()
    {
        return $this->hasMany('App\Models\ElPackage', 'post_id', 'id');
    }

    public function pallet()
    {
        return $this->hasOne('App\Models\PalletDeatil','pallet_id','pallet_id');
    }

    public function client()
    {
        return $this->hasOne('App\User','id','client_id');
    }

    public function warehouse()
    {
        return $this->hasOne('App\Models\Warehouse','id','warehouse_id');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','post_author_id');
    }

    public function location() {
        return $this->belongsTo(Post::class, 'location_id')->where('post_type', 'rack');
    }
}
