<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

/**
 * Check that a path can be added to a hash-only url
 *  
 */
class SetPathOnHashOnlyUrlTest extends AbstractRegularUrlTest {      
    
    public function testAddPathToHashOnly() {        
        $url = new \webignition\Url\Url('#');
        $url->setPath('/index.html');
        
        $this->assertEquals('/index.html#', (string)$url);      
    }
    
    public function testAddPathToHashAndIdentifier() {        
        $url = new \webignition\Url\Url('#foo');
        $url->setPath('/index.html');
        
        $this->assertEquals('/index.html#foo', (string)$url);      
    }    
}