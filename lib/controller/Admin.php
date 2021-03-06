<?php
namespace Distvan\Controller;

use Distvan\Language;
use Distvan\Model\Category;
use Distvan\Settings;
use Distvan\Config;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Admin
 * 
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Admin extends Action
{
    /**
     * Show Admin login
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response|static
     */
    public function index(Request $request, Response $response, $args)
    {
        $settings = new Settings();
        $parts = explode('/', $settings->get('admin_url'));
        $hash = $parts[count($parts)-1];
        if($args['key'] !== $hash)
        {
            $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('main'));

            return $response = $response->withRedirect($uri, 301);
        }

        $csrf = $this->_ci->get('csrf');

        $data['nameKey'] = $csrf->getTokenNameKey();
        $data['valueKey'] = $csrf->getTokenValueKey();
        $data['name'] = $request->getAttribute($data['nameKey']);
        $data['value'] = $request->getAttribute($data['valueKey']);

        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/login.html', $data, 'html');

        return $response;
    }

    /**
     * Admin Authentication
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     */
    public function login(Request $request, Response $response, $args)
    {
        $lang = Language::load('AdminLogin');
        $settings = new Settings();
        $user = $request->getParam('user');
        $pass = $request->getParam('pass');

        if(password_verify($pass, $settings->get('password')) && $user == $settings->get('admin'))
        {
            $this->_session->set('loggedin', true);
                
            $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('admin-dashboard'));

            return $response = $response->withRedirect($uri, 301);
        }

        $csrf = $this->_ci->get('csrf');

        $data['nameKey'] = $csrf->getTokenNameKey();
        $data['valueKey'] = $csrf->getTokenValueKey();
        $data['name'] = $request->getAttribute($data['nameKey']);
        $data['value'] = $request->getAttribute($data['valueKey']);
        $data['error'] = $lang->get('error_bad_credentials');

        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/login.html', $data, 'html');

        return $response;
    }

    /**
     * Logout
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function logout(Request $request, Response $response, $args)
    {
        $settings = new Settings();

        $this->_session->set('loggedin', false);

        $url = $settings->get('admin_url');

        $parts = explode('/', $url);

        $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('admin',
            array('key'=>$parts[count($parts)-1])));

        return $response = $response->withRedirect($uri, 301);
    }

    /**
     * Settings handling
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function profile(Request $request, Response $response, $args)
    {
        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/dashboard.html', array('content' => 'settings'), 'html');
    }

    /**
     * Show Admin Dashboard
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function dashboard(Request $request, Response $response, $args)
    {
        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/dashboard.html', array('content' => ''), 'html');
    }

    /**
     * Categories handling
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function categories(Request $request, Response $response, $args)
    {
        $categories = array();
        $config = new Config();
        $c = $config->get();
        $category = new Category($config);

        foreach($category->getAll() as $cat)
        {
            $categories[] = array(
                'id' => $cat->getId(),
                'name' => $cat->getName(),
                'url' => $cat->getUrl()
            );
        }

        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/dashboard.html',
            array('content' => 'categories', 'categories' => $categories),'html');
    }

    /**
     * Tags handling
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function tags(Request $request, Response $response, $args)
    {
        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/dashboard.html', array('content' => 'tags'),'html');
    }

    /**
     * Articles handling
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function articles(Request $request, Response $response, $args)
    {
        $this->_ci->view->render($response, 'admin/main.html', array(), 'html');
        $this->_ci->view->render($response, 'admin/dashboard.html', array('content' => 'articles'),'html');
    }
}