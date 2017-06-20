<?php

use Qe\EmailService;
use Qe\PostService;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../includes/load.php';

$app = new Silex\Application();

require_once __DIR__.'/../includes/register.php';

require_once __DIR__.'/../includes/routes.php';

$app->run();

$app['logger']->addRecord(200, $logger->dump());
