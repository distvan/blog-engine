<?php
namespace Distvan;

use DOMDocument;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Class LocalFileStore
 *
 * Store items in local filesystem
 *
 * This class is responsible for read xml and write from local filesystem
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class LocalFileStore extends BaseStore
{
    protected $_storeBaseDir;
    protected $_dom;
    protected $_fileName;

    /**
     * LocalFileStore constructor.
     *
     * @param Config $config
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);

        $c = $this->_config->get();

        if(!isset($c['param']['path']) || empty($c['param']['path']))
        {
            throw new Exception('The LocalFileStore path is not set!');
        }
        if(!is_dir($c['param']['path']))
        {
            throw new Exception('The storage is not a directory!');
        }

        $this->_storeBaseDir = $c['param']['path'];

        //read or create one dom
        $this->_dom = $this->loadXml($this->_fileName);
    }

    /**
     * Save Xml
     *
     * @param $fileName
     * @param $xml
     * @return int
     */
    public function saveXml($fileName, $xml)
    {
        $c = $this->_config->get();

        $file = $c['param']['path'] . DIRECTORY_SEPARATOR . $fileName;

        return file_put_contents($file, $xml);
    }

    /**
     * Load xml from file
     *
     * @param $fileName
     * @return DOMDocument|NULL
     */
    protected function loadXml($fileName)
    {
        $c = $this->_config->get();
        $file = $c['param']['path'] . DIRECTORY_SEPARATOR . $fileName;

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->validateOnParse = true;
        if(file_exists($file))
        {
            $dom->load($file, LIBXML_PARSEHUGE|LIBXML_DTDLOAD|LIBXML_DTDVALID);
            $dom->validate();
            return $dom;
        }

        return NULL;
    }

    /**
     * Put contents into file and create directories if it doesn't exist
     *
     * @param $dir
     * @param $content
     * @return int
     */
    protected function forceWriteFile($dir, $content)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $dir);
        $file = array_pop($parts);
        $dir = '';

        foreach($parts as $part)
        {
            if(!is_dir($dir .= DIRECTORY_SEPARATOR . $part))
            {
                mkdir($dir);
            }
        }

        return file_put_contents($dir . DIRECTORY_SEPARATOR . $file, $content);
    }

    /**
     * Delete directories and its content recursive
     *
     * @param string $dir
     * @param boolean $removeItself
     */
    public static function deleteDirectories($dir, $removeItself=true)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach($files as $fileInfo)
        {
            $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileInfo->getRealPath());
        }

        if($removeItself)
        {
            rmdir($dir);
        }
    }
}