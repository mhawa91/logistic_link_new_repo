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

    Route::prefix('public-transportars')->group(function() {
        Route::get('list'           , 'PublicTransportarsController@index')->name('PublicTransportarsController@index');
        Route::post('list'          , 'PublicTransportarsController@postIndex')->name('PublicTransportarsController@postIndex');
        Route::post('send-rfq'      , 'PublicTransportarsController@postRfq')->name('PublicTransportarsController@postRfq');
        Route::get('select'         , 'PublicTransportarsController@select')->name('PublicTransportarsController@select');
        Route::get('{model?}'       , 'PublicTransportarsController@read')->name('PublicTransportarsController@read');
        Route::post('{model?}'      , 'PublicTransportarsController@postRead')->name('PublicTransportarsController@postRead');
    });

    Route::prefix('transporters')->group(function() {
        Route::get('list'                     , 'TransportersController@index')->name('TransportersController@index');
        Route::post('list'                    , 'TransportersController@postIndex')->name('TransportersController@postIndex');
        Route::get('create'                   , 'TransportersController@create')->name('TransportersController@create');
        Route::post('create'                  , 'TransportersController@postCreate')->name('TransportersController@postCreate');
        Route::get('create-by-user'           , 'TransportersController@createByUserId')->name('TransportersController@createByUserId');
        Route::post('create-by-user'          , 'TransportersController@postCreateByUserId')->name('TransportersController@postCreateByUserId');
        Route::get('update/{model}'           , 'TransportersController@update')->name('TransportersController@update');
        Route::post('update'                  , 'TransportersController@postUpdate')->name('TransportersController@postUpdate');       
        Route::post('delete'                  , 'TransportersController@postDelete')->name('TransportersController@postDelete');       
        Route::post('restore'                 , 'TransportersController@postRestore')->name('TransportersController@postRestore');     
        Route::post('perma-delete'            , 'TransportersController@postPermaDelete')->name('TransportersController@postPermaDelete');
        Route::post('status-update'           , 'TransportersController@postStatus')->name('TransportersController@postStatus');       
        Route::get('select'                   , 'TransportersController@select')->name('TransportersController@select');
        Route::post('export'                  , 'TransportersController@postExport')->name('TransportersController@postExport');       
        Route::get('{model}'                  , 'TransportersController@read')->name('TransportersController@read');
        Route::post('{model}'                 , 'TransportersController@postRead')->name('TransportersController@postRead');
    });

    Route::prefix('shippments-rfqs')->group(function() {
        Route::get('list'           , 'RfqController@index')->name('RfqController@index');
        Route::post('list'          , 'RfqController@postIndex')->name('RfqController@postIndex');
    });

    Route::prefix('shippments')->group(function() {
        Route::get('list'                               , 'ShippmentsController@index')->name('ShippmentsController@index');
        Route::post('list'                              , 'ShippmentsController@postIndex')->name('ShippmentsController@postIndex');
        Route::get('create'                             , 'ShippmentsController@create')->name('ShippmentsController@create');
        Route::post('create'                            , 'ShippmentsController@postCreate')->name('ShippmentsController@postCreate');
        Route::get('update/{model}'                     , 'ShippmentsController@update')->name('ShippmentsController@update');
        Route::post('update'                            , 'ShippmentsController@postUpdate')->name('ShippmentsController@postUpdate');
        Route::post('bulk-send'                         , 'ShippmentsController@postBulkSend')->name('ShippmentsController@postBulkSend');
        Route::post('delete'                            , 'ShippmentsController@postDelete')->name('ShippmentsController@postDelete');
        Route::post('restore'                           , 'ShippmentsController@postRestore')->name('ShippmentsController@postRestore');
        Route::post('perma-delete'                      , 'ShippmentsController@postPermaDelete')->name('ShippmentsController@postPermaDelete');
        Route::post('status-update'                     , 'ShippmentsController@postStatus')->name('ShippmentsController@postStatus');
        Route::get('select'                             , 'ShippmentsController@select')->name('ShippmentsController@select');

        Route::get('transportar-pricing-item'           , 'ShippmentsController@pricingItem')->name('ShippmentsController@pricingItem');
        Route::post('transportar-approve-switch'        , 'ShippmentsController@transportarApproveSwitch')->name('ShippmentsController@transportarApproveSwitch');
        Route::get('transportar-approve'                , 'ShippmentsController@transportarApprove')->name('ShippmentsController@transportarApprove');
        Route::get('transportar-approve-with-new-price' , 'ShippmentsController@transportarApproveWithPriceChange')->name('ShippmentsController@transportarApproveWithPriceChange');
        Route::get('transportar-reject'                 , 'ShippmentsController@transportarReject')->name('ShippmentsController@transportarReject');

        Route::get('client-approve-new-price'           , 'ShippmentsController@clientApprovePriceChange')->name('ShippmentsController@clientApprovePriceChange');
        Route::get('client-reject-new-price'            , 'ShippmentsController@clientRejectPriceChange')->name('ShippmentsController@clientRejectPriceChange');

        Route::get('transportar-waybill-and-driver'     , 'ShippmentsController@driverAndWaybill')->name('ShippmentsController@driverAndWaybill');
        Route::post('transportar-waybill-and-driver'    , 'ShippmentsController@postDriverAndWaybill')->name('ShippmentsController@postDriverAndWaybill');
        Route::get('transportar-drivers-select'         , 'ShippmentsController@driversSelect')->name('ShippmentsController@driversSelect');
        Route::get('location-delivery'                  , 'ShippmentsController@locationDelivery')->name('ShippmentsController@locationDelivery');

        Route::post('transportar-rfq-approve'           , 'ShippmentsController@transportarRfqApprove')->name('ShippmentsController@transportarRfqApprove');
        Route::get('transportar-rfq-reject'             , 'ShippmentsController@transportarRfqReject')->name('ShippmentsController@transportarRfqReject');

        Route::get('client-approve-rfq'                 , 'ShippmentsController@clientApproveRfq')->name('ShippmentsController@clientApproveRfq');
        Route::get('client-reject-rfq'                  , 'ShippmentsController@clientRejectRfq')->name('ShippmentsController@clientRejectRfq');

        Route::post('export'                            , 'ShippmentsController@postExport')->name('ShippmentsController@postExport');
        Route::get('{model}'                            , 'ShippmentsController@read')->name('ShippmentsController@read');
        Route::post('{model}'                           , 'ShippmentsController@postRead')->name('ShippmentsController@postRead');
    });

    Route::prefix('invoices')->group(function() {
        Route::get('list'           , 'InvoicesController@index')->name('InvoicesController@index');
        Route::post('list'          , 'InvoicesController@postIndex')->name('InvoicesController@postIndex');
        Route::get('create'         , 'InvoicesController@create')->name('InvoicesController@create');
        Route::post('create'        , 'InvoicesController@postCreate')->name('InvoicesController@postCreate');
        Route::get('update/{model}' , 'InvoicesController@update')->name('InvoicesController@update');
        Route::post('update'        , 'InvoicesController@postUpdate')->name('InvoicesController@postUpdate');
        Route::post('delete'        , 'InvoicesController@postDelete')->name('InvoicesController@postDelete');
        Route::post('restore'       , 'InvoicesController@postRestore')->name('InvoicesController@postRestore');
        Route::post('perma-delete'  , 'InvoicesController@postPermaDelete')->name('InvoicesController@postPermaDelete');
        Route::post('status-update' , 'InvoicesController@postStatus')->name('InvoicesController@postStatus');
        Route::get('select'         , 'InvoicesController@select')->name('InvoicesController@select');
        Route::post('export'        , 'InvoicesController@postExport')->name('InvoicesController@postExport');
        Route::get('{model}'        , 'InvoicesController@read')->name('InvoicesController@read');
        Route::post('{model}'       , 'InvoicesController@postRead')->name('InvoicesController@postRead');
    });

    Route::prefix('clients/invoices')->group(function() {
        Route::get('list'           , 'InvoicesController@groupedIndex')->name('InvoicesController@groupedIndex');
        Route::post('list'          , 'InvoicesController@postGroupedIndexIndex')->name('InvoicesController@postGroupedIndexIndex');
    });
});
