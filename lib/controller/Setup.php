<?php
namespace Distvan\Controller;

use Distvan\LocalFileStore;
use Distvan\LocalFileStoreCategory;
use Distvan\LocalFileStoreTag;
use Distvan\Config;
use Distvan\Settings;
use Distvan\LocalFileStoreArticle;
use Distvan\Validator;
use Distvan\Language;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Setup
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Setup extends Action
{
    protected $_data;

    /**
     * Setup constructor.
     *
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
        $config = new Config();
        $c = $config->get();
        $this->_data['storage_dir'] = $c['param']['path'];
        $this->_data['is_storage_writable'] = is_writable($c['param']['path']);
        $this->_data['register_globals_status'] = ini_get('register_globals');
        $this->_data['php_version_ok'] = version_compare(phpversion(), "7.1.0", ">=");
        $this->_data['xml_extension'] = extension_loaded('xml');

        $this->_data['installable'] = $this->_data['is_storage_writable'] && !$this->_data['register_globals_status'] &&
            $this->_data['php_version_ok'] && $this->_data['xml_extension'];
        $this->_data['install_url'] = $this->_ci->get('router')->pathFor('install');

        if(!$this->_session->exists('admin_url'))
        {
            $this->_session->set('admin_url', $this->randomString());
        }
        $this->_data['admin'] = '';
        $this->_data['author'] = '';
        $this->_data['admin_url'] = $this->_ci->get('router')->pathFor('admin', array('key' => $this->_session->get('admin_url')));
        $this->_data['facebook'] = '';
        $this->_data['twitter'] = '';
        $this->_data['pinterest'] = '';
        $this->_data['dribble'] = '';
    }

    /**
     * Check system conditions for installation
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response|static
     */
    public function index(Request $request, Response $response, $args)
    {
        if(self::isAlreadyInstalled())
        {
            $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('main'));

            return $response = $response->withRedirect($uri, 301);
        }

        $this->_ci->view->render($response, 'frontend/setup.html', $this->_data, 'html');

        return $response;
    }

    /**
     * Has Blog Engine already installed?
     *
     * @return bool
     */
    public static function isAlreadyInstalled()
    {
        $config  = new Config();

        $c = $config->get();

        if($c['storage'] == 'LocalFileStore')
        {
            $file = $c['param']['path'] . DIRECTORY_SEPARATOR . LocalFileStoreArticle::DESCRIPTOR_FILE;

            if(!file_exists($file))
            {
                return false;
            }

            return true;
        }
    }

    /**
     * Install Blog Engine
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response|static
     */
    public function install(Request $request, Response $response, $args)
    {
        $lang = Language::load('Setup');

        $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('setup'));

        if(!$this->_data['installable'])
        {
            return $response = $response->withRedirect($uri, 301);
        }

        //input validation
        $error = array();

        $settings['author'] = $request->getParam('author');
        $settings['admin'] = $request->getParam('admin');
        $settings['password'] = $request->getParam('password');
        $settings['password_again'] = $request->getParam('password_again');
        $settings['admin_url'] = $this->_data['admin_url'];
        $settings['facebook'] = $request->getParam('facebook');
        $settings['twitter'] = $request->getParam('twitter');
        $settings['pinterest'] = $request->getParam('pinterest');
        $settings['dribble'] = $request->getParam('dribble');

        if(!Validator::lengthIsValid($settings['author'], 5, 25))
        {
            $error['author'] = $lang->get('error_invalid_author');
        }

        if(!Validator::lengthIsValid($settings['admin'], 4, 10))
        {
            $error['admin'] = $lang->get('error_invalid_admin');
        }

        if(Validator::isWeekPassword($settings['password']))
        {
            $error['password'] = $lang->get('error_invalid_password');
        }

        if($settings['password'] !== $settings['password_again'])
        {
            $error['password_again'] = $lang->get('error_invalid_password_again');
        }

        $this->_data['author'] = $settings['author'];
        $this->_data['admin'] = $settings['admin'];
        $this->_data['facebook'] = $settings['facebook'];
        $this->_data['twitter'] = $settings['twitter'];
        $this->_data['pinterest'] = $settings['pinterest'];
        $this->_data['dribble'] = $settings['dribble'];

        if(!empty($error))
        {
            $this->_data['error'] = $error;

            $this->_ci->view->render($response, 'frontend/setup.html', $this->_data, 'html');

            return $response;
        }
        //end input validation

        $config  = new Config();
        $c = $config->get();

        if($c['storage'] == 'LocalFileStore')
        {
            $settings['password'] = password_hash($settings['password'], PASSWORD_BCRYPT);
            unset($settings['password_again']);
            Settings::set($settings);

            $fileStore = new LocalFileStore($config);

            $articlesXmlInit = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $articlesXmlInit .= '<!DOCTYPE articles SYSTEM "articles.dtd">' . PHP_EOL;
            $articlesXmlInit .= '<articles />';

            $categoriesInit = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $categoriesInit .= '<!DOCTYPE categories SYSTEM "categories.dtd">' . PHP_EOL;
            $categoriesInit .= '<categories />';

            $tagsInit = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $tagsInit .= '<!DOCTYPE tags SYSTEM "tags.dtd">' . PHP_EOL;
            $tagsInit .= '<tags />';

            $fileStore->saveXml(LocalFileStoreArticle::DESCRIPTOR_FILE, $articlesXmlInit);
            $fileStore->saveXml(LocalFileStoreCategory::fileName, $categoriesInit);
            $fileStore->saveXml(LocalFileStoreTag::DESCRIPTOR_FILE, $tagsInit);

            $uri = $request->getUri()->withPath($this->_ci->get('router')->pathFor('main'));

            return $response = $response->withRedirect($uri, 301);
        }
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    protected function randomString($length = 6) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }

        return $str;
    }
}