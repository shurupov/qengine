<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// ... definitions

$app->get('/', function () {

    return new Response('eee');

});

$app->run();