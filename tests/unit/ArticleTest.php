<?php

use Distvan\LocalFileStoreArticle;
use Distvan\Model\Article;
use Distvan\Config;
use Distvan\LocalFileStore;
use Distvan\ValidatorException;
use Codeception\Test\Unit;

require_once 'TestConfig.php';

class ArticleTest extends Unit
{
    protected $store;
    protected $_xmlFilePath;
    protected $_config;
    
    protected function _before()
    {
        $this->_config = new Config(TestConfig::get());
        $c = $this->_config->get();
        $this->_xmlFilePath = $c['param']['path'] . DIRECTORY_SEPARATOR . LocalFileStoreArticle::DESCRIPTOR_FILE;
        $dom = new DOMDocument();
        $dom->load($this->_xmlFilePath);
        foreach ($dom->getElementsByTagName('article') as $href) {
            $href->parentNode->removeChild($href);
        }
        $dom->save($this->_xmlFilePath);
    }

    protected function _after()
    {
        //delete all
        $c = $this->_config->get();
        $dir = $c['param']['path'] . DIRECTORY_SEPARATOR . 'lang';
        if(is_dir($dir))
        {
            LocalFileStore::deleteDirectories($dir);
        }
    }

    public function testInsertNewArticle()
    {
        $title = 'Testing article content';
        $categoryId = 'khjoii55dd';
        $url = 'testing-article-content';
        $content = '<p>Hello World!</p>';
        $metaDescription = 'This is a meta description example';
        $robots = 'index,follow';
        $ogType = 'article';

        $article = new Article($this->_config);
        $article->setTitle($title);
        $article->setCategoryId($categoryId);
        $article->setHtmlContent($content);
        $article->setUrl($url);
        $article->setMetaDescription($metaDescription);
        $article->setRobots($robots);
        $article->setOgType($ogType);
        $article->insert();

        $xml = simplexml_load_file($this->_xmlFilePath);
        $this->assertEquals($xml->article->title, $title);
        $this->assertEquals($xml->article->category_id, $categoryId);

        $fs = new LocalFileStoreArticle($this->_config);
        $fs->setObject($article);
        $directory = $fs->getSaveDirectory();

        /*
        $date = new DateTime($xml->article->creating_date);
        $url = $xml->article->url;
        $c = $this->_config->get();
        $directory = $c['param']['path'] . DIRECTORY_SEPARATOR . 'article/lang' .
            DIRECTORY_SEPARATOR . $c['default_language'] .
            DIRECTORY_SEPARATOR . $date->format('Y') . DIRECTORY_SEPARATOR . $date->format('m') . DIRECTORY_SEPARATOR . $date->format('d') .
            DIRECTORY_SEPARATOR . basename($url, ".html") . DIRECTORY_SEPARATOR;
        */

        //check content (file and xml)
        $file = $directory . LocalFileStoreArticle::CONTENT_FILE;
        $contentHtml = file_get_contents($file);
        $metaFile = $directory . LocalFileStoreArticle::META_FILE;
        $metaXml = simplexml_load_file($metaFile);

        $this->assertTrue(file_exists($file));
        $this->assertEquals($content, $contentHtml);
        $this->assertEquals($metaXml->meta->description, $metaDescription);
        $this->assertEquals($metaXml->meta->robots, $robots);
        $this->assertEquals($metaXml->meta->og_type, $ogType);
    }

    public function testSettingInvalidArticleUrl()
    {
        $title = 'Testing article content2';
        $categoryId = 'jhjhjhjhjh';
        $url = 'testing-article-?.';
        $content = '<p>Hello World!</p>';
        $metaDescription = 'This is a meta description example';
        $robots = 'index,follow';
        $ogType = 'article';

        $message = "";

        try
        {
            $article = new Article($this->_config);
            $article->setTitle($title);
            $article->setCategoryId($categoryId);
            $article->setHtmlContent($content);
            $article->setUrl($url);
            $article->setMetaDescription($metaDescription);
            $article->setRobots($robots);
            $article->setOgType($ogType);
        }
        catch(ValidatorException $e)
        {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, "Url is not valid!");
    }

    public function testAddingArticleWithExistingUrl()
    {
        $title = 'Testing article content111';
        $categoryId = 'webnbjhg';
        $url = 'testing-article-exist';
        $content = '<p>Hello World!</p>';
        $metaDescription = 'This is a meta description example';
        $robots = 'index,follow';
        $ogType = 'article';

        $article = new Article($this->_config);
        $article->setTitle($title);
        $article->setCategoryId($categoryId);
        $article->setHtmlContent($content);
        $article->setUrl($url);
        $article->setMetaDescription($metaDescription);
        $article->setRobots($robots);
        $article->setOgType($ogType);
        $article->insert();

        $message = "";

        try
        {
            $article2 = new Article($this->_config);
            $article2->setUrl($url);
            $article2->insert();
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, "Url is already exist!");
    }

