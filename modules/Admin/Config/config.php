<?php

return [
    'name' => 'Admin',

    'controll-panel-allowed-access-groups' => [
        'ROOT',
        'SYSTEM_ADMIN',
        'EMPLOYEE',
        'CONTENT_EDITOR',
        'DEFAULT',
        'TRANSPORTER',
    ],

    'controll-panel-users-list-filter' => true,
    'controll-panel-users-list-groups' => [
        'ROOT',
        'SYSTEM_ADMIN',
        'EMPLOYEE',
        'CONTENT_EDITOR'
    ],
];
