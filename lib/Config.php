<?php
namespace Distvan;

/**
 * Class Config
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Config
{
    private $_test;

    /**
     * Config constructor.
     * @param bool $testing
     */
    public function __construct($testing = [])
    {
        $this->_test = $testing;
    }

    /**
     * Get configs
     *
     * @return array|bool
     */
    public function get()
    {
        if(!empty($this->_test))
        {
            return $this->_test;
        }

        return array(
            'devmode' => true,          //development mode, in production set to false
            'default_language' => 'en', // hu or en
            'max_search_result' => 10,  //number of returning article number in search page
            'storage' => 'LocalFileStore',
            'param' => array(
                'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage'
            ),
            'cachedir' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache',
            'template' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'view',
            'date_format' => 'Y-m-d'
        );
    }
}