<?php

use Distvan\Config;
use Distvan\Model\Category;
use Distvan\LocalFileStoreCategory;
use Distvan\ValidatorException;

require_once 'TestConfig.php';

class CategoryTest extends \Codeception\Test\Unit
{
    protected $store;
    protected $_xmlFilePath;
    protected $_config;

    protected function _before()
    {
        $this->_config = new Config(TestConfig::get());
        $config = $this->_config->get();
        $this->_xmlFilePath = $config['param']['path'] . DIRECTORY_SEPARATOR . LocalFileStoreCategory::fileName;
        $dom = new DOMDocument();
        $dom->load($this->_xmlFilePath);
        foreach($dom->getElementsByTagName('category') as $href)
        {
            $href->parentNode->removeChild($href);
        }
        $dom->save($this->_xmlFilePath);
    }

    protected function _after(){}

    // tests
    public function testInsertNewCategory()
    {
        $name = 'Testcat';
        $parentId = '23ernmsgk';
        $url = '/category/test';

        $category = new Category($this->_config);
        $category->setName($name);
        $category->setParentId($parentId);
        $category->setUrl($url);
        $category->insert();

        $xml = simplexml_load_file($this->_xmlFilePath);

        $this->assertEquals($xml->category->name, $name);
        $this->assertEquals($xml->category->parent_id, $parentId);
        $this->assertEquals($xml->category->url, $url);
    }

    public function testInsertInvalidCategoryUrl()
    {
        $isError = false;
        try
        {
            $category = new Category($this->_config);
            $category->setName('catname');
            $category->setParentId('xbnh778');
            $category->setUrl('invalid url name');
            $category->insert();
        }
        catch(Exception $e)
        {
            $isError = true;
            $this->assertTrue($e instanceof ValidatorException);
        }
        $this->assertTrue($isError);
    }

    public function testGetCategory()
    {
        $name = 'TestdataName';
        $parentId = 'iui34kjkj';
        $url = '/category/testdataurl';

        $category = new Category($this->_config);
        $category->setName($name);
        $category->setParentId($parentId);
        $category->setUrl($url);
        $category->insert();

        $result = $category->get();

        $this->assertTrue($result instanceof Category);
        $this->assertEquals($result->getName(), $name);
        $this->assertEquals($result->getParentId(), $parentId);
        $this->assertEquals($result->getUrl(), $url);
    }

    public function testDeleteCategory()
    {
        $name = 'TestdataName123';
        $parentId = 'iui34wztztzt';
        $url = '/category/testdataurl123';

        $category = new Category($this->_config);
        $category->setName($name);
        $category->setParentId($parentId);
        $category->setUrl($url);
        $category->insert();

        $result = $category->get();

        $this->assertTrue($result instanceof Category);
        $this->assertEquals($result->getName(), $name);
        $this->assertEquals($result->getParentId(), $parentId);
        $this->assertEquals($result->getUrl(), $url);

        $category->delete();
    }

    public function testUpdateCategory()
    {
        $name = 'Updatecategorytest';
        $parentId = 'iui46891lzt';
        $url = '/category/updatetest345';

        $category = new Category($this->_config);
        $category->setName($name);
        $category->setParentId($parentId);
        $category->setUrl($url);
        $category->insert();

        $result = $category->get();

        $newName = 'UpdatedCategoryName';
        $newParentId = 'xxbxnxnx';
        $newUrl = '/category/updatedCatname';

        $result->setName($newName);
        $result->setParentId($newParentId);
        $result->setUrl($newUrl);
        $result->update();

        $cat = new Category($this->_config);
        $cat->setId($category->getId());
        $updatedCat = $cat->get();

        $this->assertEquals($updatedCat->getName(), $newName);
        $this->assertEquals($updatedCat->getParentId(), $newParentId);
        $this->assertEquals($updatedCat->getUrl(), $newUrl);
    }
}