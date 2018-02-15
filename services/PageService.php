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

    /** @var string $uri **/
    private $uri;

    public function isEditMode()
    {
        return $this->editMode;
    }

    public function renderAdminPanel($dataType, $additionalCollectionName)
    {
        try {

            if (!$this->editMode) {
                if ($this->request->request->get('username') == $this->app['settings']['admin']['credentials']['login'] &&
                    $this->request->request->get('password') == $this->app['settings']['admin']['credentials']['password']) {

                    $this->setEditMode(true);
                }
            }

            return $this->renderBody([
                'dataType' => $dataType,
                'additionalCollectionName' => $additionalCollectionName,
                'additionalCollection' => ( $additionalCollectionName ? $this->app['dataService']->getAllDocuments($additionalCollectionName) : [] ),
                'pageList' => ( $dataType == 'page' ? $this->app['dataService']->getAllDocuments('page') : [] ),
                'menu' => ( ($dataType == 'menu' || $dataType == 'page') ? $this->app['dataService']->getAllDocuments('menu') : []),
                'formfields' => ( $dataType == 'form' ? $this->app['dataService']->getAllDocuments('formfields') : [] )
            ], 'admin');

        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $this->setEditMode(false);
            $url = $this->request->headers->get('Referer');
            if (!$url) {
                $url = '/';
            }
            return new RedirectResponse($url);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function render($pageSlug, $itemId)
    {

//        try {

            $page = $this->app['dataService']->getPage($pageSlug);

            $additional = [];

            foreach ($page['getAdditional'] as $collectionName) {
                if ($this->editMode) {
                    $additional[$collectionName] = $this->app['dataService']->getAllDocuments($collectionName);
                } else {
                    $additional[$collectionName] = $this->app['dataService']->getDocuments($collectionName, ['visibility' => 'visible']);
                }
            }

            if (!array_key_exists('display', $page) ||  $page['display'] == 'default') {
                return $this->renderBody([
                    'page' => $page,
                    'additional' => $additional
                ]);
            }

            if ($itemId != null) {
                $item = $this->app['dataService']->getItem($page['display'], $itemId);
                return $this->renderBody([
                    'page' => $page,
                    'additional' => $additional,
                    'item' => $item,
                    'pageUri' => $pageSlug,
                    'itemId' => $itemId]
                );
            }

            if ($page == null) {
                throw new NotFoundHttpException();
            }

//        } catch (NotFoundHttpException $e) {
//            throw new NotFoundHttpException();
//        } catch (\Exception $e) {
//            throw new HttpException(500, $e->getMessage());
//        }

    }

    public function renderErrorPage($code)
    {
        $page = $this->app['dataService']->getPage("/$code");

        if ($page == null) {
            $page = $this->app['dataService']->getPage("/500");
        }

        return $this->renderBody(['page' => $page]);
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
        $this->init();

        $app['pageService'] = function () use ($app) {
            return $this;
        };
    }

    private function renderBody($parameters, $type = 'body')
    {
        $parameters = array_merge([
            'editMode' => $this->editMode,
            'requestUri' => $this->uri,
            'settings' => $this->getSettings(),
            'topMenu' => $this->getTopMenu(),
            'app' => $this->app
        ], $parameters);

        return $this->app['twig']->render("/common/layouts/$type.html.twig", $parameters);
    }

    private function init()
    {
        $this->request = Request::createFromGlobals();

        if (!$this->request->getSession()) {
            $this->request->setSession( new Session() );
        }

        $this->editMode = $this->request->getSession()->get('editMode', false);

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
                    'text' => $this->app['settings']['admin']['page']['caption']
                ],
                [
                    'uri' => $this->app['settings']['admin']['logout']['uri'],
                    'text' => $this->app['settings']['admin']['logout']['caption']
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

    private function getSettings()
    {
        $settings = [];
        foreach ($this->app['dataService']->getAllDocuments('setting') as $setting) {
            $settings[$setting->key] = $setting;
        }
        return $settings;
    }
}