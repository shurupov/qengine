<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 17.11.17
 * Time: 23:18
 */

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
        return new RedirectResponse($app['settings']['admin']['page']['uri']);
    } catch (\Exception $e) {
        return new JsonResponse([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

$app->get('/e/{collection}/remove/{id}', function ($collection, $id) use ($app) {
    try {
        $app['dataService']->removeDocument( $id, $collection );
        return new RedirectResponse($app['settings']['admin']['page']['uri']);
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
