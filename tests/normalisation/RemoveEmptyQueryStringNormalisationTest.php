<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../../lib/bootstrap.php');

/**
 * Check that normalisation removes an empty query string
 *   
 */
class RemoveEmptyQueryStringNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlAddsTrailingSlash() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com/display?' => 'http://www.example.com/display/'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}