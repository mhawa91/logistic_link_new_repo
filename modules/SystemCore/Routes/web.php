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

    Route::prefix('countries')->group(function() {
        Route::get('list'           , 'CountriesController@index')->name('CountriesController@index');
        Route::post('list'          , 'CountriesController@postIndex')->name('CountriesController@postIndex');
        Route::get('create'         , 'CountriesController@create')->name('CountriesController@create');
        Route::post('create'        , 'CountriesController@postCreate')->name('CountriesController@postCreate');
        Route::get('update/{model}' , 'CountriesController@update')->name('CountriesController@update');
        Route::post('update'        , 'CountriesController@postUpdate')->name('CountriesController@postUpdate');
        Route::post('delete'        , 'CountriesController@postDelete')->name('CountriesController@postDelete');
        Route::post('restore'       , 'CountriesController@postRestore')->name('CountriesController@postRestore');
        Route::post('perma-delete'  , 'CountriesController@postPermaDelete')->name('CountriesController@postPermaDelete');
        Route::post('status-update' , 'CountriesController@postStatus')->name('CountriesController@postStatus');
        Route::get('select'         , 'CountriesController@select')->name('CountriesController@select');
        Route::get('{model}'        , 'CountriesController@read')->name('CountriesController@read');
        Route::post('{model}'       , 'CountriesController@postRead')->name('CountriesController@postRead');
    });

    Route::prefix('cities')->group(function() {
        Route::get('list'           , 'CitiesController@index')->name('CitiesController@index');
        Route::post('list'          , 'CitiesController@postIndex')->name('CitiesController@postIndex');
        Route::get('create'         , 'CitiesController@create')->name('CitiesController@create');
        Route::post('create'        , 'CitiesController@postCreate')->name('CitiesController@postCreate');
        Route::get('update/{model}' , 'CitiesController@update')->name('CitiesController@update');
        Route::post('update'        , 'CitiesController@postUpdate')->name('CitiesController@postUpdate');
        Route::post('delete'        , 'CitiesController@postDelete')->name('CitiesController@postDelete');
        Route::post('restore'       , 'CitiesController@postRestore')->name('CitiesController@postRestore');
        Route::post('perma-delete'  , 'CitiesController@postPermaDelete')->name('CitiesController@postPermaDelete');
        Route::post('status-update' , 'CitiesController@postStatus')->name('CitiesController@postStatus');
        Route::get('select'         , 'CitiesController@select')->name('CitiesController@select');
        Route::get('{model}'        , 'CitiesController@read')->name('CitiesController@read');
        Route::post('{model}'       , 'CitiesController@postRead')->name('CitiesController@postRead');
    });
    
    Route::prefix('municipalities')->group(function() {
        Route::get('list'           , 'MunicipalitiesController@index')->name('MunicipalitiesController@index');
        Route::post('list'          , 'MunicipalitiesController@postIndex')->name('MunicipalitiesController@postIndex');
        Route::get('create'         , 'MunicipalitiesController@create')->name('MunicipalitiesController@create');
        Route::post('create'        , 'MunicipalitiesController@postCreate')->name('MunicipalitiesController@postCreate');
        Route::get('update/{model}' , 'MunicipalitiesController@update')->name('MunicipalitiesController@update');
        Route::post('update'        , 'MunicipalitiesController@postUpdate')->name('MunicipalitiesController@postUpdate');
        Route::post('delete'        , 'MunicipalitiesController@postDelete')->name('MunicipalitiesController@postDelete');
        Route::post('restore'       , 'MunicipalitiesController@postRestore')->name('MunicipalitiesController@postRestore');
        Route::post('perma-delete'  , 'MunicipalitiesController@postPermaDelete')->name('MunicipalitiesController@postPermaDelete');
        Route::post('status-update' , 'MunicipalitiesController@postStatus')->name('MunicipalitiesController@postStatus');
        Route::get('select'         , 'MunicipalitiesController@select')->name('MunicipalitiesController@select');
        Route::get('{model}'        , 'MunicipalitiesController@read')->name('MunicipalitiesController@read');
        Route::post('{model}'       , 'MunicipalitiesController@postRead')->name('MunicipalitiesController@postRead');
    });

    Route::prefix('neighbourhoods')->group(function() {
        Route::get('list'           , 'NeighbourhoodsController@index')->name('NeighbourhoodsController@index');
        Route::post('list'          , 'NeighbourhoodsController@postIndex')->name('NeighbourhoodsController@postIndex');
        Route::get('create'         , 'NeighbourhoodsController@create')->name('NeighbourhoodsController@create');
        Route::post('create'        , 'NeighbourhoodsController@postCreate')->name('NeighbourhoodsController@postCreate');
        Route::get('update/{model}' , 'NeighbourhoodsController@update')->name('NeighbourhoodsController@update');
        Route::post('update'        , 'NeighbourhoodsController@postUpdate')->name('NeighbourhoodsController@postUpdate');
        Route::post('delete'        , 'NeighbourhoodsController@postDelete')->name('NeighbourhoodsController@postDelete');
        Route::post('restore'       , 'NeighbourhoodsController@postRestore')->name('NeighbourhoodsController@postRestore');
        Route::post('perma-delete'  , 'NeighbourhoodsController@postPermaDelete')->name('NeighbourhoodsController@postPermaDelete');
        Route::post('status-update' , 'NeighbourhoodsController@postStatus')->name('NeighbourhoodsController@postStatus');
        Route::get('select'         , 'NeighbourhoodsController@select')->name('NeighbourhoodsController@select');
        Route::get('{model}'        , 'NeighbourhoodsController@read')->name('NeighbourhoodsController@read');
        Route::post('{model}'       , 'NeighbourhoodsController@postRead')->name('NeighbourhoodsController@postRead');
    });
});