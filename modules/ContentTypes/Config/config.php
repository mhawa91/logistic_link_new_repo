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
    ],

    'shippmentStatus' => [
        [
            'id'    => 'DRAFT',
            'label' => 'contenttypes::inputs.shippment_status.options.DRAFT',
        ],
        [
            'id'    => 'MATCHING',
            'label' => 'contenttypes::inputs.shippment_status.options.MATCHING',
        ],
        [
            'id'    => 'NO_MATCHING_CONTRACTS',
            'label' => 'contenttypes::inputs.shippment_status.options.NO_MATCHING_CONTRACTS',
        ],
        [
            'id'    => 'PENDING_TRANSPORTER_APPROVAL',
            'label' => 'contenttypes::inputs.shippment_status.options.PENDING_TRANSPORTER_APPROVAL',
        ],
        [
            'id'    => 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE',
            'label' => 'contenttypes::inputs.shippment_status.options.TRANSPORTER_APPROVED_WITH_PRICE_CHANGE',
        ],
        [
            'id'    => 'PENDING_PICKUP',
            'label' => 'contenttypes::inputs.shippment_status.options.PENDING_PICKUP',
        ],
        [
            'id'    => 'PENDING_DELIVERY',
            'label' => 'contenttypes::inputs.shippment_status.options.PENDING_DELIVERY',
        ],
        [
            'id'    => 'DELIVERED_PENDING_PAYMENT',
            'label' => 'contenttypes::inputs.shippment_status.options.DELIVERED_PENDING_PAYMENT',
        ],
        [
            'id'    => 'COMPLETED',
            'label' => 'contenttypes::inputs.shippment_status.options.COMPLETED',
        ],
        [
            'id'    => 'CANCELLED',
            'label' => 'contenttypes::inputs.shippment_status.options.CANCELLED',
        ],
    ],
];
