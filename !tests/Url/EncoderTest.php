<?php

namespace webignition\Tests\Url;

use webignition\Url\Url;
use webignition\Url\Encoder;

class EncoderTest extends \PHPUnit_Framework_TestCase { 
    
    public function testNoPathNoQueryUrlRemainsUnchanged() {
        $url = new Url('http://example.com/');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertTrue((string)$url == (string)$encodedUrl);
    }
    
    public function testEncodeSingleSpaceInPath() {
        $url = new Url('http://example.com/foo bar/');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertEquals('http://example.com/foo%20bar/', (string)$encodedUrl);
    }
    
    public function testEncodeMultipleSpacesInPath() {
        $url = new Url('http://example.com/foo1 bar1/foo2 bar2/foo3 bar3/');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertEquals('http://example.com/foo1%20bar1/foo2%20bar2/foo3%20bar3/', (string)$encodedUrl);
    }    
    
    public function testEncodeSinglePercentInPath() {
        $url = new Url('http://example.com/foo%bar/');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertEquals('http://example.com/foo%25bar/', (string)$encodedUrl);
    }  
    
    
    public function testEncodeMultiplePercentsInPath() {
        $url = new Url('http://example.com/foo1%bar1/foo2%bar2/foo3%bar3/');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertEquals('http://example.com/foo1%25bar1/foo2%25bar2/foo3%25bar3/', (string)$encodedUrl);
    }
    
    public function testEncodeWithNoPath() {
        $url = new Url('http://example.com');
        $encoder = new Encoder();
        
        $encodedUrl = $encoder->encode($url);
        
        $this->assertEquals('http://example.com', (string)$encodedUrl);        
    }
    
}