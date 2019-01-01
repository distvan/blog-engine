<?php
namespace Distvan;

use DOMDocument;
use DOMXPath;
use Distvan\Model\Article;
use Distvan\Model\Meta;

class XmlMetaHandler extends XmlHandler
{
    private $_article;

    public function __construct(Article $article, Config $config, DOMDocument $dom)
    {
        $this->_article = $article;
        parent::__construct($dom, $config);
    }

    public function addToXml($dom)
    {
        $metas = $dom->documentElement;

        $meta = $dom->createElement('meta');

        $title = $dom->createElement('title');
        $title->appendChild($dom->createTextNode($this->_article->getTitle()));
        $meta->appendChild($title);

        $description = $dom->createElement('description');
        $description->appendChild($dom->createTextNode($this->_article->getMetaDescription()));
        $meta->appendChild($description);

        $robots = $dom->createElement('robots');
        $robots->appendChild($dom->createTextNode($this->_article->getRobots()));
        $meta->appendChild($robots);

        $ogType = $dom->createElement('og_type');
        $ogType->appendChild($dom->createTextNode($this->_article->getOgType()));
        $meta->appendChild($ogType);

        $metas->appendChild($meta);
    }

    public function getMeta()
    {
        $meta = new Meta();
        $metaXml = $this->getXmlMeta();
        $meta->setTitle($metaXml->getElementsByTagName('title')->item(0)->nodeValue);
        $meta->setDescription($metaXml->getElementsByTagName('description')->item(0)->nodeValue);
        $meta->setRobots($metaXml->getElementsByTagName('robots')->item(0)->nodeValue);
        $meta->setOgType($metaXml->getElementsByTagName('og_type')->item(0)->nodeValue);

        return $meta;
    }

    protected function getXmlMeta()
    {
        $xpath = new DOMXPath($this->_dom);
        $result = $xpath->query('//meta');

        return $result->item(0);
    }

    public static function createXml()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML('<metas/>');
        
        return $dom;
    }

    public static function loadXmlFromFile($file)
    {
        $dom = new DOMDocument('1.0', 'utf-8');

        if(file_exists($file))
        {
            $dom->load($file, LIBXML_PARSEHUGE|LIBXML_DTDLOAD|LIBXML_DTDVALID);
        }

        return $dom;
    }
}