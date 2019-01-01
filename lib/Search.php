<?php
namespace Distvan;

use Distvan\Model\Article;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Class Search
 *
 * @package Distvan
 */
class Search
{
    /**
     * Search the first keyword in the article files and return the Article object
     *
     * @param $keyword
     * @return array of Articles
     */
    public static function fullTextIn($keyword)
    {
        $config = new Config();
        $c = $config->get();
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($c['param']['path']));

        //get only Article files into files
        $matchedFiles = array();

        foreach($rii as $file)
        {
            if($file->isDir() || $file->getExtension() != 'html')
            {
                continue;
            }
            foreach(file($file) as $fli => $fl)
            {
                if(strpos($fl, $keyword) !== false)
                {
                    $matchedFiles[] = $file->getPathname();
                    break;
                }
            }
            if(count($matchedFiles) > $c['max_search_result'])
            {
                break;
            }
        }

        $result = array();

        //get Article Objects
        foreach($matchedFiles as $file)
        {
            $parts = explode(DIRECTORY_SEPARATOR, $file);
            $url = $parts[count($parts)-2];
            $year = $parts[count($parts)-5];
            $month = $parts[count($parts)-4];
            $day = $parts[count($parts)-3];
            $creatingDate = $year . '-' . $month . '-' . $day;
            $art = new Article($config);
            $art->setUrl($url);
            $art->setCreatingDate($creatingDate);
            $article = $art->get();
            
            array_push($result, $article);
        }

        return $result;
    }
}