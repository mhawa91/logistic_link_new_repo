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

    Route::prefix('geo')->group(function() {
        Route::post('search'  , 'GeoCoderController@search');
        Route::post('reverse' , 'GeoCoderController@reverse');
    });
    
    // Route::post('geo', function() {

    //     $location = new \Modules\Address\Entities\LocationService;
    //     $location->geo(request()->lat, request()->lng);
    //     $location->search('شارع جميرا, بر دبي, الميناء, دبي, الإمارات العربية المتحدة');
    //     return $location->data('ar');

    //     $osm = new \Modules\Address\Entities\OpenStreetMapService;
    //     $osm->geo(request()->lat, request()->lng);
    //     $osm->search('شارع جميرا, بر دبي, الميناء, دبي, الإمارات العربية المتحدة');
    //     return $osm->data('ar');

    //     // $models = $location->firstOrFail();
    //     $models = $location->firstOrCreate();
        
    //     return [
    //         'country'       => $models['country'],
    //         'city'          => $models['city'],
    //         'municipality'  => $models['municipality'],
    //         'neighborhood'  => $models['neighborhood'],
    //         'postal_code'   => $location->data('ar', 'postal_code'),
    //         'street'        => $location->data('ar', 'street'),
    //         'full_address'  => $location->data('ar', 'full_address'),
    //         'neighborhoods' => [],
    //     ];
    // });
});