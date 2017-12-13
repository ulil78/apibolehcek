<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
//
//
// });

Route::group(['middleware' => ['api']], function(){

  Route::post('/auth/signup', 'AuthController@postSignup');
  Route::post('/auth/signin', 'AuthController@postSignin');

  // Frontend Content
  Route::get('/sliders', 'FrontendController@getSlider');
  Route::get('/categories', 'FrontendController@getCategories');
  Route::get('/levelone', 'FrontendController@getLevelOne');
  Route::get('/leveltwo', 'FrontendController@getLevelTwo');



});
