<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Slim\App;
use Slim\Views\PhpRenderer;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Slim\Middleware\Session;
use SlimSession\Helper;
use Slim\Csrf\Guard;
use Distvan\SetupMiddleware;
use Distvan\AdminMiddleware;
use Distvan\Config;
use Distvan\ErrorHandler;
use Distvan\PhpErrorHandler;

$config = new Config();
$c = $config->get();
$app = new App([
    'settings' => [
        'debug' => $c['devmode'],
        'displayErrorDetails' => $c['devmode'],
        'addContentLengthHeader' => false
    ]
]);

##### setup dependencies #########

$container = $app->getContainer();

$app->add(new Session(['name' => 'blog_session', 'autorefresh' => true]));

if(!$c['devmode'])
{
    $container['errorHandler'] = function () use ($container) {
        return new ErrorHandler($container->get('logger'));
    };

    $container['phpErrorHandler'] = function () use ($container) {
        return new PhpErrorHandler($container->get('logger'));
    };
}

$container['csrf'] = function(){
    return new Guard();
};

$container['view'] = new PhpRenderer($c['template']);

$container['logger'] = function()  {
    $logger = new Logger('blog');
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler(dirname(dirname(__FILE__)) . '/log/app.log'), Logger::DEBUG);
    return $logger;
};

$container['session'] = function(){
    return new Helper();
};

#### setup routes

### INSTALLATION ####

$app->get('/setup', 'Distvan\Controller\Setup:index')
    ->setName('setup');

$app->post('/install', 'Distvan\Controller\Setup:install')
    ->setName('install');

### ADMIN ######

$app->post('/admin/login', 'Distvan\Controller\Admin:login')
    ->setName('admin-login')
    ->add($container->get('csrf'));

$app->get('/admin/dashboard', 'Distvan\Controller\Admin:dashboard')
    ->setName('admin-dashboard')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/categories', 'Distvan\Controller\Admin:categories')
    ->setName('admin-categories')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/tags', 'Distvan\Controller\Admin:tags')
    ->setName('admin-tags')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/articles', 'Distvan\Controller\Admin:articles')
    ->setName('admin-articles')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/profile', 'Distvan\Controller\Admin:profile')
    ->setName('admin-profile')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/logout', 'Distvan\Controller\Admin:logout')
    ->setName('logout')
    ->add(new AdminMiddleware($container->get('session'), $container->get('router')));

$app->get('/admin/{key}', 'Distvan\Controller\Admin:index')
    ->setName('admin')
    ->add($container->get('csrf'));

### FRONTEND ###

$app->get('/', 'Distvan\Controller\Frontend:index')
    ->add(new SetupMiddleware())
    ->setName('main');

$app->get('/tags/{tag}', 'Distvan\Controller\Frontend:showTag');

$app->post('/search', 'Distvan\Controller\Frontend:search');

$app->get('/{category}', 'Distvan\Controller\Frontend:showCategory');

$app->get('/lang/{lang}/{year}/{month}/{day}/{article_url}', 'Distvan\Controller\Frontend:showArticle');

if($c['devmode'])
{
    $provider = new Kitchenu\Debugbar\ServiceProvider();
    $provider->register($app);
}

$app->run();