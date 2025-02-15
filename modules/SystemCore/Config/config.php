<?php

return [

    'name' => 'SystemCore',

    'developer' => [
        'active' => true,
        'name'   => 'Babel Solutions', //Can be translated attr EX: systemcore::strings.key
        'url'    => 'https://babel-solutions.com', //Can be translated attr EX: systemcore::strings.key
    ],

    'adminPanelRoutePath' => 'admin',

    'authRedirect' => 'admin',

    'auth' => [
        'LOGIN_ATTEMPTS_CYCLE_IN_MINUTES' => 1,
        'MAX_LOGIN_ATTEMPTS_PER_CYCLE'    => 1,
        'REDIRECT_TO_ROUTE_NAME'          => 'AdminDashboardController@index',
        'LOGIN_ROUTE'                     => 'AuthController@login',
        'HTTP_AUTH_MIDDLEWARE'            => 'HttpAuth',
        'API_AUTH_MIDDLEWARE'             => 'ApiAuth',
        'HTTP_PERMISSIONS_MIDDLEWARE'     => 'NeedPermissions',
        'API_PERMISSIONS_MIDDLEWARE'      => 'NeedPermissions',
    ],

    'adminRegistration' => [
        'active'    => true,
        'user_type' => 'DEFAULT',
    ],

    'adminTwoFactorLogin' => [
        'active'    => false,
    ],

    'models' => [
        'User'      => \App\Models\User::class,
        'Ability'   => \Modules\SystemCore\Entities\Ability::class,
        'Role'      => \Modules\SystemCore\Entities\Role::class,
    ],

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
        \Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class => \Modules\SystemCore\Entities\Services\SystemConfigService::class,
    ],

    'users' => [
        [
            'name'     => 'System Root',
            'type'     => 'ROOT',
            'username' => env('ROOT_USERNAME', 'sudo'),
            'email'    => env('ROOT_EMAIL', 'sudo@localhost.com'),
            'password' => env('ROOT_PASSWORD', 'RANDOM{!Root123!@#'),
        ],
        [
            'name'     => 'System Admin',
            'type'     => 'SYSTEM_ADMIN',
            'username' => env('ADMIN_USERNAME', 'admin'),
            'email'    => env('ADMIN_EMAIL', 'admin@localhost.com'),
            'password' => env('ADMIN_PASSWORD', 'RANDOM{!Admin123!@#'),
        ]
    ],
];
