<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check correct parsing of relative URLs of the form: * 
 * /path/exmaple.html
 *  
 */
class RelativeRootUrlTest extends AbstractUrlTest {   
    
    public function testScheme() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasScheme());
        $this->assertNull($url->getScheme());
    }
    
    public function testHost() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasHost());
        $this->assertNull($url->getHost());
    } 

    public function testUser() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasUser());
        $this->assertNull($url->getUser());
    }  
    
    public function testPass() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasPass());
        $this->assertNull($url->getPass());
    }      
    
    public function testPath() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals($url->getPath(), $this->relativeRootUrl());
    }
    
    public function testQuery() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasQuery());
        $this->assertNull($url->getQuery());
    }
    
    public function testFragment() {      
        $url = new \webignition\Url\Url($this->urls['relative-root-url']);
        
        $this->assertFalse($url->hasFragment());
        $this->assertNull($url->getFragment());
    }  
}