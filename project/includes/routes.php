<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

if ($app['pageService']->isEditMode()) {
    require_once __DIR__.'/../includes/routesApi.php';
}

$app->match($app['settings']['admin']['page']['uri'], function () use ($app) {
    return $app['pageService']->renderAdminPanel();
})->method('GET|POST');

$app->get($app['settings']['admin']['logout']['uri'], function () use ($app) {
    return $app['pageService']->logout();
});

/*$app->post('/post', function (Request $request) use ($app) {
    $app['postService']->post($request->request->all());
    return $app['twig']->render('page/index.html.twig', ['slug' => '']);
});*/

$app->match('{uri}', function () use ($app) {
    return $app['pageService']->render();
})->assert('uri', '.*')->method('GET|POST');

if (!$app['debug']) {

    $app->error(function ($code) use ($app) {
        return $app['pageService']->renderErrorPage($code);
    });
}
