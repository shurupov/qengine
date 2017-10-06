<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {

    return $app['pageService']->render("");

});

$app->post('/change', function (Request $request) use ($app) {

    try {
        $app['dataService']->change(
            $request->request->get('pk'),
            $request->request->get('name'),
            $request->request->get('value')
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

$app->get('/{slug}', function ($slug) use ($app) {

    return $app['pageService']->render($slug);

});

$app->get('/{slug}/{subslug}', function ($slug, $subslug) use ($app) {

    return $app['pageService']->render($slug . '/' . $subslug);

});

$app->error(function (\Exception $e, $headers, $code) use ($app) {
    switch ($code) {
        case 404:
            $message = 'Запрошенная страница не найдена.';
            break;
        default:
            $message = 'Просим прощения, что-то пошло не так.';
    }

    return $app['twig']->render('page/error.html.twig', ['code' => $code, 'message' => $message]);
});