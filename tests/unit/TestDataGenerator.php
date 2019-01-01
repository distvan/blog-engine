<?php

use Distvan\LocalFileStoreArticle;
use Distvan\Model\Article;
use Distvan\Config;
use Distvan\LocalFileStore;
use Distvan\Model\Category;
use Codeception\Test\Unit;

class TestDataGenerator extends Unit
{
    protected $_config;

    protected function _before(){}

    protected function _after(){}


    /*
     * Generate category data
     *
     */
    public function testCategoryData()
    {
        for($i=1;$i<=100;$i++)
        {
            $category = new Category($this->_config);
            $category->setName('Teszt-' . $i);
            $category->setUrl('testing-' . $i);
            $category->insert();
        }
    }

    /**
     *  Generate article data
     */
    public function testArticleData()
    {

    }
}
