<?php

namespace Qe;


use MongoDB\BSON\ObjectID;
use MongoDB\Database;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataService implements ServiceProviderInterface
{

    /** @var Database $db **/
    private $db;


    public function getPage($uri)
    {
        return $this->db->page->findOne(['uri' => $uri]);
    }

    public function getAllDocuments($collection)
    {
        return $this->db->$collection->find()->toArray();
    }

    public function addDocument($fields, $collection)
    {
        $this->db->$collection->insertOne($fields);
    }

    public function removeDocument($id, $collection)
    {
        $this->db->$collection->findOneAndDelete([
            '_id' => new ObjectId($id)
        ]);
    }

    public function edit($id, $path, $value, $collection = 'page', $action = 'edit')
    {

        if ($action == 'edit/list') {
            $this->update($id, $path, [], $collection);
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->update($id, $path . '.' . $k, $v, $collection);
            }
        } else {
            $this->update($id, $path, $value, $collection);
        }
    }

    public function removeField($id, $path, $collection)
    {
        $this->db->$collection->findOneAndUpdate(['_id' => new ObjectId($id)], [
            '$unset' => $this->getArray($path, "")
        ]);
    }

    public function addBlock($id, $type)
    {
        $path = 'sections.' . $this->randomString() . '.type';

        $this->update($id, $path, $type);
    }

    private function update($id, $path, $value, $collection = 'page')
    {
        $this->db->$collection->findOneAndUpdate(['_id' => new ObjectId($id)], [
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

        $this->db = $app['mongodb']->$db;

        $app['dataService'] = function () use ($app) {
            return $this;
        };
    }
}