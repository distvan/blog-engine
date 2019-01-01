<?php
namespace Distvan;

use DOMDocument;

/**
 * Class XmlHandler
 *
 * The class is responsible for xml manipulation
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
abstract class XmlHandler
{
    protected $_dom;
    protected $_config;

    /**
     * XmlHandler constructor.
     * @param DOMDocument $dom
     * @param Config $config
     */
    public function __construct(DOMDocument $dom, Config $config)
    {
        $this->_config = $config;
        $this->_dom = $dom;
    }

    public function getXml()
    {
        return $this->_dom->saveXML();
    }

    abstract public function addToXml($dom);
}