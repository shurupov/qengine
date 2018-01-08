<?php

define('INDEX_PATH', __DIR__);

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../includes/load.php';

$app = new Silex\Application();

require_once __DIR__ . '/../settings/settings.php';

require_once __DIR__.'/../includes/register.php';

$app['debug'] = $app['settings']['debug'];

require_once __DIR__.'/../includes/routes.php';

$app->run();

$app['logger']->addRecord(200, $logger->dump());
