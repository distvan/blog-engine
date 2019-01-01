<?php

namespace Distvan;

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
    public function __construct(Helper $session)
    {
        $this->_session = $session;
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
            return $response->withStatus(401);
        }

        return $next($request, $response);
    }
}