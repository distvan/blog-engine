<?php
namespace Distvan;

use Distvan\Model\Category;
use DOMXPath;
use DOMDocument;

/**
 * Class XmlCategoryHandler
 *
 * The class is responsible for Category xml manipulation
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class XmlCategoryHandler extends XmlHandler
{
    private $_category;

    /**
     * XmlCategoryHandler constructor.
     *
     * @param Category $category
     * @param Config $config
     * @param DOMDocument $dom
     */
    public function __construct(Category $category, Config $config, DOMDocument $dom)
    {
        $this->_category = $category;
        parent::__construct($dom, $config);
    }

    /**
     *  Add a category to dom xml
     */
    public function addToXml($dom)
    {
        $categories = $dom->documentElement;

        $category = $dom->createElement('category');
        $id = $dom->createAttribute('id');
        $id->appendChild($dom->createTextNode($this->_category->getId()));
        $category->appendChild($id);

        $name = $dom->createElement('name');
        $name->appendChild($dom->createTextNode($this->_category->getName()));
        $category->appendChild($name);

        $parent_id = $dom->createElement('parent_id');
        $parent_id->appendChild($dom->createTextNode($this->_category->getParentId()));
        $category->appendChild($parent_id);

        $url = $dom->createElement('url');
        $url->appendChild($dom->createTextNode($this->_category->getUrl()));
        $category->appendChild($url);

        $categories->appendChild($category);
    }

    /**
     * Get a category from dom xml
     *
     * @return Category
     * @throws ValidatorException
     */
    public function getCategory()
    {
        $category = new Category($this->_config);
        $id = $this->_category->getId();
        $categoryXml = $this->getXmlCategory($id);
        $category->setId($id);
        $category->setParentId($categoryXml->getElementsByTagName('parent_id')->item(0)->nodeValue);
        $category->setName($categoryXml->getElementsByTagName('name')->item(0)->nodeValue);
        $category->setUrl($categoryXml->getElementsByTagName('url')->item(0)->nodeValue);

        return $category;
    }

    /**
     * Get total number of categories
     *
     * @return int
     */
    public function getTotalNumber()
    {
        $xpath = new DOMXPath($this->_dom);

        $result = $xpath->query('//category');

        return (int)$result->length;
    }

    /**
     * Get all category
     *
     * @return array of Category
     * @throws ValidatorException
     */
    public function getAllCategory()
    {
        $result = array();

        $xpath = new DOMXPath($this->_dom);

        $res = $xpath->query('//categories/category');

        for($i=0;$i<$res->length;$i++)
        {
            $catXml = $res->item($i);

            $category = new Category($this->_config);
            $category->setId($catXml->getAttribute('id'));
            $category->setName($catXml->getElementsByTagName('name')->item(0)->nodeValue);
            $category->setParentId($catXml->getElementsByTagName('parent_id')->item(0)->nodeValue);
            $category->setUrl($catXml->getElementsByTagName('url')->item(0)->nodeValue);

            array_push($result, $category);
        }

        return $result;
    }

    /**
     * Search category by id and return
     *
     * @param $id
     * @return \DOMNode
     */
    protected function getXmlCategory($id)
    {
        $xpath = new DOMXPath($this->_dom);
        $result = $xpath->query('//category[@id="' . $id . '"]');

        return $result->item(0);
    }

    /**
     * Delete category from xml
     *
     */
    public function deleteCategory()
    {
        $categoryXml = $this->getXmlCategory($this->_category->getId());

        $categoryXml->parentNode->removeChild($categoryXml);
    }

    /**
     *  Update a categrory in the dom xml
     */
    public function updateCategory()
    {
        $categoryXml = $this->getXmlCategory($this->_category->getId());
        $categoryXml->getElementsByTagName('parent_id')->item(0)->nodeValue = $this->_category->getParentId();
        $categoryXml->getElementsByTagName('name')->item(0)->nodeValue = $this->_category->getName();
        $categoryXml->getElementsByTagName('url')->item(0)->nodeValue = $this->_category->getUrl();
    }
}