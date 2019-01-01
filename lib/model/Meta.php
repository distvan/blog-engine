<?php

namespace Distvan\Model;

/**
 * Class Meta
 *
 * @package Distvan\Model
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Meta
{
    private $_title;
    private $_description;
    private $_robots;
    private $_og_type;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }
    
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return mixed
     */
    public function getRobots()
    {
        return $this->_robots;
    }

    /**
     * @param mixed $robots
     */
    public function setRobots($robots)
    {
        $this->_robots = $robots;
    }

    /**
     * @return mixed
     */
    public function getOgType()
    {
        return $this->_og_type;
    }

    /**
     * @param mixed $og_type
     */
    public function setOgType($og_type)
    {
        $this->_og_type = $og_type;
    }
}