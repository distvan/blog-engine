<?php

namespace Distvan;

use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Class PhpErrorHandler
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class PhpErrorHandler
{
    protected $_logger;

    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    public function __invoke(Request $request, Response $response, Throwable $error)
    {
        $this->_logger->addError('File:' . $error->getFile() . ' Line:' . $error->getLine() . ' Error:' . $error->getMessage());

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    }
}