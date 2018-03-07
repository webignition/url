<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

/**
 * Test initialisation via init() instead of constructor
 *  
 */
class InitTest extends AbstractRegularUrlTest {      
    
    public function testInit() {
        $url = new \webignition\Url\Url();
        $url->init('http://example.com/foo/bar.html?foo=bar&foobar=boofar#identity');
        
        $this->assertTrue($url->hasScheme());
        $this->assertEquals('http', $url->getScheme());
        
        $this->assertTrue($url->hasHost());
        $this->assertEquals('example.com', $url->getHost());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals('/foo/bar.html', $url->getPath());
        
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('foo=bar&foobar=boofar', (string)$url->getQuery());
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals('identity', $url->getFragment());        
    } 
}