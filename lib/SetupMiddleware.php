<?php

namespace Distvan;

use Distvan\Controller\Setup;

/**
 * Class SetupMiddleware
 * 
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class SetupMiddleware
{
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
        if(!Setup::isAlreadyInstalled())
        {
            $request = $request->withAttribute('setup', true);
        }

        return $next($request, $response);
    }
}