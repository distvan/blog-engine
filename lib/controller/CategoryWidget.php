<?php

namespace Distvan\Controller;

use Distvan\Cache;
use Distvan\Config;
use Slim\Views\PhpRenderer;

/**
 * Class CategoryWidget
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class CategoryWidget implements iWidget
{
    public static function create()
    {
        $config = new Config();
        $c = $config->get();
        $renderer = new PhpRenderer($c['template']);

        return $renderer->fetch('frontend/widgets/category.html',
            array('categories' => Cache::get(Cache::CATEGORY)), 'html');
    }
}