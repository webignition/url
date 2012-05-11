<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that arguements in the query string are normalised into
 * alphabetical order by key
 *   
 */
class QueryArgumentNormalisationTest extends AbstractUrlTest {   
    
    public function testNormalisedUrlAlphabeticallyOrdersQueryStringArguments() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com?a=1&c=3&b=2' => 'http://www.example.com/?a=1&b=2&c=3'
        ));
        
        $this->runInputToExpectedOutputTests();
    }    
}