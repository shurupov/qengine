<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views/',
    'twig.options' => array(
        'cache' => __DIR__.'/../views_c/'
    )
));

$app->get('/', function () use ($app) {

    return $app['twig']->render('index.html.twig', ['var' => 'data']);

});

$app->run();