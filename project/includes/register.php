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
        'cache' => __DIR__.'/../cache/views/'
    )
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), [
    'swiftmailer.use_spool' => false,
    'swiftmailer.options' => array(
        'transport' => 'smtp',
        'host' => 'smtp.yandex.ru',
        'port' => '587',
        'username' => 'bakalibriki.online@ya.ru',
        'password' => 'Y0RtyT3xLh',
        'encryption' => 'tls',
        'auth_mode' => 'login'
    )
]);

$app->register(new Qe\EmailService());
$app->register(new Qe\PostService());



// Вывод логов
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$app['mailer']->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));