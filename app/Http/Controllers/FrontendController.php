<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class FrontendController extends Controller
{
    public function getSlider()
    {
      $sliders = \App\Slider::all();
      return $sliders->toJson();
    }

    public function getCategories()
    {
        $categories = \App\CatLevelOne::with('level_twos')
                                        ->where('frontend', 'true')
                                        ->where('status', 'true')
                                        ->get();
        return $categories->toJson();

    }

    public function getLevelOne()
    {
      $level_ones = \App\CatLevelOne::where('status', 'true')
                                      ->where('frontend', 'true')
                                      ->get();
      return $level_ones->toJson();
    }

    public function getLevelTwo()
    {
      $level_twos = \App\CatLevelTwo::where('status', 'true')
                                      ->where('frontend', 'true')
                                      ->get();
      return $level_twos->toJson();
    }

    public function getBrand()
    {
        $brands = \App\Brand::where('status', 'true')
                              ->orderBy('id', 'desc')
                              ->get();
        return $brands->toJson();
    }

    public function getDeal()
    {
        $datetime = date('Y-m-d H:i:s');
        $deal_todays = \DB::table('deal_todays')
                            ->where('status', 'true')
                            ->whereTime('end_date', '<', $datetime)
                            ->get();
        return $deal_todays->toJson();
    }

    public function getHotlist()
    {
        $hotlists = \App\HotList::where('status', 'true')
                                  ->teke('10')
                                  ->get();
        return $hotlists->toJson();
    }

    public function getBigBanner($id)
    {
        $big = \App\BannerBig::where('cat_level_one_id', $id)
                              ->where('status', 'true')
                              ->take(1)
                              ->get();
        return $big->toJson();
    }

    Public function getSmallBanner($id)
    {
      $smalls = \App\BannerSmall::where('cat_level_one_id', $id)
                                 ->where('status', 'true')
                                 ->take(3)
                                 ->get();
      return $smalls->toJson();
    }
}
