<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that all the parts of a full, complete URL are present and valid
 *  
 */
class CompleteUrlTest extends AbstractUrlTest {   
    
    public function testStringRepresentation() {        
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasScheme());
        $this->assertEquals($this->urls['normalised-complete'], (string)$url);        
    }
    
    public function testScheme() {
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasScheme());
        $this->assertEquals($url->getScheme(), self::SCHEME_HTTP);
    }
    
    public function testHost() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasHost());
        $this->assertEquals($url->getHost(), self::HOST);
    }
    
    public function testPort() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasPort());
        $this->assertEquals($url->getPort(), self::PORT_REGULAR);
    }    

    public function testUser() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasUser());
        $this->assertEquals($url->getUser(), self::USER);
    }  
    
    public function testPass() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasPass());
        $this->assertEquals($url->getPass(), self::PASS);
    }      
    
    public function testPath() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals($url->getPath(), $this->completeUrlPath());
    }
    
    public function testQuery() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasQuery());       
        $this->assertEquals($this->sortedCompleteUrlQueryString(), (string)$url->getQuery());
    }
    
    public function testFragment() {      
        $url = new \webignition\Url\Url($this->urls['complete']);
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals($url->getFragment(), self::FRAGMENT);
    }    
    
}