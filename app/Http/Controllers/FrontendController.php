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
}
