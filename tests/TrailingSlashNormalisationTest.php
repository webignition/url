<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../lib/bootstrap.php');

/**
 * Check that normalisation appends a trailing slash to directory-ending URLs
 * http://www.example.com => http://www.example.com/
 *   
 */
class TrailingSlashNormalisationTest extends AbstractUrlTest {   
    
    public function testNormalisedUrlAddsTrailingSlash() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com' => 'http://www.example.com/',
            'http://www.example.com/' => 'http://www.example.com/',
            'http://www.example.com/part1' => 'http://www.example.com/part1/',
            'http://www.example.com/part1/' => 'http://www.example.com/part1/',
            'http://www.example.com/part1/part2' => 'http://www.example.com/part1/part2/',
            'http://www.example.com/part1/part2/' => 'http://www.example.com/part1/part2/',
            'http://www.example.com/part1/part2/example.html' => 'http://www.example.com/part1/part2/example.html'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}