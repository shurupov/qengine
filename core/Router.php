<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 21.01.19
 * Time: 0:52
 */

namespace QEngine\Core;


use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Router
{
    /* @var Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function initRoutes()
    {
        if ($this->app['pageService']->isEditMode()) {
            $this->initApiRoutes();
        }

        $app = $this->app;

        $app->match($app['settings']['admin']['page']['uri'] . '/{adminType}/{dataType}/{panelMethod}', function ($adminType, $dataType, $panelMethod) use ($app) {
            return $app['pageService']->renderAdminPanel($adminType, $dataType, $panelMethod);
        })->method('GET|POST')
            ->value('adminType', 'standard')
            ->value('dataType', null)
            ->value('panelMethod', 'index');

        $app->get($app['settings']['admin']['logout']['uri'], function () use ($app) {
            return $app['pageService']->logout();
        });

        $app->post($app['settings']['form']['postControllerUri'], function (Request $request) use ($app) {
            return $app['postService']->post($request->request->all());
        });

        $app->get('/{slug}/{id}', function ($slug, $id) use ($app) {
            return $app['pageService']->render('/' . $slug, $id);
        })  ->value('id', null)
            ->value('slug', '');

        if (!$app['debug']) {

            $app->error(function (HttpException $error) use ($app) {
                return $app['pageService']->renderErrorPage($error->getStatusCode());
            });
        }
    }

    private function initApiRoutes()
    {
        $app = $this->app;
        $app->post('/e/{collection}/{action}', function (Request $request, $collection, $action) use ($app) {
            try {
                $app['dataService']->edit(
                    $request->request->get('pk'),
                    $request->request->get('name'),
                    $request->request->get('value'),
                    $collection,
                    $action
                );
                return new JsonResponse(['status' => 'ok']);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        })->assert('action','edit|edit/list');

        $app->post('/e/{collection}/add', function (Request $request, $collection) use ($app) {
            try {
                $app['dataService']->addDocument(
                    $request->request->all(), $collection
                );
                return new RedirectResponse($request->headers->get('referer'));
            } catch (\Exception $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });

        $app->get('/e/{collection}/remove/{id}', function (Request $request, $collection, $id) use ($app) {
            try {
                $app['dataService']->removeDocument( $id, $collection );
                return new RedirectResponse($request->headers->get('referer'));
            } catch (\Exception $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });

        $app->post('/e/{collection}/field/remove', function (Request $request, $collection) use ($app) {
            try {
                $app['dataService']->removeField(
                    $request->request->get('pk'),
                    $request->request->get('name'),
                    $collection
                );
                return new JsonResponse(['status' => 'ok']);
            } catch (\Exception $e) {
                return json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });

        $app->post('/e/page/block/add', function (Request $request) use ($app) {
            try {
                $app['dataService']->addBlock(
                    $request->request->get('id'),
                    $request->request->get('type')
                );
                return new JsonResponse(['status' => 'ok']);
            } catch (\Exception $e) {
                return json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });

        $app->post('/e/{collection}/picture', function (Request $request, $collection) use ($app) {
            try {
                $uri = $app['pictureService']->saveImage(
                    $request->request->get('pk'),
                    $request->request->get('name'),
                    $request->request->get('value'),
                    $request->request->get('settings'),
                    $collection
                );
                return new JsonResponse(['status' => 'ok', 'uri' => $uri]);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });
    }

}