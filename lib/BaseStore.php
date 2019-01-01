<?php
namespace Distvan;

/**
 * Class BaseStore
 * 
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class BaseStore
{
    protected $_config;
    protected $_object;

    /**
     * BaseStore constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Object setter
     *
     * @param $object
     */
    public function setObject($object)
    {
        $this->_object = $object;
    }
}