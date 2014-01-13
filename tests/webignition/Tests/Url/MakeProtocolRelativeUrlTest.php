<?php

namespace webignition\Tests\Url;
use webignition\Tests\Url\AbstractRegularUrlTest;

/**
 * Check that absolute URLs with schemes can be formed into a protocol-relative
 * equivalent
 *  
 */
class MakeProtocolRelativeUrlTest extends AbstractRegularUrlTest {   
    
    public function testNullifySchemeMakesUrlProtocolRelative() {        
        $url = new \webignition\Url\Url('http://example.com/foo/bar/index.html');
        $url->setScheme(null);        
        
        $this->assertEquals('//example.com/foo/bar/index.html', (string)$url);
    }
    
}