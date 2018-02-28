<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

$app->get('/c/{method}', function (Request $request, $method) use ($app) {

    $controllersPath = __DIR__.'/../extensions/controllers';
    if (is_dir($controllersPath)) {
        if ($dh = opendir($controllersPath)) {
            while (($file = readdir($dh)) !== false) {
                if ($file == "." || $file == "..") {
                    continue;
                }
                if (substr($file, -4) == '.php') {
                    $className = substr($file, 0, strlen($file) - 4);

                    require_once $controllersPath.'/'.$file;

                    if (class_exists($className)) {

                        $methods = get_class_methods($className);

                        $controller = new $className($app);

                        foreach ($methods as $controllerMethod) {
                            if ($method == $controllerMethod) {

                                $response = $controller->$method($request->query->all(), $request->request->all(), $request->files->all());

                                if (is_array($response)) {
                                    return new JsonResponse( $response );
                                } else {
                                    return new Response( $response );
                                }


                            }
                        }

                    }
                }
            }
            closedir($dh);
        }
    }


});

