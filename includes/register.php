<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 15:12
 */

/*$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../cache/log/log',
));*/

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views/',
    'twig.options' => array(
        'cache' => ( $app['settings']['debug'] ? false : __DIR__.'/../cache/views/' ),
        'strict_variables' => false
    ),

));

$test = new Twig_Filter('remained', function ($dateStr, $days = 0, $day1 = 'день', $days234 = 'дня', $days567890 = 'дней') { //todo refactoring
    $now = new DateTime('now');
    $now->setTime(0,0);
    $date = new DateTime($dateStr);
    $date->setTime(0,0);
    $diff = $now->diff($date);
    $result = $diff->d - $days;

    if ($result > 10 && $result < 20) {
        $dayString = $days567890;
    } else switch ($result % 10) {
        case 1 :
            $dayString = $day1;
            break;
        case 2 :case 3 :case 4 :
            $dayString = $days234;
            break;
        default :
            $dayString = $days567890;
            break;
    }

    return $result . ' ' . $dayString;
});
$app['twig']->addFilter($test);

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

// Вывод логов
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$app['mailer']->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));