    public function testDeleteArticle()
    {
        $title = 'Testing article3 title';
        $categoryId = 'xmzerthca';
        $url = 'testing-article3';
        $content = '<p>Hello World again!</p>';

        $article = new Article($this->_config);
        $article->setTitle($title);
        $article->setCategoryId($categoryId);
        $article->setHtmlContent($content);
        $article->setUrl($url);
        $article->insert();

        $xml = simplexml_load_file($this->_xmlFilePath);

        $this->assertEquals($xml->article->title, $title);
        $this->assertEquals($xml->article->category_id, $categoryId);

        $fs = new LocalFileStoreArticle($this->_config);
        $fs->setObject($article);
        $directory = $fs->getSaveDirectory();
        /*
        $date = new DateTime($xml->article->creating_date);
        $url = $xml->article->url;
        $c = $this->_config->get();
        $directory = $c['param']['path'] . DIRECTORY_SEPARATOR . 'lang' .
            DIRECTORY_SEPARATOR . $c['default_language'] .
            DIRECTORY_SEPARATOR . $date->format('Y') . DIRECTORY_SEPARATOR . $date->format('m') . DIRECTORY_SEPARATOR . $date->format('d') .
            DIRECTORY_SEPARATOR . basename($url, ".html") . DIRECTORY_SEPARATOR;
        */

        $file = $directory . LocalFileStoreArticle::CONTENT_FILE;
        $metaFile = $directory . LocalFileStoreArticle::META_FILE;

        $this->assertTrue(file_exists($file));
        $this->assertTrue(file_exists($metaFile));

        //delete
        $article->delete();

        $dom = new DOMDocument();
        $dom->load($this->_xmlFilePath);
        $xpath = new DOMXPath($dom);
        $result = $xpath->query('//article[@id="' . $article->getId() . '"]');

        $this->assertEquals($result->length, 0);
        $this->assertFalse(file_exists($file));
        $this->assertFalse(file_exists($metaFile));
    }

    public function testGetArticle()
    {
        $title = 'Testing article4 title';
        $categoryId = 'plksgtw';
        $url = 'testing-article4';
        $content = '<p>Hello World testing!</p>';
        $metaDescription = 'This is a meta description example';
        $robots = 'index,follow';
        $ogType = 'article';

        $article = new Article($this->_config);
        $article->setTitle($title);
        $article->setCategoryId($categoryId);
        $article->setHtmlContent($content);
        $article->setUrl($url);
        $article->setMetaDescription($metaDescription);
        $article->setRobots($robots);
        $article->setOgType($ogType);
        $article->insert();

        $new = new Article($this->_config);
        $new->setHtmlContent($content);
        $new->setId($article->getId());
        $result = $new->get();

        $this->assertTrue($result instanceof Article);
        $this->assertEquals($result->getTitle(), $title);
        $this->assertEquals($result->getCategoryId(), $categoryId);
        $this->assertEquals($result->getUrl(), $url);
        $this->assertEquals($result->getHtmlContent(), $content);
        $this->assertEquals($result->getMetaDescription(), $metaDescription);
        $this->assertEquals($result->getRobots(), $robots);
        $this->assertEquals($result->getOgType(), $ogType);

        $article->delete();
    }


    public function UpdateArticle()
    {
        $title = 'Update testing article5 title';
        $categoryId = 'izuvblz';
        $url = 'update-testing-article5';
        $content = '<p>Hello World update testing!</p>';
        $metaDescription = 'This is an update meta description example';
        $robots = 'index,follow';
        $ogType = 'article';

        $article = new Article($this->_config);

        $article->setTitle($title);
        $article->setCategoryId($categoryId);
        $article->setHtmlContent($content);
        $article->setUrl($url);
        $article->setMetaDescription($metaDescription);
        $article->setRobots($robots);
        $article->setOgType($ogType);
        $article->insert();

        $new = new Article($this->_config);
        $new->setId($article->getId());
        $result = $new->get();

        $title = 'Updated testing article6 title';
        $categoryId = 'izukjkjkjk';
        $url = 'updated-testing-article5';
        $content = '<p>New Hello World updated testing!</p>';
        $metaDescription = 'Updated meta description example';
        $robots = 'index,nofollow';
        $ogType = 'book';

        //update
        $result->setTitle($title);
        $result->setCategoryId($categoryId);
        $result->setUrl($url);
        $result->setHtmlContent($content);
        $result->setMetaDescription($metaDescription);
        $result->setRobots($robots);
        $result->setOgType($ogType);
        $result->update();
    }
}