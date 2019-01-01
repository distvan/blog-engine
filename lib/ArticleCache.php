<?php

namespace Distvan;

use Distvan\Model\Article;

/**
 * Class ArticleCache
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class ArticleCache implements iCache
{
    const BY_CATEGORY = 'by_category';
    const BY_TAG = 'by_tag';
    const RECENT = 'recent';

    /**
     * @param $option
     * @return string
     */
    public static function getName($option = array())
    {
        switch($option['type'])
        {
            case self::BY_CATEGORY: return 'article.' . self::BY_CATEGORY  . '.' . $option['category_id'] . '.cache';
            case self::BY_TAG: return 'article.' . self::BY_TAG . '.' . $option['tag'] . '.cache';
            case self::RECENT: return 'article.' . self::RECENT . '.cache';
        }

        return 'article.cache';
    }

    /**
     * create tag cache
     *
     * @param $option
     */
    public static function create($option = array())
    {
        $config = new Config();
        $article = new Article($config);
        $c = $config->get();

        $articles = array();

        if(isset($option['type']) && $option['type'] == self::BY_CATEGORY)
        {
            $article->setCategoryId($option['category_id']);
            $result = $article->getArticlesByCategory();
        }
        elseif(isset($option['type']) && $option['type'] == self::BY_TAG)
        {
            $result = $article->getArticlesByTag($option['tag']);
        }
        elseif(isset($option['type']) && $option['type'] == self::RECENT)
        {
            $result = $article->getRecentArticles();
        }

        foreach($result as $art)
        {
            $articles[] = array(
                'title' => $art->getTitle(),
                'creating_date' => $art->getCreatingDate(),
                'url' => $art->getPublicUrl(),
                'content' => substr(strip_tags($art->getHtmlContent()), 0, 100)
            );
        }

        $file = $c['cachedir'] . DIRECTORY_SEPARATOR . self::getName($option);

        file_put_contents($file, serialize($articles));

        return $articles;
    }
}