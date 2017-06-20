<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/', function () use ($app) {

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});

$app->get('/{slug}', function ($slug) use ($app) {

    try {
        return $app['twig']->render('page/' . $slug . '.html.twig', ['slug' => $slug]);
    } catch (Twig_Error_Loader $e) {
        $app->abort(404);
        return null;
    }

});

$app->get('/{slug}/{subslug}', function ($slug, $subslug) use ($app) {

    try {
        return $app['twig']->render("page/$slug/$subslug.html.twig", ['slug' => $slug]);
    } catch (Twig_Error_Loader $e) {
        $app->abort(404);
        return null;
    }

});

$app->post('/post', function (Request $request) use ($app) {

    $app['postService']->post($request->request->all());

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

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