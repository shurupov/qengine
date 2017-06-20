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

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

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

$app->register(new EmailService());
$app->register(new PostService());



// Вывод логов
$logger = new Swift_Plugins_Loggers_EchoLogger();
$app['mailer']->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$app->get('/', function () use ($app) {

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});

$app->get('/{slug}', function ($slug) use ($app) {

    return $app['twig']->render('page/' . $slug . '.html.twig', ['slug' => $slug]);

});

$app->post('/post', function (Request $request) use ($app) {

    $sent = $app['postService']->post($request->request->all());

    return $app['twig']->render('page/contacts.html.twig', ['slug' => 'contacts']);

});

try {
    $app->run();
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

print $logger->dump();