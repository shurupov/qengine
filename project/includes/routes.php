<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

$app->get('/', function (Request $request) use ($app) {

    return $app['pageService']->render("", $request);

});

$app->post('/e/{collection}/edit', function (Request $request, $collection) use ($app) {

    try {
        $app['dataService']->edit(
            $request->request->get('pk'),
            $request->request->get('name'),
            $request->request->get('value'),
            $collection
        );

        return json_encode(['status' => 'ok']);
    } catch (\Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

});

$app->post('/e/{collection}/add', function (Request $request, $collection) use ($app) {

    try {
        $app['dataService']->addDocument(
            $request->request->all(),
            $collection
        );
        return new RedirectResponse('/' . $app['settings']['admin']['slug']);

    } catch (\Exception $e) {
        return json_encode([
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
        return new RedirectResponse('/' . $app['settings']['admin']['slug']);
    } catch (\Exception $e) {

        return json_encode([
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

        return json_encode(['status' => 'ok']);
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
        return json_encode(['status' => 'ok']);
    } catch (\Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

});

$app->post('/post', function (Request $request) use ($app) {

    $app['postService']->post($request->request->all());

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});

$app->get('/{slug}', function ($slug, Request $request) use ($app) {

    return $app['pageService']->render($slug, $request);

});

$app->post('/{slug}', function ($slug, Request $request) use ($app) {

    return $app['pageService']->render($slug, $request);

});

$app->get('/{slug}/{subslug}', function ($slug, $subslug, Request $request) use ($app) {

    return $app['pageService']->render($slug . '/' . $subslug, $request);

});

$app->error(function (\Exception $e, $headers, $code) use ($app) {
    switch ($code) {
        case 404:
            return $app['pageService']->render("404");
        case 500:
            return $app['pageService']->render("500");
        default:
            return $app['pageService']->render("error");
    }
});