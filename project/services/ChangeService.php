<?php

namespace Qe;


use MongoDB\Collection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChangeService implements ServiceProviderInterface
{

    /** @var Collection $pageCollection **/
    private $pageCollection;

    public function change($slug, $data)
    {
        $document = ['ee' => 'oo'];

//        $this->pageCollection->insertOne($document);

//        $ee = $this->pageCollection->findOne(['slug' => $slug])->getArrayCopy();

        $this->pageCollection->findOneAndUpdate(['slug' => $slug], [ '$set' => ['qq' => 'uu'] ]);

//        var_dump($ee);

//        die;

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

        $app['changeService'] = function () use ($app) {
            return $this;
        };
    }
}