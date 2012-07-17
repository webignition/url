<?php
ini_set('display_errors', 'On');

/**
 * Check that a path can be added to a bare root url
 *  
 */
class SetPathOnRootUrlTest extends AbstractNormalisedUrlTest {      
    
    public function testAddPathToRootUrl() {        
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url->setPath('/index.html');
        
        $this->assertEquals('http://example.com/index.html', (string)$url);
    }
}