<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatLevelTwo extends Model
{

  

    public function level_one()
    {
        return $this->belongsTo('App\CatLevelOne');
    }

    public function level_threes()
    {
      return $this->hasMany('App\CatLevelThree');
    }
}
