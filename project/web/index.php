<?php

use Qe\EmailService;
use Qe\PostService;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../services/include_services.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views/',
    'twig.options' => array(
        'cache' => __DIR__.'/../cache/views/'
    )
));

//$app->register(new Qe\PostService(), array());

/*$app['postService'] = $app->factory(function () {
    return new PostService();
});*/

$app->register(new EmailService());
$app->register(new PostService());

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

$app->post('/post', function (Request $request) use ($app) {

    $app['postService']->test($request->request->all());


//    var_dump($request->request->all());
//    (new Qe\PostService())->test($request->request->all());
//    die;

//    return new \Symfony\Component\HttpFoundation\Response('ee');

    /*try {
        $app['postService']->test($request->request->all());
        die;
    } catch (Exception $e) {
        echo $e->getMessage();
    }*/

});

$app->run();