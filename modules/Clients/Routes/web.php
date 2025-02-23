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
    
    Route::prefix('clients')->group(function() {
        Route::get('list'           , 'ClientsController@index')->name('ClientsController@index');
        Route::post('list'          , 'ClientsController@postIndex')->name('ClientsController@postIndex');
        Route::get('create'         , 'ClientsController@create')->name('ClientsController@create');
        Route::post('create'        , 'ClientsController@postCreate')->name('ClientsController@postCreate');
        Route::get('update/{model}' , 'ClientsController@update')->name('ClientsController@update');
        Route::post('update'        , 'ClientsController@postUpdate')->name('ClientsController@postUpdate');
        Route::post('delete'        , 'ClientsController@postDelete')->name('ClientsController@postDelete');
        Route::post('restore'       , 'ClientsController@postRestore')->name('ClientsController@postRestore');
        Route::post('perma-delete'  , 'ClientsController@postPermaDelete')->name('ClientsController@postPermaDelete');
        Route::post('status-update' , 'ClientsController@postStatus')->name('ClientsController@postStatus');
        Route::get('select'         , 'ClientsController@select')->name('ClientsController@select');
        Route::get('{model}'        , 'ClientsController@read')->name('ClientsController@read');
        Route::post('{model}'       , 'ClientsController@postRead')->name('ClientsController@postRead');
    });
});
