<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatLevelOne extends Model
{

    

    public function level_twos()
    {
      return $this->hasMany('App\CatLevelTwo');
    }

    public function level_two_frontends()
    {

        return $this->hasMany('App\CatLevelTwo')->withDefault(function ($twos) {
            $twos->frontend = 'true';

        });

    }
}
