<?php

namespace Distvan;

/**
 * Interface iCache
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
interface iCache
{
    public static function getName($option);
    public static function create($option);
}