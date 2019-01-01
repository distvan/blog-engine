<?php

namespace Distvan;

class Language
{
    public static function load($className)
    {
        $config = new Config();
        $c = $config->get();

        $className = __NAMESPACE__ . '\\Language\\' . ucfirst($c['default_language']) . '\\' . $className;

        return new $className();
    }
}