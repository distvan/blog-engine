<?php
namespace Distvan;

use Distvan\Model\Category;

/**
 * Class CategoryCache
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class CategoryCache implements iCache
{
    /**
     * Get cache file name
     *
     * @param $option String
     * @return string
     */
    public static function getName($option = '')
    {
        return 'category.cache';
    }

    /**
     * Create cache
     *
     * @param $option String
     */
    public static function create($option = '')
    {
        $config = new Config();
        $category = new Category($config);
        $c = $config->get();

        $categories = $category->getAll();
        $result = array();
        foreach($categories as $cat)
        {
            $result[] = array(
                'id' => $cat->getId(),
                'name' => $cat->getName(),
                'url' => $cat->getUrl()
            );
        }
        $file = $c['cachedir'] . DIRECTORY_SEPARATOR . self::getName();

        file_put_contents($file, serialize($result));

        return $result;
    }
}