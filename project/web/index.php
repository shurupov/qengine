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

$app['swiftmailer.use_spool'] = false;
$app['swiftmailer.options'] = array(
    'transport' => 'smtp',
    'host' => 'smtp.yandex.ru',
    'port' => '587',
    'username' => 'bakalibriki.online@ya.ru',
    'password' => 'Y0RtyT3xLh',
    'encryption' => 'tls',
    'auth_mode' => 'login'
);

$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new EmailService());
$app->register(new PostService());

$app->get('/', function () use ($app) {

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});

$app->get('/{slug}', function ($slug) use ($app) {

    return $app['twig']->render('page/' . $slug . '.html.twig', ['slug' => $slug]);

});

$app->post('/post', function (Request $request) use ($app) {

    $sent = $app['postService']->post($request->request->all());

    return $app->redirect("/?sent=" . $sent);

});

try {
    $app->run();
} catch (Exception $e) {
    echo $e->getTraceAsString();
}