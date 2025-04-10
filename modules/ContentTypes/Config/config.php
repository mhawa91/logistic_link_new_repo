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
    'name'           => 'ContentTypes',

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

    'loadingTypes' => [
        [
            'id'    => 'CITY_TO_CITY',
            'label' => 'contenttypes::inputs.loading_type.options.CITY_TO_CITY',
        ],
        [
            'id'    => 'CITY_TO_CITY_MULTIPLE_LOCATIONS',
            'label' => 'contenttypes::inputs.loading_type.options.CITY_TO_CITY_MULTIPLE_LOCATIONS',
        ],
        [
            'id'    => 'CITY_TO_CITY_ROUND_TRIP',
            'label' => 'contenttypes::inputs.loading_type.options.CITY_TO_CITY_ROUND_TRIP',
        ],
    ]
];
