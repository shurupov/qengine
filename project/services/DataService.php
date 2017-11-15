<?php

namespace Qe;


use MongoDB\Collection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataService implements ServiceProviderInterface
{

    /** @var Collection $pageCollection **/
    private $pageCollection;
    /** @var Collection $menuCollection **/
    private $menuCollection;


    public function getPage($slug)
    {
        return $this->pageCollection->findOne(['slug' => $slug]);
    }

    public function getAllPages()
    {
        return $this->pageCollection->find()->toArray();
    }

    public function getMenu()
    {
        return $this->menuCollection->find()->toArray();
    }

    public function addPage($slug, $title)
    {
        $this->pageCollection->insertOne([
            'slug'  => $slug,
            'title' => $title
        ]);
    }

    public function removePage($slug)
    {
        $this->pageCollection->findOneAndDelete([
            'slug' => $slug
        ]);
    }

    public function change($slug, $path, $value)
    {

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->update($slug, $path . '.' . $k, $v);
            }
        } else {
            $this->update($slug, $path, $value);
        }
    }

    public function remove($slug, $path)
    {
        $this->pageCollection->findOneAndUpdate(['slug' => $slug], [
            '$unset' => $this->getArray($path, "")
        ]);
    }

    public function addBlock($slug, $type)
    {
        $path = 'sections.' . $this->randomString() . '.type';

        $this->update($slug, $path, $type);
    }

    private function update($slug, $path, $value)
    {
        $this->pageCollection->findOneAndUpdate(['slug' => $slug], [
            '$set' => $this->getArray($path, $value)
        ]);
    }

    private function randomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
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
        $db = $app['settings']['db']['name'];

        /** @var Collection $page */
        $this->pageCollection = $app['mongodb']->$db->page;
        $this->menuCollection = $app['mongodb']->$db->menu;

        $app['dataService'] = function () use ($app) {
            return $this;
        };
    }
}