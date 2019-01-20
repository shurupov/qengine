<?php

namespace QEngine\Service;


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

    public function getPageWithDisplay()
    {
        return $this->db->page->findOne(['display' => ['$ne' => 'default']]);
    }

    public function getItem($collection, $itemId)
    {
        return $this->db->$collection->findOne(['itemId' => $itemId]);
    }

    public function getDocumentById($collection, $id)
    {
        return $this->db->$collection->findOne([ '_id' => new ObjectId($id) ]);
    }

    public function getDocument($collection, $filter = [])
    {
        return $this->db->$collection->findOne($filter);
    }

    public function getDocuments($collection, $filter = [], $sort = ['_id' => 1])
    {
        return $this->db->$collection->find($filter, [ 'sort' => $sort ])->toArray();
    }

    public function addDocument($fields, $collection)
    {
        $insertResult = $this->db->$collection->insertOne($fields);
        return $insertResult->getInsertedId();
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

    public function update($id, $path, $value, $collection = 'page')
    {
        $this->db->$collection->findOneAndUpdate(['_id' => new ObjectId($id)], [
            '$set' => $this->getArray($path, $value)
        ]);
    }

    public function randomString()
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