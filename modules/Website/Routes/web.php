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
Route::post('contact-us'    , 'WebsiteController@postContactRequest')->name('WebsiteController@postContactRequest');
