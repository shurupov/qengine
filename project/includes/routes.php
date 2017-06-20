<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 20.06.17
 * Time: 16:50
 */

$app->get('/', function () use ($app) {

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});

$app->get('/{slug}', function ($slug) use ($app) {

    return $app['twig']->render('page/' . $slug . '.html.twig', ['slug' => $slug]);

});

$app->post('/post', function (Request $request) use ($app) {

    $app['postService']->post($request->request->all());

    return $app['twig']->render('page/index.html.twig', ['slug' => '']);

});