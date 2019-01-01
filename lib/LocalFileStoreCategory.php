<?php
namespace Distvan;

/**
 * Class LocalFileStoreCategory
 * This class responsible to handle I/O actions for categories
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class LocalFileStoreCategory extends LocalFileStore implements iStorage
{
    const fileName = 'descriptor/categories.xml';

    /**
     * LocalFileStoreCategory constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_fileName = self::fileName;
        parent::__construct($config);
    }

    /**
     * Insert a Category to the category descriptor file
     *
     * @return int
     */
    public function insert()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);

        $handler->addToXml($this->_dom);

        return $this->saveXml(self::fileName, $handler->getXml());
    }

    /**
     * Get all category
     *
     * @return array of Category
     */
    public function getAll()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);

        return $handler->getAllCategory();
    }

    /**
     * Get a Category from the category descriptor file
     *
     * @return Model\Category
     */
    public function get()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);

        return $handler->getCategory();
    }

    /**
     * Get total number of categories
     *
     * @return int
     */
    public function getTotalNumber()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);
        
        return $handler->getTotalNumber();
    }

    /**
     * Delete a Category from the category descriptor file
     *
     * @return int
     */
    public function delete()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);

        $handler->deleteCategory();

        return $this->saveXml(self::fileName, $handler->getXml());
    }

    /**
     * Update a Category in the category descriptor file
     *
     * @return int
     */
    public function update()
    {
        $handler = new XmlCategoryHandler($this->_object, $this->_config, $this->_dom);

        $handler->updateCategory();

        return $this->saveXml(self::fileName, $handler->getXml());
    }
}