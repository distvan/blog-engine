<?php
namespace Distvan;

use DateTime;
use Distvan\Model\Article;
use Exception;

/**
 * Class LocalFileStoreArticle
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class LocalFileStoreArticle extends LocalFileStore implements iStorage
{
    const DESCRIPTOR_FILE = 'descriptor/articles.xml';
    const CONTENT_FILE = 'article.html';
    const META_FILE = 'meta.xml';

    /**
     * LocalFileStoreArticle constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_fileName = self::DESCRIPTOR_FILE;
        parent::__construct($config);
    }

    /**
     * Insert an Article content and create meta
     *
     * @throws Exception
     */
    public function insert()
    {
        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        $fileName = self::DESCRIPTOR_FILE;
        
        $handler->addToXml($this->_dom);

        if($handler->isExistingUrl())
        {
            throw new Exception('Url is already exist!');
        }

        $result = $this->saveXml($fileName, $handler->getXml());

        if($result !== FALSE)
        {
            $htmlContent = $this->_object->getHtmlContent();

            //create content
            $this->forceWriteFile($this->getSaveDirectory() . self::CONTENT_FILE, $htmlContent);

            //save meta
            $metaDom = XmlMetaHandler::createXml();
            $metaHandler = new XmlMetaHandler($this->_object, $this->_config, $metaDom);
            $metaHandler->addToXml($metaDom);
            file_put_contents($this->getSaveDirectory() . self::META_FILE, $metaDom->saveXML());
        }
    }

    public function getTotalNumber()
    {
        // TODO: Implement getTotalNumber() method.
    }

    /**
     * @return array
     */
    public function getArticlesByCategory()
    {
        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        $result = $handler->getArticlesByCategoryId();

        return $this->getObjectFromXml($result);
    }

    /**
     * @param $tag
     * @return array
     */
    public function getArticlesByTag($tag)
    {
        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        $result = $handler->getArticlesByTag($tag);

        return $this->getObjectFromXml($result);
    }

    /**
     * Get num count recently created articles
     *
     * @return array
     */
    public function getRecentArticles($num = 3)
    {
        $articles = array();

        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        //get creating dates in descendant order
        $dates = $handler->getArticlesCreatingDate();
        $c = $this->_config->get();

        foreach($dates as $dateStr)
        {
            if(count($articles) == $num)
            {
                break;
            }
            $date = new DateTime($dateStr);
            $url = $c['param']['path'] . DIRECTORY_SEPARATOR . 'article/lang' .
                DIRECTORY_SEPARATOR . $c['default_language'] .
                DIRECTORY_SEPARATOR . $date->format('Y') . DIRECTORY_SEPARATOR . $date->format('m') .
                DIRECTORY_SEPARATOR . $date->format('d') . DIRECTORY_SEPARATOR;

            if(is_dir($url))
            {
                $articleUrls = $this->getDir($url);
                foreach($articleUrls as $articleUrl)
                {
                    if(count($articles) == $num)
                    {
                        break;
                    }
                    $article = new Article($this->_config);
                    $article->setCreatingDate($dateStr);
                    $article->setUrl($articleUrl);
                    $result = $article->get();

                    array_push($articles, $result);
                }
            }
        }

        return $articles;
    }

    /**
     * Get directory names in start directory
     *
     * @param $startDir
     * @return array
     */
    private function getDir($startDir)
    {
        $result = array();

        foreach(glob($startDir . '*', GLOB_ONLYDIR) as $dir)
        {
            $dir = str_replace($startDir, '', $dir);
            array_push($result, $dir);
        }

        return $result;
    }

    /**
     * @param $result
     * @return array
     */
    private function getObjectFromXml($result)
    {
        $articles = array();

        foreach($result as $article)
        {
            if($article instanceof Article)
            {
                $lfs = new LocalFileStoreArticle($this->_config);
                $lfs->setObject($article);
                $metaXmlDom = XmlMetaHandler::loadXmlFromFile($lfs->getSaveDirectory() . self::META_FILE);
                $metaHandler = new XmlMetaHandler($article, $this->_config, $metaXmlDom);
                $meta = $metaHandler->getMeta();
                $article->setTitle($meta->getTitle());
                array_push($articles, $article);
            }
        }

        return $articles;
    }

    /**
     * Get an Article
     * You should setup the article Id or
     * creatingDate + url
     *
     * @return bool|Article
     * @throws Exception
     */
    public function get()
    {
        if(empty($this->_object->getHtmlContent()))
        {
            $directory = $this->getSaveDirectory();

            if(is_dir($directory))
            {
                $metaDom = XmlMetaHandler::loadXmlFromFile($directory . self::META_FILE);
                $metaHandler = new XmlMetaHandler($this->_object, $this->_config, $metaDom);
                $meta = $metaHandler->getMeta();
                $htmlContent = file_get_contents($directory . self::CONTENT_FILE);
                $this->_object->setTitle($meta->getTitle());
                $this->_object->setHtmlContent($htmlContent);
                $this->_object->setMetaDescription($meta->getDescription());
                $this->_object->setRobots($meta->getRobots());
                $this->_object->setOgType($meta->getOgType());

                return $this->_object;
            }
            else
            {
                throw new LocalFileStoreArticleException('Article not found!',
                    LocalFileStoreArticleException::EXCEPTION_ARTICLE_NOT_FOUND);
            }
        }

        $this->checkId();

        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);
        $articleXml = $handler->getXmlArticle($this->_object->getId());
        $this->_object->setCategoryId($articleXml->getElementsByTagName('category_id')->item(0)->nodeValue);
        $this->_object->setUrl($articleXml->getElementsByTagName('url')->item(0)->nodeValue);
        $this->_object->setCreatingDate($articleXml->getElementsByTagName('creating_date')->item(0)->nodeValue);
        $this->_object->setTags($articleXml->getElementsByTagName('tags')->item(0)->nodeValue);

        $directory = $this->getSaveDirectory();

        if(is_dir($directory))
        {
            $metaDom = XmlMetaHandler::loadXmlFromFile($directory . self::META_FILE);
            $metaHandler = new XmlMetaHandler($this->_object, $this->_config, $metaDom);
            $meta = $metaHandler->getMeta();
            $htmlContent = file_get_contents($directory . self::CONTENT_FILE);
            $this->_object->setTitle($meta->getTitle());
            $this->_object->setHtmlContent($htmlContent);
            $this->_object->setMetaDescription($meta->getDescription());
            $this->_object->setRobots($meta->getRobots());
            $this->_object->setOgType($meta->getOgType());

            return $this->_object;
        }

        return FALSE;
    }

    /**
     * Get Article save directory
     *
     * @return string
     * @throws Exception
     */
    public function getSaveDirectory()
    {
        $this->checkId();

        $c = $this->_config->get();

        $date = new DateTime($this->_object->getCreatingDate());

        return $c['param']['path'] . DIRECTORY_SEPARATOR . 'article/lang' .
            DIRECTORY_SEPARATOR . $c['default_language'] .
            DIRECTORY_SEPARATOR . $date->format('Y') . DIRECTORY_SEPARATOR . $date->format('m') .
            DIRECTORY_SEPARATOR . $date->format('d') . DIRECTORY_SEPARATOR .
            basename($this->_object->getUrl(), ".html") . DIRECTORY_SEPARATOR;
    }

    /**
     * Delete an Article content, descriptor and meta datas
     *
     * @throws Exception
     */
    public function delete()
    {
        $this->checkId();

        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        $handler->deleteArticle();

        $this->saveXml(self::DESCRIPTOR_FILE, $handler->getXml());

        $this->deleteDirectories($this->getSaveDirectory());
    }

    /**
     * Update an Article content, descriptor and meta datas
     *
     */
    public function update()
    {
        $handler = new XmlArticleHandler($this->_object, $this->_config, $this->_dom);

        if($handler->isExistingUrl())
        {
            throw new Exception('Url is already exist!');
        }


    }

    /**
     *  Checking id
     */
    private function checkId()
    {
        if(empty($this->_object->getId()))
        {
            throw new Exception('Empty Id!');
        }
    }
}

class LocalFileStoreArticleException extends Exception
{
    const EXCEPTION_URL_IS_ALREADY_EXISTS = 10000;
    const EXCEPTION_ARTICLE_NOT_FOUND = 100001;
}