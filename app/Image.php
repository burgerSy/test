<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Redis;

class Image extends Model
{
    //
    
    // Redis::connection();

    public $table = 'images';
    public $fillable = ['url'];
}
