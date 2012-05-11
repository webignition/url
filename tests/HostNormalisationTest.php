<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that normalisation ignores host case
 *  
 */
class HostNormalisationTest extends AbstractUrlTest {   
    
    public function testNormalisedUrlIgnoresHostCase() {      
        $casedHosts = array(
            'www.exaMPle.coM',
            'www.example.com',
            'WWW.example.COM',
            'wWw.examplE.com'
        );
        
        foreach ($casedHosts as $casedHost) {
            $url = new \webignition\Url\Url(self::SCHEME_HTTP.'://'.$casedHost.$this->completeUrlPath());
            $this->assertTrue($url->getHost() === self::HOST);
        }
    } 
}