<?php

/**
 * Check that URL can be correctly parsed out into its component parts
 *  
 */
class ParseUrlContainingWhitespaceTest extends AbstractRegularUrlTest {   
    
    public function testTrailingTab() {        
        $url = new \webignition\Url\Url("http://example/page/\t");
        $this->assertEquals('http://example/page/', (string)$url);
    }

    public function testTrailingNewLine() {        
        $url = new \webignition\Url\Url("http://example/page/\n");
        $this->assertEquals('http://example/page/', (string)$url);
    }
    
    public function testTrailingLineReturn() {        
        $url = new \webignition\Url\Url("http://example/page/\r");
        $this->assertEquals('http://example/page/', (string)$url);
    }    
    
    public function testLeadingTab() {        
        $url = new \webignition\Url\Url("\thttp://example/page/");
        $this->assertEquals('http://example/page/', (string)$url);
    }

    public function testLeadingNewLine() {        
        $url = new \webignition\Url\Url("\nhttp://example/page/");
        $this->assertEquals('http://example/page/', (string)$url);
    }
    
    public function testLeadingLineReturn() {        
        $url = new \webignition\Url\Url("\rhttp://example/page/");
        $this->assertEquals('http://example/page/', (string)$url);
    }
    
    public function testTabInMiddle() {        
        $url = new \webignition\Url\Url("http://example/\tpage/");
        $this->assertEquals('http://example/page/', (string)$url);
    }    
    
    public function testNewlineInMiddle() {        
        $url = new \webignition\Url\Url("http://example/\npage/");
        $this->assertEquals('http://example/page/', (string)$url);
    } 
    
    public function testLineReturnInMiddle() {        
        $url = new \webignition\Url\Url("http://example/\rpage/");
        $this->assertEquals('http://example/page/', (string)$url);
    }  
    
    public function testManyTabsNewLinesAndLineReturns() {
        $url = new \webignition\Url\Url("\rhttp://example/\t\npage/\r");
        $this->assertEquals('http://example/page/', (string)$url);        
    }
}