<?php

$twigPath = __DIR__.'/../extensions/twig';
if (is_dir($twigPath)) {
    if ($dh = opendir($twigPath)) {
        while (($file = readdir($dh)) !== false) {
            if ($file == "." || $file == "..") {
                continue;
            }
            if (substr($file, -4) == '.php') {
                $className = substr($file, 0, strlen($file) - 4);

                require_once $twigPath.'/'.$file;

                if (class_exists($className)) {

                    $methods = get_class_methods($className);

                    $extension = new $className($app);

                    foreach ($methods as $method) {
                        if (substr($method, -6) == 'Filter') {

                            $filterName = substr($method, 0, strlen($method) - 6);

                            $app['twig']->addFilter( new Twig_Filter(  $filterName, [ $extension, $method ] ) );
                        }
                        if (substr($method, -8) == 'Function') {

                            $functionName = substr($method, 0, strlen($method) - 8);

                            $app['twig']->addFunction( new Twig_Function( $functionName, [ $extension, $method ] ) );
                        }
                    }

                }
            }
        }
        closedir($dh);
    }
}
