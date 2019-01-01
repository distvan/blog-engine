<?php
namespace Distvan\Controller;

use Interop\Container\ContainerInterface;

/**
 * Class Action
 *
 * @package Distvan\Controller
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Action
{
    protected $_ci;
    protected $_session;

    public function __construct(ContainerInterface $ci)
    {
        $this->_ci = $ci;
        $this->_session = $ci->get('session');
    }
}