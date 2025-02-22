<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your module
    |
    */
    'name'           => 'Address',

    /*
    |--------------------------------------------------------------------------
    | Auto Binded Model Interfaces
    |--------------------------------------------------------------------------
    |
    | The model interfaces namespaces listed here will be automatically loaded 
    | on the request to your module service provider. Feel free to add your own
    | implemntation to this array to centerlize functionality of your models.
    | ModelInterfaceNamespace => ModelServiceNamespace
    |
    */
    'implemntations' => [],

    /*
    |--------------------------------------------------------------------------
    | GEO Coding Drivers
    |--------------------------------------------------------------------------
    |
    | Google Cloud Platform API KEY | Open Street Maps
    |
    */
    'google' => [
        'url' => env('GOOGLE_GEO_URL'),
        'key' => env('GOOGLE_GEO_KEY'),
    ],

    'osm_geo' => [
        'url' => env('OSM_GEO_URL'),
        'key' => env('OSM_GEO_KEY', null),
    ],

    'osm_search' => [
        'url' => env('OSM_SEARCH_URL'),
        'key' => env('OSM_SEARCH_KEY', null),
    ],
    
    'map' => [
        'lat'  => env('MAP_FOCUS_LAT', '25.2275839'),
        'lng'  => env('MAP_FOCUS_LNG', '55.3942383'),
        'zoom' => env('MAP_FOCUS_ZOOM', '10.75'),
    ],

    'address_types' => [
        'HOME' => [
            'label' => 'address::inputs.address_type.HOME.label',
            'icon'  => 'icons/address/home.png',
        ], 
        'WORK' => [
            'label' => 'address::inputs.address_type.WORK.label',
            'icon'  => 'icons/address/work.png',
        ], 
        'OTHER' => [
            'label' => 'address::inputs.address_type.OTHER.label',
            'icon'  => 'icons/address/other.png',
        ]
    ],
];
