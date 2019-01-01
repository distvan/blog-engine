<?php
namespace Distvan;

use call_user_func;

/**
 * Class Cache
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Cache
{
    const CATEGORY = 'CategoryCache';
    const TAG = 'TagCache';
    const ARTICLE = 'ArticleCache';

    /**
     * Delete one type of cache
     *
     * @param $type
     */
    public static function del($type)
    {
        $cacheName = class_user_func($type . '::getName');

        $config = new Config();
        $c = $config->get();

        $file = $c['cachedir'] . DIRECTORY_SEPARATOR . $cacheName;

        if(file_exists($file))
        {
            unlink($file);
        }
    }

    /**
     * Delete all files in the cache directory
     *
     */
    public static function delAll()
    {
        $config = new Config();
        $c = $config->get();

        LocalFileStore::deleteDirectories($c['cachedir'], false);
    }

    /**
     * Get one type of cache (if it not exists, create)
     *
     * @param $type
     * @param $option
     * @return bool|mixed
     */
    public static function get($type, $option = '')
    {
        $config = new Config();
        $c = $config->get();

        $cacheName = call_user_func(__NAMESPACE__ . '\\' . $type . '::getName', $option);

        $file = $c['cachedir'] . DIRECTORY_SEPARATOR . $cacheName;

        if(file_exists($file))
        {
            $content = file_get_contents($file);

            return unserialize($content);
        }
        else
        {
            return call_user_func(__NAMESPACE__ . '\\' . $type . '::create', $option);
        }

        return false;
    }
}