<?php

namespace Distvan\Controller;

use Distvan\Cache;
use Distvan\Config;
use Slim\Views\PhpRenderer;

/**
 * Class TagWidget
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class TagWidget implements iWidget
{
    public static function create()
    {
        $config = new Config();
        $c = $config->get();
        $renderer = new PhpRenderer($c['template']);

        return $renderer->fetch('frontend/widgets/tag.html',
            array('tags' => Cache::get(Cache::TAG)), 'html');
    }
}