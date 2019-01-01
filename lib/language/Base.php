<?php
namespace Distvan\Language;

/**
 * Class Base
 *
 * @package Distvan\Language
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Base 
{
    protected $_lang;

    /**
     * Get language text
     *
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return isset($this->_lang[$key]) ? $this->_lang[$key] : '???';
    }
}