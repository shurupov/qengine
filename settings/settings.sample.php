<?php

$app['settings'] = [
    'admin' => [
        'page' => [
            'uri' => '/admin',
            'caption' => 'Настройки'
        ],
        'logout' => [
            'uri' => '/logout',
            'caption' => 'Выход'
        ],
        'credentials' => [
            'login' => 'admin',
            'password' => 'great'
        ]
    ],
    'db' => [
        'name' => 'qe'
    ],
    'form' => [
        'siteUrl' => 'http://qe.dev',
        'logo' => INDEX_PATH . '/templates/diagram/images/logo.png',
        'postControllerUri' => '/post',
        'successfulSentPage' => '/sent'
    ],
    'template' => [ // startup-kit | bowling | diagram | others will be soon
        'name' => 'startup-kit'
    ],
    'mail' => [
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'username' => '',
        'emailFrom' => '',
        'password' => '',
        'encryption' => 'tls',
        'auth_mode' => 'login'
    ],
    'debug' => true
];