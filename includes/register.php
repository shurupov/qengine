<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 15:12
 */

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../cache/log/log',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views/',
    'twig.options' => array(
        'cache' => ( $app['settings']['debug'] ? false : __DIR__.'/../cache/views/' ),
        'strict_variables' => false,
        'debug' => $app['settings']['debug']
    ),
));

$app['twig']->addExtension(new Twig_Extension_Debug());

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), [
    'swiftmailer.use_spool' => false,
    'swiftmailer.options' => $app['settings']['mail']
]);

$app->register(new Lalbert\Silex\Provider\MongoDBServiceProvider(), [
    'mongodb.config' => [
        'server' => 'mongodb://localhost:27017',
        'options' => [],
        'driverOptions' => [],
    ]
]);

$app->register(new Qe\PostService());
$app->register(new Qe\DataService());
$app->register(new Qe\PageService());
$app->register(new Qe\PictureService());

// Вывод логов
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$app['mailer']->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));