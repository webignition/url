<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that a regular protocol-agnostic URL has all parts present
 * and recognised
 *  
 */
class ProtocolRelativeUrlTest extends AbstractUrlTest {   
    
    public function testScheme() {      
        $url = new \webignition\Url\Url($this->urls['protocol-agnostic-regular']);
        
        $this->assertFalse($url->hasScheme());
        $this->assertNull($url->getScheme());
    }
    
    public function testHost() {      
        $url = new \webignition\Url\Url($this->urls['protocol-agnostic-regular']);
        
        $this->assertTrue($url->hasHost());
        $this->assertEquals(self::HOST, $url->getHost());
    }
    
    public function testPort() {      
        $url = new \webignition\Url\Url($this->urls['protocol-agnostic-regular']);
        
        $this->assertTrue($url->hasPort());
        $this->assertEquals(self::PORT_REGULAR, $url->getPort());
    }  
    
    public function testPath() {      
        $url = new \webignition\Url\Url($this->urls['protocol-agnostic-regular']);
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals($this->completeUrlPath(), $url->getPath());
    }    
    
}