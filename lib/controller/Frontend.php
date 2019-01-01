<?php
namespace Distvan\Controller;

use Distvan\ArticleCache;
use Distvan\Config;
use Distvan\Model\Article;
use Distvan\Cache;
use Distvan\Search;
use Distvan\Language;
use Distvan\Settings;
use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;
use DateTime;


/**
 * Class Frontend
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Frontend extends Action
{
    protected $_settings;
    protected $_social;

    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
        $this->_settings = new Settings();
        $this->_social = array(
            'facebook' => $this->_settings->get('facebook'),
            'twitter' => $this->_settings->get('twitter'),
            'pinterest' => $this->_settings->get('pinterest'),
            'dribble' => $this->_settings->get('dribble')
        );
    }

    /**
     * Blog Main page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response|static
     */
    public function index(Request $request, Response $response, $args)
    {
        $isSetup = $request->getAttribute('setup');

        if(isset($isSetup) && $isSetup)
        {
            $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('setup'));
            
            return $response = $response->withRedirect($uri, 301);
        }

        $articles = Cache::get(Cache::ARTICLE, array('type' => ArticleCache::RECENT));

        $this->_ci->view->render($response, 'frontend/main.html', array('article' => false), 'html');
        $this->_ci->view->render($response, 'frontend/index.html',
            array('articles' => $articles,
                'social' => $this->_social,
                'author' => $this->_settings->get('author'),
            ), 'html');

        return $response;
    }

    /**
     * Show a category and its articles
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function showCategory(Request $request, Response $response, $args)
    {
        $categoryId = '';
        $categoryUrl = '/'.$args['category'];

        $categories = Cache::get(Cache::CATEGORY);

        foreach($categories as $category)
        {
            if($category['url'] == $categoryUrl)
            {
                $categoryId = $category['id'];
                break;
            }
        }

        $articles = Cache::get(Cache::ARTICLE, array('type' => ArticleCache::BY_CATEGORY, 'category_id' => $categoryId));

        $this->_ci->view->render($response, 'frontend/main.html', array('article' => false), 'html');
        $this->_ci->view->render($response, 'frontend/articles_by_query.html',
            array('articles' => $articles,
                'author' => $this->_settings->get('author'),
                'social' => $this->_social
            ), 'html');

        return $response;
    }

    /**
     * Show articles by tag
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function showTag(Request $request, Response $response, $args)
    {
        $tag = $args['tag'];

        $articles = Cache::get(Cache::ARTICLE, array('type' => ArticleCache::BY_TAG, 'tag' => $tag));

        $this->_ci->view->render($response, 'frontend/main.html', array('article' => false), 'html');
        $this->_ci->view->render($response, 'frontend/articles_by_query.html',
            array('articles' => $articles,
                'author' => $this->_settings->get('author'),
                'social' => $this->_social
            ), 'html');

        return $response;
    }

    /**
     * Show an article
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Distvan\ValidatorException
     */
    public function showArticle(Request $request, Response $response, $args)
    {
        $config = new Config();
        $c = $config->get();
        $year = (int)$args['year'];
        $month = (int)$args['month'];
        $day = (int)$args['day'];
        $articleUrl = $args['article_url'];
        $date = new DateTime();
        $date->setDate($year, $month, $day);
        $creatingDate = $date->format($c['date_format']);

        $article = new Article($config);
        $article->setUrl($articleUrl);
        $article->setCreatingDate($creatingDate);
        $result = $article->get();

        $data['article'] = true;
        $data['title'] = $result->getTitle();
        $data['content'] = $result->getHtmlContent();
        $data['meta_description']= $result->getMetaDescription();
        $data['og_type'] = $result->getOgType();
        $data['robots'] = $result->getRobots();
        $data['creating_date'] = $date->format('Y M d.');
        $data['author'] = $this->_settings->get('author');
        $data['social'] = $this->_social;

        $this->_ci->view->render($response, 'frontend/main.html', $data, 'html');
        $this->_ci->view->render($response, 'frontend/article.html', $data, 'html');

        return $response;
    }

    public function search(Request $request, Response $response, $args)
    {
        $data = array();
        $articles = array();
        $params = $request->getParams();
        $lang = Language::load('Search');

        if(strlen($params['keyword']) >= 5)
        {
            $data['text_result'] = $lang->get('search_result') . $params['keyword'];
            $result = Search::fullTextIn($params['keyword']);
            foreach($result as $res)
            {
                $articles[] = array(
                    'title' => $res->getTitle(),
                    'creating_date' => $res->getCreatingDate(),
                    'url' => $res->getPublicUrl()
                );
            }
        }
        else
        {
            $data['error'] = $lang->get('error_short_keyword');
        }

        $this->_ci->view->render($response, 'frontend/main.html', array('article' => false), 'html');
        $this->_ci->view->render($response, 'frontend/articles_by_query.html',
            array('articles' => $articles,
                'data' => $data,
                'social' => $this->_social
            ), 'html');
    }
}