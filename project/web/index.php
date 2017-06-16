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
    return $app['twig']->render('index.html.twig');
});

$app->get('/{name}', function ($name) use ($app) {

//    echo $name . '.html.twig'; die;

    try {
        return $app['twig']->render($name . '.html.twig');
    } catch (Exception $e) {
        echo $e->getMessage();
    }


});

$app->run();