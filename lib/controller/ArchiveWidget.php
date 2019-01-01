<?php

namespace Distvan\Controller;

use Distvan\Config;
use Slim\Views\PhpRenderer;

/**
 * Class ArchiveWidget
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class ArchiveWidget implements iWidget
{
    public static function create()
    {
        $config = new Config();
        $c = $config->get();
        $renderer = new PhpRenderer($c['template']);

        //return $renderer->fetch('frontend/widgets/archive.html', array(), 'html');
    }
}