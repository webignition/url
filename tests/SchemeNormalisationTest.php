<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that normalisation ignores scheme case
 *  
 */
class SchemeNormalisationTest extends AbstractUrlTest {   
    
    public function testNormalisedUrlIgnoresSchemeCase() {      
        $casedHttpUrls = array(
            'Http:'.$this->protocolRelativeUrl(),
            'hTtp:'.$this->protocolRelativeUrl()
        );
        
        $casedHttpsUrls = array(
            'httPS:'.$this->protocolRelativeUrl(),
            'HttpS:'.$this->protocolRelativeUrl(),
        );
        
        foreach ($casedHttpUrls as $casedHttpUrl) {
            $url = new \webignition\Url\Url($casedHttpUrl);
            $this->assertTrue($url->getScheme() === self::SCHEME_HTTP);
        }
        
        foreach ($casedHttpsUrls as $casedHttpsUrl) {
            $url = new \webignition\Url\Url($casedHttpsUrl);
            $this->assertTrue($url->getScheme() === self::SCHEME_HTTPS);
        }
    } 
}