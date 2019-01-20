<?php

//use Symfony\Component\Yaml\Yaml;

define('QENGINE_INDEX_PATH', __DIR__);
define('QENGINE_ROOT_PATH', __DIR__ . '/..');

require_once __DIR__.'/../vendor/autoload.php';

$app = new \QEngine\Core\App();

//$app = new Silex\Application();

//$app['settings'] = Yaml::parseFile('../settings/settings.yml');

//setlocale(LC_ALL, $app['settings']['locale']);

//require_once __DIR__.'/../includes/register.php';

require_once __DIR__.'/../includes/twigExtensions.php';
require_once __DIR__.'/../includes/controllerExtensions.php';

$app['debug'] = $app['settings']['debug'];

//require_once __DIR__.'/../includes/routes.php';

$app->run();

//$app['logger']->addRecord(200, $logger->dump());
