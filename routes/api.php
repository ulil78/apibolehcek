<?php

Route::post('/auth/signup', 'AuthController@postSignup');
Route::post('/auth/signin', 'AuthController@postSignin');


// Frontend Content
Route::get('/sliders', 'FrontendController@getSlider');
Route::get('/brands', 'FrontendController@getBrand');
Route::get('/infos', 'FrontendController@getInfo');
Route::get('/deals', 'FrontendController@getDeal');
Route::get('/hotlists', 'FrontendController@getHotlist');
Route::get('/banner/big/{id}', 'FrontendController@getBigBanner');
Route::get('/banner/small/{id}', 'FrontendController@getSmallBanner');

/* Menu Category */
Route::get('/categories', 'FrontendController@getCategories');
Route::get('/levelone', 'FrontendController@getLevelOne');
Route::get('/leveltwo', 'FrontendController@getLevelTwo');

/* Product */
Route::get('/product/level-one/{id}/', 'ProductFrontController@getProductLevelOne');
Route::get('/product/level-two/{id}/', 'ProductFrontController@getProductLevelTwo');
Route::get('/product/level-three/{id}/', 'ProductFrontController@getProductLevelThree');
Route::get('/product/detail/{id}/', 'ProductFrontController@getProductDetail');

//sellers
Route::get('/seller/{id}', 'ProductFrontController@getSeller');
Route::get('/product/seller/{id}', 'ProductFrontController@getSellerProduct');
Route::get('/review/product/{id}', 'ProductFrontController@getReview');


/* General */
Route::get('/about', 'GeneralController@getAbout');
Route::get('/contact', 'GeneralController@getContact');
Route::get('/official-partner', 'GeneralController@getOfficialPartner');
Route::get('/our-activity', 'GeneralController@getOurActivity');
Route::get('/seller-story', 'GeneralController@getSellerStory');
Route::get('/how-to-shop', 'GeneralController@getHowToShop');
Route::get('/how-to-sell', 'GeneralController@getHowToSell');
Route::get('/withdrawal', 'GeneralController@getWithdrawal');
Route::get('/payment', 'GeneralController@getPayment');
Route::get('/refund', 'GeneralController@getRefund');




// jwt.auth middleware
Route::group(['middleware' => ['jwt.auth']], function(){
  // User Section
  Route::get('user', 'UserController@show');
  Route::post('user/profile/update', 'UserController@updateProfile');
  Route::post('user/password/update', 'UserController@updatePassword');
});
