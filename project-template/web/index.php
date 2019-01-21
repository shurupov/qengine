<?php

define('QENGINE_INDEX_PATH', __DIR__);
define('QENGINE_ROOT_PATH', __DIR__ . '/..');

require_once QENGINE_ROOT_PATH . '/vendor/autoload.php';

$app = new QEngine\Core\App();

$app->run();
