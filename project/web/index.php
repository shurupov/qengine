<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views/',
    'twig.options' => array(
        'cache' => __DIR__.'/../cache/views/'
    )
));

$app->get('/', function () use ($app) {

    try {
        return $app['twig']->render('page/index.html.twig', ['slug' => '']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }

});

$app->get('/{slug}', function ($slug) use ($app) {

    try {
        return $app['twig']->render('page/' . $slug . '.html.twig', ['slug' => $slug]);
    } catch (Exception $e) {
        echo $e->getMessage();
    }

});

$app->run();