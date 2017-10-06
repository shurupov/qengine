<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 27.09.17
 * Time: 0:17
 */

namespace Qe;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Twig_Error_Loader;

class PageService implements ServiceProviderInterface
{
    /** @var Container $app **/
    private $app;

    public function render($slug)
    {

        $template = 'startup-kit';

//        $editMode = true;
        $editMode = false;

        $page = $this->app['dataService']->getPage($slug);

        if ($page == null) {
            $this->app->abort(404);
            return null;
        }

        try {
            return $this->app['twig']->render("/templates/$template/body.html.twig", [
                'page' => $page,
                'template' => $template,
                'editMode' => $editMode
            ]);
        } catch (\Exception $e) {
            $this->app->abort(500);
            return null;
        }
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $this->app = $app;

        $app['pageService'] = function () use ($app) {
            return $this;
        };
    }
}