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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageService implements ServiceProviderInterface
{
    /** @var Container $app **/
    private $app;

    /** @var Request $request **/
    private $request;

    /** @var boolean $editMode **/
    private $editMode;

    /** @var string $template **/
    private $template;

    /** @var string $uri **/
    private $uri;

    public function init()
    {
        $this->request = Request::createFromGlobals();

        if (!$this->request->getSession()) {
            $this->request->setSession( new Session() );
        }

        $this->editMode = $this->request->getSession()->get('editMode', false);

        $this->template = $this->app['settings']['template']['name'];

        $this->uri = $this->request->getRequestUri();

        if (substr($this->uri, -1) == '/') {
            $this->uri = substr($this->uri, 0, strlen($this->uri) - 1);
        }

        if ($this->uri == '') {
            $this->uri = '/';
        }
    }

    private function getTopMenu()
    {
        $topMenu = $this->app['dataService']->getAllDocuments('menu');

        if ($this->editMode) {
            $topMenu = array_merge( $topMenu, [
                [
                    'uri' => $this->app['settings']['admin']['page']['uri'],
                    'text' => 'Настройки'
                ],
                [
                    'uri' => $this->app['settings']['admin']['logout']['uri'],
                    'text' => 'Выход'
                ],
            ]);
        }

        return $topMenu;

    }

    private function setEditMode($editMode)
    {
        $this->editMode = $editMode;
        $this->request->getSession()->set('editMode', $editMode);
    }

    public function render()
    {

        try {

            if ($this->uri == $this->app['settings']['admin']['page']['uri']) {

                if (!$this->editMode) {
                    if ($this->request->request->get('username') == $this->app['settings']['admin']['credentials']['login'] &&
                        $this->request->request->get('password') == $this->app['settings']['admin']['credentials']['password']) {

                        $this->setEditMode(true);
                    }
                }

                return $this->app['twig']->render("/templates/$this->template/admin.html.twig", [
                    'editMode' => $this->editMode,
                    'requestUri' => $this->uri,
                    'allPages' => $this->app['dataService']->getAllDocuments('page'),
                    'menu' => $this->app['dataService']->getAllDocuments('menu'),
                    'topMenu' => $this->getTopMenu()
                ]);
            }

            if ($this->request->getRequestUri() == $this->app['settings']['admin']['logout']['uri']) {
                $this->setEditMode(false);
                return new RedirectResponse('/');
            }

            $page = $this->app['dataService']->getPage($this->uri);

            if ($page == null) {
                throw new NotFoundHttpException();
            }

            return $this->app['twig']->render("/templates/$this->template/body.html.twig", [
                'page' => $page,
                'editMode' => $this->editMode,
                'requestUri' => $this->uri,
                'topMenu' => $this->getTopMenu()
            ]);

        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException();
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
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