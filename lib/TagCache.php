<?php

namespace Distvan;

use Distvan\Model\Tag;

/**
 * Class TagCache
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class TagCache implements iCache
{
    /**
     * @param $option
     * @return string
     */
    public static function getName($option = '')
    {
        return 'tag.cache';
    }

    /**
     * create tag cache
     *
     * @param $option
     */
    public static function create($option = '')
    {
        $config = new Config();
        $tag = new Tag($config);
        $c = $config->get();

        $tags = $tag->getAll();

        $result = array();

        foreach($tags as $tag)
        {
            $result[] = array(
                'name' => $tag->getName(),
                'url' => $tag->getPublicUrl()
            );
        }

        $file = $c['cachedir'] . DIRECTORY_SEPARATOR . self::getName();

        file_put_contents($file, serialize($result));

        return $result;
    }
}