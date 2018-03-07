<?php

namespace webignition\Tests\Url;
use webignition\Url\Url;

/**
 * Check that URL properties can be set
 *  
 */
class SetPathTest extends AbstractRegularUrlTest {  
    
    public function testSetPlainPath() {
        $url = new Url('http://example.com/');
                
        $url->setPath('/path');        
        $this->assertEquals('/path', $url->getPath());
        $this->assertEquals('http://example.com/path', (string)$url);
    } 
    
    public function testSetPathWhenExistingPathIsUrlEncoded() {
        $url = new Url('js/scriptaculous.js?load=effects,builder');
        $this->assertEquals('js/scriptaculous.js?load=effects%2Cbuilder', (string)$url);
        
        $url->setPath('/js/scriptaculous.js');
        $this->assertEquals('/js/scriptaculous.js?load=effects%2Cbuilder', (string)$url);
    }    
    
    public function testSetPathWhenExistingPathContainsEncodedUrl() {        
        $url = new Url('http://example.com/task/http%3A%2F%2Fexample.com%2F/');
         
        $url->setPath('/additional' . (string)$url->getPath());
        $this->assertEquals('/additional/task/http%3A%2F%2Fexample.com%2F/', $url->getPath());
        $this->assertEquals('http://example.com/additional/task/http%3A%2F%2Fexample.com%2F/', (string)$url);        
        
    }

    public function testSetPathOnUrlWithPlusesInQuery() {
        $url = new Url('example.html?foo=++');
        $url->setPath('/example.html');

        $this->assertEquals('/example.html?foo=%2B%2B', (string)$url);
    }
}