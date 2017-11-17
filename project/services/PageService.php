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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PageService implements ServiceProviderInterface
{
    /** @var Container $app **/
    private $app;

    public function render($slug)
    {

        $request = Request::createFromGlobals();

        try {

            if (!$request->getSession()) {
                $request->setSession( new Session() );
            }

            $template = $this->app['settings']['template']['name'];

            $editMode = $request->getSession()->get('editMode', false);

            $topMenu = $this->app['dataService']->getAllDocuments('menu');

            if ($slug == $this->app['settings']['admin']['slug']) {

                if (!$editMode) {
                    if ($request->request->get('username') == $this->app['settings']['admin']['login'] &&
                        $request->request->get('password') == $this->app['settings']['admin']['password']) {

                        $request->getSession()->set('editMode', true);
                        $editMode = true;
                    }
                }

                if ($editMode) {
                    $topMenu = array_merge( $topMenu, [
                        [
                            'uri' => '/' . $this->app['settings']['admin']['slug'],
                            'text' => 'Настройки'
                        ],
                        [
                            'uri' => '/' . $this->app['settings']['admin']['logout'],
                            'text' => 'Выход'
                        ],
                    ]);
                }

                return $this->app['twig']->render("/templates/$template/admin.html.twig", [
                    'template' => $template,
                    'editMode' => $editMode,
                    'slug' => $slug,
                    'allPages' => $this->app['dataService']->getAllDocuments('page'),
                    'menu' => $this->app['dataService']->getAllDocuments('menu'),
                    'topMenu' => $topMenu
                ]);
            }

            if ($slug == $this->app['settings']['admin']['logout']) {
                $request->getSession()->set('editMode', false);
                return new RedirectResponse('/');
            }

            $page = $this->app['dataService']->getPage($slug);

            if ($editMode) {
                $topMenu = array_merge_recursive( $topMenu, [
                    [
                        'uri' => $this->app['settings']['admin']['slug'],
                        'text' => 'Настройки'
                    ],
                    [
                        'uri' => $this->app['settings']['admin']['logout'],
                        'text' => 'Выход'
                    ],
                ]);
            }

            if ($page == null) {
                $this->app->abort(404);
                return null;
            }

            return $this->app['twig']->render("/templates/$template/body.html.twig", [
                'page' => $page,
                'template' => $template,
                'editMode' => $editMode,
                'slug' => $slug,
                'topMenu' => $topMenu
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