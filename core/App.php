<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 21.01.19
 * Time: 0:11
 */

namespace QEngine\Core;


use Silex\Application;
use Symfony\Component\Yaml\Yaml;

class App
{
    /* @var Application $app */
    private $app;

    public function __construct()
    {
        $this->app = new Application();

        $this->app['settings'] = Yaml::parseFile(QENGINE_ROOT_PATH . '/settings.yml');
        setlocale(LC_ALL, $this->app['settings']['locale']);

        $registrar = new Registrar($this->app);
        $registrar->register();

        /* TODO: refactor and include the following
        require_once __DIR__.'/../includes/twigExtensions.php';
        require_once __DIR__.'/../includes/controllerExtensions.php';
        */

        $app['debug'] = $this->app['settings']['debug'];

        $router = new Router($this->app);
        $router->initRoutes();

    }

    public function run()
    {
        $this->app->run();
    }

    public function log()
    {
//        TODO: Investigate if we really need this
//        $this->app['logger']->addRecord(200, $logger->dump());
    }
}