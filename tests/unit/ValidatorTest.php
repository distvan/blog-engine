<?php
use Distvan\Validator;

class ValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before(){}

    protected function _after(){}

    public function testValidUrl()
    {
        $validUrls = array(
            'valid_url_input',
            'valid-url-input',
            '123_VALID-URL',
            'itcontaints_only_alpha',
            'itcontaintsonlyalpha',
            'ITCONTAINTSONLYALPHA',
            '012223456789',
            '----',
            '____',
            'It-containts_everyTHing-and-89765',
            '1_3-9_012345',
            'AZazy',
            '/category/category-name'
        );
        
        foreach($validUrls as $url)
        {
            $this->assertTrue(Validator::isValidUrl($url));
        }
    }

    public function testInvalidUrl()
    {
        $invalidUrls = array(
            'invalid url because space',
            '  ',
            ' ',
            'invalid-url-becauseáéőüö',
            '?',
            '0123_-op.',
            'X0,',
            '+'
        );

        foreach($invalidUrls as $url)
        {
            $this->assertFalse(Validator::isValidUrl($url));
        }
    }
}