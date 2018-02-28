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

                        $reflectionClass = new ReflectionClass($className);
                        $reflectionMethods = $reflectionClass->getMethods();

                        foreach ($reflectionMethods as $reflectionMethod) {
                            if ($method == $reflectionMethod->name) {

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
                                    }
                                }

                                $response = $reflectionMethod->invokeArgs(new $className($app), $parameters);

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


})->method('GET|POST');

