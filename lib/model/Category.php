<?php
namespace Distvan\Model;

use Distvan\Validator;
use Distvan\ValidatorException;

/**
 * Class Category
 *
 * @package Distvan\Model
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Category extends Base
{
    private $_parentId;
    private $_name;
    private $_url;

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return substr(strrchr(__CLASS__, '\\'), 1);
    }

    public function getAll()
    {
        return $this->_store->getAll();
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->_parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        if(!Validator::isValidUrl($url))
        {
            throw new ValidatorException('The url is not valid!');
        }
        $this->_url = $url;
    }
}