<?php
namespace Distvan\Model;

use Distvan\Validator;
use Distvan\ValidatorException;
use DateTime;
use Distvan\Config;

/**
 * Class Article
 *
 * @package Distvan\Model
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Article extends Base
{
    private $_title;
    private $_url;
    private $_categoryId;
    private $_creatingDate;
    private $_tags;
    private $_htmlContent;

    private $_meta_description; //min 160 max 300
    private $_robots;           //instructions to robots
    private $_og_type;          //open graph type

    /**
     * Article constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $c = $this->_config->get();
        $dateFormat = isset($c['date_format']) ? $c['date_format'] : 'Y-m-d';
        $this->_creatingDate = date($dateFormat);
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return substr(strrchr(__CLASS__, '\\'), 1);
    }

    /**
     * Get articles by category
     *
     * @return array of Articles
     */
    public function getArticlesByCategory()
    {
        return $this->_store->getArticlesByCategory();
    }

    /**
     * Get articles by tag
     *
     * @param string $tag
     * @return array of Article
     */
    public function getArticlesByTag($tag)
    {
        return $this->_store->getArticlesByTag($tag);
    }

    /**
     * Get articles's distinct creating dates
     *
     * @return array
     */
    public function getRecentArticles($num = 3)
    {
        return $this->_store->getRecentArticles($num);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Get article's public url
     *
     * @return string
     */
    public function getPublicUrl()
    {
        $date = new DateTime($this->getCreatingDate());
        $c = $this->_config->get();

        return '/lang/' . $c['default_language'] . '/' . $date->format('Y')
            . '/' . $date->format('m') . '/' . $date->format('d') . '/' . $this->getUrl();

    }

    /**
     * @param $url
     * @throws ValidatorException
     */
    public function setUrl($url)
    {
        if(!Validator::isValidUrl($url))
        {
            throw new ValidatorException('Url is not valid!');
        }
        $this->_url = $url;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->_categoryId;
    }

    /**
     * @param mixed $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->_categoryId = $categoryId;
    }

    /**
     * @return bool|string
     */
    public function getCreatingDate()
    {
        return $this->_creatingDate;
    }

    /**
     * @param $date
     */
    public function setCreatingDate($date)
    {
        $this->_creatingDate = $date;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->_tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getHtmlContent()
    {
        return $this->_htmlContent;
    }

    /**
     * @param mixed $htmlContent
     */
    public function setHtmlContent($htmlContent)
    {
        $this->_htmlContent = $htmlContent;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->_meta_description;
    }

    /**
     * @param mixed $meta_description
     */
    public function setMetaDescription($meta_description)
    {
        $this->_meta_description = $meta_description;
    }

    /**
     * @return mixed
     */
    public function getRobots()
    {
        return $this->_robots;
    }

    /**
     * @param mixed $robots
     */
    public function setRobots($robots)
    {
        $this->_robots = $robots;
    }

    /**
     * @return mixed
     */
    public function getOgType()
    {
        return $this->_og_type;
    }

    /**
     * @param mixed $og_type
     */
    public function setOgType($og_type)
    {
        $this->_og_type = $og_type;
    }
}