<?php

$app['settings'] = [
    'admin' => [
        'slug' => '_admin',
        'login' => 'admin',
        'password' => 'great'
    ],
    'db' => [
        'name' => 'qe'
    ],
    'template' => [ // startup-kit | others will be soon
        'name' => 'startup-kit'
    ],
    'mail' => [
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'auth_mode' => 'login'
    ]
];