<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatLevelThree extends Model
{
    public function level_two()
    {
        return $this->belongsTo('App\CatLevelTwo');
    }

    public function products()
    {
      return $this->hasMany('App\Product');
    }
}
