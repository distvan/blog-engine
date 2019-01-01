<?php
namespace Distvan\Model;

use Distvan\Config;
use Distvan\iStorage;
use Distvan\StoreFactory;

/**
 * Class Base
 *
 * This class is an interface to a datastore
 *
 * @package Distvan\Model
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
abstract class Base implements iStorage
{
    protected $_id;
    protected $_store;
    protected $_config;

    /**
     * Base constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
        $c = $config->get();
        $this->_store = StoreFactory::create($c['storage'] . $this->getClassName(), $config);
        $this->_id = uniqid();
        $this->_store->setObject($this);
    }

    /**
     * @return mixed
     */
    abstract public function getClassName();

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set Id
     *
     * @param $value
     */
    public function setId($value)
    {
        $this->_id = $value;
    }

    /**
     * Get total number of items
     * 
     * @return int
     */
    public function getTotalNumber()
    {
        return (int)$this->_store->getTotalNumber();
    }

    /**
     * Get Object from store
     *
     * @return mixed
     */
    public function get()
    {
        return $this->_store->get();
    }

    /**
     * Put Object into store
     *
     * @return mixed
     */
    public function insert()
    {
        return $this->_store->insert();
    }

    /**
     * Update Object in the store
     *
     * @return mixed
     */
    public function update()
    {
        return $this->_store->update();
    }

    /**
     * Delete Object from store
     *
     * @return mixed
     */
    public function delete()
    {
        return $this->_store->delete();
    }
}