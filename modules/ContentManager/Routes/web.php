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

    Route::prefix('content/dashboard')->group(function() {
        Route::get('get-counts'           , 'ContentDashboardController@getCount')->name('ContentDashboardController@getCount');
    });
    
    Route::prefix('pages')->group(function() {
        Route::get('list/{type}'    , 'PageController@index')->name('PageController@index');
        Route::post('list'          , 'PageController@postIndex')->name('PageController@postIndex');
        Route::get('create/{type}'  , 'PageController@create')->name('PageController@create');
        Route::post('create'        , 'PageController@postCreate')->name('PageController@postCreate');
        Route::get('update/{model}' , 'PageController@update')->name('PageController@update');
        Route::post('update'        , 'PageController@postUpdate')->name('PageController@postUpdate');
        Route::post('delete'        , 'PageController@postDelete')->name('PageController@postDelete');
        Route::post('restore'       , 'PageController@postRestore')->name('PageController@postRestore');
        Route::post('perma-delete'  , 'PageController@postPermaDelete')->name('PageController@postPermaDelete');
        Route::post('status-update' , 'PageController@postStatus')->name('PageController@postStatus');
        Route::get('select/{type}'  , 'PageController@select')->name('PageController@select');
        Route::get('{model}'        , 'PageController@read')->name('PageController@read');
        Route::post('{model}'       , 'PageController@postRead')->name('PageController@postRead');
    });
    Route::prefix('categories')->group(function() {
        Route::get('list/{type}'    , 'CategoryController@index')->name('CategoryController@index');
        Route::post('list'          , 'CategoryController@postIndex')->name('CategoryController@postIndex');
        Route::get('create/{type}'  , 'CategoryController@create')->name('CategoryController@create');
        Route::post('create'        , 'CategoryController@postCreate')->name('CategoryController@postCreate');
        Route::get('update/{model}' , 'CategoryController@update')->name('CategoryController@update');
        Route::post('update'        , 'CategoryController@postUpdate')->name('CategoryController@postUpdate');
        Route::post('delete'        , 'CategoryController@postDelete')->name('CategoryController@postDelete');
        Route::post('restore'       , 'CategoryController@postRestore')->name('CategoryController@postRestore');
        Route::post('perma-delete'  , 'CategoryController@postPermaDelete')->name('CategoryController@postPermaDelete');
        Route::post('status-update' , 'CategoryController@postStatus')->name('CategoryController@postStatus');
        Route::get('select'         , 'CategoryController@select')->name('CategoryController@select');
        Route::get('{model}'        , 'CategoryController@read')->name('CategoryController@read');
        Route::post('{model}'       , 'CategoryController@postRead')->name('CategoryController@postRead');
    });
    Route::prefix('contents')->group(function() {
        Route::get('list/{type}'    , 'ContentController@index')->name('ContentController@index');
        Route::post('list'          , 'ContentController@postIndex')->name('ContentController@postIndex');
        Route::get('create/{type}'  , 'ContentController@create')->name('ContentController@create');
        Route::post('create'        , 'ContentController@postCreate')->name('ContentController@postCreate');
        Route::get('update/{model}' , 'ContentController@update')->name('ContentController@update');
        Route::post('update'        , 'ContentController@postUpdate')->name('ContentController@postUpdate');
        Route::post('delete'        , 'ContentController@postDelete')->name('ContentController@postDelete');
        Route::post('restore'       , 'ContentController@postRestore')->name('ContentController@postRestore');
        Route::post('perma-delete'  , 'ContentController@postPermaDelete')->name('ContentController@postPermaDelete');
        Route::post('status-update' , 'ContentController@postStatus')->name('ContentController@postStatus');
        Route::get('select/{type}'  , 'ContentController@select')->name('ContentController@select');
        Route::get('tag/select'     , 'ContentController@tagSelect')->name('ContentController@tagSelect');
        Route::get('{model}'        , 'ContentController@read')->name('ContentController@read');
        Route::post('{model}'       , 'ContentController@postRead')->name('ContentController@postRead');
    });
    Route::prefix('sections')->group(function() {
        Route::get('update'         , 'SectionController@update')->name('SectionController@update');
        Route::post('update'        , 'SectionController@postUpdate')->name('SectionController@postUpdate');
    });
});
