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

    Route::prefix('transporters')->group(function() {
        Route::get('list'           , 'TransportersController@index')->name('TransportersController@index');
        Route::post('list'          , 'TransportersController@postIndex')->name('TransportersController@postIndex');
        Route::get('create'         , 'TransportersController@create')->name('TransportersController@create');
        Route::post('create'        , 'TransportersController@postCreate')->name('TransportersController@postCreate');       
        Route::get('update/{model}' , 'TransportersController@update')->name('TransportersController@update');
        Route::post('update'        , 'TransportersController@postUpdate')->name('TransportersController@postUpdate');       
        Route::post('delete'        , 'TransportersController@postDelete')->name('TransportersController@postDelete');       
        Route::post('restore'       , 'TransportersController@postRestore')->name('TransportersController@postRestore');     
        Route::post('perma-delete'  , 'TransportersController@postPermaDelete')->name('TransportersController@postPermaDelete');
        Route::post('status-update' , 'TransportersController@postStatus')->name('TransportersController@postStatus');       
        Route::get('select'         , 'TransportersController@select')->name('TransportersController@select');
        Route::post('export'        , 'TransportersController@postExport')->name('TransportersController@postExport');       
        Route::get('{model}'        , 'TransportersController@read')->name('TransportersController@read');
        Route::post('{model}'       , 'TransportersController@postRead')->name('TransportersController@postRead');
    });
});
