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
    Route::get('dashboard'          , 'AdminDashboardController@index')->name('AdminDashboardController@index');
    Route::post('editor-upload'     , 'AdminDashboardController@editorUpload')->name('AdminDashboardController@editorUpload');

    Route::prefix('dashboard/users')->group(function() {
        Route::get('counts-all-types'            , 'UsersDashboardController@allTypesCount')->name('UsersDashboardController@allTypesCount');
        Route::get('counts-all-types-by-months'  , 'UsersDashboardController@countsByMonths')->name('UsersDashboardController@countsByMonths');
        Route::get('lastest-accounts'            , 'UsersDashboardController@latestAccounts')->name('UsersDashboardController@latestAccounts');
    });

    Route::prefix('/')->group(function() {
        Route::get('login'          , 'AuthController@login')->name('AuthController@login');
        Route::post('login'         , 'AuthController@postLogin')->name('AuthController@postLogin');
        Route::get('reset'          , 'AuthController@reset')->name('AuthController@reset');
        Route::post('reset'         , 'AuthController@postReset')->name('AuthController@postReset');
        Route::get('reset/{hash}'   , 'AuthController@newPassword')->name('AuthController@newPassword');
        Route::post('reset/{hash}'  , 'AuthController@postNewPassword')->name('AuthController@postNewPassword');
        Route::post('logout'        , 'AuthController@postLogout')->name('AuthController@postLogout');

        Route::get('register'          , 'AuthController@register')->name('AuthController@register');
        Route::post('register'         , 'AuthController@postRegister')->name('AuthController@postRegister');
        Route::get('confirm-register'  , 'AuthController@confirmRegister')->name('AuthController@confirmRegister');
        Route::post('confirm-register' , 'AuthController@postConfirmRegister')->name('AuthController@postConfirmRegister');
    });

    Route::prefix('users')->group(function() {
        Route::get('list'           , 'UsersController@index')->name('UsersController@index');
        Route::post('list'          , 'UsersController@postIndex')->name('UsersController@postIndex');
        Route::get('create'         , 'UsersController@create')->name('UsersController@create');
        Route::post('create'        , 'UsersController@postCreate')->name('UsersController@postCreate');
        Route::get('update/{model}' , 'UsersController@update')->name('UsersController@update');
        Route::post('update'        , 'UsersController@postUpdate')->name('UsersController@postUpdate');
        Route::get('profile'        , 'UsersController@profile')->name('UsersController@profile');
        Route::post('profile'       , 'UsersController@postProfile')->name('UsersController@postProfile');
        Route::post('company'       , 'UsersController@postCompany')->name('UsersController@postCompany');
        Route::post('delete'        , 'UsersController@postDelete')->name('UsersController@postDelete');
        Route::post('restore'       , 'UsersController@postRestore')->name('UsersController@postRestore');
        Route::post('perma-delete'  , 'UsersController@postPermaDelete')->name('UsersController@postPermaDelete');
        Route::post('status-update' , 'UsersController@postStatus')->name('UsersController@postStatus');
        Route::get('select'         , 'UsersController@select')->name('UsersController@select');
        Route::get('{model}'        , 'UsersController@read')->name('UsersController@read');
        Route::post('{model}'       , 'UsersController@postRead')->name('UsersController@postRead');
    });

    Route::prefix('config')->group(function() {
        Route::get('update'         , 'ConfigController@update')->name('ConfigController@update');
        Route::post('update'        , 'ConfigController@postUpdate')->name('ConfigController@postUpdate');
    });
});
