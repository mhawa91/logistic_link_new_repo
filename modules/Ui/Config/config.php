<?php

return [
    'name'  => 'Ui',

    'theme' => 'dashlite',

    'display_language' => env('DISPLAY_LANGUAGE', null) ? explode(',', env('DISPLAY_LANGUAGE')) : [
        'ar',
        'en',
    ],
    'frontend_display_language' => env('FRONTEND_DISPLAY_LANGUAGE', null) ? explode(',', env('FRONTEND_DISPLAY_LANGUAGE')) : [
        'ar',
        'en',
    ],

    'logos' => [
        'en' => [
            'flag' => [
                'publicPath'     => 'modules/ui/dashlite-3x/flags/en-uk.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '74px',
                'widthMobile'    => '75px'
            ],
            'favicon' => [
                'publicPath'     => 'logos/favicon-ar.png',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '36px'
            ],
            'white' => [
                'publicPath'     => 'logos/logo-white-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '72px'
            ],
            'dark' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '72px'
            ],
            'email' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '150px',
                'maxHeight'      => '150px',
                'widthMobile'    => '72px'
            ],
        ],
        'ar' => [
            'flag' => [
                'publicPath'     => 'modules/ui/dashlite-3x/flags/ar.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '74px',
                'widthMobile'    => '75px'
            ],
            'favicon' => [
                'publicPath'     => 'logos/favicon-ar.png',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '36px'
            ],
            'white' => [
                'publicPath'     => 'logos/logo-white-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '72px'
            ],
            'dark' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '72px'
            ],
            'email' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '150px',
                'maxHeight'      => '150px',
                'widthMobile'    => '130px'
            ],
        ],
        'tr' => [
            'flag' => [
                'publicPath'     => 'modules/ui/dashlite-3x/flags/tr.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '74px',
                'widthMobile'    => '75px'
            ],
            'favicon' => [
                'publicPath'     => 'logos/favicon-ar.png',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '36px'
            ],
            'white' => [
                'publicPath'     => 'logos/logo-white-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '50px',
                'widthMobile'    => '72px'
            ],
            'dark' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '75px',
                'widthMobile'    => '72px'
            ],
            'email' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '150px',
                'maxHeight'      => '150px',
                'widthMobile'    => '130px'
            ],
        ],
        'ru' => [
            'flag' => [
                'publicPath'     => 'modules/ui/dashlite-3x/flags/ru.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '74px',
                'widthMobile'    => '75px'
            ],
            'favicon' => [
                'publicPath'     => 'logos/favicon-ar.png',
                'maxWidth'       => '190px',
                'maxHeight'      => '150px',
                'widthMobile'    => '36px'
            ],
            'white' => [
                'publicPath'     => 'logos/logo-white-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '150px',
                'widthMobile'    => '72px'
            ],
            'dark' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '190px',
                'maxHeight'      => '150px',
                'widthMobile'    => '72px'
            ],
            'email' => [
                'publicPath'     => 'logos/logo-dark-en.svg',
                'maxWidth'       => '150px',
                'maxHeight'      => '150px',
                'widthMobile'    => '130px'
            ],
        ],
    ],
];
