<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$app->match($app['settings']['admin']['page']['uri'] . '/{panelType}/{panel}/{method}', function (Request $request, $panelType, $panel, $method) use ($app) {

    $panelPath = __DIR__.'/../extensions/panels/'.$panel.'/';
    $className = strtoupper($panel[0].substr($panel, 1)).'PanelController';

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
                                $parameters [] = $request->query->all();
                                break;
                            case 'post':
                            case 'POST':
                                $parameters [] = $request->request->all();
                                break;
                            case 'files':
                            case 'FILES':
                                $parameters [] = $request->files->all();
                                break;
                            case 'dataService':
                            case 'dataservice':
                                $parameters [] = $app['dataService'];
                                break;
                            case 'pageService':
                            case 'pageservice':
                                $parameters [] = $app['pageService'];
                                break;
                            case 'postService':
                            case 'postservice':
                                $parameters [] = $app['postService'];
                                break;
                        }
                    }

                    $response = $reflectionMethod->invokeArgs(new $className($app), $parameters);

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

                    return $app['pageService']->renderAdminPanel(\Qe\PageService::PANELS_ADMIN_PANEL, $panel, $response);
                }
            }

        }
    }
})->method('GET|POST')
  ->value('panelType', 'panels')
  ->value('panelMethod', 'index');

