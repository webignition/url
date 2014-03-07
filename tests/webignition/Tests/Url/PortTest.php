<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

/**
 *  
 */
class PortTest extends AbstractRegularUrlTest {      
    
    public function testGet()
    {        
        $url = new \webignition\Url\Url('http://example.com:8080/');
        $this->assertEquals(8080, $url->getPort());
        $this->assertTrue($url->hasPort());
    }

    public function testSet()
    {
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setPort(8080);
        $this->assertEquals(8080, $url->getPort());
        $this->assertTrue($url->hasPort());
    }

    public function testAdd()
    {
        $url = new \webignition\Url\Url('http://example.com/');
        $url->addPort(8080);
        $this->assertTrue($url->hasPort());
        $this->assertEquals(8080, $url->getPort());
        
    }

    public function testReplace()
    {
        $url = new \webignition\Url\Url('http://example.com:8080/');
        $this->assertTrue($url->hasPort());
        $this->assertEquals(8080, $url->getPort());

        $url->setPort(8888);
        $this->assertTrue($url->hasPort());
        $this->assertEquals(8888, $url->getPort());
    }

    public function testUnset()
    {
        $url = new \webignition\Url\Url('http://example.com:8080/');
        $this->assertTrue($url->hasPort());
        $this->assertEquals(8080, $url->getPort());

        $url->setPort(null);
        $this->assertFalse($url->hasPort());
        $this->assertEquals('http://example.com/', (string)$url);
    }
}
