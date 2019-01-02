<?php

namespace Distvan;

use Slim\Router;
use SlimSession\Helper;


/**
 * Class AdminMiddleware
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class AdminMiddleware
{
    protected $_session;
    protected $_router;

    public function __construct(Helper $session, Router $router)
    {
        $this->_session = $session;
        $this->_router = $router;
    }

    /**
     * Setup middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if(!$this->_session->get('loggedin'))
        {
            $uri = $request->getUri()->withPath($this->_router->pathFor('main'));

            return $response->withRedirect($uri, 301);
        }

        return $next($request, $response);
    }
}