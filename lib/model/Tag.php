<?php

namespace Distvan\Model;

/**
 * Class Tag
 *
 * @package Distvan\Model
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Tag extends Base
{
    private $_name;
    private $_url;

    /**
     * @return string
     */
    public function getClassName()
    {
        return substr(strrchr(__CLASS__, '\\'), 1);
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->_store->getAll();
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

    public function getPublicUrl()
    {
        return '/tags/' . $this->getUrl();
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
}