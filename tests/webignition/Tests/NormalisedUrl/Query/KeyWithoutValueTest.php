<?php

namespace webignition\Tests\NormalisedUrl\Query;

/**
 * Check that a query containing a key with no value is correctly handled
 * e.g http://example.com?key
 *     http://example.com?key1=value1&key2
 */
class KeyWithoutValueTest extends \PHPUnit_Framework_TestCase {      
    
    public function testKeyOnlyQueryIsPresentInString() {        
        $queryString = 'key';        
        $query = new \webignition\NormalisedUrl\Query\Query($queryString);        
        $this->assertEquals($queryString, (string)$query);
    } 
    
    public function testKeyOnlyAsPartOfQueryIsPresentInString() {
        $queryString = 'key1=value1&key2';        
        $query = new \webignition\NormalisedUrl\Query\Query($queryString);        
        $this->assertEquals($queryString, (string)$query);        
    }
}