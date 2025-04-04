<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostExtra extends Model
{

    protected $table = 'post_extras';

    protected $fillable = [
        'post_id', 'key_name', 'key_value',
    ];

    protected $primaryKey = 'post_extra_id';
}
