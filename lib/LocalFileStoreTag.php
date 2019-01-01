<?php

namespace Distvan;


/**
 * Class LocalFileStoreTag
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class LocalFileStoreTag extends LocalFileStore implements iStorage
{
    const DESCRIPTOR_FILE = 'descriptor/tags.xml';

    public function __construct(Config $config)
    {
        $this->_fileName = self::DESCRIPTOR_FILE;
        parent::__construct($config);
    }

    public function getTotalNumber()
    {
        // TODO: Implement getTotalNumber() method.
    }

    public function insert()
    {
        $handler = new XmlTagHandler($this->_object, $this->_config, $this->_dom);

        $handler->addToXml($this->_dom);

        $result = $this->saveXml(self::DESCRIPTOR_FILE, $handler->getXml());
    }

    public function delete()
    {
        $handler = new XmlTagHandler($this->_object, $this->_config, $this->_dom);

        $handler->deleteArticle();

        $this->saveXml(self::DESCRIPTOR_FILE, $handler->getXml());
    }

    public function get()
    {
        $handler = new XmlTagHandler($this->_object, $this->_config, $this->_dom);

        return $handler->getTag();
    }

    public function getAll()
    {
        $handler = new XmlTagHandler($this->_object, $this->_config, $this->_dom);

        return $handler->getAllTag();
    }

    public function update()
    {
        // TODO: Implement update() method.
    }
}