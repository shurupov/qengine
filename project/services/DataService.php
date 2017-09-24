<?php

namespace Qe;


use MongoDB\Collection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataService implements ServiceProviderInterface
{

    /** @var Collection $pageCollection **/
    private $pageCollection;

    public function getPage($slug)
    {
        return $this->pageCollection->findOne(['slug' => $slug]);
    }

    public function change($slug, $path, $value)
    {

        $this->pageCollection->findOneAndUpdate(['slug' => $slug], [
            '$set' => $this->getArray($path, $value)
        ]);

    }

    private function getArray($path, $value)
    {
        $branches = explode('.', $path, 1);

        if (count($branches) == 1) {
            return [ $path => $value ];
        } else {

            $key = $branches[0];

            if (is_int($key)) {
                $key = intval($key);
            }

            return [ $key => $this->getArray($branches[1], $value) ];
        }
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $db = 'qe';

        /** @var Collection $page */
        $this->pageCollection = $app['mongodb']->$db->page;

        $app['dataService'] = function () use ($app) {
            return $this;
        };
    }
}