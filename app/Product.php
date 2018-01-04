<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  public function level_one()
  {
      return $this->belongsTo('App\CatLevelOne');
  }

  public function level_two()
  {
      return $this->belongsTo('App\CatLevelTwo');
  }

  public function level_three()
  {
      return $this->belongsTo('App\CatLevelThree');
  }

  public function seller()
  {
      return $this->belongsTo('App\Seller')->withDefault([
        'status' => 'true',
      ]);
  }

  public function brand()
  {
      return $this->belongsTo('App\Brand');
  }

  public function rack()
  {
      return $this->belongsTo('App\Rack');
  }

  public function product_images()
  {
    return $this->hasMany('App\ProductImage');

  }

  public function reviews()
  {
      return $this->hasMany('App\Review');
  }
}
