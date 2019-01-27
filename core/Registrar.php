<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 21.01.19
 * Time: 0:29
 */

namespace QEngine\Core;


use Lalbert\Silex\Provider\MongoDBServiceProvider;
use QEngine\Service\DataService;
use QEngine\Service\PageService;
use QEngine\Service\PictureService;
use QEngine\Service\PostService;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Twig_Extension_Debug;

class Registrar
{
    /* @var Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function register()
    {
        $this->registerLogger();
        $this->registerTwig();
        $this->registerMailer();
        $this->registerDatabaseConnector();
        $this->registerServices();
    }

    private function registerLogger()
    {
        $this->app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => QENGINE_ROOT_PATH . '/cache/log/log',
        ));
    }

    private function registerTwig()
    {
        $this->app->register(new TwigServiceProvider(), array(
            'twig.path' => QENGINE_ROOT_PATH . '/views/',
            'twig.options' => array(
                'cache' => ( $this->app['settings']['debug'] ? false : QENGINE_ROOT_PATH . '/cache/views/' ),
                'strict_variables' => false,
                'debug' => $this->app['settings']['debug']
            ),
        ));

        $this->app['twig']->addExtension(new Twig_Extension_Debug());
    }

    private function registerMailer()
    {
        $this->app->register(new SwiftmailerServiceProvider(), [
            'swiftmailer.use_spool' => false,
            'swiftmailer.options' => $this->app['settings']['mail']
        ]);

        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $this->app['mailer']->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
    }

    private function registerDatabaseConnector()
    {
        if ($this->app['settings']['db']['type'] == 'db') {
            $this->app->register(new MongoDBServiceProvider(), [
                'mongodb.config' => [
                    'server' => $this->app['settings']['db']['url'],
                    'options' => [],
                    'driverOptions' => [],
                ]
            ]);
        }
    }

    private function registerServices()
    {
        $this->app->register(new PostService());
        $this->app->register(new DataService());
        $this->app->register(new PageService());
        $this->app->register(new PictureService());
    }
}