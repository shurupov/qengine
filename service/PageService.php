<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 27.09.17
 * Time: 0:17
 */

namespace QEngine\Service;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

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

    const STANDARD_ADMIN_PANEL = 'standard';
    const DATA_ADMIN_PANEL = 'data';
    const PANELS_ADMIN_PANEL = 'panels';

    const STANDARD_ADMIN_PANEL_PAGE_SLUG = 'page';

    const ADMIN_LAYOUT_TYPE = 'admin';

    public function isEditMode()
    {
        return $this->editMode;
    }

    public function renderAdminPanel($panelType, $slug, $panelMethod)
    {

        if (!$this->editMode) {
            if ($this->request->request->get('username') == $this->app['settings']['admin']['credentials']['login'] &&
                $this->request->request->get('password') == $this->app['settings']['admin']['credentials']['password']) {

                $this->setEditMode(true);
            }
        }

        if (!$this->isEditMode()) {
            return $this->renderBody([], 'admin');
        }

        switch ($panelType) {
            case self::STANDARD_ADMIN_PANEL :
                return $this->renderStandardAdminPanel($slug);
                break;
            case self::DATA_ADMIN_PANEL :
                return $this->renderDataAdminPanel($slug);
                break;
            case self::PANELS_ADMIN_PANEL :
                return $this->renderPanelsAdminPanel($slug, $panelMethod);
                break;
        }

        throw new NotFoundHttpException();
    }

    private function renderPanelsAdminPanel($panel, $method)
    {
        $panelFound = false;

        $panels = [];
        $panelPath = __DIR__.'/../extensions/panels/';
        if (is_dir($panelPath) && $dh = opendir($panelPath)) {
            while (($dir = readdir($dh)) !== false) {
                if ($dir == "." || $dir == "..") {
                    continue;
                }
                $panelFound = $panelFound || ($dir == $panel);
                $settings = Yaml::parseFile($panelPath.$dir.'/settings.yml');
                $panels[] = [
                    'name' => $dir,
                    'title' => $settings['title']
                ];
            }
        }

        if (count($panels) == 0 || (!empty($panel) && !$panelFound)) {
            throw new NotFoundHttpException();
        }

        if (empty($panel)) {
            return new RedirectResponse($this->app['settings']['admin']['page']['uri'].'/'.self::PANELS_ADMIN_PANEL.'/'.$panels[0]['name']);
        }

        $response = $this->executePanelMethod($panel, $method);

        if ($response instanceof Response) {
            return $response;
        }

        return $this->renderBody([
            'adminType' => self::PANELS_ADMIN_PANEL,
            'panel' => $panel,
            'method' => $method,
            'panels' => $panels,
            'panelData' => $response
        ], 'admin');
    }

    private function renderStandardAdminPanel($slug)
    {
        if (empty($slug)) {
            return new RedirectResponse($this->app['settings']['admin']['page']['uri'].'/'.self::STANDARD_ADMIN_PANEL.'/'.self::STANDARD_ADMIN_PANEL_PAGE_SLUG);
        }

        $data = [
            'adminType' => self::STANDARD_ADMIN_PANEL,
            'dataType' => $slug,
            'list' => $this->app['dataService']->getDocuments($slug)
        ];

        if ($slug = self::STANDARD_ADMIN_PANEL_PAGE_SLUG) {
            $data['menu'] = $this->app['dataService']->getDocuments('menu');
        }

        return $this->renderBody($data, self::ADMIN_LAYOUT_TYPE);
    }

    private function renderDataAdminPanel($dataType)
    {
        if (empty($dataType)) {
            foreach ($this->app['settings']['additionalData'] as $collectionName=>$collection) {
                return new RedirectResponse($this->app['settings']['admin']['page']['uri'].'/'.self::DATA_ADMIN_PANEL.'/'.$collectionName);
            }
        }

        $renderData = [
            'adminType' => self::DATA_ADMIN_PANEL,
            'dataType' => $dataType
        ];

        if (array_key_exists($dataType, $this->app['settings']['additionalData'])) {
            $renderData['additionalCollection'] = $this->app['dataService']->getDocuments($dataType, [], ['_id' => -1]);
        }

        $pages = $this->app['dataService']->getDocuments('page');
        foreach ($pages as $page) {
            if ($page->display == $dataType) {
                $renderData['linkPrefix'] = $page->uri . '/';
                break;
            }
        }

        return $this->renderBody($renderData, self::ADMIN_LAYOUT_TYPE);
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

    public function render($pageSlug, $itemId = null)
    {

        $page = $this->app['dataService']->getPage($pageSlug);

        if ($page == null) {
            $page = $this->app['dataService']->getPageWithDisplay();
            $itemId = substr($pageSlug, 1);
            $pageSlug = $page['uri'];
        }

        if ($page == null) {
            throw new NotFoundHttpException();
        }

        $additional = [];

        foreach ($page['getAdditional'] as $collectionName) {
            if ($this->editMode) {
                $additional[$collectionName] = $this->app['dataService']->getDocuments($collectionName, [], ['_id' => -1]);
            } else {
                $additional[$collectionName] = $this->app['dataService']->getDocuments($collectionName, ['visibility' => 'visible'], ['_id' => -1]);
            }
        }

        if (!array_key_exists('display', $page) ||  $page['display'] == 'default') {
            return $this->renderBody([
                'page' => $page,
                'additional' => $additional,
                'request' => $this->request
            ]);
        }



        if ($itemId != null && ($item = $this->app['dataService']->getItem($page['display'], $itemId)) != null) {

            return $this->renderBody([
                'page' => $page,
                'additional' => $additional,
                'item' => $item,
                'pageUri' => $pageSlug,
                'itemId' => $itemId,
                'request' => $this->request
            ]);
        }

        throw new NotFoundHttpException();

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
        $topMenu = $this->app['dataService']->getDocuments('menu');

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
        foreach ($this->app['dataService']->getDocuments('setting') as $setting) {
            $settings[$setting->key] = $setting;
        }
        return $settings;
    }

    private function executePanelMethod($panel, $method)
    {
        $panelPath = __DIR__.'/../extensions/panels/'.$panel.'/';
        $className = strtoupper($panel[0]).substr($panel, 1).'PanelController';

        if (file_exists($panelPath.$className.'.php')) {

            require_once $panelPath . $className . '.php';

            if (class_exists($className)) {

                $reflectionClass = new ReflectionClass($className);
                $reflectionMethods = $reflectionClass->getMethods();

                foreach ($reflectionMethods as $reflectionMethod) {
                    if ($method == preg_replace('/^([\w\d]+)(Redirect|Json)$/', '${1}', $reflectionMethod->name)) {

                        $reflectionParameters = $reflectionMethod->getParameters();

                        $parameters = [];

                        foreach ($reflectionParameters as $reflectParameter) {
                            switch ($reflectParameter->name) {
                                case 'get':
                                case 'GET':
                                    $parameters [] = $this->request->query->all();
                                    break;
                                case 'post':
                                case 'POST':
                                    $parameters [] = $this->request->request->all();
                                    break;
                                case 'files':
                                case 'FILES':
                                    $parameters [] = $this->request->files->all();
                                    break;
                                case 'dataService':
                                case 'dataservice':
                                    $parameters [] = $this->app['dataService'];
                                    break;
                                case 'pageService':
                                case 'pageservice':
                                    $parameters [] = $this;
                                    break;
                                case 'postService':
                                case 'postservice':
                                    $parameters [] = $this->app['postService'];
                                    break;
                            }
                        }

                        $response = $reflectionMethod->invokeArgs(new $className($this->app), $parameters);

                        if (preg_match('/^([\w\d]+?)(Redirect|Json)?$/', $reflectionMethod->name, $matches)) {

                            $type = $matches[2];

                            switch ($type) {
                                case 'Redirect' :
                                    return new RedirectResponse($response);
                                    break;
                                case 'Json' :
                                    return new JsonResponse($response);
                                    break;
                            }

                        }

                        return $response;
                    }
                }
            }
        }

        throw new BadRequestHttpException();
    }
}