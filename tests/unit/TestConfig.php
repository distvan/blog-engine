<?php

class TestConfig{

    public static function get()
    {
        return array(
            'default_language' => 'hu',
            'storage' => 'LocalFileStore',
            'param' => array(
                'path' => dirname(__DIR__) . '/_data/storage'
            ),
            'date_format' => 'Y-m-d'
        );
    }
}
?>