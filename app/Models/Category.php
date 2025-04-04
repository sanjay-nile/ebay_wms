<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function subcategory()
    {
        return $this->hasMany(\App\Models\Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(\App\Models\Category::class, 'parent_id');
    }
}
