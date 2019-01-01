<?php
namespace Distvan;

/**
 * Interface iStorage
 *
 * @package Distvan
 * @link https://www.dobrenteiistvan.hu
 * @author Istvan Dobrentei
 */
interface iStorage
{
    public function get();
    public function insert();
    public function update();
    public function delete();
    public function getTotalNumber();
}