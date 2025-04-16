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
    'name'           => 'Clients',

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

    'week_days' => [
        'sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
    ],
    'links' => [
        'website',
        'linktree',
        'facebook',
        'instagram',
        'youtube',
    ],
];
