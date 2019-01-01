<?php
namespace Distvan;

use Distvan\Model\Article;
use DOMDocument;
use DOMXPath;
use DateTime;

/**
 * Class XmlArticleWriter
 * 
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class XmlArticleHandler extends XmlHandler
{
    private $_article;

    /**
     * XmlArticleHandler constructor.
     *
     * @param Article $article
     * @param Config $config
     */
    public function __construct(Article $article, Config $config, DOMDocument $dom)
    {
        $this->_article = $article;
        parent::__construct($dom, $config);
    }

    /**
     * Add Category to xml
     *
     * @param $dom
     */
    public function addToXml($dom)
    {
        $articles = $dom->documentElement;

        $article = $dom->createElement('article');
        $id = $dom->createAttribute('id');
        $id->appendChild($dom->createTextNode($this->_article->getId()));
        $article->appendChild($id);

        $title = $dom->createElement('title');
        $title->appendChild($dom->createTextNode($this->_article->getTitle()));
        $article->appendChild($title);

        $category_id = $dom->createElement('category_id');
        $category_id->appendChild($dom->createTextNode($this->_article->getCategoryId()));
        $article->appendChild($category_id);

        $url = $dom->createElement('url');
        $url->appendChild($dom->createTextNode($this->_article->getUrl()));
        $article->appendChild($url);

        $creating_date = $dom->createElement('creating_date');
        $creating_date->appendChild($dom->createTextNode($this->_article->getCreatingDate()));
        $article->appendChild($creating_date);

        $tags = $dom->createElement('tags');
        $tags->appendChild($dom->createTextNode($this->_article->getTags()));
        $article->appendChild($tags);

        $articles->appendChild($article);
    }

    /**
     * Get xml Article by id
     *
     * @param $id
     * @return mixed
     */
    public function getXmlArticle($id)
    {
        $xpath = new DOMXPath($this->_dom);
        $result = $xpath->query('//article[@id="' . $id . '"]');

        return $result->item(0);
    }

    /**
     * Get articles by category id
     *
     * @return array
     */
    public function getArticlesByCategoryId()
    {
        $xpath = new DOMXPath($this->_dom);

        $res = $xpath->query('//articles/article[category_id="' . $this->_article->getCategoryId() . '"]');

        return $this->objectFromXml($res);
    }

    /**
     * Get articles by tag
     *
     * @param $tag
     * @return array
     */
    public function getArticlesByTag($tag)
    {
        $xpath = new DOMXPath($this->_dom);

        $res = $xpath->query('//articles/article/tags[contains(.,"' . $tag . '")]/parent::*');

        return $this->objectFromXml($res);
    }

    /**
     * Get article object list from xml result
     *
     * @param $result
     * @return array
     * @throws ValidatorException
     */
    private function objectFromXml($result)
    {
        $back = array();

        for($i=0;$i<$result->length;$i++)
        {
            $articleXml = $result->item($i);

            $article = new Article($this->_config);
            $article->setId($articleXml->getAttribute('id'));
            $article->setCategoryId($articleXml->getElementsByTagName('category_id')->item(0)->nodeValue);
            $article->setUrl($articleXml->getElementsByTagName('url')->item(0)->nodeValue);
            $article->setCreatingDate($articleXml->getElementsByTagName('creating_date')->item(0)->nodeValue);
            $article->setTags($articleXml->getElementsByTagName('tags')->item(0)->nodeValue);

            array_push($back, $article);
        }

        return $back;
    }

    /**
     * Delete xml Article by id
     */
    public function deleteArticle()
    {
        $articleXml = $this->getXmlArticle($this->_article->getId());

        $articleXml->parentNode->removeChild($articleXml);
    }

    /**
     * Is article url a unique name?
     *
     * @return bool
     */
    public function isExistingUrl()
    {
        $xpath = new DOMXPath($this->_dom);

        $result = $xpath->query('//articles/article[url="' . $this->_article->getUrl() . '"]');

        return $result->length > 1;
    }

    /**
     * Get article's distinct creating dates
     *
     * @return array
     */
    public function getArticlesCreatingDate()
    {
        $back = array();

        $xpath = new DOMXPath($this->_dom);

        $result = $xpath->query('//articles/article[not(creating_date=preceding-sibling::article/creating_date)]/creating_date');

        for($i=0;$i<$result->length;$i++)
        {
            $resultXml = $result->item($i);
            array_push($back, $resultXml->nodeValue);
        }

        usort($back, array($this, 'dateSort'));

        return $back;
    }

    /**
     * Sort input descendant order
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function dateSort($a, $b)
    {
        return strtotime($b) - strtotime($a);
    }
}