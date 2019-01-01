<?php

namespace Distvan;

use Slim\Handlers\Error;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

/**
 * Class ErrorHandler
 * 
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class ErrorHandler extends Error
{
    public function __invoke(Request $request, Response $response, Exception $exception)
    {
        if($exception instanceof LocalFileStoreArticleException &&
            $exception->getCode()  == LocalFileStoreArticleException::EXCEPTION_ARTICLE_NOT_FOUND)
        {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found');
        }
        
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    }
}