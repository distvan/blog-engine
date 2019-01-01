<?php
namespace Distvan\Controller;

use Distvan\Cache;
use Distvan\ArticleCache;
use Distvan\Config;
use Slim\Views\PhpRenderer;

/**
 * Class RecentWidget
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class RecentWidget implements iWidget
{
    public static function create()
    {
        $config = new Config();
        $c = $config->get();
        $renderer = new PhpRenderer($c['template']);

        $articles = Cache::get(Cache::ARTICLE, array('type' => ArticleCache::RECENT));

        return $renderer->fetch('frontend/widgets/recent.html', array('articles' => $articles), 'html');
    }
}