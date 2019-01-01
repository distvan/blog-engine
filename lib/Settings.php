<?php
namespace Distvan;

/**
 * Class Settings
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Settings
{
    const FILENAME = 'settings.blog';

    /**
     * @param $key
     * @return bool
     */
    public static function get($key)
    {
        $config = new Config();
        $c = $config->get();

        $content = file_get_contents($c['param']['path'] . DIRECTORY_SEPARATOR . self::FILENAME);
        $settings = unserialize($content);

        if(isset($settings[$key]))
        {
            return $settings[$key];
        }

        return false;
    }

    /**
     * @param $settings array
     */
    public static function set($settings)
    {
        $config = new Config();
        $c = $config->get();

        file_put_contents($c['param']['path'] . DIRECTORY_SEPARATOR . self::FILENAME, serialize($settings));
    }
}