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

Route::prefix('v1')->group(function() {
    Route::post('test-s3', function() {
        return app('S3GraphManager')->remove(request()->file_name);
        $FileName = app('S3GraphManager')->process('user', request()->test_file);
        return app('S3GraphManager')->src('original', 'user', $FileName);
    });
});