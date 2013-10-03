<?php

/**
 * 
 */
class HashOnlyToStringTest extends AbstractRegularUrlTest {   
    
    public function testHashOnlyUrlResolvesToBlankString() {         
        $url = new \webignition\Url\Url('#');                        
        
        $this->assertEquals('#', (string)$url);
    }  
    
    public function testHashWithIdentifierResolvesToHashWithIdentifier() {         
        $url = new \webignition\Url\Url('#foo');                        
        
        $this->assertEquals('#foo', (string)$url);
    }      
}