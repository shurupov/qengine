<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

use Symfony\Component\HttpFoundation\Request;

if ($app['pageService']->isEditMode()) {
    require_once __DIR__.'/../includes/routesApi.php';
}

$app->match($app['settings']['admin']['page']['uri'] . '/{dataType}/{collection}', function ($dataType, $collection) use ($app) {
    return $app['pageService']->renderAdminPanel($dataType, $collection);
})->method('GET|POST')
    ->value('dataType', 'page')
    ->value('collection', null);

$app->get($app['settings']['admin']['logout']['uri'], function () use ($app) {
    return $app['pageService']->logout();
});

$app->post($app['settings']['form']['postControllerUri'], function (Request $request) use ($app) {
    return $app['postService']->post($request->request->all());
});

$app->get('{uri}', function () use ($app) {
    return $app['pageService']->render();
})->assert('uri', '.*');

if (!$app['debug']) {

    $app->error(function ($code) use ($app) {
        return $app['pageService']->renderErrorPage($code);
    });
}
