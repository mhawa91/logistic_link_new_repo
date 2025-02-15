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

    Route::prefix('contact-us/dashboard')->group(function() {
        Route::get('get-counts'           , 'ContactUsDashboardController@getCount')->name('ContactUsDashboardController@getCount');
    });

    Route::prefix('contact-us')->group(function() {
        Route::get('list'           , 'ContactUsController@index')->name('ContactUsController@index');
        Route::post('list'          , 'ContactUsController@postIndex')->name('ContactUsController@postIndex');
        Route::get('create'         , 'ContactUsController@create')->name('ContactUsController@create');
        Route::post('create'        , 'ContactUsController@postCreate')->name('ContactUsController@postCreate');
        Route::get('update/{model}' , 'ContactUsController@update')->name('ContactUsController@update');
        Route::post('update'        , 'ContactUsController@postUpdate')->name('ContactUsController@postUpdate');
        Route::post('delete'        , 'ContactUsController@postDelete')->name('ContactUsController@postDelete');
        Route::post('restore'       , 'ContactUsController@postRestore')->name('ContactUsController@postRestore');
        Route::post('perma-delete'  , 'ContactUsController@postPermaDelete')->name('ContactUsController@postPermaDelete');
        Route::post('status-update' , 'ContactUsController@postStatus')->name('ContactUsController@postStatus');
        Route::get('select'         , 'ContactUsController@select')->name('ContactUsController@select');
        Route::get('{model?}'       , 'ContactUsController@read')->name('ContactUsController@read');
        Route::post('{model}'       , 'ContactUsController@postRead')->name('ContactUsController@postRead');
    });
});
