<?php
namespace Distvan;

/**
 * Class StoreFactory
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class StoreFactory
{
    /**
     * Factory to create a storage class
     *
     * @param $className
     * @param Config $config
     * @return mixed
     */
    public static function create($className, Config $config)
    {
        $class = __NAMESPACE__ . "\\" . $className;
        return new $class($config);
    }
}