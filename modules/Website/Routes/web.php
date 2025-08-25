<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix(config('systemcore.adminPanelRoutePath'))->group(function() {
    
});

Route::get('/'              , 'WebsiteController@index')->name('WebsiteController@index');
Route::get('privacy-policy' , 'WebsiteController@privacyPolicy')->name('WebsiteController@privacyPolicy');
Route::post('contact-us'    , 'WebsiteController@postContactRequest')->name('WebsiteController@postContactRequest');

Route::prefix('social/auth')->group(function() {
    Route::get('facebook'           , 'WebsiteAuthController@loginByFacebook')->name('WebsiteAuthController@loginByFacebook');
    Route::get('post-facebook'      , 'WebsiteAuthController@postLoginByFacebook')->name('WebsiteAuthController@postLoginByFacebook');

    Route::get('google'             , 'WebsiteAuthController@loginByGoogle')->name('WebsiteAuthController@loginByGoogle');
    Route::get('post-google'        , 'WebsiteAuthController@postLoginByGoogle')->name('WebsiteAuthController@postLoginByGoogle');
});
