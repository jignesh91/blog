<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $guarded = [];

    public function author()
    {
    	return $this->belongsTo('App\user','author_id');
    }
}
