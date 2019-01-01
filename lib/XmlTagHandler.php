<?php

namespace Distvan;

use DOMDocument;
use DOMXPath;
use Distvan\Model\Tag;

/**
 * Class XmlTagHandler
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class XmlTagHandler extends XmlHandler
{
    private $_tag;

    /**
     * XmlTagHandler constructor.
     *
     * @param Tag $tag
     * @param Config $config
     * @param DOMDocument $dom
     */
    public function __construct(Tag $tag, Config $config, DOMDocument $dom)
    {
        $this->_tag = $tag;
        parent::__construct($dom, $config);
    }

    /**
     * @param $dom
     */
    public function addToXml($dom)
    {
        $tags = $dom->documentElement;

        $tag = $dom->createElement('tag');

        $name = $dom->createElement('name');
        $name->appendChild($dom->createTextNode($this->_tag->getName()));
        $tag->appendChild($name);

        $url = $dom->createElement('url');
        $url->appendChild($dom->createTextNode($this->_tag->getUrl()));
        $tag->appendChild($url);

        $tags->appendChild($tag);
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        $tag = new Tag($this->_config);

        $tagXml = $this->getXmlTag($this->_tag->getName());
        $tag->setName($tagXml->getElementsByTagName('name')->item(0)->nodeValue);
        $tag->setUrl($tagXml->getElementsByTagName('url')->item(0)->nodeValue);

        return $tag;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getXmlTag($url)
    {
        $xpath = new DOMXPath($this->_dom);

        $result = $xpath->query('//tags/tag[url="' . $url . '"]');

        return $result->item(0);
    }

    /**
     * Delete a tag from xml
     */
    public function deleteTag()
    {
        $tagXml = $this->getXmlTag($this->_tag->getUrl());

        $tagXml->parentNode->removeChild($tagXml);
    }

    /**
     * Get All tag
     *
     * @return array
     */
    public function getAllTag()
    {
        $result = array();

        $xpath = new DOMXPath($this->_dom);

        $res = $xpath->query('//tags/tag');

        for($i=0;$i<$res->length;$i++)
        {
            $tagXml = $res->item($i);
            $tag = new Tag($this->_config);
            $tag->setName($tagXml->getElementsByTagName('name')->item(0)->nodeValue);
            $tag->setUrl($tagXml->getElementsByTagName('url')->item(0)->nodeValue);

            array_push($result, $tag);
        }

        return $result;
    }
}