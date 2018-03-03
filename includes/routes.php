<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

if ($app['pageService']->isEditMode()) {
    require_once __DIR__.'/../includes/routesApi.php';
}

$app->match($app['settings']['admin']['page']['uri'] . '/{dataType}', function ($dataType) use ($app) {
    return $app['pageService']->renderAdminPanel($dataType);
})->method('GET|POST')
    ->value('dataType', 'page')
    ->value('collection', null);

$app->get($app['settings']['admin']['logout']['uri'], function () use ($app) {
    return $app['pageService']->logout();
});

$app->post($app['settings']['form']['postControllerUri'], function (Request $request) use ($app) {
    return $app['postService']->post($request->request->all());
});

$app->get('/{slug}/{id}', function ($slug, $id, Request $request) use ($app) {
    return $app['pageService']->render('/' . $slug, $id, $request);
})  ->value('id', null)
    ->value('slug', '');

if (!$app['debug']) {

    $app->error(function (HttpException $error) use ($app) {
        return $app['pageService']->renderErrorPage($error->getStatusCode());
    });
}
