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
    'name'           => 'ContentManager',

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
    'implemntations' => [
        \Modules\ContentManager\Entities\Contracts\SectionInterface::class  => \Modules\ContentManager\Entities\Services\SectionService::class,
        \Modules\ContentManager\Entities\Contracts\CategoryInterface::class => \Modules\ContentManager\Entities\Services\CategoryService::class,
        \Modules\ContentManager\Entities\Contracts\ContentInterface::class  => \Modules\ContentManager\Entities\Services\ContentService::class
    ],
];
