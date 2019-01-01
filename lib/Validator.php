<?php
namespace Distvan;

use Exception;
/**
 * Class Validator
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
class Validator
{
    /**
     * Check url containts only alpha english chars, numbers, hyphens, underscores, slashes
     *
     * @param $url
     * @return bool
     */
    public static function isValidUrl($url)
    {
        return !preg_match('/[^A-Za-z0-9-_\/]/', $url);
    }

    /**
     * Check input string length is between input range
     *
     * @param String $value
     * @param $min
     * @param $max
     * @return bool
     */
    public static function lengthIsValid($value, $min, $max)
    {
        return (strlen(trim($value)) >= $min && strlen(trim($value)) <= $max);
    }
    
    /**
     * Is input password week?
     *
     * @param $password
     * @return bool
     */
    public static function isWeekPassword($password)
    {
        return (strlen($password) < 6 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password));
    }
}
class ValidatorException extends Exception{}